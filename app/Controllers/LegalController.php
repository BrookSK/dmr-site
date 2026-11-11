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
        $this->view('site/legal/page', [
            'prefix'      => 'privacy',
            'title'       => ct_plain('seo.privacy.title'),
            'description' => ct_plain('seo.privacy.description'),
            'canonical'   => base_url('politica-de-privacidade'),
            'solidHeader' => true,
        ]);
    }

    public function terms(Request $request): void
    {
        $this->view('site/legal/page', [
            'prefix'      => 'terms',
            'title'       => ct_plain('seo.terms.title'),
            'description' => ct_plain('seo.terms.description'),
            'canonical'   => base_url('termos-de-uso'),
            'solidHeader' => true,
        ]);
    }
}
