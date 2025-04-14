<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

// Sanitiza o parâmetro 'id'
$idProduto = filter_input(INPUT_GET, 'idProd', FILTER_SANITIZE_NUMBER_INT);
$idPV = filter_input(INPUT_GET, 'idPV', FILTER_SANITIZE_NUMBER_INT);

if ($idProduto && $idPV) {
    $PDO = db_connect();
    $sql = "DELETE FROM planilha_viabilidade_adicoes WHERE id_pv = :idPV AND id = :idProduto";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':idPV', $idPV, PDO::PARAM_INT);
    $stmt->bindParam(':idProduto', $idProduto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&&visualizar=1&idEstudo='.$idPV.'&flag=success&tip=Produto excluído com sucesso!&tabAtiva=2');
        exit();
    } else {
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&&flag=erro&tip=Nenhuma informação foi encontrada. Reinicie o processo!');
        exit();
    }
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&&visualizar=1&idEstudo='.$idPV.'&flag=erro&tip=IDs inválido.');
    exit();
}