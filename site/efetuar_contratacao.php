<?php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step']) && $_POST['step'] === 'registration') {
        
        $planId = $_POST['plano_id'] ?? null;
        
        if (!$planId) {
            $_SESSION['error_message'] = "Nenhum plano selecionado. Por favor, volte e selecione um plano.";
            header("Location: planos.php");
            exit;
        }
        
        // Get the plan details
        $plan = getPlanById($planId);
        
        if (!$plan) {
            $_SESSION['error_message'] = "Plano inválido. Por favor, volte e selecione um plano válido.";
            header("Location: planos.php");
            exit;
        }
        
        // Determine if the user is PJ or PF
        $tipoPessoa = strtoupper(clean_input($_POST['tipo_pessoa']));
        
        try {
            $PDO->beginTransaction();
            
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
                    'users_type_id' => 2 // MASTER
                ];
            } else { // PF
                $cliente['cpf'] = clean_input($_POST['cpf']);
                
                // Definir dados do usuário
                $user = [
                    'nome' => $cliente['nome_completo'],
                    'email' => $cliente['email_principal'],
                    'senha_hash' => password_hash(clean_input($_POST['senha_pf']), PASSWORD_DEFAULT),
                    'users_type_id' => 2 // MASTER
                ];
            }
            
            // Verificações adicionais antes de registrar
            // Verificar email único
            if (isEmailRegistered($user['email'])) {
                throw new Exception("O e-mail informado já está cadastrado. Se esqueceu sua senha, tente recuperá-la.");
            }
            
            // Verificar CPF/CNPJ único
            if ($tipoPessoa === 'PJ' && isCNPJRegistered($cliente['cnpj'])) {
                throw new Exception("O CNPJ informado já está cadastrado.");
            } elseif ($tipoPessoa === 'PF' && isCPFRegistered($cliente['cpf'])) {
                throw new Exception("O CPF informado já está cadastrado.");
            }
            
            // Register the user and cliente (PJ or PF)
            $result = registerUserAndClient($user, $cliente);
            
            if ($result) {
                // Create a subscription
                try {
                    $subscriptionResult = createSubscription($result['user_id'], $result['client_id'], $planId);
                    
                    if (!$subscriptionResult) {
                        throw new Exception("Não foi possível criar a assinatura. Verifique o log para mais detalhes.");
                    }
                    
                    // Store user/client info in session (for later use)
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['client_id'] = $result['client_id'];
                    $_SESSION['subscription_id'] = $subscriptionResult;
                    
                    // Create MercadoPago payment
                    $mpPayment = createMercadoPagoPayment($result['user_id'], $result['client_id'], $plan, $subscriptionResult);
                    
                    if (!$mpPayment) {
                        throw new Exception("Erro ao processar o pagamento. Por favor, tente novamente mais tarde.");
                    }
                    
                    // Se for plano gratuito (teste)
                    if ($plan['preco'] <= 0) {
                        // Ativar a assinatura imediatamente
                        updateSubscriptionStatus($subscriptionResult, 'ativa');
                        
                        // Criar um registro de pagamento zerado para histórico
                        recordPayment($subscriptionResult, 'FREE_TRIAL', 0, 'aprovado');
                        
                        // Logar o usuário
                        $_SESSION['logged_in'] = true;
                        
                        // Redirecionar para a página de sucesso
                        $_SESSION['success_message'] = "Cadastro realizado com sucesso! Sua assinatura gratuita está ativa.";
                        header("Location: cadastro_sucesso.php");
                        exit;
                    } else {
                        // Redirecionar para o MercadoPago para pagamento
                        $PDO->commit();
                        header("Location: " . $mpPayment->init_point);
                        exit;
                    }
                } catch (Exception $e) {
                    $PDO->rollBack();
                    error_log("Error in subscription process: " . $e->getMessage());
                    $_SESSION['error_message'] = "Erro ao criar assinatura: " . $e->getMessage();
                    var_dump($_SESSION['error_message']);
                    die;
                    header("Location: contratar.php?plano_id=" . $planId);
                    exit;
                }
            } else {
                throw new Exception("Erro ao cadastrar usuário. Por favor, tente novamente mais tarde.");
            }
            
            $PDO->commit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $PDO->rollBack();
            header("Location: contratar.php?plano_id=" . $planId);
            exit;
        }
    } else {
        // Acesso direto sem parâmetros corretos
        header("Location: planos.php");
        exit;
    }
} else {
    // Acesso direto sem POST
    header("Location: planos.php");
    exit;
}
?>