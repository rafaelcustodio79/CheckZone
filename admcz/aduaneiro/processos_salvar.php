<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // pega os dados GERAIS do formuário
    $idCliente = isset($_POST['idCliente']) ? $_POST['idCliente'] : null;
    $ref_cliente = isset($_POST['ref_cliente']) ? $_POST['ref_cliente'] : null;
    $ref_calvet = isset($_POST['ref_calvet']) ? $_POST['ref_calvet'] : null;
    $idTipoProcesso = isset($_POST['idTipoProcesso']) ? $_POST['idTipoProcesso'] : null;
    $idUsuario = isset($_POST['idUsuario']) ? $_POST['idUsuario'] : null;
    $statusProcesso = 'Em aberto';
    //$data_add = date('Y-m-d');

    try {
        // Início da query base
        $sql = "INSERT INTO processos(id_usuario, id_empresa, ref_cliente, ref_calvet, id_tipo_processo, status_processo) 
                VALUES (:id_usuario, :id_empresa, :ref_cliente, :ref_calvet, :id_tipo_processo, :status_processo)";

        // Preparar o statement
        $stmt = $PDO->prepare($sql);

        // Bind dos parâmetros obrigatórios
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':id_empresa', $idCliente);
        $stmt->bindParam(':ref_cliente', $ref_cliente);
        $stmt->bindParam(':ref_calvet', $ref_calvet);
        $stmt->bindParam(':id_tipo_processo', $idTipoProcesso);
        $stmt->bindParam(':status_processo', $statusProcesso);

        // Execute the statement
        if ($stmt->execute()) {
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos&flag=success&tip=Processo aberto com sucesso!');
            exit;
        } else {
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos&flag=erro&tip=Não foi possível cadastrar o processo.');
            exit;
        }
        
    } catch (PDOException $e) {
        // Error handling
        $errorMsg = urlencode($e->getMessage());
        header('Location: ../aduaneiro.php?a=aduaneiro&b=processos&flag=erro&tip='.$errorMsg);
        exit();
    }

}
