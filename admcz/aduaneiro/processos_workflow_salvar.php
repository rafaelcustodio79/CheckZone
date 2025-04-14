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
    $id_aux_wf = $_POST['id_aux_wf'] ?? null;
    $data_acao = $_POST['data_acao'] ?? null;
    $numero_di = $_POST['numero_di'] ?? null;

        // Inserir um novo registro
        $stmt = $PDO->prepare("INSERT INTO processos_workflow (
            id_processo, id_empresa, id_usuario, id_aux_wf, data_acao
        ) VALUES (
            :idProcesso, :idEmpresa, :idUsuario, :id_aux_wf, :data_acao
        )");
    


    try {
        // Executar a consulta com os parâmetros
    $stmt->bindParam(':idProcesso', $idProcesso);
    $stmt->bindParam(':idEmpresa', $idEmpresa);
    $stmt->bindParam(':idUsuario', $idUsuario);
    $stmt->bindParam(':id_aux_wf', $id_aux_wf);
    $stmt->bindParam(':data_acao', $data_acao);

        // Execute the statement
        if ($stmt->execute()) {
            # Salvar a ratreabilidade
            $local = 'Histórico / Workflow';
            $sqlR = "INSERT INTO processos_rastreabilidade(id_processo, id_usuario, id_empresa, local, id_aux_wf) 
                VALUES (:id_processo, :id_usuario, :id_empresa, :local, :id_aux_wf)";
            // Preparar o statement
            $stmtR = $PDO->prepare($sqlR);

            // Bind dos parâmetros obrigatórios
            $stmtR->bindParam(':id_usuario', $idUsuario);
            $stmtR->bindParam(':id_empresa', $idEmpresa);
            $stmtR->bindParam(':id_processo', $idProcesso);
            $stmtR->bindParam(':local', $local);
            $stmtR->bindParam(':id_aux_wf', $id_aux_wf);

            // Executa a gravação da rastreabilidade
            $stmtR->execute();

            if ($id_aux_wf==8){
                $sqlUpdate = "UPDATE processos_informacoes SET presenca_carga = :presenca_carga WHERE id_processo = :idProcesso AND id_empresa = :idEmpresa";
                $stmtUpdate = $PDO->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':presenca_carga', $data_acao);
                $stmtUpdate->bindParam(':idProcesso', $idProcesso);
                $stmtUpdate->bindParam(':idEmpresa', $idEmpresa);
                $stmtUpdate->execute();
            }

            if ($id_aux_wf==10){
                $sqlUpdate = "UPDATE processos_informacoes SET n_di = :numero_di, registro_di = :data_acao WHERE id_processo = :idProcesso AND id_empresa = :idEmpresa";
                $stmtUpdate = $PDO->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':numero_di', $numero_di);
                $stmtUpdate->bindParam(':data_acao', $data_acao);
                $stmtUpdate->bindParam(':idProcesso', $idProcesso);
                $stmtUpdate->bindParam(':idEmpresa', $idEmpresa);
                $stmtUpdate->execute();
            }

            // Redirecionar para a página do processo
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=success&tip=Ação no Workflow inserido com sucesso!');
            exit;
        } else {
            header('Location: ../aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso='.$idProcesso.'&idEmpresa='.$idEmpresa.'&flag=erro&tip=Não foi possível inserir a ação.');
            exit;
        }
        
    } catch (PDOException $e) {
        // Error handling
        $errorMsg = urlencode($e->getMessage());
        header('Location: ../aduaneiro.php?a=aduaneiro&b=processos&flag=erro&tip='.$errorMsg);
        exit();
    }

}