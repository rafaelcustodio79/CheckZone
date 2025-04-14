<?php

session_start();

// inclui o arquivo de inicialização
require '../../functions/globals.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $idEmpresa = $_POST['id_empresa'];
    $invoice_number = $_POST['invoice_number'];
    $idIM = $_POST['idIM'];
    $beneficiary_name = $_POST['beneficiary_name'];
    $beneficiary_an = $_POST['beneficiary_an'];
    $beneficiary_address = $_POST['beneficiary_address'];
    $swift_code = $_POST['swift_code'];
    $beneficiary_bank = $_POST['beneficiary_bank'];
    $remark = $_POST['remark'];

    try {
        // Conexão com o banco de dados
        $PDO = db_connect();
        $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara a query de inserção
        $sql = "INSERT INTO invoice_maker_bank (id_im, id_empresa, invoice_number, beneficiary_name, beneficiary_an, beneficiary_address, swift_code, beneficiary_bank, remark) 
                VALUES (:id_im, :id_empresa, :invoice_number, :beneficiary_name, :beneficiary_an, :beneficiary_address, :swift_code, :beneficiary_bank, :remark)";
        
        $stmt = $PDO->prepare($sql);
        
        // Vincula os parâmetros
        $stmt->bindParam(':id_im', $idIM);
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->bindParam(':invoice_number', $invoice_number);
        $stmt->bindParam(':beneficiary_name', $beneficiary_name);
        $stmt->bindParam(':beneficiary_an', $beneficiary_an);
        $stmt->bindParam(':beneficiary_address', $beneficiary_address);
        $stmt->bindParam(':swift_code', $swift_code);
        $stmt->bindParam(':beneficiary_bank', $beneficiary_bank);
        $stmt->bindParam(':remark', $remark);
        
        // Executa a query
        if ($stmt->execute()) {
            header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_gerar&flag=success&tip=Todos os passos salvos com sucesso!&invoice='.$invoice_number.'&idIM='.$idIM);
            exit();
        } else {
            header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_criar&flag=erro&tip=Não foi possível realizar a operação.');
            exit();
        }

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}