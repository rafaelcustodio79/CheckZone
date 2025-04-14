<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$idEstudo = isset($_POST['id_estudo']) ? $_POST['id_estudo'] : null;
$valor_mercadoria = isset($_POST['valor_mercadoria']) ? $_POST['valor_mercadoria'] : null;
$valor_sem_ponto = str_replace(".", "", $valor_mercadoria);
$valor_mercadoria = str_replace(",", ".", $valor_sem_ponto);
if ($valor_mercadoria == null) {
    $valor_mercadoria = 0.00;
} else {
    $valor_mercadoria = (float) $valor_mercadoria;
}
$peso_liquido = isset($_POST['peso_liquido']) ? $_POST['peso_liquido'] : null;
$peso_bruto = isset($_POST['peso_bruto']) ? $_POST['peso_bruto'] : null;
$qt_produtos = isset($_POST['qt_produtos']) ? $_POST['qt_produtos'] : null;
$cbm = isset($_POST['cbm']) ? $_POST['cbm'] : null;


if ($idEstudo) {
    // Atualiza com as informações iniciais
    $sql = "UPDATE planilha_viabilidade
            SET valor_mercadoria = :valor_mercadoria,
                peso_liquido = :peso_liquido,
                peso_bruto = :peso_bruto,
                qt_produtos = :qt_produtos,
                cbm = :cbm
            WHERE id_pv = :id_estudo";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':valor_mercadoria', $valor_mercadoria);
    $stmt->bindParam(':peso_liquido', $peso_liquido);
    $stmt->bindParam(':peso_bruto', $peso_bruto);
    $stmt->bindParam(':qt_produtos', $qt_produtos);
    $stmt->bindParam(':cbm', $cbm);
    $stmt->bindParam(':id_estudo', $idEstudo);

    if ($stmt->execute()) {
        unset($_SESSION['passo']); // Remove a variável de sessão 'passo'
        $_SESSION['passo'] = 4; // Atribui um novo valor a 'passo'
        unset($_SESSION['editar_3']); // Remove a variável de sessão 'passo'
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&&visualizar=1&idEstudo='.$idEstudo.'&flag=success&tip=Informações da mercadoria gravadas com sucesso!');
    } else {
        header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&&flag=erro&tip=Não foi possível realizar a operação.');
    }
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=viabilidade_criar&&flag=erro&tip=Nenhuma informação foi encontrada. Reinicie o processo!');
}