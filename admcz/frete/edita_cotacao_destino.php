<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = isset($_POST['idCotacao']) ? $_POST['idCotacao'] : null;
$pais = isset($_POST['pais']) ? $_POST['pais'] : null;
$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : null;
$endereco_no_destino = isset($_POST['endereco_no_destino']) ? $_POST['endereco_no_destino'] : null;

// Altera dados de abertura
$PDO = db_connect();
$sql = "UPDATE cotacao_destino SET pais_destino = :pais, cidade_destino = :cidade, endereco_no_destino = :endereco_no_destino WHERE id_cotacao = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':pais', $pais);
$stmt->bindParam(':cidade', $cidade);
$stmt->bindParam(':endereco_no_destino', $endereco_no_destino);
$stmt->bindParam(':idCotacao', $idCotacao);

if ($stmt->execute()) {
    $_SESSION['etapa'] = 4;

	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Dados alterados com sucesso!');
} else {
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível alterar os dados.');
}