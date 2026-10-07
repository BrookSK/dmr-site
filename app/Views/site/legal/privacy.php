<?php
$email = (string) setting('contact_email', 'contato@dmrassessoria.com.br');
$phone = (string) setting('contact_phone', '(11) 98223-1363');
$siteName = (string) setting('site_name', 'DMR Assessoria Imobiliária');
?>
<article class="legal">
    <div class="container">
        <header class="legal__header">
            <span class="eyebrow">Institucional</span>
            <h1 class="section__title">Política de Privacidade</h1>
            <p class="legal__updated">Última atualização: <?= e(date('d/m/Y')) ?></p>
        </header>

        <div class="legal__notice">
            <strong>Aviso importante:</strong> este documento é um modelo de referência e deve ser revisado por um
            profissional jurídico antes da publicação definitiva, de forma a refletir com precisão as práticas da
            <?= e($siteName) ?> e as exigências da Lei Geral de Proteção de Dados (Lei nº 13.709/2018 — LGPD).
        </div>

        <div class="legal__content">
            <p>
                A <?= e($siteName) ?> ("DMR", "nós") valoriza a privacidade de seus visitantes, clientes e parceiros.
                Esta Política de Privacidade descreve como coletamos, utilizamos, armazenamos e protegemos os dados
                pessoais tratados por meio deste site e dos canais de atendimento da empresa.
            </p>

            <h2>1. Dados que coletamos</h2>
            <p>Podemos coletar as seguintes categorias de dados:</p>
            <ul>
                <li><strong>Dados fornecidos por você:</strong> nome, e-mail, telefone/WhatsApp, tipo de atendimento e o conteúdo das mensagens enviadas por meio dos formulários do site.</li>
                <li><strong>Dados de navegação:</strong> endereço IP, informações do dispositivo e do navegador, páginas acessadas e dados de cookies, quando aplicável.</li>
                <li><strong>Dados necessários à prestação de serviços:</strong> informações relacionadas a crédito, financiamento e documentação, coletadas no contexto de uma contratação ou atendimento, sempre com a devida base legal.</li>
            </ul>

            <h2>2. Finalidade do tratamento</h2>
            <p>Utilizamos os dados pessoais para:</p>
            <ul>
                <li>responder a solicitações de contato e orçamento;</li>
                <li>prestar serviços de assessoria em crédito imobiliário e acompanhar processos;</li>
                <li>comunicar informações relevantes sobre o andamento de atendimentos;</li>
                <li>cumprir obrigações legais e regulatórias;</li>
                <li>aprimorar a experiência de navegação e os nossos serviços.</li>
            </ul>

            <h2>3. Base legal</h2>
            <p>
                O tratamento de dados é realizado com fundamento nas hipóteses previstas na LGPD, tais como o
                consentimento do titular, a execução de contrato, o cumprimento de obrigação legal e o legítimo
                interesse, sempre respeitando os direitos e as liberdades fundamentais do titular.
            </p>

            <h2>4. Formulários e contato</h2>
            <p>
                Ao preencher um formulário no site, você autoriza o uso dos dados informados para que a DMR possa
                entrar em contato e dar andamento ao atendimento solicitado. Esses dados não são utilizados para
                finalidades incompatíveis com aquelas informadas no momento da coleta.
            </p>

            <h2>5. Cookies</h2>
            <p>
                Este site pode utilizar cookies e tecnologias semelhantes para garantir o seu funcionamento,
                lembrar preferências e, eventualmente, mensurar o desempenho das páginas. Você pode gerenciar ou
                desativar cookies nas configurações do seu navegador, ciente de que isso pode afetar algumas
                funcionalidades.
            </p>

            <h2>6. Compartilhamento de dados</h2>
            <p>
                A DMR poderá compartilhar dados pessoais com instituições financeiras, bancos parceiros, cartórios e
                prestadores de serviço estritamente na medida necessária à execução do atendimento ou da contratação, bem
                como para o cumprimento de obrigações legais. Não comercializamos dados pessoais.
            </p>

            <h2>7. Armazenamento e segurança</h2>
            <p>
                Adotamos medidas técnicas e organizacionais razoáveis para proteger os dados pessoais contra acessos
                não autorizados, perda, alteração ou divulgação indevida. Os dados são mantidos pelo tempo necessário
                ao cumprimento das finalidades informadas ou das obrigações legais aplicáveis.
            </p>

            <h2>8. Direitos do titular</h2>
            <p>Nos termos da LGPD, você pode, a qualquer momento, solicitar:</p>
            <ul>
                <li>a confirmação da existência de tratamento;</li>
                <li>o acesso aos seus dados;</li>
                <li>a correção de dados incompletos, inexatos ou desatualizados;</li>
                <li>a anonimização, o bloqueio ou a eliminação de dados desnecessários;</li>
                <li>a portabilidade e a informação sobre compartilhamento;</li>
                <li>a revogação do consentimento, quando aplicável.</li>
            </ul>

            <h2>9. Contato do responsável</h2>
            <p>
                Para exercer seus direitos ou esclarecer dúvidas sobre esta Política, entre em contato pelo e-mail
                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a> ou pelo telefone <?= e($phone) ?>.
            </p>

            <h2>10. Atualizações desta Política</h2>
            <p>
                Esta Política de Privacidade pode ser atualizada periodicamente. Recomendamos a sua consulta regular.
                Alterações relevantes poderão ser comunicadas pelos canais oficiais da DMR.
            </p>
        </div>
    </div>
</article>
