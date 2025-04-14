<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = $_POST['idCotacao'];
if (empty($idCotacao)) {
    $_SESSION['etapa'] = 4;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de ORIGEM primeiramente.');
    exit;
}


$tipo_carga = 'container';

// Carga Container 1
$qt_c = isset($_POST['quantidade_C1']) ? $_POST['quantidade_C1'] : null;
$tipo_container = isset($_POST['tipo_container1']) ? $_POST['tipo_container1'] : null;
$peso_c = isset($_POST['peso_C1']) ? $_POST['peso_C1'] : null;
$tipo_peso_c = isset($_POST['tipoPeso_C1']) ? $_POST['tipoPeso_C1'] : null;

// insere no banco CARGA 1 cotação
$PDO = db_connect();
$sql = "INSERT INTO cotacao_carga(id_cotacao, tipo_carga, qt_c, tipo_container, peso_c, tipo_peso_c) VALUES (:idCotacao, :tipo_carga, :qt_c, :tipo_container, :peso_c, :tipo_peso_c)";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao);
$stmt->bindParam(':tipo_carga', $tipo_carga);
$stmt->bindParam(':qt_c', $qt_c);
$stmt->bindParam(':tipo_container', $tipo_container);
$stmt->bindParam(':peso_c', $peso_c);
$stmt->bindParam(':tipo_peso_c', $tipo_peso_c);


if ($stmt->execute()) {
    $_SESSION['etapa'] = 4;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&liCarga=2&flag=success&tip=Carga cadastrada com sucesso!');
    exit;
} else {
    // Tratar erro, por exemplo, redirecionar para uma página de erro ou registrar o erro em um log.
    header('Location: ../frete.php?a=frete&b=frete_cotacao&liCarga=2&flag=erro&tip=Erro na inserção dos dados.');
    exit;
}