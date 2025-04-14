<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idCotacao = isset($_POST['idCotacao']) ? $_POST['idCotacao'] : null;
$num_proc_interno = isset($_POST['proc_interno']) ? $_POST['proc_interno'] : null;
$modal = isset($_POST['modal']) ? $_POST['modal'] : null;
if ($modal === 'aer') {
    $incoterms = isset($_POST['incoterms-aer']) ? $_POST['incoterms-aer'] : null;
} elseif ($modal === 'mar') {
    $incoterms = isset($_POST['incoterms-mar']) ? $_POST['incoterms-mar'] : null;
} else {
    $incoterms = isset($_POST['incoterms-rod']) ? $_POST['incoterms-rod'] : null;
}

// Altera dados de abertura
$PDO = db_connect();
$sql = "UPDATE cotacao_abertura SET n_proc_interno = :n_proc_interno, modal = :modal, incoterms = :incoterms WHERE id = :idCotacao";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':n_proc_interno', $num_proc_interno);
$stmt->bindParam(':modal', $modal);
$stmt->bindParam(':incoterms', $incoterms);
$stmt->bindParam(':idCotacao', $idCotacao);

if ($stmt->execute()) {
    
    $_SESSION['etapa'] = 2;

	header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Dados alterados com sucesso!');
} else {
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível alterar os dados.');
}