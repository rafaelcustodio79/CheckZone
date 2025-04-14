<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = isset($_POST['idCotacao']) ? $_POST['idCotacao'] : null;
$pais = isset($_POST['pais']) ? $_POST['pais'] : null;
$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : null;
$endereco_coleta = isset($_POST['endereco_coleta']) ? $_POST['endereco_coleta'] : null;

// Altera dados de abertura
$PDO = db_connect();
$sql = "UPDATE cotacao_origem SET pais_origem = :pais, cidade_origem = :cidade, endereco_coleta = :endereco_coleta WHERE id_cotacao = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':pais', $pais);
$stmt->bindParam(':cidade', $cidade);
$stmt->bindParam(':endereco_coleta', $endereco_coleta);
$stmt->bindParam(':idCotacao', $idCotacao);

if ($stmt->execute()) {

    $_SESSION['etapa'] = 3;

	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Dados alterados com sucesso!');
} else {
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível alterar os dados.');
}