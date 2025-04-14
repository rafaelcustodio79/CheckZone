<?php
// Include globals.php which already has DB PDO connection functions and utilities
require_once 'functions/globals.php';

// Inicia a sessão para garantir que as mensagens sejam mantidas
session_start();

// Incluir SDK do Mercado Pago
require_once 'vendor/autoload.php';

// Configurar credenciais do Mercado Pago
MercadoPago\SDK::setAccessToken(MP_ACCESS_TOKEN);

// Conectar ao banco de dados
$PDO = db_connect();

// Verificar se o usuário está logado ou tem uma assinatura em processo
$subscription_id = $_GET['subscription_id'] ?? $_SESSION['subscription_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Se não tiver subscription_id ou user_id, redirecionar para a página de planos
if (!$subscription_id && !$user_id) {
    $_SESSION['error_message'] = "Você precisa selecionar um plano primeiro.";
    header('Location: planos.php');
    exit;
}

// Buscar assinatura pelo ID
$subscription = null;
if ($subscription_id) {
    $subscription = getSubscriptionById($subscription_id);
}
// Ou buscar assinatura pendente do usuário
else if ($user_id) {
    $stmt = $PDO->prepare("SELECT * FROM subscriptions WHERE user_id = :user_id AND status IN ('pendente', 'falha') ORDER BY id DESC LIMIT 1");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($subscription) {
        $subscription_id = $subscription['id'];
        $_SESSION['subscription_id'] = $subscription_id;
    }
}

// Se não encontrou assinatura, redirecionar para página de planos
if (!$subscription) {
    $_SESSION['error_message'] = "Assinatura não encontrada ou já processada.";
    header('Location: planos.php');
    exit;
}

// Buscar o plano associado à assinatura
$plan = null;
if (!empty($subscription['plan_id'])) {
    $plan = getPlanById($subscription['plan_id']);
}

// Se não encontrou o plano, redirecionar
if (!$plan) {
    $_SESSION['error_message'] = "Plano não encontrado.";
    header('Location: planos.php');
    exit;
}

// Buscar dados do usuário
$user = null;
if (!empty($subscription['user_id'])) {
    $stmt = $PDO->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $subscription['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verificar se temos todos os dados necessários
if (!$user || !$plan) {
    $_SESSION['error_message'] = "Dados incompletos para processamento do pagamento.";
    header('Location: planos.php');
    exit;
}

// Criar preferência de pagamento no Mercado Pago
try {
    // Criar preferência
    $preference = new MercadoPago\Preference();
    
    // Configurar item a ser pago
    $item = new MercadoPago\Item();
    $item->title = "Plano {$plan['nome_plano']}";
    $item->quantity = 1;
    $item->unit_price = $plan['preco'];
    $item->currency_id = "BRL";
    
    // Definir preferência
    $preference->items = [$item];
    
    // Adicionar payer (pagador)
    $payer = new MercadoPago\Payer();
    if (!empty($user['cpf'])) {
        $payer->identification = array(
            "type" => "CPF", 
            "number" => preg_replace('/[^0-9]/', '', $user['cpf'])
        );
    } else if (!empty($user['cnpj'])) {
        $payer->identification = array(
            "type" => "CNPJ", 
            "number" => preg_replace('/[^0-9]/', '', $user['cnpj'])
        );
    }
    
    $payer->email = $user['email'];
    $payer->name = $user['nome'] ?? $user['nome_completo'] ?? $user['razao_social'];
    $preference->payer = $payer;
    
    // Configurar URLs de retorno
    $preference->back_urls = array(
        "success" => "https://" . $_SERVER['HTTP_HOST'] . "/dev/site/payment_success.php",
        "failure" => "https://" . $_SERVER['HTTP_HOST'] . "/dev/site/payment_failure.php",
        "pending" => "https://" . $_SERVER['HTTP_HOST'] . "/dev/site/payment_pending.php"
    );
    
    // Auto retorno após pagamento
    $preference->auto_return = "approved";
    
    // External reference para identificar a assinatura
    $preference->external_reference = $subscription_id;
    
    // Excluir meios de pagamento não desejados
    $preference->payment_methods = array(
        "excluded_payment_types" => array(
            array("id" => "ticket"),
            array("id" => "atm")
        ),
        "installments" => 12
    );
    
    // Salvar a preferência
    $preference->save();
    
    // Armazenar o ID da preferência em uma variável (com verificação de segurança)
    $preference_id = $preference->id;
    
    // Verificar se temos um ID válido
    if (empty($preference_id)) {
        error_log("Erro: ID de preferência vazio após salvar no Mercado Pago");
        throw new Exception("Não foi possível obter ID da preferência de pagamento");
    }
    
    error_log("Preferência criada com sucesso. ID: " . $preference_id);
    
    // Atualizar a assinatura com o ID da preferência (sem usar updated_at)
    $stmt = $PDO->prepare("UPDATE subscriptions SET mercado_pago_id = :mp_id WHERE id = :id");
    $stmt->bindParam(':mp_id', $preference_id);
    $stmt->bindParam(':id', $subscription_id);
    $stmt->execute();
    
    // Guardar o ID da preferência para referência futura
    $_SESSION['preference_id'] = $preference_id;
    
} catch (Exception $e) {
    error_log("Erro ao criar preferência de pagamento: " . $e->getMessage());
    $_SESSION['error_message'] = "Erro ao processar pagamento: " . $e->getMessage();
}

// Exibir mensagem de erro, se houver
$mensagemErro = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
if (!empty($mensagemErro)) {
    unset($_SESSION['error_message']);
}

// Determinar a URL do checkout com base no ambiente
$checkoutUrl = "https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=" . $preference_id;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CheckZone - Vistoria Online | Pagamento</title>
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

    .plan-details {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .btn-payment {
        width: 100%;
        padding: 12px;
        font-weight: 600;
        font-size: 16px;
        margin-top: 20px;
    }

    .security-info {
        display: flex;
        align-items: center;
        margin-top: 20px;
        color: #6c757d;
        font-size: 14px;
    }

    .security-info i {
        margin-right: 10px;
        color: #28a745;
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
        <div class="payment-container">
            <div class="text-center mb-4">
                <img src="images/logo-default.png" alt="CheckZone" width="187" />
                <h2 class="mt-4">Finalizar Pagamento</h2>
                <p class="text-muted">Complete os dados abaixo para finalizar sua assinatura</p>
            </div>

            <?php if (!empty($mensagemErro)): ?>
            <div class="alert alert-danger mb-4">
                <?php echo $mensagemErro; ?>
            </div>
            <?php endif; ?>

            <div class="plan-details">
                <h4 class="mb-3">Resumo da Compra</h4>
                <div class="row">
                    <div class="col-md-8">
                        <p><strong>Plano:</strong> <?php echo htmlspecialchars($plan['nome_plano']); ?></p>
                        <p><strong>Recorrência:</strong>
                            <?php echo $plan['recorrencia'] === 'mensal' ? 'Mensal' : 'Anual'; ?></p>
                        <p><strong>Cliente:</strong>
                            <?php echo htmlspecialchars($user['nome'] ?? $user['nome_completo'] ?? $user['razao_social']); ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <h5 class="text-primary">R$ <?php echo number_format($plan['preco'], 2, ',', '.'); ?></h5>
                    </div>
                </div>
            </div>

            <!-- Informações de Pagamento -->
            <div class="payment-form">
                <h4 class="mb-4">Informações de Pagamento</h4>

                <div class="text-center">
                    <p>Clique no botão abaixo para continuar para a página de pagamento seguro.</p>
                    <a href="<?php echo $checkoutUrl; ?>" class="btn btn-primary btn-payment btn-lg">
                        <i class="fas fa-lock me-2"></i>Pagar
                    </a>
                </div>
            </div>

            <div class="security-info mt-4">
                <i class="fas fa-lock"></i>
                <span>Suas informações de pagamento são protegidas com criptografia de ponta a ponta.</span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>