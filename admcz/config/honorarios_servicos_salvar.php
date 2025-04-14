<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // pega os dados GERAIS do formuário
    $idHonorario = isset($_POST['idHonorario']) ? $_POST['idHonorario'] : null;
    $idEmpresa = isset($_POST['idEmpresa']) ? $_POST['idEmpresa'] : null;
    $servico = isset($_POST['servico']) ? $_POST['servico'] : null;
    $fc_tipo = isset($_POST['fc_tipo']) ? $_POST['fc_tipo'] : null;
    $base_calculo = isset($_POST['base_calculo']) ? $_POST['base_calculo'] : null;
    $fc_valor = isset($_POST['fc_valor']) ? $_POST['fc_valor'] : null;
    $valor_servico = isset($_POST['valor_servico']) ? format_to_number(clean_input($_POST['valor_servico'])) : null;

    if($base_calculo=='sal_minimo') {
        $valor_servico_novo = $valor_servico * $fc_valor;
    }else{
        $valor_servico_novo = $valor_servico;
    }


    try {
        // Início da query base
        $sql = "INSERT INTO honorarios_aux_servicos(id_honorario, id_empresa, servico, fc_tipo, fc_valor, valor_servico, base_calculo) 
                VALUES (:id_honorario, :id_empresa, :servico, :fc_tipo, :fc_valor, :valor_servico, :base_calculo)";

        // Preparar o statement
        $stmt = $PDO->prepare($sql);

        // Bind dos parâmetros obrigatórios
        $stmt->bindParam(':id_honorario', $idHonorario);
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->bindParam(':servico', $servico);
        $stmt->bindParam(':fc_tipo', $fc_tipo);
        $stmt->bindParam(':fc_valor', $fc_valor);
        $stmt->bindParam(':valor_servico', $valor_servico_novo);
        $stmt->bindParam(':base_calculo', $base_calculo);

        // Execute the statement
        if ($stmt->execute()) {
            header('Location: ../config.php?a=config&b=honorarios_criar&idEmpresa='.$idEmpresa.'&idHonorario='.$idHonorario.'&flag=success&tip=Serviço de honorário criado com sucesso!');
            exit;
        } else {
            header('Location: ../config.php?a=config&b=honorarios_criar&idEmpresa='.$idEmpresa.'&idHonorario='.$idHonorario.'&flag=erro&tip=Não foi possível cadastrar o serviço.');
            exit;
        }
    } catch (PDOException $e) {
        // Error handling
        $errorMsg = urlencode($e->getMessage());
        header('Location: ../config.php?a=config&b=honorarios&flag=erro&tip='.$errorMsg);
        exit();
    }

}