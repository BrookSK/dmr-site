<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Models\Setting;

/**
 * Landing page institucional da DMR.
 */
final class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('site/home', [
            'title'       => 'DMR Assessoria Imobiliária | Crédito Imobiliário e Financiamento',
            'description' => 'Crédito imobiliário com acompanhamento do início ao fim. Mais de 30 anos de experiência e atendimento próximo para construtoras, imobiliárias, corretores e clientes.',
            'canonical'   => base_url(''),
            'ogImage'     => asset('img/og-image.svg'),
            'contactSuccess' => Session::flash('contact_success'),
            'contactError'   => Session::flash('contact_error'),
            'contactOld'     => Session::flash('contact_old'),
            'scrollToContact' => Session::flash('scroll_contact') === '1',
        ]);
    }
}
