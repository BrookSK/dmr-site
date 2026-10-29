<?php
/**
 * Logos oficiais dos bancos parceiros (arquivos em public/assets/img/banks/,
 * obtidos do Wikimedia Commons). BRB, Banco do Brasil e Credihome removidos a
 * pedido da cliente; "Consórcios" incluído como modalidade (sem logo).
 */
$banks = [
    ['file' => 'caixa.svg',     'name' => 'Caixa Econômica Federal'],
    ['file' => 'santander.svg', 'name' => 'Santander'],
    ['file' => 'bradesco.svg',  'name' => 'Bradesco'],
    ['file' => 'itau.svg',      'name' => 'Itaú', 'square' => true],
];
?>
<?php foreach ($banks as $b): ?>
    <li class="bank<?= !empty($b['square']) ? ' bank--square' : '' ?>">
        <img src="<?= e(asset('img/banks/' . $b['file'])) ?>" alt="<?= e($b['name']) ?>" loading="lazy" decoding="async">
    </li>
<?php endforeach; ?>
    <li class="bank bank--text">
        <span class="bank__ico" aria-hidden="true"><?= icon('consorcio', 22) ?></span>
        <span>Consórcios</span>
    </li>
