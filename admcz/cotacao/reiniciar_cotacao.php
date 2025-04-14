<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$idUsuario = $_SESSION['user_id'];
$idEmpresa = $_SESSION['empresa_id'];
$protocolo = $_SESSION['protocolo'];

//enviar email ADM sistema
$sqlDadosUser = "SELECT * FROM usuario WHERE id = :idUsuario";
$stmtDU = $PDO->prepare($sqlDadosUser);
$stmtDU->bindParam(':idUsuario', $idUsuario);
$stmtDU->execute();
$dadosUsuario = $stmtDU->fetchAll(PDO::FETCH_ASSOC);

$sqlDadosEmpresa = "SELECT * FROM empresa WHERE id_empresa = :idEmpresa";
$stmtDE = $PDO->prepare($sqlDadosEmpresa);
$stmtDE->bindParam(':idEmpresa', $idEmpresa);
$stmtDE->execute();
$dadosEmpresa = $stmtDE->fetchAll(PDO::FETCH_ASSOC);

$nomeUsuario = $dadosUsuario[0]['nome'];
$nomeEmpresa = $dadosEmpresa[0]['nome_empresa'];
$data_add = date('d/m/Y');
$hora_add = date('H:i:s');

$idCotacao = $_GET['idCotacao'] ? $_GET['idCotacao'] : null;

//require "envio_email_adm_reinicio.php";

// remove do banco todo os dados da  cotacao  não finalizada
$sql = "DELETE FROM cotacao_abertura WHERE id = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao, PDO::PARAM_INT);
$stmt->execute();

$sql = "DELETE FROM cotacao_complemento WHERE id_cotacao = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao, PDO::PARAM_INT);
$stmt->execute();

$sql = "DELETE FROM cotacao_mercadoria WHERE id_cotacao = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao, PDO::PARAM_INT);
$stmt->execute();

$sql = "DELETE FROM cotacao_carga WHERE id_cotacao = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao, PDO::PARAM_INT);
$stmt->execute();

$sql = "DELETE FROM cotacao_destino WHERE id_cotacao = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao, PDO::PARAM_INT);
$stmt->execute();

$sql = "DELETE FROM cotacao_origem WHERE id_cotacao = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->execute()) {
    unset($_SESSION['etapa']);
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Pedido de cotação reiniciado com sucesso!');
    exit();
} else {
    header('Location: ../frete.php?a=frete&b=frete_cotacaoflag=erro&tip=Não foi possível realizar operação.');
    exit();
}