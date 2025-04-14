<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

// Sanitiza o parâmetro 'id'
// Sanitiza o valor de idIM recebido via GET
$idProd = filter_input(INPUT_GET, 'idProd', FILTER_SANITIZE_NUMBER_INT);
$idIM = filter_input(INPUT_GET, 'idIM', FILTER_SANITIZE_NUMBER_INT);
$invoice = filter_input(INPUT_GET, 'invoice', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($idIM === null || $invoice === null) {
    header('Location: ../ferramentas.php?a=ferramentas&b=invoice_maker_listar&flag=erro&tip=ID da Invoice não fornecida.');
    exit;
}

if ($idProd && $idIM && $invoice) {
    $PDO = db_connect();
    $sql = "DELETE FROM invoice_maker_goods WHERE id_im_goods = :idProd AND id_im = :idIM AND invoice_number = :invoice";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':idIM', $idIM, PDO::PARAM_INT);
    $stmt->bindParam(':invoice', $invoice, PDO::PARAM_STR);
    $stmt->bindParam(':idProd', $idProd, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['active_step'] = 4;
        header('Location: ../ferramentas.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice.'&flag=success&tip=Produto excluído com sucesso!&tabAtiva=2');
        exit();
    } else {
        header('Location: ../ferramentas.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice.'flag=erro&tip=Nenhuma informação foi encontrada. Reinicie o processo!');
        exit();
    }
} else {
    header('Location: ../ferramentas.php?a=ferramentas&binvoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice.'&flag=erro&tip=IDs inválido.');
    exit();
}