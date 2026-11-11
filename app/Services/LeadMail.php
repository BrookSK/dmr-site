<?php

declare(strict_types=1);

namespace App\Services;

/**
 * E-mail de "novo contato pelo site", enviado para a equipe da DMR.
 *
 * Layout de e-mail (tabelas + estilos inline) para funcionar no Gmail,
 * Outlook e Apple Mail, inclusive no celular. Cores do manual da marca:
 * grafite #1c1a17, dourado #CBAE6C / #9C7B2C, marrom #A3805F, creme #EAE6DF.
 * Fontes de e-mail seguras: Georgia (títulos, no lugar da Fraunces) e Arial.
 */
final class LeadMail
{
    /**
     * @param array{name:string,email:string,phone:string,channel:string,profile:string,interest?:string,company?:string,city?:string,message?:string,received_at?:string} $lead
     */
    public static function subject(array $lead, bool $test = false): string
    {
        $parts = array_filter([$lead['profile'], $lead['interest'] ?? '']);
        return ($test ? '[Teste] ' : '') . 'Novo contato pelo site: ' . $lead['name'] . ' (' . implode(' · ', $parts) . ')';
    }

    /** Dados de exemplo (preview no painel e e-mail de teste). @return array<string,string> */
    public static function sample(): array
    {
        return [
            'name'        => 'Mariana Souza',
            'email'       => 'mariana.souza@exemplo.com.br',
            'phone'       => '(17) 99999-1234',
            'channel'     => 'Quero financiar',
            'profile'     => 'Comprador(a)',
            'interest'    => 'Financiamento residencial',
            'company'     => '',
            'city'        => 'São José do Rio Preto/SP',
            'message'     => "Olá! Estou procurando um apartamento de 2 quartos e queria entender quanto consigo financiar.\nPosso usar meu FGTS na entrada?",
            'received_at' => date('d/m/Y \à\s H:i'),
        ];
    }

    /** @param array<string,string> $lead */
    public static function html(array $lead, bool $test = false): string
    {
        $siteName = (string) setting('site_name', 'DMR Assessoria Imobiliária');
        $received = $lead['received_at'] ?? date('d/m/Y \à\s H:i');
        $digits = (string) preg_replace('/\D+/', '', $lead['phone']);
        if (strlen($digits) === 10 || strlen($digits) === 11) {
            $digits = '55' . $digits; // número brasileiro sem DDI
        }
        $firstName = trim(explode(' ', trim($lead['name']))[0] ?? '');
        $waUrl = 'https://wa.me/' . $digits . '?text=' . rawurlencode('Olá, ' . $firstName . '! Aqui é da DMR Assessoria Imobiliária. Recebemos seu contato pelo site.');
        $mailUrl = 'mailto:' . $lead['email'] . '?subject=' . rawurlencode('DMR Assessoria Imobiliária — seu contato pelo site');

        // Logo: e-mail não exibe SVG; usa a logo do painel só se for PNG/JPG/WEBP.
        $logoPath = (string) setting('site_logo', '');
        $logoUrl = preg_match('/\.(png|jpe?g|webp)$/i', $logoPath) ? (string) brand_logo_url('dark') : '';
        $brand = $logoUrl !== ''
            ? '<img src="' . e($logoUrl) . '" alt="' . e($siteName) . '" height="40" style="display:block;height:40px;width:auto;border:0;outline:none;">'
            : '<div style="font-family:Georgia,\'Times New Roman\',serif;font-size:26px;letter-spacing:5px;color:#ffffff;line-height:1;">DMR</div>'
              . '<div style="font-family:Arial,Helvetica,sans-serif;font-size:10px;letter-spacing:2.6px;text-transform:uppercase;color:#CBAE6C;padding-top:6px;">Assessoria Imobiliária</div>';

        $rows = [
            ['Nome', e($lead['name'])],
            ['WhatsApp', '<a href="' . e($waUrl) . '" style="color:#1c1a17;text-decoration:underline;">' . e($lead['phone']) . '</a>'],
            ['E-mail', '<a href="' . e($mailUrl) . '" style="color:#1c1a17;text-decoration:underline;word-break:break-all;">' . e($lead['email']) . '</a>'],
            ['Perfil', e($lead['profile'])],
        ];
        foreach (['interest' => 'Interesse', 'company' => 'Empresa', 'city' => 'Cidade / Estado'] as $k => $label) {
            if (trim((string) ($lead[$k] ?? '')) !== '') {
                $rows[] = [$label, e((string) $lead[$k])];
            }
        }

        $rowsHtml = '';
        foreach ($rows as $i => [$label, $value]) {
            $border = $i === 0 ? '' : 'border-top:1px solid #efe9df;';
            $rowsHtml .= '<tr>'
                . '<td class="lbl" width="130" valign="top" style="' . $border . 'padding:12px 12px 12px 0;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#7a7066;">' . $label . '</td>'
                . '<td class="val" valign="top" style="' . $border . 'padding:12px 0;font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#1c1a17;font-weight:bold;">' . $value . '</td>'
                . '</tr>';
        }

        $message = trim((string) ($lead['message'] ?? ''));
        $messageHtml = $message !== ''
            ? nl2br(e($message), false)
            : '<span style="color:#7a7066;font-style:italic;">A pessoa não escreveu mensagem.</span>';

        $testBanner = $test
            ? '<tr><td style="background:#fbf2dd;padding:10px 32px;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#7d6223;">Este é um <strong>e-mail de teste</strong> com dados de exemplo. Os contatos reais chegam neste mesmo formato.</td></tr>'
            : '';

        $preheader = e($lead['name'] . ' · ' . $lead['profile'] . (($lead['interest'] ?? '') !== '' ? ' · ' . $lead['interest'] : '') . ' · ' . $lead['phone']);
        $channel = e($lead['channel']);
        $receivedEsc = e($received);
        $siteEsc = e($siteName);
        $waEsc = e($waUrl);
        $mailEsc = e($mailUrl);

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="x-apple-disable-message-reformatting">
<title>Novo contato pelo site</title>
<style>
  @media only screen and (max-width: 620px) {
    .wrap { padding: 0 !important; }
    .card { border-radius: 0 !important; }
    .pad { padding-left: 22px !important; padding-right: 22px !important; }
    .h1 { font-size: 22px !important; }
    .lbl, .val { display: block !important; width: 100% !important; }
    .lbl { padding: 12px 0 2px 0 !important; }
    .val { padding: 0 0 12px 0 !important; border-top: 0 !important; }
    .btn-cell { display: block !important; width: 100% !important; padding: 0 0 10px 0 !important; }
    .btn-cell a { display: block !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background:#f6f3ee;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:#f6f3ee;">{$preheader}</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f6f3ee;">
  <tr><td class="wrap" align="center" style="padding:28px 12px;">
    <table role="presentation" class="card" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e6e0d6;">
      <tr><td class="pad" style="background:#1c1a17;padding:26px 32px;">{$brand}</td></tr>
      <tr><td style="height:3px;line-height:3px;font-size:0;background:#CBAE6C;">&nbsp;</td></tr>
      {$testBanner}
      <tr><td class="pad" style="padding:30px 32px 6px 32px;">
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:2.4px;text-transform:uppercase;color:#7d6223;font-weight:bold;">{$channel}</div>
        <h1 class="h1" style="margin:10px 0 6px 0;font-family:Georgia,'Times New Roman',serif;font-weight:normal;font-size:26px;line-height:1.25;color:#1c1a17;">Novo contato pelo site</h1>
        <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.6;color:#4d463e;">Recebido em {$receivedEsc}. Responda o quanto antes: quem acabou de preencher está esperando o retorno.</p>
      </td></tr>
      <tr><td class="pad" style="padding:18px 32px 4px 32px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">{$rowsHtml}</table>
      </td></tr>
      <tr><td class="pad" style="padding:14px 32px 6px 32px;">
        <div style="font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#7a7066;padding-bottom:8px;">Mensagem</div>
        <div style="background:#f6f3ee;border-left:3px solid #CBAE6C;border-radius:0 8px 8px 0;padding:16px 18px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#1c1a17;">{$messageHtml}</div>
      </td></tr>
      <tr><td class="pad" style="padding:24px 32px 30px 32px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
          <td class="btn-cell" width="50%" style="padding-right:6px;">
            <a href="{$waEsc}" style="display:block;background:#CBAE6C;color:#1c1a17;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;text-decoration:none;text-align:center;padding:14px 10px;border-radius:6px;">Responder no WhatsApp</a>
          </td>
          <td class="btn-cell" width="50%" style="padding-left:6px;">
            <a href="{$mailEsc}" style="display:block;background:#ffffff;color:#1c1a17;border:1px solid #1c1a17;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;text-decoration:none;text-align:center;padding:13px 10px;border-radius:6px;">Responder por e-mail</a>
          </td>
        </tr></table>
      </td></tr>
      <tr><td class="pad" style="background:#faf8f4;border-top:1px solid #efe9df;padding:18px 32px;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.6;color:#7a7066;">
        Enviado automaticamente pelo formulário do site da {$siteEsc}. Se você responder este e-mail, a resposta vai direto para o cliente. O contato também fica salvo no painel do site.
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }

    /** Versão em texto puro (para clientes de e-mail sem HTML). @param array<string,string> $lead */
    public static function text(array $lead, bool $test = false): string
    {
        $lines = [];
        if ($test) {
            $lines[] = 'E-MAIL DE TESTE (dados de exemplo)';
            $lines[] = '';
        }
        $lines[] = 'NOVO CONTATO PELO SITE — ' . $lead['channel'];
        $lines[] = 'Recebido em ' . ($lead['received_at'] ?? date('d/m/Y \à\s H:i'));
        $lines[] = '';
        $lines[] = 'Nome: ' . $lead['name'];
        $lines[] = 'WhatsApp: ' . $lead['phone'];
        $lines[] = 'E-mail: ' . $lead['email'];
        $lines[] = 'Perfil: ' . $lead['profile'];
        foreach (['interest' => 'Interesse', 'company' => 'Empresa', 'city' => 'Cidade / Estado'] as $k => $label) {
            if (trim((string) ($lead[$k] ?? '')) !== '') {
                $lines[] = $label . ': ' . $lead[$k];
            }
        }
        $lines[] = '';
        $lines[] = 'Mensagem:';
        $lines[] = trim((string) ($lead['message'] ?? '')) !== '' ? (string) $lead['message'] : '(sem mensagem)';
        $lines[] = '';
        $lines[] = 'Responder este e-mail responde direto para o cliente.';
        return implode("\n", $lines);
    }

    /**
     * Destinatários: "E-mail(s) que recebem os contatos" (SMTP) → e-mail de contato → remetente.
     *
     * @return array<int,string>
     */
    public static function recipients(): array
    {
        foreach (['notify_email', 'contact_email', 'smtp_from_email'] as $key) {
            $raw = (string) setting($key, '');
            $list = array_values(array_unique(array_filter(
                array_map('trim', preg_split('/[,;\s]+/', $raw) ?: []),
                static fn (string $m): bool => (bool) filter_var($m, FILTER_VALIDATE_EMAIL)
            )));
            if ($list !== []) {
                return $list;
            }
        }
        return [];
    }
}
