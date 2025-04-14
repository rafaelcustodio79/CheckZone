<?php
session_start();

// inclui o arquivo de inicialização
require '../../functions/globals.php';

$idEmpresa = $_SESSION['empresa_id'];
$idUsuario = $_SESSION['user_id'];
$idIM = isset($_POST['idIM']) ? $_POST['idIM'] : null;
$invoice_number = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : null;
$currency = isset($_POST['currency']) ? $_POST['currency'] : null;
$reason_for_export = isset($_POST['reason_for_export']) ? $_POST['reason_for_export'] : null;
$incoterms = isset($_POST['incoterms']) ? $_POST['incoterms'] : null;
$terms_payment = isset($_POST['terms_payment']) ? $_POST['terms_payment'] : null;
$country_origin = isset($_POST['country_origin']) ? $_POST['country_origin'] : null;
$shipping_cost = isset($_POST['shipping_cost']) ? format_to_number(clean_input($_POST['shipping_cost'])) : null;
$insurance = isset($_POST['insurance']) ? format_to_number(clean_input($_POST['insurance'])) : null;
$others_expanses = isset($_POST['others_expanses']) ? format_to_number(clean_input($_POST['others_expanses'])) : null;

$idIM = isset($_POST['idIM']) ? $_POST['idIM'] : null; // ID do registro a ser atualizado

if ($idIM) {
    // Fazer o update no banco de dados
    try {
        $PDO = db_connect();
        $stmt = $PDO->prepare("UPDATE invoice_maker 
                               SET invoice_number = :invoice_number, 
                                   currency = :currency, 
                                   reason_for_export = :reason_for_export, 
                                   incoterms = :incoterms, 
                                   terms_payment = :terms_payment, 
                                   country_origin = :country_origin, 
                                   shipping_cost = :shipping_cost, 
                                   insurance = :insurance,
                                   others_expanses = :others_expanses
                               WHERE id_im = :idIM AND id_empresa = :empresa_id AND id_usuario = :usuario_id");
        
        $stmt->bindParam(':empresa_id', $idEmpresa);
        $stmt->bindParam(':usuario_id', $idUsuario);
        $stmt->bindParam(':invoice_number', $invoice_number);
        $stmt->bindParam(':currency', $currency);
        $stmt->bindParam(':reason_for_export', $reason_for_export);
        $stmt->bindParam(':incoterms', $incoterms);
        $stmt->bindParam(':terms_payment', $terms_payment);
        $stmt->bindParam(':country_origin', $country_origin);
        $stmt->bindParam(':shipping_cost', $shipping_cost);
        $stmt->bindParam(':insurance', $insurance);
        $stmt->bindParam(':others_expanses', $others_expanses);
        $stmt->bindParam(':idIM', $idIM); // Parâmetro para o ID do registro a ser atualizado
        
        $stmt->execute();

        // Redirecionamento com mensagem de sucesso
        $_SESSION['active_step'] = 1;
        header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice_number.'&flag=success&tip=Dados da Invoice atualizados com sucesso!&tabAtiva=2');
        exit();
    } catch (PDOException $e) {
        // Redirecionamento em caso de erro
        header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice_number.'&flag=erro&tip=Não foi possível realizar a operação. '.$e->getMessage());
    }
} else {
    // Lidar com o caso de não ter o ID do registro para atualizar
    header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&flag=erro&tip=ID do registro não encontrado.');
    exit();
}