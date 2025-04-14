<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = $_POST['idCotacao'];
if (empty($idCotacao)) {
	header('Location: ../cotacao.php?pg=nova&flag=erro&tip=Cadastre as informações de ORIGEM primeiramente.');
}
$pais = isset($_POST['pais']) ? $_POST['pais'] : null;
$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : null;
$endereco_no_destino = isset($_POST['endereco_no_destino']) ? $_POST['endereco_no_destino'] : null;

// insere no banco ABERTURA cotação
$PDO = db_connect();
$sql = "INSERT INTO cotacao_destino(id_cotacao, pais_destino, cidade_destino, endereco_no_destino) VALUES (:idCotacao, :pais, :cidade, :endereco_no_destino)";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao);
$stmt->bindParam(':pais', $pais);
$stmt->bindParam(':cidade', $cidade);
$stmt->bindParam(':endereco_no_destino', $endereco_no_destino);

if ($stmt->execute()) {
	
	$_SESSION['etapa'] = 4;

	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Destino cadastrado com sucesso!');
} else {
	$_SESSION['etapa'] = 3;
	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível cadastrar.');
}