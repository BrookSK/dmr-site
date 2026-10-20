<?php
/**
 * Logos dos bancos parceiros.
 *
 * NOTA: estas são representações SVG aproximadas, nas cores oficiais de cada
 * marca, usadas como placeholder visual. Ao obter os arquivos oficiais
 * (de preferência licenciados / autorizados pela DMR), substitua cada bloco por:
 *   <div class="bank"><img src="<?= e(asset('img/banks/caixa.svg')) ?>" alt="Caixa"></div>
 *
 * BRB removido a pedido da cliente.
 */
?>
<!-- Caixa Econômica Federal -->
<div class="bank" title="Caixa Econômica Federal">
    <svg viewBox="0 0 150 44" role="img" aria-label="Caixa">
        <g transform="translate(6 10)">
            <path d="M0 12 12 0h12L12 12 24 24H12z" fill="#0070AF"/>
            <path d="M16 12 28 0h12L28 12l12 12H28z" fill="#F7901E"/>
        </g>
        <text x="60" y="30" font-family="Arial, sans-serif" font-weight="800" font-size="20" fill="#0070AF">CAIXA</text>
    </svg>
</div>

<!-- Banco do Brasil -->
<div class="bank" title="Banco do Brasil">
    <svg viewBox="0 0 160 44" role="img" aria-label="Banco do Brasil">
        <rect x="6" y="6" width="32" height="32" rx="3" fill="#FAE100"/>
        <g transform="translate(22 22)" fill="#0038A8">
            <rect x="-11" y="-3" width="22" height="6" transform="rotate(45)"/>
            <rect x="-11" y="-3" width="22" height="6" transform="rotate(-45)"/>
        </g>
        <text x="48" y="21" font-family="Arial, sans-serif" font-weight="800" font-size="13" fill="#0038A8">BANCO DO</text>
        <text x="48" y="37" font-family="Arial, sans-serif" font-weight="800" font-size="13" fill="#0038A8">BRASIL</text>
    </svg>
</div>

<!-- Santander -->
<div class="bank" title="Santander">
    <svg viewBox="0 0 175 44" role="img" aria-label="Santander">
        <g transform="translate(22 22)">
            <circle r="17" fill="#EC0000"/>
            <path d="M0-13c4 5 4 9 0 14M-5-10c3 4 3 8 0 12M5-10c3 4 3 8 0 12" stroke="#fff" stroke-width="2.4" fill="none" stroke-linecap="round"/>
        </g>
        <text x="46" y="30" font-family="Arial, sans-serif" font-weight="700" font-size="19" fill="#EC0000">Santander</text>
    </svg>
</div>

<!-- Bradesco -->
<div class="bank" title="Bradesco">
    <svg viewBox="0 0 160 44" role="img" aria-label="Bradesco">
        <g transform="translate(22 22)">
            <circle r="17" fill="#CC092F"/>
            <path d="M0-12 3.5-4 12-4 5 1.5 7.5 10 0 5-7.5 10-5 1.5-12-4-3.5-4z" fill="#fff"/>
        </g>
        <text x="46" y="30" font-family="Arial, sans-serif" font-weight="700" font-size="19" fill="#CC092F">Bradesco</text>
    </svg>
</div>

<!-- Itaú -->
<div class="bank" title="Itaú">
    <svg viewBox="0 0 130 44" role="img" aria-label="Itaú">
        <rect x="6" y="6" width="32" height="32" rx="6" fill="#EC7000"/>
        <circle cx="22" cy="15" r="2.6" fill="#003399"/>
        <rect x="20" y="19" width="4" height="13" rx="2" fill="#003399"/>
        <text x="46" y="30" font-family="Arial, sans-serif" font-weight="800" font-size="20" fill="#003399">Itaú</text>
    </svg>
</div>
