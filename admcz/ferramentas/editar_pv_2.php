<?php
session_start();

$visualizar = isset($_GET['visualizar']) ? $_GET['visualizar'] : null;
$idEstudo = isset($_GET['idEstudo']) ? $_GET['idEstudo'] : null;

if (empty($idEstudo)) {
	header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&flag=erro&tip=Não foi possível realizar a operação.');
}

// inclui o arquivo de inicialização
unset($_SESSION['passo']); // Remove a variável de sessão 'passo'
unset($_SESSION['editar_1']); // Remove a variável de sessão 'passo'
unset($_SESSION['editar_2']); // Remove a variável de sessão 'passo'
$_SESSION['passo'] = 2; // Atribui um novo valor a 'passo'
$_SESSION['editar_2'] = 1; // Atribui um novo valor a 'passo'
header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&visualizar='.$visualizar.'&idEstudo='.$idEstudo.'&flag=success&tip=Edite as informações desejadas...');