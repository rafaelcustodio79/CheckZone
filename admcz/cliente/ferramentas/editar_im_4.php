<?php
session_start();

$idIM = filter_input(INPUT_GET, 'idIM', FILTER_SANITIZE_NUMBER_INT);
$invoice = filter_input(INPUT_GET, 'invoice', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Redirecionamento com mensagem de sucesso
$_SESSION['active_step'] = 5;
header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice.'&flag=success&tip=Produtos alterados com sucesso!&tabAtiva=2');
exit();