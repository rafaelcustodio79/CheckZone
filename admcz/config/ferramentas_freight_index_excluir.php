<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

// Verifica se o ID foi passado e se é um número válido
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false) {
    // Se o ID não for válido, redireciona com uma mensagem de erro
    header('Location: ../config.php?a=config&b=ferramentas_freight_index&flag=erro&tip=ID inválido.');
    exit;
}
$PDO = db_connect();
// Prepara a consulta SQL para remover o dado
$sql = "DELETE FROM frete_index WHERE id = :id";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

// Executa a consulta e verifica se foi bem-sucedida
if ($stmt->execute()) {
    // Redireciona com uma mensagem de sucesso
    header('Location: ../config.php?a=config&b=ferramentas_freight_index&flag=success&tip=Dado removido com sucesso!');
} else {
    // Redireciona com uma mensagem de erro se a execução falhar
    header('Location: ../config.php?a=config&b=ferramentas_freight_index&flag=erro&tip=Não foi possível realizar a operação.');
}
exit;