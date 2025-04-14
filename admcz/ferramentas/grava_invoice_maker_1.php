<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idEmpresa = $_SESSION['empresa_id'];
$idUsuario = $_SESSION['user_id'];
$invoice_number = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : null;
$currency = isset($_POST['currency']) ? $_POST['currency'] : null;
$reason_for_export = isset($_POST['reason_for_export']) ? $_POST['reason_for_export'] : null;
$incoterms = isset($_POST['incoterms']) ? $_POST['incoterms'] : null;
$terms_payment = isset($_POST['terms_payment']) ? $_POST['terms_payment'] : null;
$country_origin = isset($_POST['country_origin']) ? $_POST['country_origin'] : null;
$shipping_cost = isset($_POST['shipping_cost']) ? format_to_number(clean_input($_POST['shipping_cost'])) : null;
$insurance = isset($_POST['insurance']) ? format_to_number(clean_input($_POST['insurance'])) : null;
$others_expanses = isset($_POST['others_expanses']) ? format_to_number(clean_input($_POST['others_expanses'])) : null;

// Fazer a inserção no banco de dados
try {
    $PDO = db_connect();
    $stmt = $PDO->prepare("INSERT INTO invoice_maker (id_empresa, id_usuario, invoice_number, currency, reason_for_export, incoterms, terms_payment, country_origin, shipping_cost, insurance, others_expanses) 
                            VALUES (:empresa_id, :usuario_id, :invoice_number, :currency, :reason_for_export, :incoterms, :terms_payment, :country_origin, :shipping_cost, :insurance, :others_expanses)");
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
    $stmt->execute();

    // Atualizar a sessão com a última etapa completada
    unset($_SESSION['etapa_atual']); // Remove a variável de sessão
    $_SESSION['etapa_atual'] = 2;  // Indica que a etapa 1 foi completada

    // Cria a sessão com o número da INVOICE
    $_SESSION['invoice_number'] = $invoice_number;

    // Retorna um JSON de sucesso
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Verifica se o erro é uma violação de chave única (invoice_number duplicado)
    if ($e->getCode() == 23000) { // Código de erro SQL para violação de chave única
        // Verifica se a mensagem do erro contém referência ao campo 'invoice_number'
        if (strpos($e->getMessage(), 'invoice_number') !== false) {
            $errorMsg = 'O número da invoice já existe. Por favor, use outro número.';
        } else {
            $errorMsg = 'Erro ao tentar inserir os dados. Por favor, tente novamente.';
        }
    } else {
        // Mensagem padrão para outros erros
        $errorMsg = 'Ocorreu um erro: ' . $e->getMessage();
    }
    // Retorna a resposta como JSON com a mensagem de erro em português
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}