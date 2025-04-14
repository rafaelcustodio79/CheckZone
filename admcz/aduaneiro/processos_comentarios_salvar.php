<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter dados do formulário e proteger contra injeção SQL
    $idUsuario = $_POST['idUsuario'] ?? null;
    $idProcesso = $_POST['idProcesso'] ?? null;
    $idEmpresa = $_POST['idEmpresa'] ?? null;
    $comentario = $_POST['comentario'] ?? null;

        // Inserir um novo registro
        $stmt = $PDO->prepare("INSERT INTO processos_comentarios (
            id_processo, id_empresa, comentario, id_usuario
        ) VALUES (
            :idProcesso, :idEmpresa, :comentario, :id_usuario
        )");
    


    try {
        // Executar a consulta com os parâmetros
    $stmt->bindParam(':idProcesso', $idProcesso);
    $stmt->bindParam(':idEmpresa', $idEmpresa);
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':id_usuario', $idUsuario);

        // Execute the statement
        if ($stmt->execute()) {
            # Salvar a ratreabilidade
            $local = 'Comentário';
            $sqlR = "INSERT INTO processos_rastreabilidade(id_processo, id_usuario, id_empresa, local) 
                VALUES (:id_processo, :id_usuario, :id_empresa, :local)";
            // Preparar o statement
            $stmtR = $PDO->prepare($sqlR);

            // Bind dos parâmetros obrigatórios
            $stmtR->bindParam(':id_usuario', $idUsuario);
            $stmtR->bindParam(':id_empresa', $idEmpresa);
            $stmtR->bindParam(':id_processo', $idProcesso);
            $stmtR->bindParam(':local', $local);

            // Executa a gravação da rastreabilidade
            $stmtR->execute();


            // Redirecionar para a página do processo
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=success&tip=Comentário inserido com sucesso!');
            exit;
        } else {
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=erro&tip=Não foi possível inserir o comentário.');
            exit;
        }
        
    } catch (PDOException $e) {
        // Error handling
        $errorMsg = urlencode($e->getMessage());
        header('Location: ../aduaneiro.php?a=aduaneiro&b=processos&flag=erro&tip='.$errorMsg);
        exit();
    }

}