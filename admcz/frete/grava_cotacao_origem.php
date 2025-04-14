<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = $_POST['idCotacao'];
if (empty($idCotacao)) {
	$_SESSION['etapa'] = 1;
	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de ABERTURA primeiramente.');
}
$pais = isset($_POST['pais']) ? $_POST['pais'] : null;
$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : null;
$endereco_coleta = isset($_POST['endereco_coleta']) ? $_POST['endereco_coleta'] : null;

// insere no banco ABERTURA cotação
$PDO = db_connect();
$sql = "INSERT INTO cotacao_origem(id_cotacao, pais_origem, cidade_origem, endereco_coleta) VALUES (:idCotacao, :pais, :cidade, :endereco_coleta)";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao);
$stmt->bindParam(':pais', $pais);
$stmt->bindParam(':cidade', $cidade);
$stmt->bindParam(':endereco_coleta', $endereco_coleta);

if ($stmt->execute()) {

	$tipoCotacao = $_SESSION['tipo_cotacao'];

	

	$_SESSION['etapa'] = 3;

	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Origem cadastrada com sucesso!');
} else {
	$_SESSION['etapa'] = 2;
	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível cadastrar.');
}