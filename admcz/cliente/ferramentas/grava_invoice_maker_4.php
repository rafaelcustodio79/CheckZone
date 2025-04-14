<?php
session_start();

unset($_SESSION['etapa_atual']); // Remove a variável de sessão

$idIM = filter_input(INPUT_GET, 'idIM', FILTER_SANITIZE_NUMBER_INT);
$invoice = filter_input(INPUT_GET, 'invoice', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Redirecionamento com mensagem de sucesso
$_SESSION['etapa_atual'] = 5;
header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_criar&idIM='.$idIM.'&invoice='.$invoice.'&flag=success&tip=Produtos salvos com sucesso!');
exit();