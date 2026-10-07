<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\User;
use App\Services\AuthService;

/**
 * Painel inicial do administrativo.
 */
final class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        $userModel = new User();
        $contactModel = new Contact();

        $smtp = Setting::group('smtp');
        $smtpConfigured = !empty($smtp['smtp_host']) && !empty($smtp['smtp_from_email']);

        $this->adminView('admin/dashboard/index', [
            'title'           => 'Dashboard',
            'totalUsers'      => $userModel->count(),
            'activeUsers'     => $userModel->countActive(),
            'totalContacts'   => $contactModel->count(),
            'recentContacts'  => $contactModel->recent(5),
            'smtpConfigured'  => $smtpConfigured,
            'siteName'        => Setting::get('site_name', 'DMR Assessoria Imobiliária'),
            'canManageUsers'  => AuthService::can('users.manage'),
            'canManageSettings' => AuthService::can('settings.manage'),
        ]);
    }
}
