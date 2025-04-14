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
$dbStatus = $statusMap[$status] ?? 'pendente';

// Verificação de pagamento aprovado
$isApproved = ($status === 'approved');

// Obter o valor do pagamento usando a API do Mercado Pago
$valor = 0;
if ($payment_id) {
    try {
        // Buscar detalhes do pagamento usando a API
        $payment = MercadoPago\Payment::find_by_id($payment_id);
        
        if ($payment) {
            // Obter o valor do pagamento
            $valor = $payment->transaction_amount;
        } else {
            error_log("Erro: Não foi possível encontrar o pagamento com ID: {$payment_id}");
        }
    } catch (Exception $e) {
        error_log("Erro ao obter detalhes do pagamento: " . $e->getMessage());
    }
}

try {
    // Buscar a assinatura pelo external_reference (que é o ID da assinatura)
    $subscription = null;
    
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
        // Registrar o pagamento
        recordPayment($subscription['id'], $payment_id, $valor, $dbStatus);
        
        // Se o pagamento foi aprovado, atualizar o status da assinatura
        if ($isApproved) {
            updateSubscriptionStatus($subscription['id'], 'ativa');
            
            // Adicionar mensagem de sucesso para exibir na próxima página
            $_SESSION['payment_success'] = true;
            $_SESSION['message'] = "Pagamento aprovado com sucesso! Sua assinatura está ativa.";
        } else {
            // Status diferente de aprovado
            $_SESSION['payment_success'] = false;
            $_SESSION['message'] = "Pagamento com status: " . $dbStatus . ". Entre em contato com o suporte.";
            
            // Se status for de falha, atualizar a assinatura
            if ($dbStatus === 'falha' || $dbStatus === 'cancelado') {
                updateSubscriptionStatus($subscription['id'], $dbStatus);
            }
        }
        
        // Redirecionar para a página de dashboard ou sucesso
        //header('Location: /dashboard.php');
        //exit;
    } else {
        // Assinatura não encontrada
        $_SESSION['payment_success'] = false;
        $_SESSION['message'] = "Erro: Não foi possível encontrar sua assinatura.";
        error_log("Erro: Assinatura não encontrada. External reference: {$external_reference}, Preference ID: {$preference_id}");
        header('Location: /erro.php');
        exit;
    }
    
} catch(PDOException $e) {
    // Em produção, registre o erro em um arquivo de log
    error_log("Erro na conexão com banco de dados: " . $e->getMessage());
    $_SESSION['payment_success'] = false;
    $_SESSION['message'] = "Ocorreu um erro no processamento do pagamento. Entre em contato com o suporte.";
    header('Location: /erro.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CheckZone - Vistoria Online | Pagamento Concluído</title>
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

    .success-icon {
        font-size: 80px;
        color: #28a745;
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

    .payment-method {
        display: inline-block;
        padding: 8px 15px;
        background-color: #f0f8ff;
        border-radius: 30px;
        color: #0d6efd;
        font-weight: 600;
        margin-top: 10px;
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
            <?php if ($isApproved): ?>
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="mb-4">Pagamento Aprovado!</h1>
            <p class="lead">Seu pagamento foi processado com sucesso e seu pedido está confirmado.</p>

            <?php if (!empty($payment_type)): ?>
            <div class="payment-method">
                <i class="fas fa-credit-card me-2"></i>
                <?php
                    switch($payment_type) {
                        case 'credit_card':
                            echo 'Cartão de Crédito';
                            break;
                        case 'debit_card':
                            echo 'Cartão de Débito';
                            break;
                        case 'bank_transfer':
                            echo 'Transferência Bancária';
                            break;
                        case 'pix':
                            echo 'PIX';
                            break;
                        default:
                            echo ucfirst(str_replace('_', ' ', $payment_type));
                    }
                ?>
            </div>
            <?php endif; ?>

            <?php if (isset($orderInfo) && !empty($orderInfo)): ?>
            <div class="order-details mt-4">
                <h4>Detalhes do Pedido</h4>
                <div class="row mt-3">
                    <div class="col-md-6 text-md-start">
                        <p><strong>Número do Pedido:</strong> #<?php echo $external_reference; ?></p>
                        <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($orderInfo['data_pedido'])); ?>
                        </p>
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($orderInfo['cliente_nome']); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Valor Total:</strong> R$
                            <?php echo number_format($orderInfo['valor_total'], 2, ',', '.'); ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Aprovado</span></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="payment-info">
                <div class="payment-info-item">
                    <small class="text-muted">ID do Pagamento</small>
                    <p class="mb-0"><?php echo $payment_id; ?></p>
                </div>
                <div class="payment-info-item">
                    <small class="text-muted">Número do Pedido</small>
                    <p class="mb-0"><?php echo $merchant_order_id; ?></p>
                </div>
            </div>

            <div class="btn-actions">
                <a href="#" class="btn btn-primary btn-lg">Acessar Plataforma</a>
                <a href="index.php" class="btn btn-outline-secondary btn-lg ms-2">Voltar ao site</a>
            </div>

            <!-- Email de confirmação -->
            <div class="mt-4">
                <p class="small text-muted">Um e-mail de confirmação foi enviado para o endereço cadastrado.</p>
            </div>

            <?php elseif (isset($dbError) && $dbError): ?>

            <div class="text-center">
                <i class="fas fa-exclamation-circle" style="font-size: 80px; color: #ffc107; margin-bottom: 20px;"></i>
                <h2>Ocorreu um erro ao processar seu pedido</h2>
                <p class="lead">Nossa equipe foi notificada e está trabalhando para resolver o problema.</p>
                <p>Por favor, entre em contato com nosso suporte se o problema persistir.</p>
                <div class="mt-4">
                    <a href="contato.php" class="btn btn-outline-primary">Contato</a>
                    <a href="index.php" class="btn btn-primary ms-2">Voltar à Página Inicial</a>
                </div>
            </div>

            <?php else: ?>

            <div class="text-center">
                <i class="fas fa-question-circle" style="font-size: 80px; color: #ffc107; margin-bottom: 20px;"></i>
                <h2>Status do Pagamento: <?php echo ucfirst($status); ?></h2>
                <p class="lead">Seu pagamento está sendo processado ou pode requerer atenção adicional.</p>
                <p>Status atual: <strong><?php echo ucfirst($collection_status); ?></strong></p>
                <div class="mt-4">
                    <a href="verificar-status.php?order=<?php echo $external_reference; ?>"
                        class="btn btn-primary">Verificar Status do Pedido</a>
                    <a href="index.php" class="btn btn-outline-secondary ms-2">Voltar à Página Inicial</a>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>

    <!-- Envio de e-mail de confirmação (simulado aqui - em um caso real seria feito no backend) -->
    <?php if ($isApproved && isset($orderInfo) && !empty($orderInfo['cliente_email'])): ?>
    <script>
    // Este código é apenas para demonstração
    // Na implementação real, o envio de email seria feito no lado do servidor (PHP)
    console.log("Email de confirmação enviado para: <?php echo $orderInfo['cliente_email']; ?>");
    </script>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>