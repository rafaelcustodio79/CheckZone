<?php
session_start();

// inclui o arquivo de inicialização
require '../../functions/globals.php';

$idEmpresa = $_SESSION['empresa_id'];
$invoice_number = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : null;
$beneficiary_name = $_POST['beneficiary_name'];
$beneficiary_an = $_POST['beneficiary_an'];
$beneficiary_address = $_POST['beneficiary_address'];
$swift_code = $_POST['swift_code'];
$beneficiary_bank = $_POST['beneficiary_bank'];
$remark = $_POST['remark'];

$idIM = isset($_POST['idIM']) ? $_POST['idIM'] : null; // ID do registro a ser atualizado

if ($idIM) {
    // Fazer o update no banco de dados
    try {
        $PDO = db_connect();
        $stmt = $PDO->prepare("UPDATE invoice_maker_bank SET beneficiary_name = :beneficiary_name, beneficiary_an = :beneficiary_an, beneficiary_address = :beneficiary_address, swift_code = :swift_code, beneficiary_bank = :beneficiary_bank, remark = :remark 
        WHERE id_im = :idIM AND id_empresa = :empresa_id AND invoice_number = :invoice_number");

        // Bind dos parâmetros
        $stmt->bindParam(':empresa_id', $idEmpresa);
        $stmt->bindParam(':invoice_number', $invoice_number);
        $stmt->bindParam(':beneficiary_name', $beneficiary_name);
        $stmt->bindParam(':beneficiary_an', $beneficiary_an);
        $stmt->bindParam(':beneficiary_address', $beneficiary_address);
        $stmt->bindParam(':swift_code', $swift_code);
        $stmt->bindParam(':beneficiary_bank', $beneficiary_bank);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':idIM', $idIM);

        $stmt->execute();

        // Redirecionamento com sucesso
        $_SESSION['active_step'] = 5;
        ob_start(); // Iniciar buffer de saída
        header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.urlencode($idIM).'&invoice='.urlencode($invoice_number).'&flag=success&tip=Dados do Bank atualizados com sucesso!');
        ob_end_flush(); // Limpar e enviar o buffer
        exit();
    } catch (PDOException $e) {
        // Redirecionamento em caso de erro
        ob_start();
        header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.urlencode($idIM).'&invoice='.urlencode($invoice_number).'&flag=erro&tip=Não foi possível realizar a operação. '.urlencode($e->getMessage()));
        ob_end_flush();
        exit();
    }
} else {
    // Caso não haja ID para atualização
    header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&flag=erro&tip=ID do registro não encontrado.');
    exit();
}