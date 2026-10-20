<?php
/** @var string|null $contactSuccess */
/** @var string|null $contactError */
/** @var array|null $contactOld */
/** @var bool $scrollToContact */

use App\Core\Csrf;

$whats = preg_replace('/\D+/', '', (string) setting('whatsapp_number', '5511982231363'));
$phone = (string) setting('contact_phone', '(11) 98223-1363');
$insta = ltrim((string) setting('instagram', 'dmrassessoria'), '@');
$old = is_array($contactOld ?? null) ? $contactOld : [];
$waLink = static fn (string $msg): string => 'https://wa.me/' . $whats . '?text=' . rawurlencode($msg);
?>

<!-- ============================ HERO ============================ -->
<section class="hero">
    <picture>
        <source media="(max-width: 760px)" srcset="<?= e(asset('img/hero-mobile.jpg')) ?>">
        <img class="hero__img" src="<?= e(asset('img/hero.jpg')) ?>" alt="" width="2400" height="1632" fetchpriority="high" decoding="async">
    </picture>
    <div class="hero__shade" aria-hidden="true"></div>
    <div class="container hero__inner">
        <div class="hero__content reveal">
            <span class="eyebrow eyebrow--light">Assessoria em crédito imobiliário</span>
            <h1 class="hero__title">Seu financiamento conduzido por quem tem <em>30 anos</em> de experiência.</h1>
            <p class="hero__text">Análise, documentação, aprovação no banco e registro. A DMR cuida de cada etapa até você receber as chaves.</p>
            <div class="hero__actions">
                <a class="btn btn--gold" href="<?= e($waLink('Olá! Quero financiar meu imóvel com a DMR.')) ?>" target="_blank" rel="noopener">Simular meu financiamento</a>
                <a class="btn btn--ghost-light" href="#servicos">Conheça os serviços</a>
            </div>
        </div>
    </div>
</section>

<!-- ============================ NÚMEROS ============================ -->
<section class="stats" aria-label="Números da DMR">
    <div class="container">
        <ul class="stats__grid reveal">
            <li class="stat"><strong>30+</strong><span>anos no mercado imobiliário</span></li>
            <li class="stat"><strong>100 mil+</strong><span>unidades financiadas</span></li>
            <li class="stat"><strong>5</strong><span>grandes bancos parceiros</span></li>
            <li class="stat"><strong>100%</strong><span>do processo acompanhado, até as chaves</span></li>
        </ul>
    </div>
</section>

<!-- ============================ QUEM SOMOS ============================ -->
<section class="section" id="sobre">
    <div class="container about">
        <figure class="about__photo reveal">
            <img src="<?= e(asset('img/proprietaria.jpg')) ?>" alt="Fundadora da DMR Assessoria Imobiliária" width="800" height="1000" loading="lazy" decoding="async">
        </figure>
        <div class="about__text reveal">
            <span class="eyebrow">Quem é a DMR</span>
            <h2 class="section__title">Experiência que encurta o caminho até a casa própria.</h2>
            <p>Há mais de três décadas a DMR assessora compradores, corretores, imobiliárias e construtoras em todas as etapas do crédito imobiliário. Mais de 100 mil unidades já passaram pelas nossas mãos.</p>
            <p>Somos correspondentes bancários e trabalhamos com os maiores bancos do país para encontrar a condição certa para cada perfil — com atendimento próximo e alguém que conhece o seu processo do início ao fim.</p>
            <ul class="about__list">
                <li><?= icon('check', 18) ?> Atendimento direto, sem intermediários</li>
                <li><?= icon('check', 18) ?> Acompanhamento em tempo real</li>
                <li><?= icon('check', 18) ?> Equipe com mais de 20 anos de mercado</li>
            </ul>
        </div>
    </div>
</section>

<!-- ============================ SERVIÇOS ============================ -->
<section class="section section--soft" id="servicos">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Serviços</span>
            <h2 class="section__title">Tudo o que o seu financiamento precisa, em um só lugar.</h2>
        </div>
        <div class="services reveal">
            <article class="service">
                <span class="service__ico"><?= icon('credito') ?></span>
                <h3>Financiamento imobiliário</h3>
                <p>Comparamos as condições dos bancos e conduzimos a contratação.</p>
            </article>
            <article class="service">
                <span class="service__ico"><?= icon('analise') ?></span>
                <h3>Análise de crédito</h3>
                <p>Avaliamos renda e perfil antes de avançar, para aprovar com segurança.</p>
            </article>
            <article class="service">
                <span class="service__ico"><?= icon('juridico') ?></span>
                <h3>Análise jurídica e registro</h3>
                <p>Documentação, certidões, ITBI e cartório sem você correr atrás.</p>
            </article>
            <article class="service">
                <span class="service__ico"><?= icon('fgts') ?></span>
                <h3>Uso do FGTS e Home Equity</h3>
                <p>FGTS na compra e crédito com garantia de imóvel quitado.</p>
            </article>
        </div>
    </div>
</section>

<!-- ============================ COMO FUNCIONA ============================ -->
<section class="section" id="como-funciona">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Como funciona</span>
            <h2 class="section__title">Quatro etapas. Uma equipe com você em todas.</h2>
        </div>
        <ol class="steps reveal">
            <li class="step"><span class="step__n">01</span><h3>Análise</h3><p>Entendemos seu perfil e as melhores opções de crédito.</p></li>
            <li class="step"><span class="step__n">02</span><h3>Documentação</h3><p>Organizamos e enviamos tudo o que o banco pede.</p></li>
            <li class="step"><span class="step__n">03</span><h3>Aprovação</h3><p>Acompanhamos a análise junto à instituição.</p></li>
            <li class="step"><span class="step__n">04</span><h3>Chaves na mão</h3><p>Contrato, registro e liberação do seu imóvel.</p></li>
        </ol>
    </div>
</section>

<!-- ============================ BANCOS ============================ -->
<section class="section section--soft section--tight" id="bancos">
    <div class="container">
        <div class="section__head section__head--center reveal">
            <span class="eyebrow">Bancos parceiros</span>
            <h2 class="section__title">Trabalhamos com os maiores bancos do país.</h2>
        </div>
        <ul class="banks reveal">
            <?= \App\Core\View::renderPartial('site/partials/banks') ?>
        </ul>
    </div>
</section>

<!-- ============================ CONTATO ============================ -->
<section class="section section--dark" id="contato">
    <div class="container cta">
        <div class="cta__intro reveal">
            <span class="eyebrow eyebrow--light">Fale com a DMR</span>
            <h2 class="section__title section__title--light">Vamos tirar o seu imóvel do papel?</h2>
            <p class="cta__text">Conte o seu momento e um especialista entra em contato para orientar o melhor caminho.</p>
            <ul class="cta__contacts">
                <li><a href="https://wa.me/<?= e($whats) ?>" target="_blank" rel="noopener"><span><?= icon('externo', 18) ?></span><?= e($phone) ?> · WhatsApp</a></li>
                <li><a href="https://instagram.com/<?= e($insta) ?>" target="_blank" rel="noopener"><span><?= icon('externo', 18) ?></span>@<?= e($insta) ?></a></li>
            </ul>
        </div>

        <div class="cta__form reveal">
            <?php if (!empty($contactSuccess)): ?><div class="form-alert form-alert--ok"><?= e($contactSuccess) ?></div><?php endif; ?>
            <?php if (!empty($contactError)): ?><div class="form-alert form-alert--error"><?= e($contactError) ?></div><?php endif; ?>

            <form action="<?= e(base_url('contato')) ?>" method="post" class="contact-form" novalidate>
                <?= Csrf::field() ?>
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="field">
                    <label for="name">Nome</label>
                    <input type="text" id="name" name="name" required autocomplete="name" value="<?= old('name', $old) ?>">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="phone">WhatsApp</label>
                        <input type="tel" id="phone" name="phone" required autocomplete="tel" value="<?= old('phone', $old) ?>">
                    </div>
                    <div class="field">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required autocomplete="email" value="<?= old('email', $old) ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="audience">Você é</label>
                    <select id="audience" name="audience">
                        <?php
                        $opts = ['cliente' => 'Comprador de imóvel', 'corretor' => 'Corretor', 'imobiliaria' => 'Imobiliária', 'construtora' => 'Construtora / Incorporadora', 'home-equity' => 'Interessado em Home Equity', 'outro' => 'Outro'];
                        $sel = $old['audience'] ?? 'cliente';
                        foreach ($opts as $v => $l): ?>
                            <option value="<?= e($v) ?>" <?= $sel === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="message">Mensagem <small>(opcional)</small></label>
                    <textarea id="message" name="message" rows="3"><?= old('message', $old) ?></textarea>
                </div>
                <button type="submit" class="btn btn--gold btn--block">Quero ser atendido</button>
                <p class="form-privacy">Ao enviar, você concorda com a <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>.</p>
            </form>
        </div>
    </div>
</section>

<?php if ($scrollToContact): ?>
<script>window.addEventListener('load',function(){var c=document.getElementById('contato');if(c){c.scrollIntoView({behavior:'smooth'});}});</script>
<?php endif; ?>
