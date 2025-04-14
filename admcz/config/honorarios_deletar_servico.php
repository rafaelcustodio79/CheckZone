<?php
$idServico = filter_var($_GET['idServico'], FILTER_SANITIZE_NUMBER_INT);
$idEmpresa = filter_var($_GET['idEmpresa'], FILTER_SANITIZE_NUMBER_INT);
$idHonorario = filter_var($_GET['idHonorario'], FILTER_SANITIZE_NUMBER_INT);


if (!is_numeric($idServico)||!is_numeric($idEmpresa)||!is_numeric($idEmpresa)) {
    header('Location: config.php?a=configs&b=honorarios&flag=erro&tip=Não foi possível realizar a operação.');
    exit();
}

$PDO = db_connect();

// Remove do banco
$sqlDelete = "DELETE FROM honorarios_aux_servicos WHERE id_servico = :idServico AND id_honorario = :idHonorario AND id_empresa = :idEmpresa";
$stmtDelete = $PDO->prepare($sqlDelete);
$stmtDelete->bindParam(':idServico', $idServico, PDO::PARAM_INT);
$stmtDelete->bindParam(':idHonorario', $idHonorario, PDO::PARAM_INT);
$stmtDelete->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);

try {
    $stmtDelete->execute();

    header('Location: comexmanager/config.php?a=config&b=honorarios_criar&idEmpresa='.$idEmpresa.'&idHonorario='.$idHonorario.'&flag=success&tip=Serviço de honorário deletado com sucesso!');
    exit();
} catch (PDOException $e) {
    // Captura exceções do banco de dados
    header('Location: comexmanager/config.php?a=config&b=honorarios_criar&idEmpresa='.$idEmpresa.'&idHonorario='.$idHonorario.'&flag=erro&tip=Não foi possível realizar a operação: '. $e->getMessage());
    exit();
}