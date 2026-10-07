<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

/**
 * Páginas institucionais: Política de Privacidade e Termos de Uso.
 */
final class LegalController extends Controller
{
    public function privacy(Request $request): void
    {
        $this->view('site/legal/privacy', [
            'title'       => 'Política de Privacidade | DMR Assessoria Imobiliária',
            'description' => 'Saiba como a DMR Assessoria Imobiliária coleta, utiliza e protege seus dados pessoais.',
            'canonical'   => base_url('politica-de-privacidade'),
            'solidHeader' => true,
        ]);
    }

    public function terms(Request $request): void
    {
        $this->view('site/legal/terms', [
            'title'       => 'Termos de Uso | DMR Assessoria Imobiliária',
            'description' => 'Condições de uso do site da DMR Assessoria Imobiliária.',
            'canonical'   => base_url('termos-de-uso'),
            'solidHeader' => true,
        ]);
    }
}
