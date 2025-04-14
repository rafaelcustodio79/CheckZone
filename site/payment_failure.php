<?php
// Include globals.php which already has DB PDO connection functions and utilities
require_once 'functions/globals.php';

// Inicia a sessão para garantir que as mensagens sejam mantidas
session_start();

// Incluir SDK do Mercado Pago
require_once 'vendor/autoload.php';

// Configurar Mercado Pago SDK
MercadoPago\SDK::setAccessToken(MP_ACCESS_TOKEN);

$PDO = db_connect();

// Captura os parâmetros da URL
$collection_id = $_GET['collection_id'] ?? '';
$collection_status = $_GET['collection_status'] ?? '';
$payment_id = $_GET['payment_id'] ?? '';
$status = $_GET['status'] ?? '';
$external_reference = $_GET['external_reference'] ?? '';
$payment_type = $_GET['payment_type'] ?? '';
$merchant_order_id = $_GET['merchant_order_id'] ?? '';
$preference_id = $_GET['preference_id'] ?? '';
$site_id = $_GET['site_id'] ?? '';
$processing_mode = $_GET['processing_mode'] ?? '';
$merchant_account_id = $_GET['merchant_account_id'] ?? '';

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
$dbStatus = $statusMap[$status] ?? 'falha';

// Se não há status específico, considerar como falha
if (empty($status)) {
    $dbStatus = 'falha';
    $status = 'rejected'; // valor default para exibição
}

// Obter detalhes da assinatura
$subscription = null;
$plan = null;
$errorMessage = '';
$errorCode = '';

try {
    // Buscar a assinatura pelo external_reference (que é o ID da assinatura)
    if (!empty($external_reference)) {
        $subscription = getSubscriptionById($external_reference);
    } else if (!empty($preference_id)) {
        // Buscar por preference_id se external_reference não estiver disponível
        $stmt = $PDO->prepare("SELECT * FROM subscriptions WHERE mercado_pago_id = :mercado_pago_id");
        $stmt->bindParam(':mercado_pago_id', $preference_id);
        $stmt->execute();
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($subscription) {
    
        if (!empty($subscription['plan_id'])) {
            $plan = getPlanById($subscription['plan_id']);
        }
        
        // Registrar a tentativa de pagamento com falha
        if (!empty($payment_id)) {
            // Tentar obter o valor, se disponível
            $valor = 0;
            try {
                $payment = MercadoPago\Payment::find_by_id($payment_id);
                if ($payment) {
                    $valor = $payment->transaction_amount;
                    $errorCode = $payment->status_detail;
                    
                    // Determinar a mensagem de erro baseada no código de erro
                    switch($errorCode) {
                        case 'cc_rejected_bad_filled_date':
                            $errorMessage = 'Data de validade incorreta';
                            break;
                        case 'cc_rejected_bad_filled_security_code':
                            $errorMessage = 'Código de segurança incorreto';
                            break;
                        case 'cc_rejected_bad_filled_other':
                            $errorMessage = 'Dados do cartão incorretos';
                            break;
                        case 'cc_rejected_insufficient_amount':
                            $errorMessage = 'Saldo insuficiente';
                            break;
                        case 'cc_rejected_call_for_authorize':
                            $errorMessage = 'É necessário autorizar o pagamento com o banco emissor';
                            break;
                        case 'cc_rejected_high_risk':
                            $errorMessage = 'Pagamento rejeitado por risco elevado';
                            break;
                        default:
                            $errorMessage = 'Pagamento rejeitado pela operadora do cartão';
                    }
                }
            } catch (Exception $e) {
                error_log("Erro ao obter detalhes do pagamento: " . $e->getMessage());
                $errorMessage = 'Não foi possível processar o pagamento';
            }
            
            // Registrar o pagamento com falha
            recordPayment($subscription['id'], $payment_id, $valor, $dbStatus);
        }
        
        // Atualizar o status da assinatura, se necessário
        if ($dbStatus === 'falha' || $dbStatus === 'cancelado') {
            updateSubscriptionStatus($subscription['id'], 'falha');
        }
        
        // Adicionar mensagem de erro para exibir na próxima página, se necessário
        $_SESSION['payment_success'] = false;
        $_SESSION['message'] = "O pagamento não foi aprovado. Tente novamente ou entre em contato com o suporte.";
        
    } else {
        // Assinatura não encontrada
        $errorMessage = "Não foi possível encontrar sua assinatura.";
        error_log("Erro: Assinatura não encontrada. External reference: {$external_reference}, Preference ID: {$preference_id}");
    }
    
} catch(PDOException $e) {
    // Em produção, registre o erro em um arquivo de log
    error_log("Erro na conexão com banco de dados: " . $e->getMessage());
    $errorMessage = "Ocorreu um erro no processamento do pagamento. Entre em contato com o suporte.";
}

// Se não há mensagem de erro específica, use uma genérica
if (empty($errorMessage)) {
    $errorMessage = "O pagamento não pôde ser concluído.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CheckZone - Vistoria Online | Falha no Pagamento</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .payment-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .error-icon {
        font-size: 80px;
        color: #dc3545;
        margin-bottom: 20px;
    }

    .warning-icon {
        font-size: 80px;
        color: #ffc107;
        margin-bottom: 20px;
    }

    .order-details {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 30px;
    }

    .payment-info {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .payment-info-item {
        background-color: #e9ecef;
        padding: 10px 15px;
        border-radius: 6px;
        flex: 1 1 200px;
    }

    .btn-actions {
        margin-top: 30px;
    }

    .error-details {
        background-color: #f8d7da;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .payment-container {
            margin: 20px;
            padding: 20px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="payment-container text-center">
            <img src="images/logo-default.png" alt="" width="187" />

            <div class="error-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h1 class="mb-4">Falha no Pagamento</h1>
            <p class="lead">Não foi possível processar seu pagamento neste momento.</p>

            <?php if (!empty($errorMessage)): ?>
            <div class="error-details">
                <h5><i class="fas fa-exclamation-triangle me-2"></i> Motivo da falha</h5>
                <p class="mb-0"><?php echo htmlspecialchars($errorMessage); ?></p>
            </div>
            <?php endif; ?>

            <?php if (isset($subscription) && !empty($subscription)): ?>
            <div class="order-details mt-4">
                <h4>Detalhes da Assinatura</h4>
                <div class="row mt-3">
                    <div class="col-md-6 text-md-start">
                        <p><strong>ID da Assinatura:</strong> #<?php echo htmlspecialchars($subscription['id']); ?></p>
                        <p><strong>Data:</strong>
                            <?php echo isset($subscription['data_inicio']) && !empty($subscription['data_inicio']) ? date('d/m/Y H:i', strtotime($subscription['data_inicio'])) : 'Não disponível'; ?>
                        </p>
                        <?php if (!empty($subscription['user_id'])): ?>
                        <p><strong>ID do Usuário:</strong> <?php echo htmlspecialchars($subscription['user_id']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Plano:</strong>
                            <?php echo !empty($plan['nome_plano']) ? htmlspecialchars($plan['nome_plano']) : 'Não especificado'; ?>
                        </p>
                        <p><strong>Valor:</strong> R$
                            <?php echo !empty($plan['preco']) ? number_format($plan['preco'], 2, ',', '.') : '0,00'; ?>
                        </p>
                        <p><strong>Status:</strong> <span class="badge bg-danger">Falha</span></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($payment_id) || !empty($preference_id)): ?>
            <div class="payment-info">
                <?php if (!empty($payment_id)): ?>
                <div class="payment-info-item">
                    <small class="text-muted">ID do Pagamento</small>
                    <p class="mb-0"><?php echo htmlspecialchars($payment_id); ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($preference_id)): ?>
                <div class="payment-info-item">
                    <small class="text-muted">ID da Preferência</small>
                    <p class="mb-0"><?php echo htmlspecialchars($preference_id); ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($errorCode)): ?>
                <div class="payment-info-item">
                    <small class="text-muted">Código de Erro</small>
                    <p class="mb-0"><?php echo htmlspecialchars($errorCode); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="btn-actions">
                <a href="payment.php?subscription_id=<?php echo htmlspecialchars($subscription['id']); ?>&external_reference=<?php echo htmlspecialchars($external_reference); ?>&preference_id=<?php echo htmlspecialchars($preference_id); ?>"
                    class="btn btn-primary btn-lg">Tentar Novamente</a>
                <a href="index.php" class="btn btn-outline-secondary btn-lg ms-2">Voltar ao site</a>
            </div>

            <div class="mt-4">
                <h5>Precisa de ajuda?</h5>
                <p>Entre em contato com nosso suporte:</p>
                <p><i class="fas fa-envelope me-2"></i> suporte@checkzone.com.br</p>
                <p><i class="fas fa-phone me-2"></i> (11) 1234-5678</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>