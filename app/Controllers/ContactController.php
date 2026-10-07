<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Contact;
use App\Models\Setting;
use App\Services\MailService;
use Throwable;

/**
 * Processa o formulário de contato/orçamento da landing page.
 *
 * Fluxo: valida -> checa honeypot -> persiste o lead -> envia e-mail via SMTP
 * configurado no painel. Nenhuma credencial fica no código.
 */
final class ContactController extends Controller
{
    /** Rótulos legíveis para o tipo de atendimento. */
    private const AUDIENCES = [
        'cliente'       => 'Cliente final',
        'corretor'      => 'Corretor',
        'imobiliaria'   => 'Imobiliária',
        'construtora'   => 'Construtora',
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
            Session::flash('contact_success', 'Mensagem enviada. Em breve entraremos em contato.');
            Session::flash('scroll_contact', '1');
            $this->redirect('/#contato');
        }

        $name     = $request->string('name');
        $email     = $request->string('email');
        $phone     = $request->string('phone');
        $audience  = $request->string('audience', 'outro');
        $message   = $request->string('message');

        $errors = $this->validate($name, $email, $phone);
        if ($errors !== null) {
            $this->fail($errors, compact('name', 'email', 'phone', 'audience', 'message'));
        }

        if (!array_key_exists($audience, self::AUDIENCES)) {
            $audience = 'outro';
        }

        // Limita tamanho da mensagem para evitar abuso.
        $message = mb_substr($message, 0, 3000);

        $contact = new Contact();
        $sent = false;

        // Tenta enviar e-mail (não bloqueia o registro do lead em caso de falha).
        try {
            $mailer = new MailService();
            if ($mailer->isConfigured()) {
                $toEmail = (string) Setting::get('contact_email', Setting::get('smtp_from_email'));
                if ($toEmail !== '') {
                    $mailer->send(
                        $toEmail,
                        (string) Setting::get('site_name', 'DMR Assessoria Imobiliária'),
                        'Novo contato pelo site — ' . self::AUDIENCES[$audience],
                        $this->emailBody($name, $email, $phone, $audience, $message),
                        $email,
                        $name
                    );
                    $sent = true;
                }
            }
        } catch (Throwable $e) {
            error_log('[DMR][contato] Falha no envio de e-mail: ' . $e->getMessage());
            $sent = false;
        }

        $contact->store([
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'audience'   => $audience,
            'message'    => $message,
            'ip'         => $request->ip(),
            'user_agent' => mb_substr($request->userAgent(), 0, 255),
            'email_sent' => $sent,
        ]);

        Session::flash('contact_success', 'Recebemos sua mensagem. A DMR entrará em contato em breve.');
        Session::flash('scroll_contact', '1');
        $this->redirect('/#contato');
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

    private function emailBody(string $name, string $email, string $phone, string $audience, string $message): string
    {
        $name = e($name);
        $email = e($email);
        $phone = e($phone);
        $audienceLabel = e(self::AUDIENCES[$audience] ?? 'Outro');
        $message = nl2br(e($message !== '' ? $message : '(sem mensagem)'));
        $date = date('d/m/Y H:i');

        return <<<HTML
<div style="font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;max-width:600px;margin:0 auto;">
  <div style="background:#000;color:#CBAE6C;padding:22px;text-align:center;font-size:20px;letter-spacing:3px;">DMR</div>
  <div style="padding:24px;border:1px solid #EAE6DF;border-top:none;">
    <h2 style="color:#A3805F;margin-top:0;">Novo contato pelo site</h2>
    <table style="width:100%;border-collapse:collapse;font-size:15px;">
      <tr><td style="padding:8px 0;color:#6f6760;width:150px;">Nome</td><td style="padding:8px 0;"><strong>{$name}</strong></td></tr>
      <tr><td style="padding:8px 0;color:#6f6760;">E-mail</td><td style="padding:8px 0;">{$email}</td></tr>
      <tr><td style="padding:8px 0;color:#6f6760;">Telefone</td><td style="padding:8px 0;">{$phone}</td></tr>
      <tr><td style="padding:8px 0;color:#6f6760;">Tipo</td><td style="padding:8px 0;">{$audienceLabel}</td></tr>
    </table>
    <h3 style="color:#A3805F;margin-bottom:6px;">Mensagem</h3>
    <p style="background:#faf8f4;padding:14px;border-radius:8px;margin:0;">{$message}</p>
    <p style="color:#999;font-size:13px;margin-top:20px;">Recebido em {$date}.</p>
  </div>
</div>
HTML;
    }
}
