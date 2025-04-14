<?php
header('Content-Type: text/html; charset=utf-8');

const GOOGLE_API_KEY = 'AIzaSyDZT8X3HETmXGPUhXhDpyz977ElQU922wo';

const MP_ACCESS_TOKEN = 'TEST-2976472045594851-080521-f7d20272db5ca683af86449a5ece9040-609496368';

const MP_PUBLIC_KEY  = 'TEST-0c382f13-d79e-42a9-a0f1-4ebe880c5f49';

// inclui o arquivo do bd
require 'db.php';

// Conecta com o MySQL usando PDO
function db_connect()
{
    $PDO = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $PDO;
}

// Verifica se o usuário está logado
function isLoggedIn()
{
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    } else {
        return true;
    }
}

/**
 * Converte datas entre os padrões ISO e brasileiro
 * Fonte: http://rberaldo.com.br/php-conversao-de-datas-formato-brasileiro-e-formato-iso/
 */
function dateConvert($date)
{
    // Verificar se há horas no formato
    if (strpos($date, ' ') !== false) {
        // A data contém tempo
        [$datePart, $timePart] = explode(' ', $date);
    } else {
        // A data não contém tempo
        $datePart = $date;
        $timePart = '';
    }

    if (!strstr($datePart, '/')) {
        // $date está no formato ISO (yyyy-mm-dd) e deve ser convertida para dd/mm/yyyy
        sscanf($datePart, '%d-%d-%d', $y, $m, $d);
        $convertedDate = sprintf('%02d/%02d/%04d', $d, $m, $y);
    } else {
        // $date está no formato brasileiro (dd/mm/yyyy) e deve ser convertida para ISO
        sscanf($datePart, '%d/%d/%d', $d, $m, $y);
        $convertedDate = sprintf('%04d-%02d-%02d', $y, $m, $d);
    }

    // Verifica se há parte de tempo e a adiciona no final
    if ($timePart) {
        $convertedDate .= ' ' . $timePart;
    }

    return $convertedDate;
}

function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para converter valores no formato brasileiro para formato numérico
function format_to_number($value)
{
    // Remove pontos (separadores de milhar) e substitui vírgula por ponto (separador decimal)
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);
    return $value;
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

/**
 * Verifica se um CPF já está cadastrado
 * @param string $cpf CPF a verificar
 * @return bool True se já estiver cadastrado
 */
function isCPFRegistered($cpf) {
    try {
        $PDO = db_connect();
        $sql = "SELECT id FROM clients WHERE cpf = :cpf";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica se um CNPJ já está cadastrado
 * @param string $cnpj CNPJ a verificar
 * @return bool True se já estiver cadastrado
 */
function isCNPJRegistered($cnpj) {
    try {
        $PDO = db_connect();
        $sql = "SELECT id FROM clients WHERE cnpj = :cnpj";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
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

/**
 * Função atualizada para registrar um usuário e cliente
 * Adaptada para funcionar tanto com a estrutura antiga quanto com a nova
 */
function registerUserAndClient($user, $cliente) {
    try {
        $PDO = db_connect();
        $PDO->beginTransaction();


        // Verificar se a coluna user_id existe na tabela clients
        $checkColumn = $PDO->query("SHOW COLUMNS FROM clients LIKE 'user_id'");
        $hasUserIdColumn = $checkColumn->rowCount() > 0;

        // Verificar se a tabela client_users existe
        $checkTable = $PDO->query("SHOW TABLES LIKE 'client_users'");
        $hasClientUsersTable = $checkTable->rowCount() > 0;

        // 1. Primeiro insere o usuário
        if ($hasUserIdColumn) {
            // Usando a nova estrutura - usuário primeiro, depois cliente vinculado
            $sqlUser = "INSERT INTO users (nome, email, senha_hash, users_type_id, user_status, criado_em) 
                        VALUES (:nome, :email, :senha_hash, :users_type_id, :user_status, NOW())";

            $stmtUser = $PDO->prepare($sqlUser);
            $stmtUser->execute([
                ':nome' => $user['nome'],
                ':email' => $user['email'],
                ':senha_hash' => $user['senha_hash'],
                ':users_type_id' => $user['users_type_id'],
                ':user_status' => 1
            ]);

            $userId = $PDO->lastInsertId();

            // 2. Insere o cliente com referência ao usuário
            if ($cliente['tipo_pessoa'] === 'PJ') {
                $sqlClient = "INSERT INTO clients (
                                user_id, tipo_pessoa, nome_fantasia, razao_social, nome_completo, cnpj,
                                telefone_principal, email_principal, endereco, numero,
                                complemento, bairro, cidade, estado, cep, status, plano_id, created_at
                            ) VALUES (
                                :user_id, :tipo_pessoa, :nome_fantasia, :razao_social, :nome_completo, :cnpj,
                                :telefone_principal, :email_principal, :endereco, :numero,
                                :complemento, :bairro, :cidade, :estado, :cep, :status, :plano_id, :created_at
                            )";
                            
                $stmtClient = $PDO->prepare($sqlClient);
                $stmtClient->execute([
                    ':user_id' => $userId,
                    ':tipo_pessoa' => $cliente['tipo_pessoa'],
                    ':nome_fantasia' => $cliente['nome_fantasia'],
                    ':razao_social' => $cliente['razao_social'],
                    ':nome_completo' => $cliente['nome_completo'],
                    ':cnpj' => $cliente['cnpj'],
                    ':telefone_principal' => $cliente['telefone_principal'],
                    ':email_principal' => $cliente['email_principal'],
                    ':endereco' => $cliente['endereco'],
                    ':numero' => $cliente['numero'],
                    ':complemento' => $cliente['complemento'],
                    ':bairro' => $cliente['bairro'],
                    ':cidade' => $cliente['cidade'],
                    ':estado' => $cliente['estado'],
                    ':cep' => $cliente['cep'],
                    ':status' => $cliente['status'],
                    ':plano_id' => $cliente['plano_id'],
                    ':created_at' => $cliente['created_at']
                ]);
            } else { // PF
                $sqlClient = "INSERT INTO clients (
                                user_id, tipo_pessoa, nome_fantasia, nome_completo, cpf,
                                telefone_principal, email_principal, endereco, numero,
                                complemento, bairro, cidade, estado, cep, status, plano_id, created_at
                            ) VALUES (
                                :user_id, :tipo_pessoa, :nome_fantasia, :nome_completo, :cpf,
                                :telefone_principal, :email_principal, :endereco, :numero,
                                :complemento, :bairro, :cidade, :estado, :cep, :status, :plano_id, :created_at
                            )";
                            
                $stmtClient = $PDO->prepare($sqlClient);
                $stmtClient->execute([
                    ':user_id' => $userId,
                    ':tipo_pessoa' => $cliente['tipo_pessoa'],
                    ':nome_fantasia' => $cliente['nome_completo'], // Para PF, o nome fantasia é o nome completo
                    ':nome_completo' => $cliente['nome_completo'], // Para PF, preencher o nome completo
                    ':cpf' => $cliente['cpf'],
                    ':telefone_principal' => $cliente['telefone_principal'],
                    ':email_principal' => $cliente['email_principal'],
                    ':endereco' => $cliente['endereco'],
                    ':numero' => $cliente['numero'],
                    ':complemento' => $cliente['complemento'],
                    ':bairro' => $cliente['bairro'],
                    ':cidade' => $cliente['cidade'],
                    ':estado' => $cliente['estado'],
                    ':cep' => $cliente['cep'],
                    ':status' => $cliente['status'],
                    ':plano_id' => $cliente['plano_id'],
                    ':created_at' => $cliente['created_at']
                ]);
            }
        } else {
            // Se não há tabela client_users, mostrar uma mensagem de erro
            throw new Exception("Erro na execucão do processo.");
        }

        $clientId = $clientId ?? $PDO->lastInsertId();

        // 3. Cria a relação na tabela client_users se ela existir
        if ($hasClientUsersTable) {
            $sqlClientUser = "INSERT INTO client_users (client_id, user_id, status, created_at)
                                VALUES (:client_id, :user_id, 1, NOW())";
            
            $stmtClientUser = $PDO->prepare($sqlClientUser);
            $stmtClientUser->execute([
                ':client_id' => $clientId,
                ':user_id' => $userId
            ]);
        }

        $PDO->commit();

        return [
            'user_id' => $userId,
            'client_id' => $clientId
        ];
    } catch (Exception $e) {
        $PDO->rollBack();
        error_log("Database error: " . $e->getMessage());
        throw $e; // Repassar a exceção para ser tratada pelo chamador
    }
}

/**
 * Cria uma nova assinatura para o usuário
 * @param int $userId ID do usuário
 * @param int $clientId ID do cliente
 * @param int $planId ID do plano
 * @return int|false ID da assinatura criada ou false em caso de erro
 */
function createSubscription($userId, $clientId, $planId) {
    try {
        
        $PDO = db_connect();
        
        // Obter informações do plano
        $plan = getPlanById($planId);
        if (!$plan) {
            throw new Exception("Plano não encontrado.");
        }
        
        // Definir data de término com base na recorrência do plano
        $dataFim = null;
        if (isset($plan['recorrencia'])) {
            if ($plan['recorrencia'] === 'mensal') {
                $dataFim = date('Y-m-d H:i:s', strtotime('+1 month'));
            } else {
                $dataFim = date('Y-m-d H:i:s', strtotime('+1 year'));
            }
        } else {
            // Se não tiver recorrência, assumir 1 mês
            $dataFim = date('Y-m-d H:i:s', strtotime('+1 month'));
        }
        
        $sql = "";
        $params = [];
        
            // SQL com client_id
            $sql = "INSERT INTO subscriptions (user_id, client_id, plan_id, status, data_inicio, data_fim) 
                    VALUES (:user_id, :client_id, :plan_id, 'pendente', NOW(), :data_fim)";
            $params = [
                ':user_id' => $userId,
                ':client_id' => $clientId,
                ':plan_id' => $planId,
                ':data_fim' => $dataFim
            ];
        
        
        $stmt = $PDO->prepare($sql);
        $stmt->execute($params);
        
        return $PDO->lastInsertId();
    } catch (PDOException $e) {
        error_log("Database error in createSubscription: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log("Error in createSubscription: " . $e->getMessage());
        return false;
    }
}

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
            $item->title = $plan['nome_plano'];
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
            $planName = $plan['nome_plano'] . ' - ' . $plan['descricao'];
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

/**
 * Verifica se o usuário tem permissão para realizar determinada ação
 */
function userHasPermission($userId, $permissionName) {
    try {
        $PDO = db_connect();
        
        // Primeiro verifica as permissões do tipo de usuário
        $stmt = $PDO->prepare("
            SELECT p.id 
            FROM permissions p
            JOIN user_type_permissions utp ON p.id = utp.permission_id
            JOIN users u ON utp.users_type_id = u.users_type_id
            WHERE u.id = :userId AND p.nome = :permissionName
        ");
        
        $stmt->execute([
            'userId' => $userId,
            'permissionName' => $permissionName
        ]);
        
        $hasTypePermission = ($stmt->rowCount() > 0);
        
        // Verifica se há exceções específicas para o usuário
        $stmt = $PDO->prepare("
            SELECT granted 
            FROM user_permissions 
            WHERE user_id = :userId AND permission_id = (
                SELECT id FROM permissions WHERE nome = :permissionName
            )
        ");
        
        $stmt->execute([
            'userId' => $userId,
            'permissionName' => $permissionName
        ]);
        
        $userPermission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userPermission) {
            return (bool)$userPermission['granted'];
        }
        
        return $hasTypePermission;
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar permissão: " . $e->getMessage());
        return false;
    }
}

/**
 * Exibe mensagem flash e a remove da sessão
 * @param string $tipo Tipo de mensagem (success, error, warning, info)
 * @return string HTML da mensagem ou string vazia se não houver mensagem
 */
function exibeMensagemFlash($tipo) {
    $variavelSessao = "{$tipo}_message";
    
    if (isset($_SESSION[$variavelSessao])) {
        $mensagem = $_SESSION[$variavelSessao];
        unset($_SESSION[$variavelSessao]);
        
        $classeCss = match($tipo) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            default => 'alert-info'
        };
        
        return "<div class='alert {$classeCss} alert-dismissible fade show' role='alert'>
                    {$mensagem}
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
    }
    
    return '';
}

/**
 * Formata um valor monetário para o padrão brasileiro
 * @param float $valor Valor a ser formatado
 * @return string Valor formatado
 */
function formataMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}