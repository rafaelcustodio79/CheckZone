<?php
// Include globals.php which already has DB connection functions and utilities
require_once 'functions/globals.php';

// Initialize MercadoPago SDK
require_once 'vendor/autoload.php'; // Make sure MercadoPago SDK is installed

// Set MercadoPago access token
$accessToken = 'TEST-4600284399943154-080521-b63195126107433ed1ec90a69136410f-609496368';
MercadoPago\SDK::setAccessToken($accessToken);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step']) && $_POST['step'] === 'plan_selection') {
        // Store the selected plan in session
        session_start();
        $_SESSION['selected_plan_id'] = $_POST['plan_id'];
        
        // Redirect to registration form
        header('Location: ' . $_SERVER['PHP_SELF'] . '?step=register');
        exit;
    }
    elseif (isset($_POST['step']) && $_POST['step'] === 'registration') {
        session_start();
        $planId = $_SESSION['selected_plan_id'] ?? null;
        
        if (!$planId) {
            die("Nenhum plano selecionado. Por favor, volte e selecione um plano.");
        }
        
        // Get the plan details
        $plan = getPlanById($planId);
        
        if (!$plan) {
            die("Plano inválido. Por favor, volte e selecione um plano válido.");
        }
        
        // Process the registration form
        $company = [
            'razao_social' => clean_input($_POST['razao_social']),
            'nome_fantasia' => clean_input($_POST['nome_fantasia']),
            'cnpj' => clean_input($_POST['cnpj']),
            'telefone_principal' => clean_input($_POST['telefone']),
            'email_principal' => clean_input($_POST['email']),
            'cep' => clean_input($_POST['cep']),
            'endereco' => clean_input($_POST['endereco']),
            'numero' => (int)clean_input($_POST['numero']),
            'complemento' => clean_input($_POST['complemento'] ?? ''),
            'bairro' => clean_input($_POST['bairro']),
            'cidade' => clean_input($_POST['cidade']),
            'estado' => clean_input($_POST['estado']),
            'status_empresa' => 1,
            'plano_id' => $planId,
            'created_at' => date('Y-m-d')
        ];
        
        $user = [
            'nome' => clean_input($_POST['nome_responsavel']),
            'email' => clean_input($_POST['email']),
            'senha_hash' => password_hash(clean_input($_POST['senha']), PASSWORD_DEFAULT),
            'users_type_id' => 2, // Assuming 2 is for regular users
            'telefone_usuario' => clean_input($_POST['telefone']),
            'user_status' => 1
        ];
        
        // Register the user and company
        $result = registerUserAndCompany($user, $company);
        
        if ($result) {
            // Create a subscription
            $subscriptionResult = createSubscription($result['user_id'], $planId);
            
            if ($subscriptionResult) {
                // Create MercadoPago payment
                $mpPayment = createMercadoPagoPayment($result['user_id'], $result['company_id'], $plan, $subscriptionResult);
                
                if ($mpPayment) {
                    // Redirect to MercadoPago
                    header("Location: " . $mpPayment->init_point);
                    exit;
                } else {
                    die("Erro ao criar pagamento. Por favor, tente novamente mais tarde.");
                }
            } else {
                die("Erro ao criar assinatura. Por favor, tente novamente mais tarde.");
            }
        } else {
            die("Erro ao registrar usuário e empresa. Por favor, tente novamente mais tarde.");
        }
    }
}

// Get plan by ID
function getPlanById($planId) {
    try {
        $pdo = db_connect();
        $sql = "SELECT * FROM plans WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $planId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Get all plans
function getPlans() {
    try {
        $pdo = db_connect();
        $sql = "SELECT * FROM plans ORDER BY preco ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Register user and company
function registerUserAndCompany($user, $company) {
    try {
        $pdo = db_connect();
        $pdo->beginTransaction();
        
        // Insert company
        $sqlCompany = "INSERT INTO company (razao_social, nome_fantasia, cnpj, telefone_principal, 
                      email_principal, cep, endereco, numero, complemento, bairro, cidade, estado, 
                      status_empresa, plano_id, created_at) 
                      VALUES (:razao_social, :nome_fantasia, :cnpj, :telefone_principal, 
                      :email_principal, :cep, :endereco, :numero, :complemento, :bairro, :cidade, 
                      :estado, :status_empresa, :plano_id, :created_at)";
        
        $stmtCompany = $pdo->prepare($sqlCompany);
        $stmtCompany->execute([
            ':razao_social' => $company['razao_social'],
            ':nome_fantasia' => $company['nome_fantasia'],
            ':cnpj' => $company['cnpj'],
            ':telefone_principal' => $company['telefone_principal'],
            ':email_principal' => $company['email_principal'],
            ':cep' => $company['cep'],
            ':endereco' => $company['endereco'],
            ':numero' => $company['numero'],
            ':complemento' => $company['complemento'],
            ':bairro' => $company['bairro'],
            ':cidade' => $company['cidade'],
            ':estado' => $company['estado'],
            ':status_empresa' => $company['status_empresa'],
            ':plano_id' => $company['plano_id'],
            ':created_at' => $company['created_at']
        ]);
        
        $companyId = $pdo->lastInsertId();
        
        // Insert user
        $sqlUser = "INSERT INTO users (nome, email, senha_hash, users_type_id, telefone_usuario, 
                  user_status, company_id, criado_em) 
                  VALUES (:nome, :email, :senha_hash, :users_type_id, :telefone_usuario, 
                  :user_status, :company_id, NOW())";
        
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            ':nome' => $user['nome'],
            ':email' => $user['email'],
            ':senha_hash' => $user['senha_hash'],
            ':users_type_id' => $user['users_type_id'],
            ':telefone_usuario' => $user['telefone_usuario'],
            ':user_status' => $user['user_status'],
            ':company_id' => $companyId
        ]);
        
        $userId = $pdo->lastInsertId();
        
        $pdo->commit();
        
        return [
            'user_id' => $userId,
            'company_id' => $companyId
        ];
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Create a subscription
function createSubscription($userId, $planId) {
    try {
        $pdo = db_connect();
        
        $sql = "INSERT INTO subscriptions (user_id, plan_id, status, data_inicio, data_fim) 
                VALUES (:user_id, :plan_id, 'pendente', NOW(), NULL)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':plan_id' => $planId
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Create a MercadoPago payment
function createMercadoPagoPayment($userId, $companyId, $plan, $subscriptionId) {
    try {
        // Get the recurring type
        $recorrencia = $plan['recorrencia']; // 'mensal' or 'anual'
        $interval = ($recorrencia === 'mensal') ? 1 : 12;
        
        // For 'Check 30' (free trial), we'll handle it differently
        if ($plan['preco'] == 0) {
            // For trial, we create a preference with future payment
            $preference = new MercadoPago\Preference();
            
            $item = new MercadoPago\Item();
            $item->title = $plan['nome'];
            $item->quantity = 1;
            $item->unit_price = 0;
            $item->currency_id = "BRL";
            
            $preference->items = [$item];
            $preference->external_reference = $subscriptionId;
            
            $preference->back_urls = [
                "success" => "https://checkzone.com.br/payment_success.php",
                "failure" => "https://checkzone.com.br/payment_failure.php",
                "pending" => "https://checkzone.com.br/payment_pending.php"
            ];
            
            $preference->auto_return = "approved";
            $preference->save();
            
            // Update subscription with MP data
            updateSubscriptionWithMercadoPagoData($subscriptionId, $preference->id);
            
            return $preference;
        } else {
            // For regular paid subscriptions
            $planName = $plan['nome'] . ' - ' . $plan['descricao'];
            $planPrice = $plan['preco'];
            
            $preference = new MercadoPago\Preference();
            
            $item = new MercadoPago\Item();
            $item->title = $planName;
            $item->quantity = 1;
            $item->unit_price = $planPrice;
            $item->currency_id = "BRL";
            
            $preference->items = [$item];
            $preference->external_reference = $subscriptionId;
            
            $preference->back_urls = [
                "success" => "https://checkzone.com.br/payment_success.php",
                "failure" => "https://checkzone.com.br/payment_failure.php",
                "pending" => "https://checkzone.com.br/payment_pending.php"
            ];
            
            $preference->auto_return = "approved";
            $preference->save();
            
            // Update subscription with MP data
            updateSubscriptionWithMercadoPagoData($subscriptionId, $preference->id);
            
            return $preference;
        }
    } catch (Exception $e) {
        error_log("MercadoPago error: " . $e->getMessage());
        return false;
    }
}

// Update subscription with MercadoPago ID
function updateSubscriptionWithMercadoPagoData($subscriptionId, $mercadoPagoId) {
    try {
        $pdo = db_connect();
        
        $sql = "UPDATE subscriptions SET mercado_pago_id = :mp_id WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':mp_id' => $mercadoPagoId,
            ':id' => $subscriptionId
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Record payment
function recordPayment($subscriptionId, $mercadoPagoPaymentId, $valor, $status) {
    try {
        $pdo = db_connect();
        
        $sql = "INSERT INTO payments (subscription_id, mercado_pago_payment_id, valor, status, data_pagamento) 
                VALUES (:subscription_id, :mp_payment_id, :valor, :status, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':subscription_id' => $subscriptionId,
            ':mp_payment_id' => $mercadoPagoPaymentId,
            ':valor' => $valor,
            ':status' => $status
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Update subscription status
function updateSubscriptionStatus($subscriptionId, $status) {
    try {
        $pdo = db_connect();
        
        $sql = "UPDATE subscriptions SET status = :status WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':id' => $subscriptionId
        ]);
        
        // If approved, set the end date based on the plan
        if ($status === 'ativa') {
            $subscription = getSubscriptionById($subscriptionId);
            $plan = getPlanById($subscription['plan_id']);
            
            $interval = ($plan['recorrencia'] === 'mensal') ? '1 month' : '1 year';
            
            $sql = "UPDATE subscriptions SET data_fim = DATE_ADD(NOW(), INTERVAL " . $interval . ") WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $subscriptionId]);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Get subscription by ID
function getSubscriptionById($subscriptionId) {
    try {
        $pdo = db_connect();
        $sql = "SELECT * FROM subscriptions WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $subscriptionId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

$currentStep = $_GET['step'] ?? 'plans';
$plans = getPlans();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CheckZone - Planos</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f5f5f5;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .plans-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .plan-card {
        border-radius: 8px;
        padding: 20px;
        width: 30%;
        min-width: 300px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .plan-title {
        font-size: 24px;
        margin: 0 0 20px;
    }

    .plan-price {
        font-size: 36px;
        font-weight: bold;
        margin: 0 0 5px;
    }

    .plan-period {
        margin: 0 0 20px;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 30px;
    }

    .feature-list li {
        margin-bottom: 10px;
        padding-left: 25px;
        position: relative;
    }

    .feature-active:before {
        content: "✓";
        position: absolute;
        left: 0;
        color: black;
    }

    .feature-inactive:before {
        content: "✗";
        position: absolute;
        left: 0;
        color: #ccc;
    }

    .btn-contract {
        display: block;
        width: 100%;
        padding: 15px;
        border-radius: 25px;
        border: none;
        color: white;
        font-weight: bold;
        cursor: pointer;
        text-align: center;
    }

    .check30 {
        background-color: #eaeae1;
    }

    .check-top {
        background-color: #acb5b5;
    }

    .check-basico {
        background-color: #eaeae1;
    }

    .btn-check30,
    .btn-check-basico {
        background-color: #092d8a;
    }

    .btn-check-top {
        background-color: #222;
    }

    .validity {
        margin-top: 20px;
        font-size: 14px;
    }

    /* Registration form styles */
    .registration-form {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-col {
        flex: 1;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input,
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    .submit-btn {
        background-color: #092d8a;
        color: white;
        padding: 15px;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
    }
    </style>
</head>

<body>
    <div class="container">
        <?php if ($currentStep === 'plans'): ?>
        <h1>Escolha seu plano</h1>
        <div class="plans-container">
            <?php foreach ($plans as $plan): ?>
            <div class="plan-card <?php echo strtolower(str_replace(' ', '-', $plan['nome'])); ?>">
                <h2 class="plan-title"><?php echo $plan['nome']; ?></h2>
                <div class="plan-price">
                    R$<?php echo number_format($plan['preco'], 2, ',', '.'); ?>
                    <?php if ($plan['preco'] > 0): ?>
                    <span
                        style="font-size: 18px;">/<?php echo $plan['recorrencia'] === 'mensal' ? 'mês' : 'ano'; ?></span>
                    <?php endif; ?>
                </div>
                <div class="plan-period"><?php echo $plan['descricao']; ?></div>

                <ul class="feature-list">
                    <?php
                            // Aqui você pode adicionar as características do plano
                            // Esta é uma simplificação, você pode adaptá-la conforme necessário
                            $features = [
                                '1 CNPJ' => true,
                                '1 Checklist' => true,
                                '1 Inspetor' => true,
                                'Upload de fotos' => $plan['preco'] > 0,
                                'Upload de vídeos' => $plan['nome'] === 'Check Top',
                                'Alarme' => $plan['preco'] > 0,
                                'Gerar Relatório' => $plan['preco'] > 0
                            ];
                            
                            foreach ($features as $feature => $isActive):
                            ?>
                    <li class="<?php echo $isActive ? 'feature-active' : 'feature-inactive'; ?>">
                        <?php echo $feature; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($plan['preco'] == 0): ?>
                <div>Gratuito</div>
                <div class="validity">Validade: 30 dias de degustação</div>
                <?php else: ?>
                <div class="validity">Validade: conforme contrato</div>
                <?php endif; ?>

                <form method="post" action="">
                    <input type="hidden" name="step" value="plan_selection">
                    <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                    <button type="submit"
                        class="btn-contract btn-<?php echo strtolower(str_replace(' ', '-', $plan['nome'])); ?>">
                        Quero Contratar
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif ($currentStep === 'register'): ?>
        <?php
            session_start();
            $planId = $_SESSION['selected_plan_id'] ?? null;
            $plan = $planId ? getPlanById($planId) : null;
            
            if (!$plan) {
                echo '<div style="text-align: center; margin: 50px;">';
                echo '<h2>Erro ao carregar o plano selecionado</h2>';
                echo '<p>Por favor, <a href="?step=plans">volte à seleção de planos</a> e tente novamente.</p>';
                echo '</div>';
            } else {
            ?>
        <h1>Complete seu cadastro</h1>
        <p>Você selecionou o plano <strong><?php echo $plan['nome']; ?></strong> -
            R$<?php echo number_format($plan['preco'], 2, ',', '.'); ?><?php echo $plan['preco'] > 0 ? '/' . ($plan['recorrencia'] === 'mensal' ? 'mês' : 'ano') : ''; ?>
        </p>

        <div class="registration-form">
            <form method="post" action="" id="registration-form">
                <input type="hidden" name="step" value="registration">

                <h3>Dados da Empresa</h3>
                <div class="form-row">
                    <div class="form-col">
                        <label for="razao_social">Razão Social</label>
                        <input type="text" id="razao_social" name="razao_social" required>
                    </div>
                    <div class="form-col">
                        <label for="nome_fantasia">Nome Fantasia</label>
                        <input type="text" id="nome_fantasia" name="nome_fantasia" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label for="cnpj">CNPJ</label>
                        <input type="text" id="cnpj" name="cnpj" required>
                    </div>
                    <div class="form-col">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" required>
                    </div>
                    <div class="form-col">
                        <button type="button" id="buscar_cep"
                            style="margin-top: 24px; padding: 10px; background-color: #ddd; border: none; border-radius: 4px; cursor: pointer;">Buscar
                            CEP</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" name="endereco" required>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label for="numero">Número</label>
                        <input type="text" id="numero" name="numero" required>
                    </div>
                    <div class="form-col">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" name="complemento">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro" required>
                    </div>
                    <div class="form-col">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" required>
                    </div>
                    <div class="form-col">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" required>
                            <option value="">Selecione...</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                </div>

                <h3>Dados do Responsável</h3>
                <div class="form-group">
                    <label for="nome_responsavel">Nome Completo</label>
                    <input type="text" id="nome_responsavel" name="nome_responsavel" required>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" required>
                    </div>
                    <div class="form-col">
                        <label for="confirmar_senha">Confirmar Senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 30px;">
                    <button type="submit" class="submit-btn">Finalizar Cadastro</button>
                </div>
            </form>
        </div>
        <?php
            }
            ?>
        <?php endif; ?>
    </div>