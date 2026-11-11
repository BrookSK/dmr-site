<?php
/**
 * Logos das instituições (editáveis em Conteúdo do site → Página inicial →
 * Instituições financeiras). Padrões em public/assets/img/banks/.
 */
?>
<?php foreach (content_list('home.banks.items') as $b): if (trim($b['image']) === '') { continue; } ?>
    <li class="bank<?= $b['square'] === '1' ? ' bank--square' : '' ?>">
        <img src="<?= e(media_url($b['image'])) ?>" alt="<?= e($b['name']) ?>" loading="lazy" decoding="async">
    </li>
<?php endforeach; ?>
