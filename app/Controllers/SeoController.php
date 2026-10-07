<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

/**
 * Recursos de SEO gerados dinamicamente: robots.txt e sitemap.xml.
 */
final class SeoController extends Controller
{
    public function robots(Request $request): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        $sitemap = base_url('sitemap.xml');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /admin/\n\n";
        echo "Sitemap: {$sitemap}\n";
    }

    public function sitemap(Request $request): void
    {
        header('Content-Type: application/xml; charset=UTF-8');

        $urls = [
            ['loc' => base_url(''), 'priority' => '1.0', 'changefreq' => 'monthly'],
            ['loc' => base_url('politica-de-privacidade'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => base_url('termos-de-uso'), 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        $today = date('Y-m-d');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo "  <url>\n";
            echo '    <loc>' . e($u['loc']) . "</loc>\n";
            echo "    <lastmod>{$today}</lastmod>\n";
            echo "    <changefreq>{$u['changefreq']}</changefreq>\n";
            echo "    <priority>{$u['priority']}</priority>\n";
            echo "  </url>\n";
        }
        echo '</urlset>';
    }
}
