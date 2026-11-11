<?php
// Textos editáveis em Conteúdo do site → Páginas de erro. Se o banco falhar
// (causa comum de erro 500), usa os textos fixos.
try {
    $errTitle = ct('errors.500.title');
    $errText = ct('errors.500.text');
    $errButton = ct('errors.button');
} catch (Throwable $e) {
    $errTitle = 'Algo deu errado';
    $errText = 'Ocorreu um erro inesperado. Tente novamente em instantes.';
    $errButton = 'Voltar ao início';
}
?>
<section class="error-page">
    <div class="container">
        <p class="error-page__code">500</p>
        <h1 class="section__title"><?= $errTitle ?></h1>
        <p class="section__lead" style="margin:0 auto 24px;"><?= $errText ?></p>
        <a class="btn btn--primary" href="<?= e(base_url('')) ?>"><?= $errButton ?></a>
    </div>
</section>
