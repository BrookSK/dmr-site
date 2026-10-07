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
    <div class="hero__bg" aria-hidden="true"></div>
    <div class="container hero__inner">
        <div class="hero__content reveal">
            <span class="hero__eyebrow">Mais de 30 anos em crédito imobiliário</span>
            <h1 class="hero__title">Crédito imobiliário com quem acompanha você até o fim.</h1>
            <p class="hero__text">
                A DMR Assessoria Imobiliária une mais de 30 anos de experiência, atendimento próximo e
                acompanhamento completo para tornar cada etapa do financiamento mais simples, segura e transparente.
            </p>
            <div class="hero__actions">
                <a class="btn btn--gold" href="#contato">Fale com a DMR</a>
                <a class="btn btn--outline-light" href="#solucoes">Conheça nossas soluções</a>
            </div>
            <div class="hero__trust">
                <span>Atendimento humano</span>
                <span>•</span>
                <span>Acompanhamento ponta a ponta</span>
                <span>•</span>
                <span>Transparência</span>
            </div>
        </div>
    </div>
    <!-- Linha contínua: conceito de "conduzir o cliente" -->
    <svg class="hero__thread" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0,80 C300,20 600,120 1200,40" fill="none" stroke="url(#g)" stroke-width="2"/>
        <defs><linearGradient id="g" x1="0" x2="1"><stop offset="0" stop-color="#CBAE6C" stop-opacity="0"/><stop offset=".5" stop-color="#CBAE6C"/><stop offset="1" stop-color="#9C7B2C" stop-opacity="0"/></linearGradient></defs>
    </svg>
</section>

<!-- ====================== NÚMEROS / CREDIBILIDADE ====================== -->
<section class="stats" aria-label="Números da DMR">
    <div class="container stats__grid">
        <div class="stat reveal">
            <span class="stat__num">30+</span>
            <span class="stat__label">anos de experiência</span>
        </div>
        <div class="stat reveal">
            <span class="stat__num">100 mil+</span>
            <span class="stat__label">unidades viabilizadas em repasses</span>
        </div>
        <!-- Placeholder: valor em negócios gerados ainda não fornecido oficialmente. -->
        <div class="stat stat--placeholder reveal" data-placeholder="valor-negocios">
            <span class="stat__num">—</span>
            <span class="stat__label">em negócios viabilizados <small>(dado a confirmar)</small></span>
        </div>
    </div>
</section>

<!-- ============================ SOBRE ============================ -->
<section class="section" id="sobre">
    <div class="container about">
        <div class="about__text reveal">
            <span class="eyebrow">Quem é a DMR</span>
            <h2 class="section__title">Experiência que vai além do número de anos.</h2>
            <p>
                São mais de três décadas dedicadas ao crédito imobiliário, com a viabilização de mais de 100 mil unidades
                em repasses. Nesse tempo, a DMR aprendeu que cada financiamento tem suas particularidades — e que o
                diferencial está em saber conduzir cada situação com atenção.
            </p>
            <p>
                Trabalhamos próximos de construtoras, incorporadoras, imobiliárias e corretores, e também ao lado do
                cliente final. Em todos os casos, a lógica é a mesma: acompanhar o processo de ponta a ponta, para que
                ninguém precise atravessar a burocracia sozinho.
            </p>
            <a class="link-arrow" href="#jornada">Veja como acompanhamos cada etapa <span aria-hidden="true">→</span></a>
        </div>
        <div class="about__aside reveal">
            <blockquote class="pull-quote">
                <p>A DMR não simplesmente processa um financiamento. Ela acompanha o cliente até o final.</p>
            </blockquote>
        </div>
    </div>
</section>

<!-- ============================ 3 PILARES ============================ -->
<section class="section section--alt" id="pilares">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Nosso posicionamento</span>
            <h2 class="section__title">Três pilares que sustentam cada atendimento.</h2>
        </div>

        <div class="pillars">
            <article class="pillar reveal">
                <span class="pillar__num">01</span>
                <div class="pillar__body">
                    <h3>Cuidado</h3>
                    <p>
                        Cada cliente é acompanhado de forma próxima e personalizada. Um financiamento imobiliário não é
                        apenas um processo burocrático — é uma etapa importante da vida, e precisa ser conduzida com atenção.
                    </p>
                </div>
            </article>
            <article class="pillar reveal">
                <span class="pillar__num">02</span>
                <div class="pillar__body">
                    <h3>Transparência</h3>
                    <p>
                        O cliente precisa saber o que está acontecendo. Utilizamos ferramentas que permitem acompanhar o
                        andamento do processo e mantemos todas as partes envolvidas informadas.
                    </p>
                </div>
            </article>
            <article class="pillar reveal">
                <span class="pillar__num">03</span>
                <div class="pillar__body">
                    <h3>Agilidade</h3>
                    <p>
                        Experiência e processos organizados para evitar retrabalho, reduzir atrasos e fazer cada etapa
                        acontecer no momento certo.
                    </p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ==================== O QUE A DMR FAZ DE DIFERENTE ==================== -->
<section class="section" id="diferente">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">O que fazemos de diferente</span>
            <h2 class="section__title">Mais do que executar tarefas, entregamos tranquilidade.</h2>
        </div>

        <div class="value-grid">
            <article class="value-card reveal">
                <h3>Análise de crédito</h3>
                <p class="value-card__lead">Mais segurança antes de avançar.</p>
                <p>Analisamos o perfil do comprador desde o lançamento para entender renda, capacidade de financiamento, bancos disponíveis e probabilidade de aprovação.</p>
            </article>
            <article class="value-card reveal">
                <h3>Documentação</h3>
                <p class="value-card__lead">Menos retrabalho. Mais tranquilidade.</p>
                <p>Acompanhamos o envio e a gestão dos documentos, garantindo que cada etapa aconteça no momento adequado.</p>
            </article>
            <article class="value-card reveal">
                <h3>Assinaturas</h3>
                <p class="value-card__lead">Sem deixar o processo parado.</p>
                <p>Acompanhamos as assinaturas necessárias para que o processo avance com agilidade.</p>
            </article>
            <article class="value-card reveal">
                <h3>ITBI, cartório e registro</h3>
                <p class="value-card__lead">Responsabilidade em cada detalhe.</p>
                <p>Auxiliamos nas etapas burocráticas e assumimos responsabilidades importantes do processo, incluindo o cálculo do ITBI, trazendo mais segurança ao cliente.</p>
            </article>
            <article class="value-card reveal">
                <h3>Liberação das chaves</h3>
                <p class="value-card__lead">Do primeiro passo até a conclusão.</p>
                <p>Acompanhamos o processo até a sua finalização e a liberação das chaves.</p>
            </article>
        </div>
    </div>
</section>

<!-- ============================ JORNADA ============================ -->
<section class="section section--dark" id="jornada">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow eyebrow--light">A jornada acompanhada</span>
            <h2 class="section__title section__title--light">A DMR não desaparece depois que o financiamento começa.</h2>
        </div>

        <ol class="journey">
            <li class="journey__step reveal"><span class="journey__num">01</span><h3>Análise</h3><p>Entendimento do perfil e das possibilidades de crédito.</p></li>
            <li class="journey__step reveal"><span class="journey__num">02</span><h3>Documentação</h3><p>Organização e envio dos documentos necessários.</p></li>
            <li class="journey__step reveal"><span class="journey__num">03</span><h3>Aprovação</h3><p>Acompanhamento junto aos bancos e instituições.</p></li>
            <li class="journey__step reveal"><span class="journey__num">04</span><h3>Contratos</h3><p>Conferência e acompanhamento das etapas contratuais.</p></li>
            <li class="journey__step reveal"><span class="journey__num">05</span><h3>Registro</h3><p>ITBI, cartório e registro.</p></li>
            <li class="journey__step reveal"><span class="journey__num">06</span><h3>Conclusão</h3><p>Liberação dos recursos e das chaves.</p></li>
        </ol>
    </div>
</section>

<!-- ============================ SOLUÇÕES ============================ -->
<section class="section section--alt" id="solucoes">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Para quem a DMR trabalha</span>
            <h2 class="section__title">Soluções pensadas para cada ponta do negócio.</h2>
        </div>

        <!-- Cliente final -->
        <div class="audience reveal" id="cliente-final">
            <div class="audience__text">
                <span class="audience__tag">Cliente final</span>
                <h3>Você compra um imóvel. Nós acompanhamos o caminho até ele se tornar seu.</h3>
                <p>Você não é tratado como mais um número. Cada etapa é explicada, acompanhada e comunicada — para que a conquista do imóvel aconteça com segurança e sem a sensação de estar sozinho diante da burocracia.</p>
                <ul class="checklist">
                    <li>Atendimento personalizado e humano</li>
                    <li>Transparência e explicação de cada etapa</li>
                    <li>Acompanhamento documental</li>
                    <li>Suporte até a conclusão</li>
                </ul>
                <a class="btn btn--primary" href="<?= e($waLink('Olá, sou cliente e gostaria de falar com a DMR sobre meu financiamento.')) ?>" target="_blank" rel="noopener">Falar sobre meu financiamento</a>
            </div>
        </div>

        <!-- Construtoras e incorporadoras -->
        <div class="audience audience--reverse reveal" id="construtoras">
            <div class="audience__text">
                <span class="audience__tag">Construtoras e incorporadoras</span>
                <h3>Sua equipe vende. A DMR ajuda a garantir que o negócio avance.</h3>
                <p>Funcionamos como um braço estratégico e financeiro da operação, integrados à equipe comercial, do lançamento à conclusão dos repasses.</p>
                <ul class="checklist checklist--two">
                    <li>Análise de crédito no lançamento</li>
                    <li>Maior velocidade nas aprovações</li>
                    <li>Acompanhamento dos repasses</li>
                    <li>Redução de atrasos e distratos</li>
                    <li>Relatórios e acompanhamento de processos</li>
                    <li>Equipe dedicada para cada lançamento</li>
                </ul>
                <a class="btn btn--primary" href="<?= e($waLink('Olá, represento uma construtora/incorporadora e quero conhecer as soluções da DMR.')) ?>" target="_blank" rel="noopener">Falar sobre meu empreendimento</a>
            </div>
        </div>

        <!-- Imobiliárias e corretores -->
        <div class="audience reveal" id="imobiliarias">
            <div class="audience__text">
                <span class="audience__tag">Imobiliárias e corretores</span>
                <h3>O corretor não precisa enfrentar sozinho a parte mais burocrática da venda.</h3>
                <p>Apoio na análise de crédito, suporte durante a negociação e acompanhamento do cliente — para que você tenha mais segurança ao apresentar possibilidades e fechar negócios.</p>
                <ul class="checklist checklist--two">
                    <li>Apoio na análise de crédito</li>
                    <li>Suporte ao corretor na venda</li>
                    <li>Acompanhamento do cliente</li>
                    <li>Mais segurança na negociação</li>
                    <li>Velocidade e atendimento próximo</li>
                    <li>Experiência para situações difíceis</li>
                </ul>
                <a class="btn btn--primary" href="<?= e($waLink('Olá, sou corretor/imobiliária e quero contar com o suporte da DMR.')) ?>" target="_blank" rel="noopener">Contar com o suporte da DMR</a>
            </div>
        </div>
    </div>
</section>

<!-- ============================ HOME EQUITY ============================ -->
<section class="section home-equity" id="home-equity">
    <div class="container about">
        <div class="about__text reveal">
            <span class="eyebrow">Home Equity</span>
            <h2 class="section__title">Seu imóvel pode abrir novas possibilidades.</h2>
            <p>
                O crédito com garantia de imóvel permite transformar o valor de um imóvel quitado em recursos para
                projetos, negócios, organização financeira ou outras necessidades.
            </p>
            <ul class="checklist">
                <li>Possibilidade de crédito maior</li>
                <li>Taxas mais competitivas</li>
                <li>Prazos mais longos</li>
                <li>O imóvel permanece no nome do proprietário, conforme as condições da operação</li>
            </ul>
            <a class="btn btn--primary" href="<?= e($waLink('Olá, tenho interesse em Home Equity e gostaria de falar com um especialista da DMR.')) ?>" target="_blank" rel="noopener">Fale com um especialista</a>
            <p class="fineprint">As condições (crédito, taxas e prazos) dependem de análise e das políticas de cada instituição. Nenhuma aprovação ou taxa é garantida previamente.</p>
        </div>
        <div class="about__aside reveal">
            <div class="equity-card">
                <span class="equity-card__ico" aria-hidden="true">⌂</span>
                <p>Crédito com garantia de imóvel</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================ BANCOS ============================ -->
<section class="section section--alt" id="bancos">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Bancos e parcerias</span>
            <h2 class="section__title">Parcerias estratégicas para cada perfil.</h2>
            <p class="section__lead">Trabalhamos com diferentes instituições para buscar as possibilidades mais adequadas a cada situação.</p>
        </div>
        <!-- Logos oficiais devem ser inseridos posteriormente (arquivos licenciados). -->
        <ul class="banks reveal">
            <li class="bank" data-logo="santander">Santander</li>
            <li class="bank" data-logo="bradesco">Bradesco</li>
            <li class="bank" data-logo="itau">Itaú</li>
            <li class="bank" data-logo="brb">BRB</li>
            <li class="bank" data-logo="caixa">Caixa Econômica Federal</li>
        </ul>
        <p class="section__note">A disponibilidade de condições varia conforme o perfil do cliente e as políticas de cada instituição.</p>
    </div>
</section>

<!-- ===================== ATENDIMENTO HUMANIZADO ===================== -->
<section class="section section--dark humanized" id="humanizado">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow eyebrow--light">Atendimento humanizado</span>
            <h2 class="section__title section__title--light">Você não precisa contar sua história de novo a cada atendimento.</h2>
            <p class="section__lead section__lead--light">
                Acreditamos que cada cliente merece ser acompanhado por alguém que realmente conheça o seu processo.
            </p>
        </div>
        <div class="compare reveal">
            <div class="compare__col compare__col--bad">
                <h3>Em vez de</h3>
                <ul>
                    <li>Atendimento genérico</li>
                    <li>Múltiplas transferências</li>
                    <li>Respostas automáticas</li>
                    <li>Excesso de burocracia</li>
                </ul>
            </div>
            <div class="compare__col compare__col--good">
                <h3>A proposta da DMR</h3>
                <ul>
                    <li>Proximidade e acompanhamento</li>
                    <li>Responsabilidade pelo processo</li>
                    <li>Conhecimento do histórico</li>
                    <li>Comunicação clara até a conclusão</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===================== PLATAFORMA / TRANSPARÊNCIA ===================== -->
<section class="section" id="plataforma">
    <div class="container about">
        <div class="about__text reveal">
            <span class="eyebrow">Plataforma e transparência</span>
            <h2 class="section__title">Tecnologia para dar visibilidade. Pessoas para dar suporte.</h2>
            <p>
                Utilizamos ferramentas que permitem acompanhar o andamento do processo em tempo real e manter todos
                informados. A tecnologia existe para facilitar o relacionamento — não para substituí-lo.
            </p>
            <p>O que faz a diferença é a combinação entre experiência, tecnologia e atendimento humano.</p>
        </div>
        <div class="about__aside reveal">
            <div class="platform-badges">
                <span class="badge">Acompanhamento em tempo real</span>
                <span class="badge">Status de cada etapa</span>
                <span class="badge">Comunicação centralizada</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================ CASES / DEPOIMENTOS ============================ -->
<section class="section section--alt" id="depoimentos">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Prova social</span>
            <h2 class="section__title">Quem acompanha de perto, recomenda.</h2>
            <p class="section__lead">Depoimentos de parceiros e clientes serão publicados aqui.</p>
        </div>
        <!-- Placeholders: inserir depoimentos reais posteriormente. Não publicar conteúdo fictício. -->
        <div class="testimonials reveal">
            <article class="testimonial testimonial--placeholder">
                <p class="testimonial__text">[Depoimento de incorporadora — inserir posteriormente]</p>
                <footer class="testimonial__author">Incorporadora parceira</footer>
            </article>
            <article class="testimonial testimonial--placeholder">
                <p class="testimonial__text">[Depoimento de imobiliária/corretor — inserir posteriormente]</p>
                <footer class="testimonial__author">Imobiliária parceira</footer>
            </article>
            <article class="testimonial testimonial--placeholder">
                <p class="testimonial__text">[Depoimento de cliente — inserir posteriormente]</p>
                <footer class="testimonial__author">Cliente DMR</footer>
            </article>
        </div>
    </div>
</section>

<!-- ===================== MISSÃO, VISÃO E VALORES ===================== -->
<section class="section mvv" id="mvv">
    <div class="container">
        <div class="section__head reveal">
            <span class="eyebrow">Missão, visão e valores</span>
            <h2 class="section__title">O que nos orienta.</h2>
        </div>
        <div class="mvv__grid">
            <div class="mvv__card reveal">
                <h3>Missão</h3>
                <p>Realizar sonhos com inovação e responsabilidade.</p>
            </div>
            <div class="mvv__card reveal">
                <h3>Visão</h3>
                <p>Ser referência em crédito imobiliário e impacto social.</p>
            </div>
            <div class="mvv__card reveal">
                <h3>Valores</h3>
                <ul class="mvv__values">
                    <li>Ética</li><li>Respeito</li><li>Evolução</li><li>Empreendedorismo</li><li>Relações humanas</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================ CTA / CONTATO ============================ -->
<section class="section section--cta" id="contato">
    <div class="container cta">
        <div class="cta__intro reveal">
            <span class="eyebrow eyebrow--light">Próximo passo</span>
            <h2 class="section__title section__title--light">Conte com a DMR ao seu lado.</h2>
            <p class="cta__text">
                Seja para financiar um imóvel, apoiar uma venda ou estruturar o repasse de um empreendimento, a DMR está
                pronta para acompanhar cada etapa.
            </p>
            <ul class="cta__contacts">
                <li><a href="https://wa.me/<?= e($whats) ?>" target="_blank" rel="noopener"><strong>WhatsApp:</strong> <?= e($phone) ?></a></li>
                <li><a href="https://instagram.com/<?= e(ltrim((string) setting('instagram','dmrassessoria'),'@')) ?>" target="_blank" rel="noopener"><strong>Instagram:</strong> @<?= e(ltrim((string) setting('instagram','dmrassessoria'),'@')) ?></a></li>
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
                <!-- Honeypot anti-spam (deve permanecer vazio) -->
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
                        <label for="phone">Telefone / WhatsApp</label>
                        <input type="text" id="phone" name="phone" required value="<?= old('phone', $old) ?>">
                    </div>
                </div>
                <div class="field">
                    <label for="audience">Tipo de atendimento</label>
                    <select id="audience" name="audience">
                        <?php
                        $options = [
                            'cliente' => 'Cliente final',
                            'corretor' => 'Corretor',
                            'imobiliaria' => 'Imobiliária',
                            'construtora' => 'Construtora',
                            'incorporadora' => 'Incorporadora',
                            'home-equity' => 'Home Equity',
                            'outro' => 'Outro',
                        ];
                        $selected = $old['audience'] ?? 'cliente';
                        foreach ($options as $value => $label):
                        ?>
                            <option value="<?= e($value) ?>" <?= $selected === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="message">Mensagem</label>
                    <textarea id="message" name="message" rows="4"><?= old('message', $old) ?></textarea>
                </div>
                <button type="submit" class="btn btn--gold btn--block">Fale com a DMR</button>
                <p class="form-privacy">
                    Ao enviar, você concorda com a nossa
                    <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>.
                </p>
            </form>
        </div>
    </div>
</section>

<?php if ($scrollToContact): ?>
<script>window.addEventListener('load',function(){var c=document.getElementById('contato');if(c){c.scrollIntoView({behavior:'smooth'});}});</script>
<?php endif; ?>
