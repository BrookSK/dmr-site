<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Setting;
use App\Services\AuthService;
use App\Services\MailService;
use Throwable;

/**
 * Configurações gerais e de SMTP.
 *
 * Credenciais sensíveis (senha SMTP) são armazenadas criptografadas no banco,
 * nunca em arquivos PHP. Ver App\Models\Setting.
 */
final class SettingController extends Controller
{
    public function index(Request $request): void
    {
        $this->adminView('admin/settings/index', [
            'title'   => 'Configurações',
            'general' => Setting::group('general'),
            'smtp'    => Setting::group('smtp'),
            'success' => Session::flash('success'),
            'error'   => Session::flash('error'),
            'activeTab' => Session::flash('active_tab') ?? 'general',
        ]);
    }

    public function saveGeneral(Request $request): void
    {
        $this->requireCsrf($request);

        $values = [
            'site_name'       => $request->string('site_name'),
            'site_url'        => rtrim($request->string('site_url'), '/'),
            'contact_email'   => $request->string('contact_email'),
            'contact_phone'   => $request->string('contact_phone'),
            'whatsapp_number' => preg_replace('/\D+/', '', $request->string('whatsapp_number')),
            'instagram'       => ltrim($request->string('instagram'), '@'),
        ];

        if ($values['contact_email'] !== '' && !filter_var($values['contact_email'], FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Informe um e-mail de contato válido.');
            Session::flash('active_tab', 'general');
            $this->redirect('/admin/configuracoes');
        }

        Setting::updateMany($values);

        Session::flash('success', 'Configurações gerais salvas.');
        Session::flash('active_tab', 'general');
        $this->redirect('/admin/configuracoes');
    }

    public function saveSmtp(Request $request): void
    {
        $this->requireCsrf($request);

        $values = [
            'smtp_host'       => $request->string('smtp_host'),
            'smtp_port'       => preg_replace('/\D+/', '', $request->string('smtp_port')) ?: '587',
            'smtp_username'   => $request->string('smtp_username'),
            'smtp_password'   => (string) $request->input('smtp_password', ''),
            'smtp_encryption' => in_array($request->string('smtp_encryption'), ['tls', 'ssl', 'none'], true)
                ? $request->string('smtp_encryption') : 'tls',
            'smtp_from_name'  => $request->string('smtp_from_name'),
            'smtp_from_email' => $request->string('smtp_from_email'),
        ];

        if ($values['smtp_from_email'] !== '' && !filter_var($values['smtp_from_email'], FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'O e-mail remetente (From) é inválido.');
            Session::flash('active_tab', 'smtp');
            $this->redirect('/admin/configuracoes');
        }

        // A senha é marcada como secreta; se vier vazia, mantém a atual.
        Setting::updateMany($values, ['smtp_password']);

        Session::flash('success', 'Configurações de SMTP salvas.');
        Session::flash('active_tab', 'smtp');
        $this->redirect('/admin/configuracoes');
    }

    public function testSmtp(Request $request): void
    {
        $this->requireCsrf($request);

        $to = $request->string('test_email');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Informe um e-mail de destino válido para o teste.');
            Session::flash('active_tab', 'smtp');
            $this->redirect('/admin/configuracoes');
        }

        $user = AuthService::user();
        $html = $this->testEmailHtml($user['name'] ?? 'Administrador');

        try {
            (new MailService())->send(
                $to,
                'Teste DMR',
                'Teste de SMTP — DMR Assessoria Imobiliária',
                $html
            );
            Session::flash('success', 'E-mail de teste enviado para ' . $to . '. Verifique a caixa de entrada.');
        } catch (Throwable $e) {
            Session::flash('error', 'Falha no envio: ' . $e->getMessage());
        }

        Session::flash('active_tab', 'smtp');
        $this->redirect('/admin/configuracoes');
    }

    private function testEmailHtml(string $name): string
    {
        $name = e($name);
        $date = date('d/m/Y H:i');
        return <<<HTML
<div style="font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;max-width:560px;margin:0 auto;">
  <div style="background:#000;color:#CBAE6C;padding:24px;text-align:center;font-size:22px;letter-spacing:2px;">DMR</div>
  <div style="padding:24px;border:1px solid #EAE6DF;border-top:none;">
    <h2 style="color:#A3805F;">Configuração de SMTP funcionando</h2>
    <p>Olá, {$name}.</p>
    <p>Este é um e-mail de teste enviado pelo painel administrativo da DMR Assessoria Imobiliária.</p>
    <p>Se você recebeu esta mensagem, as credenciais de SMTP estão corretas e o envio de e-mails está operacional.</p>
    <p style="color:#777;font-size:13px;">Enviado em {$date}.</p>
  </div>
</div>
HTML;
    }
}
