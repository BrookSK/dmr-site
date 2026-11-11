<?php
// Textos editáveis em Conteúdo do site → Páginas de erro. Se o banco falhar,
// usa os textos fixos para a página de erro nunca quebrar.
try {
    $errTitle = ct('errors.404.title');
    $errText = ct('errors.404.text');
    $errButton = ct('errors.button');
} catch (Throwable $e) {
    $errTitle = 'Página não encontrada';
    $errText = 'O endereço que você procura não existe ou foi movido.';
    $errButton = 'Voltar ao início';
}
?>
<section class="error-page">
    <div class="container">
        <p class="error-page__code">404</p>
        <h1 class="section__title"><?= $errTitle ?></h1>
        <p class="section__lead" style="margin:0 auto 24px;"><?= $errText ?></p>
        <a class="btn btn--primary" href="<?= e(base_url('')) ?>"><?= $errButton ?></a>
    </div>
</section>
