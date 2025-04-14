<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

// Sanitiza o parâmetro 'id'
$idPV = filter_input(INPUT_GET, 'idPV', FILTER_SANITIZE_NUMBER_INT);

if ($idPV) {
    unset($_SESSION['passo']); // Remove a variável de sessão 'passo'
    $_SESSION['passo'] = 3; // Atribui um novo valor a 'passo'
    unset($_SESSION['editar_2']); // Remove a variável de sessão 'editar_2'
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&&flag=success&visualizar=1&idEstudo='.$idPV.'&tip=Todos os produtos foram gravados com sucesso!');
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&_teste&visualizar=1&idEstudo='.$idPV.'&flag=erro&tip=IDs inválido.');
    exit();
}