<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Contact;
use App\Services\LeadMail;
use App\Services\MailService;
use Throwable;

/**
 * Processa o formulário de contato da landing page.
 *
 * Fluxo: valida -> checa honeypot -> envia e-mail aos destinatários
 * configurados no painel -> persiste o lead. Nenhuma credencial fica no código.
 */
final class ContactController extends Controller
{
    /** Tipos de atendimento aceitos (chave gravada no banco => rótulo interno). */
    private const AUDIENCES = [
        'cliente'       => 'Comprador(a)',
        'corretor'      => 'Corretor',
        'imobiliaria'   => 'Imobiliária',
        'construtora'   => 'Construtora / incorporadora',
        'incorporadora' => 'Incorporadora',
        'home-equity'   => 'Home Equity',
        'outro'         => 'Outro',
    ];

    public function submit(Request $request): void
    {
        $this->requireCsrf($request);

        // Honeypot: bots preenchem o campo oculto "website".
        if ($request->string('website') !== '') {
            // Finge sucesso para não dar pistas ao bot.
            Session::flash('contact_success', ct_plain('home.contact.success'));
            Session::flash('scroll_contact', '1');
            $this->redirect('/#contato');
        }

        $name     = mb_substr($request->string('name'), 0, 150);
        $email    = mb_substr($request->string('email'), 0, 190);
        $phone    = mb_substr($request->string('phone'), 0, 40);
        $audience = $request->string('audience', 'outro');
        $message  = mb_substr($request->string('message'), 0, 3000);
        // Campos complementares dos canais "Quero financiar" e "Sou parceiro".
        $interest = $request->string('interest');
        $company  = mb_substr($request->string('company'), 0, 120);
        $city     = mb_substr($request->string('city'), 0, 120);

        $error = $this->validate($name, $email, $phone);
        if ($error !== null) {
            $this->fail($error, compact('name', 'email', 'phone', 'audience', 'message', 'interest', 'company', 'city'));
        }

        if (!array_key_exists($audience, self::AUDIENCES)) {
            $audience = 'outro';
        }
        // Só aceita uma das opções cadastradas no painel.
        $validInterests = array_map(static fn (array $i): string => trim($i['label']), content_list('home.contact.interests'));
        if (!in_array($interest, $validInterests, true)) {
            $interest = '';
        }

        $isPartner = in_array($audience, ['corretor', 'imobiliaria', 'construtora', 'incorporadora'], true);
        $lead = [
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'channel'     => $isPartner ? ct_plain('home.contact.tab_b2b') : ct_plain('home.contact.tab_b2c'),
            'profile'     => $this->profileLabel($audience),
            'interest'    => $interest,
            'company'     => $company,
            'city'        => $city,
            'message'     => $message,
            'received_at' => date('d/m/Y \à\s H:i'),
        ];

        // Envia para cada destinatário (falha de e-mail não impede salvar o lead).
        $sent = false;
        try {
            $mailer = new MailService();
            if ($mailer->isConfigured()) {
                $html = LeadMail::html($lead);
                $text = LeadMail::text($lead);
                foreach (LeadMail::recipients() as $to) {
                    try {
                        $mailer->send($to, '', LeadMail::subject($lead), $html, $email, $name, $text);
                        $sent = true;
                    } catch (Throwable $e) {
                        error_log('[DMR][contato] Falha no envio para ' . $to . ': ' . $e->getMessage());
                    }
                }
            }
        } catch (Throwable $e) {
            error_log('[DMR][contato] Falha no envio de e-mail: ' . $e->getMessage());
        }

        // A tabela de contatos não tem colunas para interesse/empresa/cidade;
        // eles entram como cabeçalho da mensagem salva.
        $details = array_filter([
            $interest !== '' ? 'Interesse: ' . $interest : null,
            $company !== '' ? 'Empresa: ' . $company : null,
            $city !== '' ? 'Cidade/UF: ' . $city : null,
        ]);
        $stored = $details !== [] ? implode("\n", $details) . ($message !== '' ? "\n\n" . $message : '') : $message;

        (new Contact())->store([
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'audience'   => $audience,
            'message'    => $stored,
            'ip'         => $request->ip(),
            'user_agent' => mb_substr($request->userAgent(), 0, 255),
            'email_sent' => $sent,
        ]);

        Session::flash('contact_success', ct_plain('home.contact.success'));
        Session::flash('scroll_contact', '1');
        $this->redirect('/#contato');
    }

    /** Rótulo do perfil igual ao que a pessoa viu no formulário. */
    private function profileLabel(string $audience): string
    {
        return match ($audience) {
            'cliente'     => self::AUDIENCES['cliente'],
            'corretor'    => ct_plain('home.contact.profile_corretor'),
            'imobiliaria' => ct_plain('home.contact.profile_imobiliaria'),
            'construtora' => ct_plain('home.contact.profile_construtora'),
            default       => self::AUDIENCES[$audience] ?? 'Outro',
        };
    }

    /** @return string|null mensagem de erro ou null se válido */
    private function validate(string $name, string $email, string $phone): ?string
    {
        if (mb_strlen($name) < 2) {
            return 'Informe seu nome.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Informe um e-mail válido.';
        }
        $digits = preg_replace('/\D+/', '', $phone);
        if (strlen((string) $digits) < 10) {
            return 'Informe um telefone/WhatsApp válido com DDD.';
        }
        return null;
    }

    /** @param array<string,string> $old */
    private function fail(string $error, array $old): void
    {
        Session::flash('contact_error', $error);
        Session::flash('contact_old', $old);
        Session::flash('scroll_contact', '1');
        $this->redirect('/#contato');
    }
}
