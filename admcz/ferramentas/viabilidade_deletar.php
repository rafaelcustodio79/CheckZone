<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

// Sanitiza o valor de idEstudo recebido via GET
$idEstudo = filter_input(INPUT_GET, 'idEstudo', FILTER_SANITIZE_NUMBER_INT);

if ($idEstudo === null) {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_listar&flag=erro&tip=ID do estudo não fornecido.');
    exit;
}

// insere no banco Proposta da cotaçao
try {
    $PDO = db_connect();
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Iniciar transação
    $PDO->beginTransaction();
    
    // Deletar registros da tabela principal
    $sql = "DELETE FROM planilha_viabilidade WHERE id_pv = :idEstudo";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':idEstudo', $idEstudo, PDO::PARAM_INT);
    $stmt->execute();
    
    // Deletar registros da tabela relacionada
    $sqlAdicoes = "DELETE FROM planilha_viabilidade_adicoes WHERE id_pv = :idEstudo";
    $stmtAdicoes = $PDO->prepare($sqlAdicoes);
    $stmtAdicoes->bindParam(':idEstudo', $idEstudo, PDO::PARAM_INT);
    $stmtAdicoes->execute();
    
    // Commit transação
    $PDO->commit();
    
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_listar&flag=success&tip=Estudo de viabilidade excluído com sucesso!');
} catch (PDOException $e) {
    // Rollback transação em caso de erro
    $PDO->rollBack();
    echo "Erro ao gravar dados: " . $e->getMessage();
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_listar&flag=erro&tip=Não foi possível realizar essa ação.');
}
?>