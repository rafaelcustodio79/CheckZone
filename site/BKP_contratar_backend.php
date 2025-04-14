<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include globals.php which already has DB connection functions and utilities
require_once 'functions/globals.php';

// Initialize MercadoPago SDK
require_once 'vendor/autoload.php'; // Make sure MercadoPago SDK is installed

// Inicia a sessão para garantir que as mensagens sejam mantidas
session_start();

$PDO = db_connect();

// Set MercadoPago access token
$accessToken = 'TEST-4600284399943154-080521-b63195126107433ed1ec90a69136410f-609496368';
MercadoPago\SDK::setAccessToken($accessToken);

// Get the plan ID from the query string
$plano_id = isset($_GET['plano_id']) ? (int)$_GET['plano_id'] : null;

// Get the selected plan
$selectedPlan = null;
if ($plano_id) {
    $selectedPlan = getPlanById($plano_id);
    
    if (!$selectedPlan) {
        die("Plano inválido. Por favor, selecione um plano válido.");
    }
    
    // Store the selected plan in session
    $_SESSION['selected_plan_id'] = $plano_id;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step']) && $_POST['step'] === 'registration') {
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
            $_SESSION['error_message'] = "O e-mail informado já está cadastrado. Se esqueceu sua senha, tente recuperá-la.";
            header("Location: contratar.php"); // Redirecione de volta para o formulário
            exit;
        }
    }
}

// Get plan by ID
function getPlanById($planId) {
    try {
        $PDO = db_connect();
        $sql = "SELECT * FROM plans WHERE id = :id";
        $stmt = $PDO->prepare($sql);
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
        $PDO = db_connect();
        $sql = "SELECT * FROM plans ORDER BY preco ASC";
        $stmt = $PDO->prepare($sql);
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
        $PDO = db_connect();
        $PDO->beginTransaction();

        // Verifica se o e-mail já está cadastrado
        if (isEmailRegistered($user['email'])) {
            throw new Exception("O e-mail informado já está cadastrado. Tente recuperar a senha.");
        }

        // Insere a empresa
        $sqlCompany = "INSERT INTO company (razao_social, nome_fantasia, cnpj, telefone_principal, 
                      email_principal, cep, endereco, numero, complemento, bairro, cidade, estado, 
                      status_empresa, plano_id, created_at) 
                      VALUES (:razao_social, :nome_fantasia, :cnpj, :telefone_principal, 
                      :email_principal, :cep, :endereco, :numero, :complemento, :bairro, :cidade, 
                      :estado, :status_empresa, :plano_id, :created_at)";

        $stmtCompany = $PDO->prepare($sqlCompany);
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
            ':status_empresa' => 1,
            ':plano_id' => $company['plano_id'],
            ':created_at' => $company['created_at']
        ]);

        $companyId = $PDO->lastInsertId();

        // Insere o usuário
        $sqlUser = "INSERT INTO users (nome, email, senha_hash, users_type_id, telefone_usuario, 
                  user_status, company_id, criado_em) 
                  VALUES (:nome, :email, :senha_hash, :users_type_id, :telefone_usuario, 
                  :user_status, :company_id, NOW())";

        $stmtUser = $PDO->prepare($sqlUser);
        $stmtUser->execute([
            ':nome' => $user['nome'],
            ':email' => $user['email'],
            ':senha_hash' => $user['senha_hash'],
            ':users_type_id' => $user['users_type_id'],
            ':telefone_usuario' => $user['telefone_usuario'],
            ':user_status' => 1,
            ':company_id' => $companyId
        ]);

        $userId = $PDO->lastInsertId();

        $PDO->commit();

        return [
            'user_id' => $userId,
            'company_id' => $companyId
        ];
    } catch (Exception $e) {
        $PDO->rollBack();
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}


function isEmailRegistered($email) {
    try {
        $PDO = db_connect();
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Create a subscription
function createSubscription($userId, $planId) {
    try {
        $PDO = db_connect();
        
        $sql = "INSERT INTO subscriptions (user_id, plan_id, status, data_inicio, data_fim) 
                VALUES (:user_id, :plan_id, 'pendente', NOW(), NULL)";
        
        $stmt = $PDO->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':plan_id' => $planId
        ]);
        
        return $PDO->lastInsertId();
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
                "success" => "https://checkzone.com.br/dev/site/payment_success.php",
                "failure" => "https://checkzone.com.br/dev/site/payment_failure.php",
                "pending" => "https://checkzone.com.br/dev/site/payment_pending.php"
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
                "success" => "https://checkzone.com.br/dev/site/payment_success.php",
                "failure" => "https://checkzone.com.br/dev/site/payment_failure.php",
                "pending" => "https://checkzone.com.br/dev/site/payment_pending.php"
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
        $PDO = db_connect();
        
        $sql = "UPDATE subscriptions SET mercado_pago_id = :mp_id WHERE id = :id";
        
        $stmt = $PDO->prepare($sql);
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
        $PDO = db_connect();
        
        $sql = "INSERT INTO payments (subscription_id, mercado_pago_payment_id, valor, status, data_pagamento) 
                VALUES (:subscription_id, :mp_payment_id, :valor, :status, NOW())";
        
        $stmt = $PDO->prepare($sql);
        $stmt->execute([
            ':subscription_id' => $subscriptionId,
            ':mp_payment_id' => $mercadoPagoPaymentId,
            ':valor' => $valor,
            ':status' => $status
        ]);
        
        return $PDO->lastInsertId();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Update subscription status
function updateSubscriptionStatus($subscriptionId, $status) {
    try {
        $PDO = db_connect();
        
        $sql = "UPDATE subscriptions SET status = :status WHERE id = :id";
        
        $stmt = $PDO->prepare($sql);
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
            $stmt = $PDO->prepare($sql);
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
        $PDO = db_connect();
        $sql = "SELECT * FROM subscriptions WHERE id = :id";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':id', $subscriptionId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}