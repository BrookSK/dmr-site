<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Contact;

/**
 * Painel → Contatos recebidos: lista e detalhes dos envios do formulário do site.
 * Exige a permissão contacts.view (middleware CanViewContacts).
 */
final class LeadController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'q'        => mb_substr($request->string('q'), 0, 100),
            'audience' => $request->string('tipo'),
            'status'   => $request->string('status'),
        ];
        $page = max(1, (int) $request->string('pagina', '1'));

        $model = new Contact();
        $this->adminView('admin/leads/index', [
            'title'    => 'Contatos recebidos',
            'filters'  => $filters,
            'result'   => $model->search($filters, $page, 20),
            'newCount' => $model->countByStatus('novo'),
            'success'  => Session::flash('success'),
        ]);
    }

    /** @param array<string,string> $params */
    public function show(Request $request, array $params): void
    {
        $model = new Contact();
        $lead = $model->find((int) ($params['id'] ?? 0));
        if ($lead === null) {
            Session::flash('success', 'Este contato não existe mais.');
            $this->redirect('/admin/contatos');
        }

        $this->adminView('admin/leads/show', [
            'title'   => 'Contato de ' . $lead['name'],
            'lead'    => $lead,
            'parsed'  => Contact::parseMessage($lead['message'] ?? ''),
            'newerId' => $model->neighbor((int) $lead['id'], (string) $lead['created_at'], true),
            'olderId' => $model->neighbor((int) $lead['id'], (string) $lead['created_at'], false),
            'success' => Session::flash('success'),
        ]);
    }

    /** @param array<string,string> $params */
    public function updateStatus(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $id = (int) ($params['id'] ?? 0);
        $status = $request->string('status');

        if (array_key_exists($status, Contact::STATUSES)) {
            (new Contact())->updateStatus($id, $status);
            Session::flash('success', 'Situação alterada para "' . Contact::STATUSES[$status] . '".');
        }
        $this->redirect('/admin/contatos/' . $id);
    }
}
