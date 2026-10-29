<?php
/** @var string|null $contactSuccess */
/** @var string|null $contactError */
/** @var array|null $contactOld */
/** @var bool $scrollToContact */

use App\Controllers\ContactController;
use App\Core\Csrf;

$whats = preg_replace('/\D+/', '', (string) setting('whatsapp_number', '5517996394104'));
$phone = (string) setting('contact_phone', '(17) 99639-4104');
$insta = ltrim((string) setting('instagram', 'dmrassessoriaoficial'), '@');
$address = (string) setting('contact_address', 'Rua Romeu Strazzi, 325 – Sala 415, Vila Sinibaldi, São José do Rio Preto/SP – CEP 15084-010');
$mapsLink = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
$old = is_array($contactOld ?? null) ? $contactOld : [];
$waLink = static fn (string $msg): string => 'https://wa.me/' . $whats . '?text=' . rawurlencode($msg);

// Canal de atendimento ativo no formulário (B2C = quero financiar, B2B = sou parceiro).
$partnerProfiles = ['corretor' => 'Corretor autônomo', 'imobiliaria' => 'Imobiliária', 'construtora' => 'Construtora ou incorporadora'];
$channel = array_key_exists((string) ($old['audience'] ?? ''), $partnerProfiles) ? 'b2b' : 'b2c';
$oldC = $channel === 'b2c' ? $old : [];
$oldP = $channel === 'b2b' ? $old : [];
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
            <span class="eyebrow eyebrow--light">Assessoria especializada em crédito imobiliário</span>
            <h1 class="hero__title">Experiência que conecta pessoas, <em>negócios e conquistas.</em></h1>
            <p class="hero__text">Há mais de 30 anos, a DMR oferece orientação especializada para quem deseja financiar um imóvel e para profissionais que movimentam o mercado imobiliário.</p>
            <div class="hero__actions">
                <a class="btn btn--gold" href="#canais">Escolha seu caminho</a>
                <a class="btn btn--ghost-light" href="<?= e($waLink('Olá! Gostaria de falar com a DMR sobre crédito imobiliário.')) ?>" target="_blank" rel="noopener">Falar no WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<!-- ============================ NÚMEROS ============================ -->
<section class="stats" aria-label="Números da DMR">
    <div class="container">
        <ul class="stats__grid reveal">
            <li class="stat"><strong>30+</strong><span>anos de experiência</span></li>
            <li class="stat"><strong>100 mil+</strong><span>unidades financiadas</span></li>
            <li class="stat"><strong>4</strong><span>grandes bancos parceiros</span></li>
            <li class="stat"><strong>100%</strong><span>do processo acompanhado</span></li>
        </ul>
    </div>
</section>

<!-- ============================ CANAIS (B2C / B2B) ============================ -->
<section class="section section--channels" id="canais" aria-label="Escolha como a DMR pode ajudar">
    <div class="container">
        <div class="channels reveal">
            <article class="channel">
                <div class="channel__art" aria-hidden="true">
                    <svg viewBox="0 0 320 160" fill="none"><path d="M55 91 160 25l105 66v57H55V91Z" stroke="currentColor" stroke-width="3"/><path d="M125 148v-52h70v52M40 91l120-77 120 77" stroke="currentColor" stroke-width="3"/><circle cx="224" cy="57" r="13" stroke="currentColor" stroke-width="2.5"/></svg>
                </div>
                <div class="channel__body">
                    <span class="eyebrow">Para você</span>
                    <h2 class="channel__title">Quero financiar um imóvel</h2>
                    <p>Entenda suas opções e avance com orientação e acompanhamento especializado.</p>
                    <a class="btn btn--dark btn--block" href="#para-voce">Conhecer soluções para você</a>
                </div>
            </article>
            <article class="channel">
                <div class="channel__art" aria-hidden="true">
                    <svg viewBox="0 0 320 160" fill="none"><path d="M50 148V49h89v99M139 148V18h131v130M78 77h32M78 103h32M78 129h32M168 47h27M215 47h27M168 75h27M215 75h27M168 103h27M215 103h27M186 148v-23h39v23" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/></svg>
                </div>
                <div class="channel__body">
                    <span class="eyebrow">Para parceiros</span>
                    <h2 class="channel__title">Sou parceiro imobiliário</h2>
                    <p>Conte com suporte especializado para apoiar seus clientes e suas operações.</p>
                    <a class="btn btn--dark btn--block" href="#para-parceiros">Conhecer soluções para parceiros</a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ============================ PARA VOCÊ (B2C) ============================ -->
<section class="section section--soft" id="para-voce">
    <div class="container split">
        <div class="reveal">
            <span class="eyebrow">Para você · Compradores</span>
            <h2 class="section__title">O caminho para seu imóvel começa com uma boa orientação.</h2>
            <p class="section__lead">A DMR ajuda você a compreender as alternativas de financiamento, organizar a documentação e acompanhar cada etapa do processo com mais clareza.</p>
            <a class="btn btn--gold" href="#contato" data-channel="b2c">Quero analisar meu financiamento</a>
        </div>
        <div class="panel reveal">
            <h3 class="panel__title">Como podemos ajudar</h3>
            <ul class="check-list">
                <li><?= icon('check', 18) ?> Financiamento imobiliário</li>
                <li><?= icon('check', 18) ?> Análise de crédito</li>
                <li><?= icon('check', 18) ?> Orientação documental e registro</li>
                <li><?= icon('check', 18) ?> Uso do FGTS, quando aplicável</li>
                <li><?= icon('check', 18) ?> Home Equity, conforme elegibilidade</li>
                <li><?= icon('check', 18) ?> Consórcios</li>
            </ul>
            <p class="small-note">A aprovação, as taxas e as condições dependem da avaliação da instituição financeira.</p>
        </div>
    </div>
</section>

<!-- ============================ PARA PARCEIROS (B2B) ============================ -->
<section class="section" id="para-parceiros">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Para parceiros · Mercado imobiliário</span>
            <h2 class="section__title">Mais suporte para quem faz o mercado imobiliário acontecer.</h2>
            <p class="section__lead">Conte com a experiência da DMR para apoiar seus clientes nas etapas de crédito imobiliário e facilitar a condução dos processos.</p>
        </div>
        <div class="partners reveal">
            <article class="partner">
                <span class="partner__ico"><?= icon('perfil') ?></span>
                <h3>Corretores</h3>
                <p>Você cuida da negociação. A DMR apoia a jornada de financiamento dos seus clientes.</p>
                <a class="partner__link" href="#contato" data-channel="b2b" data-profile="corretor">Conhecer a parceria <span aria-hidden="true">→</span></a>
            </article>
            <article class="partner">
                <span class="partner__ico"><?= icon('assessoria') ?></span>
                <h3>Imobiliárias</h3>
                <p>Suporte especializado para sua equipe comercial e maior clareza na condução dos processos.</p>
                <a class="partner__link" href="#contato" data-channel="b2b" data-profile="imobiliaria">Soluções para imobiliárias <span aria-hidden="true">→</span></a>
            </article>
            <article class="partner">
                <span class="partner__ico"><?= icon('predio') ?></span>
                <h3>Construtoras e incorporadoras</h3>
                <p>Experiência em crédito para apoiar operações imobiliárias e o atendimento aos compradores.</p>
                <a class="partner__link" href="#contato" data-channel="b2b" data-profile="construtora">Conversar com a DMR <span aria-hidden="true">→</span></a>
            </article>
        </div>
    </div>
</section>

<!-- ============================ COMO FUNCIONA ============================ -->
<section class="section section--soft" id="como-funciona">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Nossa forma de trabalhar</span>
            <h2 class="section__title">Do primeiro contato à conclusão, você conta com a DMR.</h2>
            <p class="section__lead">Um processo de assessoria estruturado para compradores e parceiros, adaptado às necessidades de cada operação.</p>
        </div>
        <ol class="steps reveal">
            <li class="step"><span class="step__n">01</span><h3>Entendimento</h3><p>Conhecemos o perfil e os objetivos da operação.</p></li>
            <li class="step"><span class="step__n">02</span><h3>Análise</h3><p>Orientamos sobre alternativas e condições disponíveis.</p></li>
            <li class="step"><span class="step__n">03</span><h3>Documentação</h3><p>Apoiamos a preparação e o encaminhamento dos documentos.</p></li>
            <li class="step"><span class="step__n">04</span><h3>Acompanhamento</h3><p>Seguimos com você ao longo das etapas até a conclusão.</p></li>
        </ol>
    </div>
</section>

<!-- ============================ SOBRE A DMR ============================ -->
<section class="section" id="sobre">
    <div class="container about">
        <figure class="about__photo reveal">
            <img src="<?= e(asset('img/fundadora.jpg')) ?>" alt="Fundadora da DMR Assessoria Imobiliária" width="1086" height="1448" loading="lazy" decoding="async">
        </figure>
        <div class="about__text reveal">
            <span class="eyebrow">Sobre a DMR</span>
            <h2 class="section__title">Experiência construída com pessoas, confiança e resultados.</h2>
            <p>Há mais de três décadas, a DMR atua no crédito imobiliário, apoiando compradores e profissionais do setor com conhecimento técnico e relacionamento próximo.</p>
            <p>Uma marca, duas jornadas de atendimento e o mesmo compromisso com clareza e acompanhamento.</p>
            <a class="btn btn--dark" href="#contato">Fale com nossa equipe</a>
        </div>
    </div>
</section>

<!-- ===================== O QUE NOS ORIENTA (MISSÃO, VISÃO E VALORES) ===================== -->
<section class="section section--soft" id="o-que-nos-orienta" aria-labelledby="orienta-titulo">
    <div class="container">
        <div class="section__head section__head--wide reveal">
            <span class="eyebrow">Nossa essência</span>
            <h2 class="section__title" id="orienta-titulo">O que nos orienta</h2>
            <p class="section__lead">Acreditamos que a experiência faz a diferença quando é acompanhada de cuidado, transparência e compromisso com cada conquista.</p>
        </div>

        <div class="purpose reveal">
            <article class="purpose__card">
                <span class="purpose__ico"><?= icon('alvo') ?></span>
                <h3>Nossa Missão</h3>
                <p>Simplificar o financiamento imobiliário com atendimento próximo, orientação especializada e processos eficientes, proporcionando tranquilidade aos compradores e resultados aos nossos parceiros.</p>
            </article>
            <article class="purpose__card">
                <span class="purpose__ico"><?= icon('visao') ?></span>
                <h3>Nossa Visão</h3>
                <p>Ser referência em atendimento e eficiência no crédito imobiliário, ampliando parcerias e tornando o acesso ao financiamento cada vez mais simples e acessível.</p>
            </article>
        </div>

        <div class="values reveal">
            <div class="values__head">
                <span class="purpose__ico"><?= icon('cuidado') ?></span>
                <h3>Nossos Valores</h3>
            </div>
            <ul class="values__grid">
                <li class="value"><strong>Honestidade</strong><p>Agimos com integridade.</p></li>
                <li class="value"><strong>Transparência</strong><p>Comunicamos com clareza.</p></li>
                <li class="value"><strong>Comprometimento</strong><p>Assumimos cada processo com responsabilidade.</p></li>
                <li class="value"><strong>Profissionalismo</strong><p>Unimos experiência e organização.</p></li>
                <li class="value"><strong>Cuidado com as pessoas</strong><p>Atendemos com atenção e respeito.</p></li>
                <li class="value"><strong>Eficiência</strong><p>Valorizamos o tempo e os recursos de todos.</p></li>
            </ul>
        </div>

        <p class="purpose__quote reveal">Cuidamos de pessoas. Simplificamos processos. Impulsionamos conquistas.</p>

        <details class="purpose__detail reveal">
            <summary>Conheça nossos compromissos em mais detalhes</summary>
            <div class="purpose__detail-body">
                <h3>Nosso propósito</h3>
                <p>Na DMR, acreditamos que financiar um imóvel deve ser uma experiência conduzida com clareza, segurança e tranquilidade. Com mais de 30 anos de experiência no mercado imobiliário, construímos nossa trajetória unindo conhecimento técnico, atendimento humano e processos estruturados. Por trás de cada financiamento existe uma conquista pessoal, uma decisão importante e uma relação de confiança.</p>
                <p>Para nossos clientes, buscamos simplificar cada etapa, evitando burocracias desnecessárias e oferecendo orientação próxima e personalizada. Para nossos parceiros, trabalhamos para tornar os processos mais eficientes, apoiar a satisfação do comprador e contribuir para a conclusão das operações com agilidade e previsibilidade.</p>

                <h3>Missão — compromisso diário</h3>
                <p>Simplificar e facilitar o acesso ao financiamento imobiliário por meio de orientação especializada, atendimento próximo e processos estruturados e eficientes. Atuamos para proporcionar aos compradores uma experiência transparente, segura e tranquila, preservando seu tempo e seus recursos. Ao mesmo tempo, apoiamos nossos parceiros com agilidade, comprometimento e acompanhamento responsável, contribuindo para a conclusão das operações e para a eficiência de seus negócios.</p>

                <h3>Visão — onde queremos chegar</h3>
                <p>Ser reconhecida como referência em atendimento ao cliente, confiança e eficiência no mercado de crédito imobiliário. Buscamos expandir nossa rede de parceiros, fortalecer relacionamentos de longo prazo e contribuir para um mercado imobiliário mais acessível, transparente e eficiente, no qual compradores e profissionais encontrem na DMR uma parceira de confiança para suas conquistas e negócios.</p>

                <h3>Valores — como agimos</h3>
                <ul>
                    <li><strong>Honestidade e integridade:</strong> agimos com ética e responsabilidade em todas as relações, fazendo o que é correto mesmo diante de desafios.</li>
                    <li><strong>Transparência:</strong> comunicamos condições, possibilidades, etapas e desafios com sinceridade para apoiar decisões conscientes.</li>
                    <li><strong>Comprometimento:</strong> acompanhamos cada operação com dedicação, responsabilidade e respeito aos compromissos estabelecidos.</li>
                    <li><strong>Profissionalismo:</strong> unimos experiência, conhecimento técnico e processos estruturados para oferecer atendimento organizado e confiável.</li>
                    <li><strong>Cuidado com as pessoas:</strong> valorizamos a escuta, a educação, a empatia e o atendimento personalizado.</li>
                    <li><strong>Eficiência com responsabilidade:</strong> buscamos reduzir obstáculos e retrabalho, respeitando o tempo e os recursos envolvidos sem comprometer a segurança ou a qualidade.</li>
                </ul>
            </div>
        </details>
    </div>
</section>

<!-- ============================ INSTITUIÇÕES ============================ -->
<section class="section section--tight" id="bancos">
    <div class="container">
        <div class="section__head section__head--center reveal">
            <span class="eyebrow">Relacionamento financeiro</span>
            <h2 class="section__title">Conexões com instituições financeiras do mercado.</h2>
            <p class="section__lead">As alternativas de financiamento são avaliadas conforme as características da operação e os critérios de cada instituição.</p>
        </div>
        <ul class="banks reveal">
            <?= \App\Core\View::renderPartial('site/partials/banks') ?>
        </ul>
    </div>
</section>

<!-- ============================ PERGUNTAS FREQUENTES ============================ -->
<section class="section section--soft" id="perguntas">
    <div class="container faq">
        <div class="section__head reveal">
            <span class="eyebrow">Dúvidas frequentes</span>
            <h2 class="section__title">Respostas para começar com confiança.</h2>
        </div>
        <div class="faq__list reveal">
            <details class="faq__item">
                <summary>Posso falar com a DMR diretamente, sem um corretor?</summary>
                <p>Sim. Compradores podem solicitar atendimento diretamente para conhecer as possibilidades de assessoria em crédito imobiliário.</p>
            </details>
            <details class="faq__item">
                <summary>Sou corretor ou representante de imobiliária. Como funciona a parceria?</summary>
                <p>Entre em contato pelo formulário de parceiros. A equipe poderá conhecer sua atuação e explicar as modalidades de suporte disponíveis.</p>
            </details>
            <details class="faq__item">
                <summary>A DMR garante a aprovação do financiamento?</summary>
                <p>Não. A aprovação e as condições dependem dos critérios e da análise da instituição financeira responsável.</p>
            </details>
        </div>
    </div>
</section>

<!-- ============================ CONTATO ============================ -->
<section class="section section--dark" id="contato">
    <div class="container cta">
        <div class="cta__intro reveal">
            <span class="eyebrow eyebrow--light">Vamos conversar</span>
            <h2 class="section__title section__title--light">Seu próximo passo merece a experiência certa.</h2>
            <p class="cta__text">Seja para financiar um imóvel ou apoiar seus negócios imobiliários, escolha seu perfil e conte com a DMR.</p>
            <ul class="cta__contacts">
                <li><a href="https://wa.me/<?= e($whats) ?>" target="_blank" rel="noopener"><span><?= icon('whatsapp', 18) ?></span><?= e($phone) ?> · WhatsApp</a></li>
                <li><a href="https://instagram.com/<?= e($insta) ?>" target="_blank" rel="noopener"><span><?= icon('instagram', 18) ?></span>@<?= e($insta) ?></a></li>
                <?php if ($address !== ''): ?>
                    <li><a href="<?= e($mapsLink) ?>" target="_blank" rel="noopener"><span><?= icon('local', 18) ?></span><address><?= e($address) ?></address></a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="cta__form reveal">
            <?php if (!empty($contactSuccess)): ?><div class="form-alert form-alert--ok"><?= e($contactSuccess) ?></div><?php endif; ?>
            <?php if (!empty($contactError)): ?><div class="form-alert form-alert--error"><?= e($contactError) ?></div><?php endif; ?>

            <div class="tabs" role="tablist" aria-label="Tipo de atendimento">
                <button class="tabs__btn" type="button" role="tab" id="tab-b2c" aria-controls="form-b2c" aria-selected="<?= $channel === 'b2c' ? 'true' : 'false' ?>" tabindex="<?= $channel === 'b2c' ? '0' : '-1' ?>" data-tab="b2c">Quero financiar</button>
                <button class="tabs__btn" type="button" role="tab" id="tab-b2b" aria-controls="form-b2b" aria-selected="<?= $channel === 'b2b' ? 'true' : 'false' ?>" tabindex="<?= $channel === 'b2b' ? '0' : '-1' ?>" data-tab="b2b">Sou parceiro</button>
            </div>

            <!-- Canal B2C: comprador -->
            <form id="form-b2c" role="tabpanel" aria-labelledby="tab-b2c" action="<?= e(base_url('contato')) ?>" method="post" class="contact-form" novalidate<?= $channel === 'b2c' ? '' : ' hidden' ?>>
                <?= Csrf::field() ?>
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="audience" value="cliente">
                <div class="field">
                    <label for="c-name">Seu nome</label>
                    <input type="text" id="c-name" name="name" required autocomplete="name" value="<?= old('name', $oldC) ?>">
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="c-phone">WhatsApp</label>
                        <input type="tel" id="c-phone" name="phone" required autocomplete="tel" value="<?= old('phone', $oldC) ?>">
                    </div>
                    <div class="field">
                        <label for="c-email">E-mail</label>
                        <input type="email" id="c-email" name="email" required autocomplete="email" value="<?= old('email', $oldC) ?>">
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="c-city">Cidade / Estado <small>(opcional)</small></label>
                        <input type="text" id="c-city" name="city" autocomplete="address-level2" value="<?= old('city', $oldC) ?>">
                    </div>
                    <div class="field">
                        <label for="c-interest">O que você procura?</label>
                        <select id="c-interest" name="interest">
                            <?php $selI = $oldC['interest'] ?? 'financiamento'; foreach (ContactController::INTERESTS as $v => $l): ?>
                                <option value="<?= e($v) ?>" <?= $selI === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label for="c-message">Mensagem <small>(opcional)</small></label>
                    <textarea id="c-message" name="message" rows="3"><?= old('message', $oldC) ?></textarea>
                </div>
                <p class="form-note">Não envie CPF, renda ou documentos por este formulário.</p>
                <button type="submit" class="btn btn--gold btn--block">Solicitar orientação</button>
                <p class="form-privacy">Ao enviar, você concorda com a <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>.</p>
            </form>

            <!-- Canal B2B: parceiros -->
            <form id="form-b2b" role="tabpanel" aria-labelledby="tab-b2b" action="<?= e(base_url('contato')) ?>" method="post" class="contact-form" novalidate<?= $channel === 'b2b' ? '' : ' hidden' ?>>
                <?= Csrf::field() ?>
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="field-row">
                    <div class="field">
                        <label for="p-name">Nome do responsável</label>
                        <input type="text" id="p-name" name="name" required autocomplete="name" value="<?= old('name', $oldP) ?>">
                    </div>
                    <div class="field">
                        <label for="p-audience">Perfil profissional</label>
                        <select id="p-audience" name="audience" required>
                            <?php $selP = $oldP['audience'] ?? 'corretor'; foreach ($partnerProfiles as $v => $l): ?>
                                <option value="<?= e($v) ?>" <?= $selP === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="p-company">Empresa <small>(opcional)</small></label>
                        <input type="text" id="p-company" name="company" autocomplete="organization" value="<?= old('company', $oldP) ?>">
                    </div>
                    <div class="field">
                        <label for="p-city">Cidade / Estado <small>(opcional)</small></label>
                        <input type="text" id="p-city" name="city" autocomplete="address-level2" value="<?= old('city', $oldP) ?>">
                    </div>
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="p-phone">WhatsApp</label>
                        <input type="tel" id="p-phone" name="phone" required autocomplete="tel" value="<?= old('phone', $oldP) ?>">
                    </div>
                    <div class="field">
                        <label for="p-email">E-mail comercial</label>
                        <input type="email" id="p-email" name="email" required autocomplete="email" value="<?= old('email', $oldP) ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="p-message">Como podemos ajudar? <small>(opcional)</small></label>
                    <textarea id="p-message" name="message" rows="3"><?= old('message', $oldP) ?></textarea>
                </div>
                <button type="submit" class="btn btn--gold btn--block">Solicitar contato comercial</button>
                <p class="form-privacy">Ao enviar, você concorda com a <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>.</p>
            </form>
        </div>
    </div>
</section>

<?php if ($scrollToContact): ?>
<script>window.addEventListener('load',function(){var c=document.getElementById('contato');if(c){c.scrollIntoView({behavior:'smooth'});}});</script>
<?php endif; ?>
