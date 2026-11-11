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
            'title'       => ct_plain('seo.home.title'),
            'description' => ct_plain('seo.home.description'),
            'canonical'   => base_url(''),
            'ogImage'     => content_image('seo.og_image'),
            'contactSuccess' => Session::flash('contact_success'),
            'contactError'   => Session::flash('contact_error'),
            'contactOld'     => Session::flash('contact_old'),
            'scrollToContact' => Session::flash('scroll_contact') === '1',
        ]);
    }
}
