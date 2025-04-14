<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = $_POST['idCotacao'];
if (empty($idCotacao)) {
    header('Location: ../cotacao.php?pg=nova&flag=erro&tip=Cadastre as informações de ORIGEM primeiramente.');
}
$produto = isset($_POST['produto']) ? $_POST['produto'] : null;
$perecivel = isset($_POST['perecivel']) ? $_POST['perecivel'] : null;
$empilhavel = isset($_POST['empilhavel']) ? $_POST['empilhavel'] : null;
$perigoso = isset($_POST['perigoso']) ? $_POST['perigoso'] : null;
$refrigeracao = isset($_POST['refrigeracao']) ? $_POST['refrigeracao'] : null;
$temp = isset($_POST['temp_refrig']) ? $_POST['temp_refrig'] : null;
$valor_total = isset($_POST['valor_total']) ? $_POST['valor_total'] : null;
$valor_total = str_replace(".", "", $valor_total);
$novo_valor_total = str_replace(",", ".", $valor_total);
if ($valor_total == null) {
    $novo_valor_total = 0.00;
} else {
    $novo_valor_total = (float) $novo_valor_total;
}
$moeda = isset($_POST['moeda']) ? $_POST['moeda'] : null;
$peso_total = isset($_POST['peso_total']) ? $_POST['peso_total'] : null;
if (!is_null($peso_total)) {
    // Substitui vírgula por ponto, se houver
    $peso_total = str_replace(',', '.', $peso_total);
    
    // Converte para float e formata com 3 casas decimais
    $peso_total = number_format((float)$peso_total, 3, '.', '');
}
$tipo_peso_total = isset($_POST['tipo_peso_total']) ? $_POST['tipo_peso_total'] : null;

// insere no banco MERCADORIA cotação
$PDO = db_connect();
$sql = "INSERT INTO cotacao_mercadoria(id_cotacao, produto, perecivel, empilhavel, perigoso, refrigeracao, temp_refrig, valor_total, moeda, peso_total, tipo_peso_total) 
		VALUES (:idCotacao, :produto, :perecivel, :empilhavel, :perigoso, :refrigeracao, :temp_refrig, :valor_total, :moeda, :peso_total, :tipo_peso_total)";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idCotacao', $idCotacao);
$stmt->bindParam(':produto', $produto);
$stmt->bindParam(':perecivel', $perecivel);
$stmt->bindParam(':empilhavel', $empilhavel);
$stmt->bindParam(':perigoso', $perigoso);
$stmt->bindParam(':refrigeracao', $refrigeracao);
$stmt->bindParam(':temp_refrig', $temp);
$stmt->bindParam(':valor_total', $novo_valor_total);
$stmt->bindParam(':moeda', $moeda);
$stmt->bindParam(':peso_total', $peso_total);
$stmt->bindParam(':tipo_peso_total', $tipo_peso_total);

if ($stmt->execute()) {
    $_SESSION['etapa'] = 6;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Mercadoria cadastrada com sucesso!');
    exit();
} else {
    $_SESSION['etapa'] = 5;
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível cadastrar.');
    exit();
}