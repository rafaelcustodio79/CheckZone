<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$idEmpresa = $_SESSION['empresa_id'];
$invoice_number = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : null;
$id_im_delivery = isset($_POST['id_im_delivery']) ? $_POST['id_im_delivery'] : null;

// Fazer a inserção no banco de dados
try {
    $PDO = db_connect();
    $stmt = $PDO->prepare("UPDATE invoice_maker 
                        SET id_im_delivery = :id_im_delivery 
                        WHERE id_empresa = :empresa_id AND invoice_number = :invoice_number");
    $stmt->bindParam(':id_im_delivery', $id_im_delivery);
    $stmt->bindParam(':empresa_id', $idEmpresa);
    $stmt->bindParam(':invoice_number', $invoice_number);
    $stmt->execute();

    // Atualizar a sessão com a última etapa completada
    $_SESSION['etapa_atual'] = 5;  // Indica que a etapa 1 foi completada

    // Retorna um JSON de sucesso
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
