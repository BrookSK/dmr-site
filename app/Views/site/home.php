<?php
/** @var string|null $contactSuccess */
/** @var string|null $contactError */
/** @var array|null $contactOld */
/** @var bool $scrollToContact */

use App\Core\Csrf;

$whats = preg_replace('/\D+/', '', (string) setting('whatsapp_number', '5511982231363'));
$phone = (string) setting('contact_phone', '(11) 98223-1363');
$old = is_array($contactOld ?? null) ? $contactOld : [];
$waLink = static fn (string $msg): string =>
    'https://wa.me/' . $whats . '?text=' . rawurlencode($msg);
?>

<!-- ============================ HERO ============================ -->
<section class="hero">
    <div class="container hero__grid">
        <div class="hero__content reveal">
            <span class="hero__eyebrow">Assessoria em crédito imobiliário</span>
            <h1 class="hero__title">Entre o sonho e as chaves, existe a DMR para te acompanhar.</h1>
            <p class="hero__text">
                Mais de 30 anos realizando o sonho da casa própria. Cuidamos de todo o financiamento
                para você — com atendimento próximo, do início até a entrega das chaves.
            </p>
            <div class="hero__actions">
                <a class="btn btn--gold" href="<?= e($waLink('Olá! Quero realizar o sonho da casa própria com a DMR.')) ?>" target="_blank" rel="noopener">Quero meu financiamento</a>
                <a class="btn btn--outline" href="#servicos">Como funciona</a>
            </div>
        </div>
        <div class="hero__media reveal">
            <img src="<?= e(asset('img/hero-casa.svg')) ?>" alt="Casal recebendo as chaves da casa própria" width="520" height="560" loading="eager" decoding="async">
            <div class="hero__badge">
                <strong>+100 mil</strong>
                <span>unidades financiadas</span>
            </div>
        </div>
    </div>
</section>

<!-- ====================== NÚMEROS / EXPERTISE ====================== -->
<section class="stats" aria-label="Expertise da DMR">
    <div class="container stats__grid">
        <div class="stat reveal"><span class="stat__num">30+</span><span class="stat__label">anos de experiência</span></div>
        <div class="stat reveal"><span class="stat__num">100 mil+</span><span class="stat__label">unidades financiadas</span></div>
        <div class="stat reveal"><span class="stat__num">5</span><span class="stat__label">grandes bancos parceiros</span></div>
        <div class="stat reveal"><span class="stat__num">100%</span><span class="stat__label">acompanhamento do processo</span></div>
    </div>
</section>

<!-- ============================ QUEM SOMOS ============================ -->
<section class="section about" id="sobre">
    <div class="container about__grid">
        <div class="about__photo reveal">
            <!-- FOTO DE EXEMPLO (placeholder de pessoa real). Substituir pela foto
                 oficial da proprietária: troque a URL por asset('img/proprietaria.jpg'). -->
            <img src="https://randomuser.me/api/portraits/women/42.jpg"
                 alt="Fundadora da DMR Assessoria Imobiliária (foto ilustrativa)"
                 width="480" height="600" loading="lazy" decoding="async">
            <span class="about__photo-note">Foto ilustrativa — substituir pela foto da proprietária</span>
        </div>
        <div class="about__text reveal">
            <span class="eyebrow">Quem é a DMR</span>
            <h2 class="section__title">Experiência que faz o financiamento acontecer.</h2>
            <p>
                São mais de 30 anos de mercado e mais de 100 mil unidades financiadas. A DMR nasceu para
                simplificar o caminho até a casa própria, cuidando da parte burocrática para que o cliente
                não precise enfrentá-la sozinho.
            </p>
            <p class="about__quote">“Não entregamos só um financiamento. Acompanhamos você até a entrega das chaves.”</p>
            <a class="btn btn--primary" href="<?= e($waLink('Olá! Gostaria de falar com a DMR sobre crédito imobiliário.')) ?>" target="_blank" rel="noopener">Fale com a nossa equipe</a>
        </div>
    </div>
</section>

<!-- ============================ SERVIÇOS ============================ -->
<section class="section section--soft" id="servicos">
    <div class="container">
        <div class="section__head section__head--center reveal">
            <span class="eyebrow">O que fazemos por você</span>
            <h2 class="section__title">Tudo o que o seu financiamento precisa.</h2>
        </div>
        <div class="services-grid reveal">
            <article class="service">
                <span class="service__ico"><?= icon('credito') ?></span>
                <h3>Crédito imobiliário</h3>
                <p>Encontramos a melhor condição de financiamento para o seu perfil.</p>
            </article>
            <article class="service">
                <span class="service__ico"><?= icon('analise') ?></span>
                <h3>Análise de crédito</h3>
                <p>Avaliamos sua capacidade e as chances de aprovação antes de avançar.</p>
            </article>
            <article class="service">
                <span class="service__ico"><?= icon('juridico') ?></span>
                <h3>Análise jurídica</h3>
                <p>Conferência completa da documentação para um processo seguro.</p>
            </article>
            <article class="service">
                <span class="service__ico"><?= icon('fgts') ?></span>
                <h3>Saque de FGTS</h3>
                <p>Apoio no uso do FGTS dentro das regras do financiamento.</p>
            </article>
        </div>
    </div>
</section>

<!-- ============================ COMO FUNCIONA ============================ -->
<section class="section" id="como-funciona">
    <div class="container">
        <div class="section__head section__head--center reveal">
            <span class="eyebrow">Simples do começo ao fim</span>
            <h2 class="section__title">Seu caminho até a casa própria.</h2>
        </div>
        <ol class="steps reveal">
            <li class="step"><span class="step__n">1</span><h3>Análise</h3><p>Entendemos seu perfil e as possibilidades de crédito.</p></li>
            <li class="step"><span class="step__n">2</span><h3>Documentação</h3><p>Organizamos tudo para o banco, sem dor de cabeça.</p></li>
            <li class="step"><span class="step__n">3</span><h3>Aprovação</h3><p>Acompanhamos a análise junto às instituições.</p></li>
            <li class="step"><span class="step__n">4</span><h3>Chaves</h3><p>Assinatura, registro e o seu imóvel na sua mão.</p></li>
        </ol>
    </div>
</section>

<!-- ============================ BANCOS ============================ -->
<section class="section section--soft" id="bancos">
    <div class="container">
        <div class="section__head section__head--center reveal">
            <span class="eyebrow">Parceiros</span>
            <h2 class="section__title">Trabalhamos com os maiores bancos do país.</h2>
            <p class="section__lead">Buscamos a condição mais adequada para cada perfil, em diferentes instituições.</p>
        </div>
        <div class="banks reveal">
            <?= \App\Core\View::renderPartial('site/partials/banks') ?>
        </div>
    </div>
</section>

<!-- ============================ CTA / CONTATO ============================ -->
<section class="section section--cta" id="contato">
    <div class="container cta">
        <div class="cta__intro reveal">
            <h2 class="section__title">Pronto para realizar o sonho da casa própria?</h2>
            <p class="cta__text">Fale agora com a DMR. A gente cuida do financiamento e acompanha você em cada etapa.</p>
            <ul class="cta__contacts">
                <li><a href="https://wa.me/<?= e($whats) ?>" target="_blank" rel="noopener"><?= icon('check', 20) ?> WhatsApp: <?= e($phone) ?></a></li>
                <li><a href="https://instagram.com/<?= e(ltrim((string) setting('instagram','dmrassessoria'),'@')) ?>" target="_blank" rel="noopener"><?= icon('check', 20) ?> @<?= e(ltrim((string) setting('instagram','dmrassessoria'),'@')) ?></a></li>
            </ul>
        </div>

        <div class="cta__form reveal">
            <?php if (!empty($contactSuccess)): ?>
                <div class="form-alert form-alert--ok"><?= e($contactSuccess) ?></div>
            <?php endif; ?>
            <?php if (!empty($contactError)): ?>
                <div class="form-alert form-alert--error"><?= e($contactError) ?></div>
            <?php endif; ?>

            <form action="<?= e(base_url('contato')) ?>" method="post" class="contact-form" novalidate>
                <?= Csrf::field() ?>
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="field">
                    <label for="name">Nome</label>
                    <input type="text" id="name" name="name" required value="<?= old('name', $old) ?>">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required value="<?= old('email', $old) ?>">
                    </div>
                    <div class="field">
                        <label for="phone">WhatsApp</label>
                        <input type="text" id="phone" name="phone" required value="<?= old('phone', $old) ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="message">Como podemos ajudar?</label>
                    <textarea id="message" name="message" rows="3"><?= old('message', $old) ?></textarea>
                    <input type="hidden" name="audience" value="cliente">
                </div>
                <button type="submit" class="btn btn--gold btn--block">Quero falar com a DMR</button>
                <p class="form-privacy">Ao enviar, você concorda com a <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>.</p>
            </form>
        </div>
    </div>
</section>

<?php if ($scrollToContact): ?>
<script>window.addEventListener('load',function(){var c=document.getElementById('contato');if(c){c.scrollIntoView({behavior:'smooth'});}});</script>
<?php endif; ?>
