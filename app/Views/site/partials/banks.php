<?php
/**
 * Logos oficiais dos bancos parceiros (arquivos em public/assets/img/banks/,
 * obtidos do Wikimedia Commons). BRB, Banco do Brasil e Credihome removidos a
 * pedido da cliente; "Consórcios" incluído como modalidade, com logotipo
 * próprio (consorcio.svg) no mesmo padrão cinza/colorido dos bancos.
 */
$banks = [
    ['file' => 'caixa.svg',     'name' => 'Caixa Econômica Federal'],
    ['file' => 'santander.svg', 'name' => 'Santander'],
    ['file' => 'bradesco.svg',  'name' => 'Bradesco'],
    ['file' => 'itau.svg',      'name' => 'Itaú', 'square' => true],
    ['file' => 'consorcio.svg', 'name' => 'Consórcios'],
];
?>
<?php foreach ($banks as $b): ?>
    <li class="bank<?= !empty($b['square']) ? ' bank--square' : '' ?>">
        <img src="<?= e(asset('img/banks/' . $b['file'])) ?>" alt="<?= e($b['name']) ?>" loading="lazy" decoding="async">
    </li>
<?php endforeach; ?>
