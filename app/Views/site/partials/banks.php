<?php
/**
 * Logos oficiais dos bancos parceiros (arquivos em public/assets/img/banks/,
 * obtidos do Wikimedia Commons). BRB removido a pedido da cliente.
 */
$banks = [
    ['file' => 'caixa.svg',     'name' => 'Caixa Econômica Federal'],
    ['file' => 'bb.svg',        'name' => 'Banco do Brasil'],
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
