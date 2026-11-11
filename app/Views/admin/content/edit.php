<?php
/** @var string $slug */
/** @var array<string,mixed> $page */
/** @var array<string,array<string,mixed>> $pages */
/** @var string|null $success */
/** @var string|null $error */
/** @var string|null $openSection */

use App\Core\Csrf;
use App\Models\Content;
use App\Services\ContentRegistry;
use App\Services\ContentRenderer;

$help = static fn (array $f): string => !empty($f['help']) ? '<small class="muted">' . e($f['help']) . '</small>' : '';

/** Prévia + campo de upload de imagem. $name/$fileName = nomes dos inputs. */
$imageInput = static function (string $id, string $fileName, string $current, string $hiddenName = '', string $removeName = '', bool $isCustom = false): string {
    $url = $current !== '' ? media_url($current) : '';
    $html = '<div class="img-field">';
    $html .= '<div class="img-field__preview" data-preview>' . ($url !== '' ? '<img src="' . e($url) . '" alt="">' : '<span class="muted">Sem imagem</span>') . '</div>';
    $html .= '<div class="img-field__actions">';
    $html .= '<input type="file" id="' . e($id) . '" name="' . e($fileName) . '" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" data-image-input>';
    if ($hiddenName !== '') {
        $html .= '<input type="hidden" name="' . e($hiddenName) . '" value="' . e($current) . '">';
    }
    if ($removeName !== '' && $isCustom) {
        $html .= '<label class="checkbox checkbox--inline"><input type="checkbox" name="' . e($removeName) . '" value="1"> <span>Voltar para a imagem original</span></label>';
    }
    $html .= '</div></div>';
    return $html;
};

/** Um subcampo dentro de item de lista. */
$subInput = static function (array $sub, string $listKey, string $uid, array $item) use ($imageInput): string {
    $name = 'f[' . $listKey . '][' . $uid . '][' . $sub['key'] . ']';
    $id = 'c-' . md5($name);
    $val = (string) ($item[$sub['key']] ?? $sub['default'] ?? '');
    $label = '<label for="' . e($id) . '">' . e($sub['label']) . '</label>';
    switch ($sub['type']) {
        case 'textarea':
            return '<div class="form-group">' . $label . '<textarea id="' . e($id) . '" name="' . e($name) . '" rows="3">' . e($val) . '</textarea></div>';
        case 'image':
            return '<div class="form-group">' . $label . $imageInput($id, 'files[' . $listKey . '][' . $uid . '][' . $sub['key'] . ']', $val, $name) . '</div>';
        case 'bool':
            return '<label class="checkbox checkbox--inline"><input type="checkbox" name="' . e($name) . '" value="1"' . ($val === '1' ? ' checked' : '') . '> <span>' . e($sub['label']) . '</span></label>';
        case 'icon':
        case 'select':
            $options = $sub['type'] === 'icon' ? ContentRegistry::ICONS : ($sub['options'] ?? []);
            $opts = '';
            foreach ($options as $ov => $ol) {
                $opts .= '<option value="' . e((string) $ov) . '"' . ((string) $ov === $val ? ' selected' : '') . '>' . e($ol) . '</option>';
            }
            return '<div class="form-group">' . $label . '<select id="' . e($id) . '" name="' . e($name) . '">' . $opts . '</select></div>';
        default:
            return '<div class="form-group">' . $label . '<input type="text" id="' . e($id) . '" name="' . e($name) . '" value="' . e($val) . '" maxlength="500"></div>';
    }
};

/** Um item (card) da lista. */
$listItem = static function (array $field, string $uid, array $item, int $n) use ($subInput): string {
    $html = '<div class="rep-item" data-rep-item>';
    $html .= '<div class="rep-item__bar"><span class="rep-item__n" data-rep-n>' . $n . '</span>';
    $html .= '<div class="rep-item__tools">'
        . '<button type="button" class="btn btn--ghost btn--sm" data-rep-up title="Mover para cima" aria-label="Mover para cima">↑</button>'
        . '<button type="button" class="btn btn--ghost btn--sm" data-rep-down title="Mover para baixo" aria-label="Mover para baixo">↓</button>'
        . '<button type="button" class="btn btn--danger btn--sm" data-rep-remove>Remover</button>'
        . '</div></div><div class="rep-item__body">';
    foreach ($field['item'] as $sub) {
        $html .= $subInput($sub, $field['key'], $uid, $item);
    }
    return $html . '</div></div>';
};
?>
<?php if (!empty($success)): ?><div class="alert alert--ok"><?= e($success) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>

<nav class="content-nav" aria-label="Páginas do conteúdo">
    <a href="<?= e(base_url('admin/conteudo')) ?>" class="content-nav__back">← Todas</a>
    <?php foreach ($pages as $ps => $p): ?>
        <a href="<?= e(base_url('admin/conteudo/' . $ps)) ?>" class="content-nav__link<?= $ps === $slug ? ' is-active' : '' ?>"><?= e($p['title']) ?></a>
    <?php endforeach; ?>
</nav>

<div class="content-head">
    <p class="muted"><?= e($page['description']) ?> Clique numa seção para abrir.</p>
    <a class="btn btn--ghost btn--sm" href="<?= e(base_url($page['url'])) ?>" target="_blank" rel="noopener">Ver no site ↗</a>
</div>

<?php foreach ($page['sections'] as $sectionSlug => $section):
    $customized = count(array_filter($section['fields'], static fn (array $f): bool => Content::has($f['key'])));
?>
<details class="panel content-section" id="sec-<?= e($sectionSlug) ?>"<?= $openSection === $sectionSlug ? ' open' : '' ?>>
    <summary class="content-section__summary">
        <span class="content-section__title"><?= e($section['title']) ?></span>
        <?php if ($customized > 0): ?><span class="pill pill--ok">Editado</span><?php endif; ?>
    </summary>

    <?php if (!empty($section['help'])): ?><p class="alert alert--warn content-section__help"><?= e($section['help']) ?></p><?php endif; ?>

    <form action="<?= e(base_url('admin/conteudo/' . $slug . '/' . $sectionSlug)) ?>" method="post" enctype="multipart/form-data" class="form">
        <?= Csrf::field() ?>
        <?php foreach ($section['fields'] as $field):
            $key = $field['key'];
            $id = 'c-' . md5($key);
            $value = ContentRenderer::value($key);
            $isCustom = Content::has($key);
            $name = 'f[' . $key . ']';
        ?>
            <?php if ($field['type'] === 'list'): $items = is_array($value) ? $value : []; $max = (int) ($field['max'] ?? 50); ?>
                <fieldset class="form-group rep" data-repeater data-max="<?= $max ?>">
                    <legend><?= e($field['label']) ?></legend>
                    <?= $help($field) ?>
                    <div class="rep-list" data-rep-list>
                        <?php foreach ($items as $i => $item): ?>
                            <?= $listItem($field, 'i' . $i, $item, $i + 1) ?>
                        <?php endforeach; ?>
                    </div>
                    <template data-rep-template><?= $listItem($field, '__UID__', [], 0) ?></template>
                    <button type="button" class="btn btn--ghost rep-add" data-rep-add>+ <?= e($field['add_label'] ?? 'Adicionar') ?></button>
                    <?php /* Garante que a lista seja enviada mesmo vazia (para poder apagar todos). */ ?>
                    <input type="hidden" name="<?= e($name) ?>[__keep]" value="">
                </fieldset>
            <?php elseif ($field['type'] === 'image'): ?>
                <div class="form-group">
                    <label for="<?= e($id) ?>"><?= e($field['label']) ?></label>
                    <?= $imageInput($id, 'files[' . $key . ']', (string) $value, '', 'remove[' . $key . ']', $isCustom) ?>
                    <?= $help($field) ?>
                </div>
            <?php elseif (in_array($field['type'], ['textarea', 'paragraphs', 'markup'], true)): ?>
                <div class="form-group">
                    <label for="<?= e($id) ?>"><?= e($field['label']) ?></label>
                    <textarea id="<?= e($id) ?>" name="<?= e($name) ?>" rows="<?= $field['type'] === 'markup' ? 18 : ($field['type'] === 'paragraphs' ? 7 : 3) ?>"<?= $field['type'] === 'markup' ? ' class="is-code"' : '' ?>><?= e((string) $value) ?></textarea>
                    <?= $help($field) ?>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="<?= e($id) ?>"><?= e($field['label']) ?></label>
                    <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>" maxlength="500">
                    <?= $help($field) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="content-section__actions">
            <button type="submit" class="btn btn--primary">Salvar seção</button>
        </div>
    </form>

    <?php if ($customized > 0): ?>
    <form action="<?= e(base_url('admin/conteudo/' . $slug . '/' . $sectionSlug . '/restaurar')) ?>" method="post" class="content-section__restore" data-confirm="Restaurar os textos e imagens originais desta seção? As alterações feitas aqui serão perdidas.">
        <?= Csrf::field() ?>
        <button type="submit" class="btn btn--danger btn--sm">Restaurar padrão desta seção</button>
    </form>
    <?php endif; ?>
</details>
<?php endforeach; ?>
