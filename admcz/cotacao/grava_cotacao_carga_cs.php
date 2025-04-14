<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = $_POST['idCotacao'];
if (empty($idCotacao)) {
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Cadastre as informações de ORIGEM primeiramente.');
    exit;
}


$tipo_carga = 'solta';

// Carga Solta 1
$tipo_embalagem1 = isset($_POST['tipo_embalagemCS1']) ? $_POST['tipo_embalagemCS1'] : null;
$qt1 = isset($_POST['quantidadeCS1']) ? $_POST['quantidadeCS1'] : null;
$dim_h1 = isset($_POST['dim_hCS1']) ? $_POST['dim_hCS1'] : null;
$dim_l1 = isset($_POST['dim_lCS1']) ? $_POST['dim_lCS1'] : null;
$dim_c1 = isset($_POST['dim_cCS1']) ? $_POST['dim_cCS1'] : null;
$cbm = isset($_POST['cbm']) ? $_POST['cbm'] : null;
if (!is_null($cbm)) {
    // Substitui vírgula por ponto, se houver
    $cbm = str_replace(',', '.', $cbm);
    
    // Converte para float e formata com 3 casas decimais
    $cbm = number_format((float)$cbm, 3, '.', '');
}
$resumo_produto = isset($_POST['resumo_produto']) ? $_POST['resumo_produto'] : null;

try {
    $PDO = db_connect();
    $sql = "INSERT INTO cotacao_carga(id_cotacao, tipo_carga, tipo_embalagem, qt, dim_h, dim_l, dim_c, cbm, resumo_produto) VALUES (:idCotacao, :tipo_carga, :tipo_embalagem, :qt, :dim_h, :dim_l, :dim_c, :cbm, :resumo_produto)";
    $stmt = $PDO->prepare($sql);

    $stmt->bindParam(':idCotacao', $idCotacao);
    $stmt->bindParam(':tipo_carga', $tipo_carga);
    $stmt->bindParam(':tipo_embalagem', $tipo_embalagem1);
    $stmt->bindParam(':qt', $qt1);
    $stmt->bindParam(':dim_h', $dim_h1);
    $stmt->bindParam(':dim_l', $dim_l1);
    $stmt->bindParam(':dim_c', $dim_c1);
    $stmt->bindParam(':cbm', $cbm);
    $stmt->bindParam(':resumo_produto', $resumo_produto);
    $stmt->execute();

    $_SESSION['etapa'] = 4;

	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Embalagem incluída com sucesso!');
    exit();


} catch (Exception $e) {
    $_SESSION['etapa'] = 4;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível cadastrar. Erro: ' . $e->getMessage());
    exit();
}