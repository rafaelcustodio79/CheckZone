<?php
// Inicia sessões
session_start();
require '../functions/globals.php';
require '../functions/verifica-log.php';

$PDO = db_connect();

// Verifica se o usuário está logado e captura seu ID
$idUsuario = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtém os parâmetros da URL
    $idProcesso = $_GET['idProcesso'] ?? null;
    $idEmpresa = $_GET['idEmpresa'] ?? null;
    $idAuxWf = $_GET['id_aux_wf'] ?? null;
    $idPr = $_GET['idPr'] ?? null;

    // Verifica se os parâmetros necessários estão presentes
    if (!empty($idProcesso) && !empty($idEmpresa) && !empty($idAuxWf) && !empty($idPr)) {
        try {
            // Deleta da tabela processos_workflow
            $sql = "DELETE FROM processos_workflow WHERE id_processo = :idProcesso AND id_empresa = :idEmpresa AND id_aux_wf = :idAuxWf";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
            $stmt->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':idAuxWf', $idAuxWf, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Deleta da tabela processos_rastreabilidade
                $sqlR = "DELETE FROM processos_rastreabilidade WHERE id_pr = :idPr";
                $stmtR = $PDO->prepare($sqlR);
                $stmtR->bindParam(':idPr', $idPr, PDO::PARAM_INT);
                $stmtR->execute();

                // Inserir informação sobre a exclusão na rastreabilidade
                $local = 'Removeu item do Workflow';
                $sqlR2 = "INSERT INTO processos_rastreabilidade(id_processo, id_usuario, id_empresa, local) 
                          VALUES (:id_processo, :id_usuario, :id_empresa, :local)";
                $stmtR2 = $PDO->prepare($sqlR2);
                $stmtR2->bindParam(':id_processo', $idProcesso);
                $stmtR2->bindParam(':id_usuario', $idUsuario);
                $stmtR2->bindParam(':id_empresa', $idEmpresa);
                $stmtR2->bindParam(':local', $local);

                $stmtR2->execute();

                // Redireciona em caso de sucesso
                header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=success&tip=Item excluído do Workflow com sucesso!');
                exit;
            } else {
                // Redireciona em caso de falha na execução
                header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=erro&tip=Não foi possível excluir o item.');
                exit;
            }

        } catch (PDOException $e) {
            // Tratamento de erro
            $errorMsg = urlencode($e->getMessage());
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=erro&tip='.$errorMsg);
            exit();
        }
    } else {
        // Redireciona em caso de parâmetros faltando
        header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=erro&tip=Parâmetros inválidos.');
        exit();
    }
} else {
    // Redireciona caso o método não seja GET
    header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=erro&tip=Requisição inválida.');
    exit();
}