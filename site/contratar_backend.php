<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include globals.php which already has DB connection functions and utilities
require_once 'functions/globals.php';

// Initialize MercadoPago SDK
require_once 'vendor/autoload.php';

// Inicia a sessão para garantir que as mensagens sejam mantidas
session_start();

$PDO = db_connect();

// Set MercadoPago access token
$accessToken = MP_ACCESS_TOKEN;
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
        
        // Determine if the user is PJ or PF
        $tipoPessoa = strtoupper(clean_input($_POST['tipo_pessoa']));
        
        // Common fields for both PJ and PF
        $cliente = [
            'tipo_pessoa' => $tipoPessoa,
            'nome_completo' => $tipoPessoa === 'PJ' ? clean_input($_POST['nome_completo']) : clean_input($_POST['nome_completo_pf']),
            'status' => 1,
            'plano_id' => $planId,
            'created_at' => date('Y-m-d H:i:s'),
            'telefone_principal' => $tipoPessoa === 'PJ' ? clean_input($_POST['telefone']) : clean_input($_POST['telefone_pf']),
            'email_principal' => $tipoPessoa === 'PJ' ? clean_input($_POST['email']) : clean_input($_POST['email_pf']),
            'cep' => $tipoPessoa === 'PJ' ? clean_input($_POST['cep']) : clean_input($_POST['cep_pf']),
            'endereco' => $tipoPessoa === 'PJ' ? clean_input($_POST['endereco']) : clean_input($_POST['endereco_pf']),
            'numero' => $tipoPessoa === 'PJ' ? (int)clean_input($_POST['numero']) : (int)clean_input($_POST['numero_pf']),
            'complemento' => $tipoPessoa === 'PJ' ? clean_input($_POST['complemento'] ?? '') : clean_input($_POST['complemento_pf'] ?? ''),
            'bairro' => $tipoPessoa === 'PJ' ? clean_input($_POST['bairro']) : clean_input($_POST['bairro_pf']),
            'cidade' => $tipoPessoa === 'PJ' ? clean_input($_POST['cidade']) : clean_input($_POST['cidade_pf']),
            'estado' => $tipoPessoa === 'PJ' ? clean_input($_POST['estado']) : clean_input($_POST['estado_pf'])
        ];
        
        // Add specific fields based on the type of person
        if ($tipoPessoa === 'PJ') {
            $cliente['razao_social'] = clean_input($_POST['razao_social']);
            $cliente['nome_fantasia'] = clean_input($_POST['nome_fantasia']);
            $cliente['cnpj'] = clean_input($_POST['cnpj']);
            $cliente['telefone_responsavel'] = clean_input($_POST['telefone_responsavel']);
            $cliente['email_responsavel'] = clean_input($_POST['email_responsavel']);
            $cliente['cargo'] = clean_input($_POST['cargo']);
            
            // Definir dados do usuário
            $user = [
                'nome' => $cliente['nome_completo'],
                'email' => $cliente['email_responsavel'],
                'senha_hash' => password_hash(clean_input($_POST['senha']), PASSWORD_DEFAULT),
                'users_type_id' => 2, // MASTER
                'telefone_usuario' => $cliente['telefone_responsavel']
            ];
        } else { // PF
            $cliente['cpf'] = clean_input($_POST['cpf']);
            
            // Definir dados do usuário
            $user = [
                'nome' => $cliente['nome_completo'],
                'email' => $cliente['email_principal'],
                'senha_hash' => password_hash(clean_input($_POST['senha']), PASSWORD_DEFAULT),
                'users_type_id' => 2, // MASTER
                'telefone_usuario' => $cliente['telefone_principal']
            ];
        }
        
        // Register the user and cliente (PJ or PF)
        $result = registerUserAndClient($user, $cliente);
        
        if ($result) {
            // Create a subscription
            $subscriptionResult = createSubscription($result['user_id'], $planId);
            
            if ($subscriptionResult) {
                // Create MercadoPago payment
                $mpPayment = createMercadoPagoPayment($result['user_id'], $result['client_id'], $plan, $subscriptionResult);
                
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

// Register user and cliente (PJ or PF)
function registerUserAndClient($user, $cliente) {
    try {
        $PDO = db_connect();
        $PDO->beginTransaction();

        // Verifica se o e-mail já está cadastrado
        if (isEmailRegistered($user['email'])) {
            throw new Exception("O e-mail informado já está cadastrado. Tente recuperar a senha.");
        }

        // Insere o cliente (PJ ou PF)
        if ($cliente['tipo_pessoa'] === 'PJ') {
            $sqlClient = "INSERT INTO clientes (tipo_pessoa, razao_social, nome_fantasia, cnpj, 
                          nome_completo, telefone_principal, email_principal, telefone_responsavel, 
                          email_responsavel, cargo, cep, endereco, numero, complemento, 
                          bairro, cidade, estado, status, plano_id, created_at) 
                          VALUES (:tipo_pessoa, :razao_social, :nome_fantasia, :cnpj, 
                          :nome_completo, :telefone_principal, :email_principal, :telefone_responsavel, 
                          :email_responsavel, :cargo, :cep, :endereco, :numero, 
                          :complemento, :bairro, :cidade, :estado, :status, :plano_id, :created_at)";
                          
            $stmtClient = $PDO->prepare($sqlClient);
            $stmtClient->execute([
                ':tipo_pessoa' => $cliente['tipo_pessoa'],
                ':razao_social' => $cliente['razao_social'],
                ':nome_fantasia' => $cliente['nome_fantasia'],
                ':cnpj' => $cliente['cnpj'],
                ':nome_completo' => $cliente['nome_completo'],
                ':telefone_principal' => $cliente['telefone_principal'],
                ':email_principal' => $cliente['email_principal'],
                ':telefone_responsavel' => $cliente['telefone_responsavel'],
                ':email_responsavel' => $cliente['email_responsavel'],
                ':cargo' => $cliente['cargo'],
                ':cep' => $cliente['cep'],
                ':endereco' => $cliente['endereco'],
                ':numero' => $cliente['numero'],
                ':complemento' => $cliente['complemento'],
                ':bairro' => $cliente['bairro'],
                ':cidade' => $cliente['cidade'],
                ':estado' => $cliente['estado'],
                ':status' => $cliente['status'],
                ':plano_id' => $cliente['plano_id'],
                ':created_at' => $cliente['created_at']
            ]);
        } else { // PF
            $sqlClient = "INSERT INTO clientes (tipo_pessoa, nome_completo, cpf,
                          telefone_principal, email_principal, cep, endereco, numero, complemento, 
                          bairro, cidade, estado, status, plano_id, created_at) 
                          VALUES (:tipo_pessoa, :nome_completo, :cpf,
                          :telefone_principal, :email_principal, :cep, :endereco, :numero, 
                          :complemento, :bairro, :cidade, :estado, :status, :plano_id, :created_at)";
                          
            $stmtClient = $PDO->prepare($sqlClient);
            $stmtClient->execute([
                ':tipo_pessoa' => $cliente['tipo_pessoa'],
                ':nome_completo' => $cliente['nome_completo'],
                ':cpf' => $cliente['cpf'],
                ':telefone_principal' => $cliente['telefone_principal'],
                ':email_principal' => $cliente['email_principal'],
                ':cep' => $cliente['cep'],
                ':endereco' => $cliente['endereco'],
                ':numero' => $cliente['numero'],
                ':complemento' => $cliente['complemento'],
                ':bairro' => $cliente['bairro'],
                ':cidade' => $cliente['cidade'],
                ':estado' => $cliente['estado'],
                ':status' => $cliente['status'],
                ':plano_id' => $cliente['plano_id'],
                ':created_at' => $cliente['created_at']
            ]);
        }

        $clientId = $PDO->lastInsertId();

        // Insere o usuário
        $sqlUser = "INSERT INTO users (nome, email, senha_hash, users_type_id, telefone_usuario, 
                  user_status, client_id, criado_em) 
                  VALUES (:nome, :email, :senha_hash, :users_type_id, :telefone_usuario, 
                  :user_status, :client_id, NOW())";

        $stmtUser = $PDO->prepare($sqlUser);
        $stmtUser->execute([
            ':nome' => $user['nome'],
            ':email' => $user['email'],
            ':senha_hash' => $user['senha_hash'],
            ':users_type_id' => $user['users_type_id'],
            ':telefone_usuario' => $user['telefone_usuario'],
            ':user_status' => 1,
            ':client_id' => $clientId
        ]);

        $userId = $PDO->lastInsertId();

        $PDO->commit();

        return [
            'user_id' => $userId,
            'client_id' => $clientId
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
function createMercadoPagoPayment($userId, $clientId, $plan, $subscriptionId) {
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