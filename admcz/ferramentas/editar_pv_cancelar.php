<?php
session_start();

$visualizar = isset($_GET['visualizar']) ? $_GET['visualizar'] : null;
$idEstudo = isset($_GET['idEstudo']) ? $_GET['idEstudo'] : null;

if (empty($idEstudo)) {
	header('Location: ../cliente.php?pg=plan_viabilidade&flag=erro&tip=Não foi possível realizar a operação.');
}

// inclui o arquivo de inicialização
unset($_SESSION['passo']); // Remove a variável de sessão 'passo'
unset($_SESSION['editar_1']); // Remove a variável de sessão 'passo'
unset($_SESSION['editar_2']); // Remove a variável de sessão 'passo'
unset($_SESSION['editar_3']); // Remove a variável de sessão 'passo'
unset($_SESSION['editar_4']); // Remove a variável de sessão 'passo'
unset($_SESSION['editar_5']); // Remove a variável de sessão 'passo'
header('Location: ../cliente.php?pg=plan_viabilidade&visualizar='.$visualizar.'&idEstudo='.$idEstudo.'&flag=success&tip=Edição de dados cancelada!');