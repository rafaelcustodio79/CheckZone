<?php

$idEmpresa = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$idEmpresa) {
    header('Location: config.php?a=config&b=empresas&flag=erro&tip=ID da empresa inválido.');
    exit;
}

$PDO = db_connect();

try {
    // Início da query base
    $sql = "INSERT INTO honorarios(id_empresa) 
            VALUES (:id_empresa)";

    // Preparar o statement
    $stmt = $PDO->prepare($sql);

    // Bind dos parâmetros obrigatórios
    $stmt->bindParam(':id_empresa', $idEmpresa);

    // Execute the statement
    if ($stmt->execute()) {
        $idHonorario = $PDO->lastInsertId();
        header('Location: config.php?a=config&b=honorarios_criar&idEmpresa='.$idEmpresa.'&idHonorario='.$idHonorario.'&flag=success&tip=Abertura de honorários gerado com sucesso!');
        exit;
    } else {
        header('Location: config.php?a=config&b=honorarios&flag=erro&tip=Não foi possível cadastrar a empresa.');
        exit;
    }
} catch (PDOException $e) {
    // Error handling
    $errorMsg = urlencode($e->getMessage());
    header('Location: config.php?a=config&b=honorarios&flag=erro&tip='.$errorMsg);
    exit();
}
