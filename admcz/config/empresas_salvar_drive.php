<?php
// Inicia sessões e importa arquivos necessários
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pega os dados do formulário
    $id_empresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : null;
    $ano = isset($_POST['ano']) ? $_POST['ano'] : null;
    $id_pasta_drive = isset($_POST['id_pasta_drive']) ? $_POST['id_pasta_drive'] : null;

    // Valida os dados recebidos
    if (!$id_empresa || !$ano || !$id_pasta_drive) {
        header('Location: ../config.php?a=config&b=empresas&flag=erro&tip=Dados inválidos ou incompletos.');
        exit();
    }

    try {
        // Insere os dados no banco
        $sql = "INSERT INTO pasta_drive (id_empresa, ano, id_pasta_drive) 
                VALUES (:id_empresa, :ano, :id_pasta_drive)";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':id_empresa', $id_empresa);
        $stmt->bindParam(':ano', $ano);
        $stmt->bindParam(':id_pasta_drive', $id_pasta_drive);

        if ($stmt->execute()) {
            header('Location: ../config.php?a=config&b=empresas&flag=success&tip=ID da pasta cadastrado com sucesso!');
            exit();
        } else {
            header('Location: ../config.php?a=config&b=empresas&flag=erro&tip=Não foi possível realizar a operação.');
            exit();
        }
    } catch (PDOException $e) {
        // Registra o erro e exibe mensagem genérica ao usuário
        error_log($e->getMessage(), 3, '/var/log/app_errors.log');
        header('Location: ../config.php?a=config&b=empresas&flag=erro&tip=Ocorreu um erro ao cadastrar a pasta.');
        exit();
    }
}