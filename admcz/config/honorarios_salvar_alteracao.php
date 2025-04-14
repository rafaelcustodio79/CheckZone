<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pega os dados do formulário
    $idHonorario = isset($_POST['idHonorario']) ? $_POST['idHonorario'] : null;
    $idEmpresa = isset($_POST['idEmpresa']) ? $_POST['idEmpresa'] : null;
    $valor_honorario = isset($_POST['valor_honorario']) ? format_to_number(clean_input($_POST['valor_honorario'])) : null;
    $percent_iss = isset($_POST['percent_iss']) ? $_POST['percent_iss'] : null;
    
    try {
        // Início da query base
        $sql = "UPDATE honorarios
        SET valor_honorarios = :valor_honorario, iss = :iss";

        // Finaliza a query com o WHERE correto
        $sql .= " WHERE id_empresa = :idEmpresa AND id_honorario = :idHonorario";

        // Prepara a declaração
        $stmt = $PDO->prepare($sql);

        // Bind dos parâmetros
        $stmt->bindParam(':valor_honorario', $valor_honorario);
        $stmt->bindParam(':idEmpresa', $idEmpresa);
        $stmt->bindParam(':idHonorario', $idHonorario);
        $stmt->bindParam(':iss', $percent_iss);

        // Executa a instrução SQL
        if ($stmt->execute()) {
            header('Location: ../config.php?a=config&b=honorarios_criar&idHonorario='.$idHonorario.'&idEmpresa='.$idEmpresa.'&flag=success&tip=Honorário alterado com sucesso!');
            exit;
        } else {
            header('Location: ../config.php?a=config&b=honorarios_criar&idHonorario='.$idHonorario.'&idEmpresa='.$idEmpresa.'&flag=erro&tip=Não foi possível editar a empresa.');
            exit;
        }
    } catch (PDOException $e) {
        // Trata o erro
        $errorMsg = urlencode($e->getMessage());
        header('Location: ../config.php?a=config&b=honorarios_criar&idHonorario='.$idHonorario.'&idEmpresa='.$idEmpresa.'&flag=erro&tip='.$errorMsg);
        exit;
    }
}