<?php
$email = (string) setting('contact_email', 'contato@dmrassessoria.com.br');
$siteName = (string) setting('site_name', 'DMR Assessoria Imobiliária');
?>
<article class="legal">
    <div class="container">
        <header class="legal__header">
            <span class="eyebrow">Institucional</span>
            <h1 class="section__title">Termos de Uso</h1>
            <p class="legal__updated">Última atualização: <?= e(date('d/m/Y')) ?></p>
        </header>

        <div class="legal__notice">
            <strong>Aviso importante:</strong> este documento é um modelo de referência e deve ser revisado por um
            profissional jurídico antes da publicação definitiva, para refletir com precisão a atuação da
            <?= e($siteName) ?> e a legislação aplicável.
        </div>

        <div class="legal__content">
            <p>
                Estes Termos de Uso regulam o acesso e a utilização do site da <?= e($siteName) ?>. Ao navegar neste
                site, você declara estar ciente e de acordo com as condições a seguir.
            </p>

            <h2>1. Objeto</h2>
            <p>
                Este site tem caráter informativo e institucional, apresentando os serviços de assessoria em crédito
                imobiliário prestados pela DMR e disponibilizando canais de contato. As informações aqui publicadas não
                constituem oferta, promessa de aprovação de crédito ou garantia de condições específicas.
            </p>

            <h2>2. Uso do site</h2>
            <p>Ao utilizar este site, você se compromete a:</p>
            <ul>
                <li>fornecer informações verdadeiras nos formulários de contato;</li>
                <li>não utilizar o site para fins ilícitos ou que violem direitos de terceiros;</li>
                <li>não tentar obter acesso não autorizado a áreas restritas, sistemas ou dados;</li>
                <li>não realizar atividades que possam comprometer a segurança ou o funcionamento do site.</li>
            </ul>

            <h2>3. Informações sobre crédito e financiamento</h2>
            <p>
                As condições de crédito, taxas, prazos e aprovações dependem de análise individual e das políticas das
                instituições financeiras envolvidas. Nenhuma informação apresentada no site deve ser interpretada como
                aprovação prévia ou garantia de contratação. Cada caso é avaliado conforme o perfil do interessado.
            </p>

            <h2>4. Propriedade intelectual</h2>
            <p>
                Os textos, marcas, logotipos, layout e demais elementos deste site são protegidos por direitos de
                propriedade intelectual e pertencem à DMR ou a seus respectivos titulares. É vedada a reprodução,
                distribuição ou utilização sem autorização prévia e expressa.
            </p>

            <h2>5. Links para terceiros</h2>
            <p>
                O site pode conter links para páginas de terceiros, como instituições financeiras e redes sociais. A DMR
                não se responsabiliza pelo conteúdo, pelas políticas ou pelas práticas desses sites externos.
            </p>

            <h2>6. Limitação de responsabilidade</h2>
            <p>
                A DMR empenha-se para manter as informações do site corretas e atualizadas, mas não garante a ausência
                de eventuais imprecisões ou indisponibilidades temporárias. O uso das informações é de responsabilidade
                do usuário.
            </p>

            <h2>7. Privacidade</h2>
            <p>
                O tratamento de dados pessoais realizado por meio do site é regido pela nossa
                <a href="<?= e(base_url('politica-de-privacidade')) ?>">Política de Privacidade</a>, que integra estes Termos de Uso.
            </p>

            <h2>8. Alterações</h2>
            <p>
                A DMR pode modificar estes Termos de Uso a qualquer momento. A versão vigente é sempre a publicada nesta
                página, com a data de atualização indicada no topo.
            </p>

            <h2>9. Contato</h2>
            <p>
                Em caso de dúvidas sobre estes Termos, entre em contato pelo e-mail
                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>.
            </p>
        </div>
    </div>
</article>
