<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Setting;
use App\Services\BrandLogoService;
use App\Services\LeadMail;
use App\Services\MailService;
use RuntimeException;
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
            'contact_address' => mb_substr($request->string('contact_address'), 0, 255),
            'branch_city'     => mb_substr($request->string('branch_city'), 0, 120),
            'branch_phone'    => mb_substr($request->string('branch_phone'), 0, 40),
            'branch_whatsapp' => preg_replace('/\D+/', '', $request->string('branch_whatsapp')),
            'instagram'       => ltrim($request->string('instagram'), '@'),
        ];

        if ($values['contact_email'] !== '' && !filter_var($values['contact_email'], FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Informe um e-mail de contato válido.');
            Session::flash('active_tab', 'general');
            $this->redirect('/admin/configuracoes');
        }

        try {
            $this->syncLogo($request, 'site_logo', 'dark', (bool) $request->input('remove_site_logo'));
            $this->syncLogo($request, 'site_logo_on_light', 'light', (bool) $request->input('remove_site_logo_on_light'));
        } catch (RuntimeException $e) {
            Session::flash('error', $e->getMessage());
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

        // E-mail(s) que recebem os contatos: aceita vários, separados por vírgula.
        $notify = array_values(array_unique(array_filter(array_map('trim', preg_split('/[,;\s]+/', $request->string('notify_email')) ?: []))));
        foreach ($notify as $mail) {
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                Session::flash('error', 'E-mail inválido em "quem recebe os contatos": ' . $mail);
                Session::flash('active_tab', 'smtp');
                $this->redirect('/admin/configuracoes');
            }
        }
        $values['notify_email'] = implode(', ', $notify);

        // A senha é marcada como secreta; se vier vazia, mantém a atual.
        Setting::updateMany($values, ['smtp_password']);
        if (Setting::get('notify_email') !== null) {
            // Garante o grupo correto mesmo se a migration 014 não tiver criado a chave.
            \App\Core\Database::instance()->run("UPDATE settings SET group_name = 'smtp' WHERE key_name = 'notify_email'");
        }

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

        // O teste usa o mesmo layout do e-mail de contato real, com dados de exemplo.
        $lead = LeadMail::sample();

        try {
            (new MailService())->send(
                $to,
                '',
                LeadMail::subject($lead, true),
                LeadMail::html($lead, true),
                null,
                null,
                LeadMail::text($lead, true)
            );
            Session::flash('success', 'E-mail de teste enviado para ' . $to . ' com o mesmo layout dos contatos reais. Verifique a caixa de entrada (e o spam).');
        } catch (Throwable $e) {
            Session::flash('error', 'Falha no envio: ' . $e->getMessage());
        }

        Session::flash('active_tab', 'smtp');
        $this->redirect('/admin/configuracoes');
    }

    /**
     * "Limpa o cache" do site:
     *  - troca a versão global dos assets (?v=), então os navegadores dos
     *    visitantes baixam CSS, JS e imagens de novo na próxima visita;
     *  - zera o OPcache do PHP, se ativo, para arquivos .php recém-enviados
     *    entrarem em vigor imediatamente.
     */
    public function clearCache(Request $request): void
    {
        $this->requireCsrf($request);

        Setting::set('cache_version', (string) time(), false, 'system');

        $opcache = false;
        if (function_exists('opcache_reset')) {
            $opcache = @opcache_reset();
        }

        Session::flash('success', 'Cache limpo. Os visitantes verão a versão mais recente do site'
            . ($opcache ? ' (OPcache do PHP também foi reiniciado).' : '.'));
        Session::flash('active_tab', 'general');
        $this->redirect('/admin/configuracoes');
    }

    /**
     * @param 'dark'|'light' $slot
     */
    private function syncLogo(Request $request, string $settingKey, string $slot, bool $remove): void
    {
        $current = Setting::get($settingKey);
        $current = is_string($current) ? $current : null;
        $file = $request->file($settingKey);

        if ($remove && $file === null) {
            BrandLogoService::delete($current);
            Setting::set($settingKey, '');
            return;
        }

        if ($file === null) {
            return;
        }

        $stored = BrandLogoService::store($file, $slot);
        if ($current && $current !== $stored) {
            BrandLogoService::delete($current);
        }
        Setting::set($settingKey, $stored);
    }

    /** Mostra no navegador como chega o e-mail de contato (dados de exemplo). */
    public function emailPreview(Request $request): void
    {
        header('X-Robots-Tag: noindex');
        \App\Core\Response::html(LeadMail::html(LeadMail::sample(), true));
    }
}
