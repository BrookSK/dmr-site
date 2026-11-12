<?php
/** @var string|null $contactSuccess */
/** @var string|null $contactError */
/** @var array|null $contactOld */
/** @var bool $scrollToContact */

/*
 * Página inicial. Todos os textos e imagens vêm de "Conteúdo do site" no painel
 * (App\Services\ContentRegistry guarda os padrões). Telefones, endereço e
 * Instagram vêm de Configurações → Geral.
 */

use App\Core\Csrf;

$offices = offices();
$whats = $offices['matriz']['whatsapp'];
$insta = ltrim((string) setting('instagram', 'dmrassessoriaoficial'), '@');
$old = is_array($contactOld ?? null) ? $contactOld : [];
$waLink = static fn (string $msg): string => 'https://wa.me/' . $whats . '?text=' . rawurlencode($msg);

// Propósito: quando preenchido no painel, substitui Missão, Visão e Valores.
$hasPurpose = trim((string) content('home.purpose.text')) !== '';

// Canal de atendimento ativo no formulário (B2C = quero financiar, B2B = sou parceiro).
$partnerProfiles = [
    'corretor'    => ct_plain('home.contact.profile_corretor'),
    'imobiliaria' => ct_plain('home.contact.profile_imobiliaria'),
    'construtora' => ct_plain('home.contact.profile_construtora'),
];
$interests = array_values(array_filter(array_map(static fn (array $i): string => trim($i['label']), content_list('home.contact.interests'))));
$channel = array_key_exists((string) ($old['audience'] ?? ''), $partnerProfiles) ? 'b2b' : 'b2c';
$oldC = $channel === 'b2c' ? $old : [];
$oldP = $channel === 'b2b' ? $old : [];
$optional = ' <small>(opcional)</small>';

$stats = content_list('home.stats.items');
$steps = content_list('home.steps.items');
$testimonials = array_values(array_filter(content_list('home.testimonials.items'), static fn (array $t): bool => trim($t['text']) !== ''));
$faq = array_values(array_filter(content_list('home.faq.items'), static fn (array $f): bool => trim($f['q']) !== ''));
?>

<!-- ============================ HERO ============================ -->
<section class="hero">
    <picture>
        <source media="(max-width: 760px)" srcset="<?= e(content_image('home.hero.image_mobile')) ?>">
        <img class="hero__img" src="<?= e(content_image('home.hero.image')) ?>" alt=""<?= content_image_dims('home.hero.image') ?> fetchpriority="high" decoding="async">
    </picture>
    <div class="hero__shade" aria-hidden="true"></div>
    <div class="container hero__inner">
        <div class="hero__content reveal">
            <span class="eyebrow eyebrow--light"><?= ct('home.hero.eyebrow') ?></span>
            <h1 class="hero__title"><?= ct_rich('home.hero.title') ?></h1>
            <p class="hero__text"><?= ct('home.hero.text') ?></p>
            <div class="hero__actions">
                <a class="btn btn--gold" href="#canais"><?= ct('home.hero.button_primary') ?></a>
                <a class="btn btn--ghost-light" href="<?= e($waLink(ct_plain('home.hero.whatsapp_message'))) ?>" target="_blank" rel="noopener"><?= ct('home.hero.button_whatsapp') ?></a>
            </div>
        </div>
    </div>
</section>

<!-- ============================ NÚMEROS ============================ -->
<?php if ($stats !== []): ?>
<section class="stats" aria-label="Números da DMR">
    <div class="container">
        <ul class="stats__grid reveal" style="--cols: <?= count($stats) ?>">
            <?php foreach ($stats as $s): ?>
                <li class="stat"><strong><?= e($s['value']) ?></strong><span><?= e($s['label']) ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>

<!-- ============================ CANAIS (B2C / B2B) ============================ -->
<section class="section section--channels" id="canais" aria-label="Escolha como a DMR pode ajudar">
    <div class="container">
        <div class="channels reveal">
            <article class="channel">
                <div class="channel__art" aria-hidden="true">
                    <svg viewBox="0 0 320 160" fill="none"><path d="M55 91 160 25l105 66v57H55V91Z" stroke="currentColor" stroke-width="3"/><path d="M125 148v-52h70v52M40 91l120-77 120 77" stroke="currentColor" stroke-width="3"/><circle cx="224" cy="57" r="13" stroke="currentColor" stroke-width="2.5"/></svg>
                </div>
                <div class="channel__body">
                    <span class="eyebrow"><?= ct('home.channels.b2c_eyebrow') ?></span>
                    <h2 class="channel__title"><?= ct('home.channels.b2c_title') ?></h2>
                    <p><?= ct('home.channels.b2c_text') ?></p>
                    <a class="btn btn--dark btn--block" href="#para-voce"><?= ct('home.channels.b2c_button') ?></a>
                </div>
            </article>
            <article class="channel">
                <div class="channel__art" aria-hidden="true">
                    <svg viewBox="0 0 320 160" fill="none"><path d="M50 148V49h89v99M139 148V18h131v130M78 77h32M78 103h32M78 129h32M168 47h27M215 47h27M168 75h27M215 75h27M168 103h27M215 103h27M186 148v-23h39v23" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/></svg>
                </div>
                <div class="channel__body">
                    <span class="eyebrow"><?= ct('home.channels.b2b_eyebrow') ?></span>
                    <h2 class="channel__title"><?= ct('home.channels.b2b_title') ?></h2>
                    <p><?= ct('home.channels.b2b_text') ?></p>
                    <a class="btn btn--dark btn--block" href="#para-parceiros"><?= ct('home.channels.b2b_button') ?></a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ============================ PARA VOCÊ (B2C) ============================ -->
<section class="section section--soft" id="para-voce">
    <div class="container split">
        <div class="reveal">
            <span class="eyebrow"><?= ct('home.b2c.eyebrow') ?></span>
            <h2 class="section__title"><?= ct_rich('home.b2c.title') ?></h2>
            <p class="section__lead"><?= ct('home.b2c.lead') ?></p>
            <a class="btn btn--gold" href="#contato" data-channel="b2c"><?= ct('home.b2c.button') ?></a>
        </div>
        <div class="panel reveal">
            <h3 class="panel__title"><?= ct('home.b2c.panel_title') ?></h3>
            <ul class="check-list">
                <?php foreach (content_list('home.b2c.items') as $item): if (trim($item['text']) === '') { continue; } ?>
                    <li><?= icon('check', 18) ?> <?= e($item['text']) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if (trim((string) content('home.b2c.note')) !== ''): ?>
                <p class="small-note"><?= ct('home.b2c.note') ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================ PARA PARCEIROS (B2B) ============================ -->
<section class="section" id="para-parceiros">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow"><?= ct('home.b2b.eyebrow') ?></span>
            <h2 class="section__title"><?= ct_rich('home.b2b.title') ?></h2>
            <p class="section__lead"><?= ct('home.b2b.lead') ?></p>
        </div>
        <div class="partners reveal">
            <?php foreach (content_list('home.b2b.items') as $p): ?>
                <article class="partner">
                    <span class="partner__ico"><?= icon($p['icon'] !== '' ? $p['icon'] : 'perfil') ?></span>
                    <h3><?= e($p['title']) ?></h3>
                    <p><?= nl2br(e($p['text']), false) ?></p>
                    <?php if (trim($p['link']) !== ''): ?>
                        <a class="partner__link" href="#contato" data-channel="b2b"<?= array_key_exists($p['profile'], $partnerProfiles) ? ' data-profile="' . e($p['profile']) . '"' : '' ?>><?= e($p['link']) ?> <span aria-hidden="true">→</span></a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================ COMO FUNCIONA ============================ -->
<section class="section section--soft" id="como-funciona">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow"><?= ct('home.steps.eyebrow') ?></span>
            <h2 class="section__title"><?= ct_rich('home.steps.title') ?></h2>
            <p class="section__lead"><?= ct('home.steps.lead') ?></p>
        </div>
        <ol class="steps reveal">
            <?php foreach ($steps as $i => $s): ?>
                <li class="step"><span class="step__n"><?= sprintf('%02d', $i + 1) ?></span><h3><?= e($s['title']) ?></h3><p><?= nl2br(e($s['text']), false) ?></p></li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- ============================ SOBRE A DMR ============================ -->
<section class="section" id="sobre">
    <div class="container about">
        <figure class="about__photo reveal">
            <img src="<?= e(content_image('home.about.image')) ?>" alt="<?= e(ct_plain('home.about.image_alt')) ?>"<?= content_image_dims('home.about.image') ?> loading="lazy" decoding="async">
        </figure>
        <div class="about__text reveal">
            <span class="eyebrow"><?= ct('home.about.eyebrow') ?></span>
            <h2 class="section__title"><?= ct_rich('home.about.title') ?></h2>
            <?= ct_paragraphs('home.about.text') ?>
            <a class="btn btn--dark" href="#contato"><?= ct('home.about.button') ?></a>
        </div>
    </div>
</section>

<?php if ($hasPurpose): ?>
<!-- ===================== NOSSO PROPÓSITO (substitui Missão, Visão e Valores) ===================== -->
<section class="section section--soft" id="o-que-nos-orienta" aria-labelledby="proposito-titulo">
    <div class="container">
        <div class="purpose-statement reveal">
            <span class="eyebrow"><?= ct('home.purpose.eyebrow') ?></span>
            <h2 class="section__title" id="proposito-titulo"><?= ct_rich('home.purpose.title') ?></h2>
            <div class="purpose-statement__text"><?= ct_paragraphs('home.purpose.text') ?></div>
        </div>
        <?php $pillars = array_values(array_filter(content_list('home.purpose.pillars'), static fn (array $p): bool => trim($p['title']) !== '')); ?>
        <?php if ($pillars !== []): ?>
            <ul class="pillars reveal" style="--cols: <?= count($pillars) ?>">
                <?php foreach ($pillars as $p): ?>
                    <li class="pillar">
                        <span class="pillar__ico"><?= icon($p['icon'] !== '' ? $p['icon'] : 'cuidado', 26) ?></span>
                        <strong><?= e($p['title']) ?></strong>
                        <?php if (trim($p['text']) !== ''): ?><span><?= e($p['text']) ?></span><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
<?php else: ?>
<!-- ===================== O QUE NOS ORIENTA (MISSÃO, VISÃO E VALORES) ===================== -->
<!-- Provisório: sai automaticamente quando o propósito for preenchido no painel. -->
<section class="section section--soft" id="o-que-nos-orienta" aria-labelledby="orienta-titulo">
    <div class="container">
        <div class="section__head section__head--wide reveal">
            <span class="eyebrow"><?= ct('home.mvv.eyebrow') ?></span>
            <h2 class="section__title" id="orienta-titulo"><?= ct_rich('home.mvv.title') ?></h2>
            <p class="section__lead"><?= ct('home.mvv.lead') ?></p>
        </div>

        <div class="purpose reveal">
            <article class="purpose__card">
                <span class="purpose__ico"><?= icon('alvo') ?></span>
                <h3><?= ct('home.mvv.mission_title') ?></h3>
                <p><?= ct('home.mvv.mission_text') ?></p>
            </article>
            <article class="purpose__card">
                <span class="purpose__ico"><?= icon('visao') ?></span>
                <h3><?= ct('home.mvv.vision_title') ?></h3>
                <p><?= ct('home.mvv.vision_text') ?></p>
            </article>
        </div>

        <div class="values reveal">
            <div class="values__head">
                <span class="purpose__ico"><?= icon('cuidado') ?></span>
                <h3><?= ct('home.mvv.values_title') ?></h3>
            </div>
            <ul class="values__grid">
                <?php foreach (content_list('home.mvv.values') as $v): ?>
                    <li class="value"><strong><?= e($v['title']) ?></strong><p><?= e($v['text']) ?></p></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if (trim((string) content('home.mvv.quote')) !== ''): ?>
            <p class="purpose__quote reveal"><?= ct('home.mvv.quote') ?></p>
        <?php endif; ?>

        <?php if (trim((string) content('home.mvv.details')) !== ''): ?>
        <details class="purpose__detail reveal">
            <summary><?= ct('home.mvv.details_label') ?></summary>
            <div class="purpose__detail-body"><?= ct_markup('home.mvv.details', 'h3') ?></div>
        </details>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ============================ INSTITUIÇÕES ============================ -->
<section class="section section--tight" id="bancos">
    <div class="container">
        <div class="section__head section__head--center reveal">
            <span class="eyebrow"><?= ct('home.banks.eyebrow') ?></span>
            <h2 class="section__title"><?= ct_rich('home.banks.title') ?></h2>
            <p class="section__lead"><?= ct('home.banks.lead') ?></p>
        </div>
        <ul class="banks reveal">
            <?= \App\Core\View::renderPartial('site/partials/banks') ?>
        </ul>
    </div>
</section>

<!-- ============================ DEPOIMENTOS ============================ -->
<?php if ($testimonials !== []): ?>
<section class="section section--soft" id="depoimentos">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow"><?= ct('home.testimonials.eyebrow') ?></span>
            <h2 class="section__title"><?= ct_rich('home.testimonials.title') ?></h2>
            <p class="section__lead"><?= ct('home.testimonials.lead') ?></p>
        </div>
        <ul class="testimonials reveal">
            <?php foreach ($testimonials as $t): ?>
                <li class="testimonial">
                    <figure>
                        <span class="testimonial__stars" role="img" aria-label="Avaliação 5 de 5">★★★★★</span>
                        <blockquote><p><?= nl2br(e($t['text']), false) ?></p></blockquote>
                        <figcaption>
                            <span class="testimonial__avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr(trim($t['name']) !== '' ? trim($t['name']) : 'D', 0, 1))) ?></span>
                            <span><strong><?= e($t['name']) ?></strong><small><?= e($t['role']) ?></small></span>
                        </figcaption>
                    </figure>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>

<!-- ============================ PERGUNTAS FREQUENTES ============================ -->
<?php if ($faq !== []): ?>
<section class="section" id="perguntas">
    <div class="container faq">
        <div class="section__head reveal">
            <span class="eyebrow"><?= ct('home.faq.eyebrow') ?></span>
            <h2 class="section__title"><?= ct_rich('home.faq.title') ?></h2>
        </div>
        <div class="faq__list reveal">
            <?php foreach ($faq as $f): ?>
                <details class="faq__item">
                    <summary><?= e($f['q']) ?></summary>
                    <p><?= nl2br(e($f['a']), false) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================ CONTATO ============================ -->
<section class="section section--dark" id="contato">
    <div class="container cta">
        <div class="cta__intro reveal">
            <span class="eyebrow eyebrow--light"><?= ct('home.contact.eyebrow') ?></span>
            <h2 class="section__title section__title--light"><?= ct_rich('home.contact.title') ?></h2>
            <p class="cta__text"><?= ct('home.contact.text') ?></p>
            <div class="offices">
                <?php foreach ($offices as $office): ?>
                    <div class="office">
                        <p class="office__label"><?= e($office['label']) ?> · <?= e($office['city']) ?></p>
                        <ul class="cta__contacts">
                            <li><a href="https://wa.me/<?= e($office['whatsapp']) ?>" target="_blank" rel="noopener"><span><?= icon('whatsapp', 18) ?></span><?= e($office['phone']) ?> · WhatsApp</a></li>
                            <?php if ($office['address'] !== ''): ?>
                                <li><a href="<?= e('https://www.google.com/maps/search/?api=1&query=' . rawurlencode($office['address'])) ?>" target="_blank" rel="noopener"><span><?= icon('local', 18) ?></span><address><?= e($office['address']) ?></address></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($insta !== ''): ?>
            <ul class="cta__contacts cta__contacts--social">
                <li><a href="https://instagram.com/<?= e($insta) ?>" target="_blank" rel="noopener"><span><?= icon('instagram', 18) ?></span>@<?= e($insta) ?></a></li>
            </ul>
            <?php endif; ?>
        </div>

        <div class="cta__form reveal">
            <?php if (!empty($contactSuccess)): ?><div class="form-alert form-alert--ok"><?= e($contactSuccess) ?></div><?php endif; ?>
            <?php if (!empty($contactError)): ?><div class="form-alert form-alert--error"><?= e($contactError) ?></div><?php endif; ?>

            <div class="tabs" role="tablist" aria-label="Tipo de atendimento">
                <button class="tabs__btn" type="button" role="tab" id="tab-b2c" aria-controls="form-b2c" aria-selected="<?= $channel === 'b2c' ? 'true' : 'false' ?>" tabindex="<?= $channel === 'b2c' ? '0' : '-1' ?>" data-tab="b2c"><?= ct('home.contact.tab_b2c') ?></button>
                <button class="tabs__btn" type="button" role="tab" id="tab-b2b" aria-controls="form-b2b" aria-selected="<?= $channel === 'b2b' ? 'true' : 'false' ?>" tabindex="<?= $channel === 'b2b' ? '0' : '-1' ?>" data-tab="b2b"><?= ct('home.contact.tab_b2b') ?></button>
            </div>

            <!-- Canal B2C: comprador -->
            <form id="form-b2c" role="tabpanel" aria-labelledby="tab-b2c" action="<?= e(base_url('contato')) ?>" method="post" class="contact-form" novalidate<?= $channel === 'b2c' ? '' : ' hidden' ?>>
                <?= Csrf::field() ?>
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="audience" value="cliente">
                <div class="field">
                    <label for="c-name"><?= ct('home.contact.b2c_name') ?></label>
                    <input type="text" id="c-name" name="name" required autocomplete="name" value="<?= old('name', $oldC) ?>">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="c-phone"><?= ct('home.contact.phone') ?></label>
                        <input type="tel" id="c-phone" name="phone" required autocomplete="tel" value="<?= old('phone', $oldC) ?>">
                    </div>
                    <div class="field">
                        <label for="c-email"><?= ct('home.contact.email') ?></label>
                        <input type="email" id="c-email" name="email" required autocomplete="email" value="<?= old('email', $oldC) ?>">
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="c-city"><?= ct('home.contact.city') ?><?= $optional ?></label>
                        <input type="text" id="c-city" name="city" autocomplete="address-level2" value="<?= old('city', $oldC) ?>">
                    </div>
                    <?php if ($interests !== []): ?>
                    <div class="field">
                        <label for="c-interest"><?= ct('home.contact.interest') ?></label>
                        <select id="c-interest" name="interest">
                            <?php $selI = (string) ($oldC['interest'] ?? $interests[0]); foreach ($interests as $label): ?>
                                <option value="<?= e($label) ?>" <?= $selI === $label ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="field">
                    <label for="c-message"><?= ct('home.contact.message') ?><?= $optional ?></label>
                    <textarea id="c-message" name="message" rows="3"><?= old('message', $oldC) ?></textarea>
                </div>
                <?php if (trim((string) content('home.contact.b2c_note')) !== ''): ?>
                    <p class="form-note"><?= ct('home.contact.b2c_note') ?></p>
                <?php endif; ?>
                <button type="submit" class="btn btn--gold btn--block"><?= ct('home.contact.b2c_button') ?></button>
                <?= ct_paragraphs('home.contact.privacy', 'form-privacy') ?>
            </form>

            <!-- Canal B2B: parceiros -->
            <form id="form-b2b" role="tabpanel" aria-labelledby="tab-b2b" action="<?= e(base_url('contato')) ?>" method="post" class="contact-form" novalidate<?= $channel === 'b2b' ? '' : ' hidden' ?>>
                <?= Csrf::field() ?>
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="field-row">
                    <div class="field">
                        <label for="p-name"><?= ct('home.contact.b2b_name') ?></label>
                        <input type="text" id="p-name" name="name" required autocomplete="name" value="<?= old('name', $oldP) ?>">
                    </div>
                    <div class="field">
                        <label for="p-audience"><?= ct('home.contact.b2b_profile') ?></label>
                        <select id="p-audience" name="audience" required>
                            <?php $selP = $oldP['audience'] ?? 'corretor'; foreach ($partnerProfiles as $v => $l): ?>
                                <option value="<?= e($v) ?>" <?= $selP === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="p-company"><?= ct('home.contact.b2b_company') ?><?= $optional ?></label>
                        <input type="text" id="p-company" name="company" autocomplete="organization" value="<?= old('company', $oldP) ?>">
                    </div>
                    <div class="field">
                        <label for="p-city"><?= ct('home.contact.city') ?><?= $optional ?></label>
                        <input type="text" id="p-city" name="city" autocomplete="address-level2" value="<?= old('city', $oldP) ?>">
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="p-phone"><?= ct('home.contact.phone') ?></label>
                        <input type="tel" id="p-phone" name="phone" required autocomplete="tel" value="<?= old('phone', $oldP) ?>">
                    </div>
                    <div class="field">
                        <label for="p-email"><?= ct('home.contact.b2b_email') ?></label>
                        <input type="email" id="p-email" name="email" required autocomplete="email" value="<?= old('email', $oldP) ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="p-message"><?= ct('home.contact.b2b_message') ?><?= $optional ?></label>
                    <textarea id="p-message" name="message" rows="3"><?= old('message', $oldP) ?></textarea>
                </div>
                <button type="submit" class="btn btn--gold btn--block"><?= ct('home.contact.b2b_button') ?></button>
                <?= ct_paragraphs('home.contact.privacy', 'form-privacy') ?>
            </form>
        </div>
    </div>
</section>

<?php if ($scrollToContact): ?>
<script>window.addEventListener('load',function(){var c=document.getElementById('contato');if(c){c.scrollIntoView({behavior:'smooth'});}});</script>
<?php endif; ?>
