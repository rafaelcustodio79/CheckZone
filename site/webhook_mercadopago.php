<?php
// Include globals.php which already has DB PDO connection functions and utilities
require_once 'functions/globals.php';

// Incluir SDK do Mercado Pago
require_once 'vendor/autoload.php';

// Configurar Mercado Pago SDK
MercadoPago\SDK::setAccessToken(MP_ACCESS_TOKEN);

$PDO = db_connect();

// Receber a notificação do webhook via POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Registrar a notificação recebida para debugging
error_log("Webhook MP recebido: " . print_r($data, true));

// Verificar se é uma notificação válida
if (!$data || !isset($data['type'])) {
    error_log("Webhook inválido recebido");
    http_response_code(400);
    exit;
}

// Processar apenas notificações de pagamento
if ($data['type'] === 'payment') {
    $payment_id = $data['data']['id'];
    
    try {
        // Obter detalhes do pagamento
        $payment = MercadoPago\Payment::find_by_id($payment_id);
        
        if (!$payment) {
            error_log("Pagamento não encontrado: {$payment_id}");
            http_response_code(404);
            exit;
        }
        
        // Extrair informações do pagamento
        $status = $payment->status;
        $valor = $payment->transaction_amount;
        $external_reference = $payment->external_reference; // Seu subscription_id
        
        // Mapeamento de status do Mercado Pago para o banco de dados
        $statusMap = [
            'approved' => 'aprovado',
            'pending' => 'pendente',
            'in_process' => 'pendente',
            'rejected' => 'falha',
            'refunded' => 'cancelado',
            'cancelled' => 'cancelado',
            'in_mediation' => 'pendente'
        ];
        
        // Converter status do Mercado Pago para o formato do banco
        $dbStatus = $statusMap[$status] ?? 'pendente';
        
        // Buscar a assinatura usando external_reference
        $stmt = $PDO->prepare("SELECT * FROM subscriptions WHERE id = :id");
        $stmt->bindParam(':id', $external_reference);
        $stmt->execute();
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$subscription) {
            error_log("Assinatura não encontrada: {$external_reference}");
            http_response_code(404);
            exit;
        }
        
        // Registrar o pagamento
        recordPayment($subscription['id'], $payment_id, $valor, $dbStatus);
        
        // Atualizar a assinatura se o pagamento foi aprovado
        if ($status === 'approved') {
            // Obter informações do plano
            $plan = getPlanById($subscription['plan_id']);
            $interval = ($plan['recorrencia'] === 'mensal') ? '1 month' : '1 year';
            
            // Atualizar status da assinatura para 'ativo'
            $stmt = $PDO->prepare("
                UPDATE subscriptions SET 
                status = 'ativo',
                data_fim = CASE 
                    WHEN data_fim > NOW() THEN DATE_ADD(data_fim, INTERVAL {$interval})
                    ELSE DATE_ADD(NOW(), INTERVAL {$interval})
                END
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $subscription['id']);
            $stmt->execute();
            
            error_log("Assinatura {$subscription['id']} ativada/renovada com sucesso. Payment ID: {$payment_id}, Valor: {$valor}");
        } else if ($status === 'rejected' || $status === 'cancelled' || $status === 'refunded') {
            // Atualizar status da assinatura para refletir falha no pagamento
            $stmt = $PDO->prepare("UPDATE subscriptions SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $dbStatus);
            $stmt->bindParam(':id', $subscription['id']);
            $stmt->execute();
            
            error_log("Pagamento falhou para assinatura {$subscription['id']} com status: {$dbStatus}");
        } else {
            // Status pendente ou em processamento
            error_log("Pagamento pendente para assinatura {$subscription['id']}: Status {$dbStatus}");
        }
        
        // Responder com sucesso
        http_response_code(200);
        exit;
        
    } catch (Exception $e) {
        error_log("Erro ao processar pagamento: " . $e->getMessage());
        http_response_code(500);
        exit;
    }
} else {
    // Outros tipos de notificação (não processados)
    error_log("Tipo de notificação não processado: " . $data['type']);
    http_response_code(200); // Aceitar, mas não processar
    exit;
}