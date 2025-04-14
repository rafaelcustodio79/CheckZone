<?php

$idEmpresa = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$acao = filter_var($_GET['acao'] ?? null, FILTER_VALIDATE_INT);

$PDO = db_connect();

$sql = ""; // Adicione esta linha

if ($acao == 0) {
    $sql = "UPDATE empresa SET ativo = 0 WHERE  id_empresa = :idEmpresa";
} else {
    $sql = "UPDATE empresa SET ativo = 1 WHERE  id_empresa = :idEmpresa";
}
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idEmpresa', $idEmpresa);

if ($stmt->execute()) {
    header('Location: config.php?a=config&b=empresas&flag=success&tip=Ação realizada com sucesso!');
    exit;
} else {
    header('Location: config.php?a=config&b=empresas&flag=erro&tip=Não foi possível realizar a operação.');
    exit;
}
