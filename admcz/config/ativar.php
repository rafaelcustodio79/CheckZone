<?php

$idBanner = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$acao = filter_var($_GET['acao'] ?? null, FILTER_VALIDATE_INT);

$PDO = db_connect();

$sql = ""; // Adicione esta linha

if ($acao == 0) {
    $sql = "UPDATE banner SET ativo = 0 WHERE  id_banner = :idBanner";
} else {
    $sql = "UPDATE banner SET ativo = 1 WHERE  id_banner = :idBanner";
}
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idBanner', $idBanner);

if ($stmt->execute()) {
    header('Location: banners.php?a=banners&b=listar&flag=success&tip=Ação realizada com sucesso!');
    exit;
} else {
    header('Location: banners.php?a=banners&b=listar&flag=erro&tip=Não foi possível realizar a operação.');
    exit;
}
