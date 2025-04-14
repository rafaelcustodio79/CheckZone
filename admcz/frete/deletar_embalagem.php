<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

// Sanitiza o valor de idIM recebido via GET
$idCotacao = filter_input(INPUT_GET, 'idCotacao', FILTER_SANITIZE_NUMBER_INT);
$idEmbalagem = filter_input(INPUT_GET, 'idEmbalagem', FILTER_SANITIZE_NUMBER_INT);

if ($idCotacao === null || $idEmbalagem === null) {
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Dados incompletos.');
    exit;
}

// insere no banco Proposta da cotaçao
try {
    $PDO = db_connect();
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Iniciar transação
    $PDO->beginTransaction();
    
    // Deletar registros da tabela principal
    $sql = "DELETE FROM cotacao_carga WHERE id = :idEmbalagem AND id_cotacao = :idCotacao";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':idEmbalagem', $idEmbalagem, PDO::PARAM_INT);
    $stmt->bindParam(':idCotacao', $idCotacao, PDO::PARAM_INT);
    $stmt->execute();
    
    // Commit transação
    $PDO->commit();
    
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Embalagem excluída com sucesso!');
} catch (PDOException $e) {
    // Rollback transação em caso de erro
    $PDO->rollBack();
    echo "Erro ao gravar dados: " . $e->getMessage();
    header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=erro&tip=Não foi possível realizar essa ação. ' . $e->getMessage());
}