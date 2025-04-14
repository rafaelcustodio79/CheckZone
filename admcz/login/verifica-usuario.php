<?php
/**
 * Script de autenticação - ChecZone
 * 
 * Este script verifica as credenciais do usuário e configura a sessão
 * com base no tipo de usuário e suas permissões.
 */

// Inicia a sessão antes de qualquer saída HTML
session_start();

// Inclui o arquivo de inicialização
require_once '../functions/globals.php';
require_once '../functions/security.php';

// Proteção contra CSRF
if (!isset($_POST['csrf_token']) || !verificaCSRFToken($_POST['csrf_token'])) {
    registrarTentativaAcesso('Tentativa de login sem token CSRF válido', $_SERVER['REMOTE_ADDR']);
    header('Location: index.php?flag=erro&tip=Erro de segurança. Por favor, tente novamente.');
    exit;
}

// Proteção contra ataques de força bruta
if (verificaLimiteTentativas($_SERVER['REMOTE_ADDR'])) {
    header('Location: index.php?flag=erro&tip=Muitas tentativas de login. Tente novamente em 15 minutos.');
    exit;
}

// Verifica o captcha
if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
    registrarTentativaAcesso('Falha no captcha', $_SERVER['REMOTE_ADDR']);
    header('Location: index.php?flag=erro&tip=Por favor, confirme o captcha!');
    exit;
}

// Configura a chave secreta do reCAPTCHA
$recaptcha_secret = RECAPTCHA_SECRET_KEY;

// Verifica se o captcha é válido
$recaptcha_response = $_POST['g-recaptcha-response'];
$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
$data = [
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result_json = file_get_contents($verify_url, false, $context);
$result = json_decode($result_json);

if (!$result->success) {
    header('Location: index.php?flag=erro&tip=Falha na verificação do captcha. Tente novamente.');
    exit;
}

// Resgata variáveis do formulário com proteção contra injeção
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

// Validação básica dos dados
if (empty($email) || empty($senha) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    incrementarTentativaLogin($_SERVER['REMOTE_ADDR']);
    header('Location: index.php?flag=erro&tip=Preencha todos os campos corretamente!');
    exit;
}

try {
    $PDO = db_connect();
    
    // Consulta SQL segura usando consultas preparadas para buscar o usuário
    $sql = "SELECT u.id, u.nome, u.email, u.senha_hash, u.users_type_id, u.admin_id, 
                    u.master_id, u.client_id, u.user_status, u.user_photo, u.google_2fa_secret, u.criado_em
            FROM users u
            LEFT JOIN users_type ut ON u.users_type_id = ut.id
            WHERE u.email = :email AND u.user_status = 1";
    
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe e se a senha está correta
    if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
        incrementarTentativaLogin($_SERVER['REMOTE_ADDR'], $email);
        registrarTentativaAcesso('Credenciais inválidas', $_SERVER['REMOTE_ADDR'], $email);
        header('Location: index.php?flag=erro&tip=Usuário ou senha inválidos!');
        exit;
    }

    // Verifica se o usuário está ativo (user_status = 1)
    if ($usuario['user_status'] != 1) {
        registrarTentativaAcesso('Tentativa de login em conta inativa', $_SERVER['REMOTE_ADDR'], $email);
        header('Location: index.php?flag=warning&tip=Usuário inativo. Entre em contato com o Administrador do sistema!');
        exit;
    }

    // Reset contador de tentativas após login bem-sucedido
    resetarTentativasLogin($_SERVER['REMOTE_ADDR'], $email);
    
    // Inicializa a sessão com informações básicas do usuário
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['user_name'] = $usuario['nome'];
    $_SESSION['user_email'] = $usuario['email'];
    $_SESSION['users_type_id'] = $usuario['users_type_id'];
    $_SESSION['user_type'] = $usuario['tipo_usuario'] ?? '';
    $_SESSION['user_photo'] = $usuario['user_photo'] ?? 'default.png';
    $_SESSION['ultimo_acesso'] = time();
    $_SESSION['ip_login'] = $_SERVER['REMOTE_ADDR'];
    
    // Carrega as permissões do usuário
    $user_id = $usuario['id'];
    
    // Busca permissões do tipo de usuário (da tabela user_type_permissions)
    $sql = "SELECT p.nome 
            FROM permissions p
            INNER JOIN user_type_permissions utp ON p.id = utp.permission_id
            WHERE utp.users_type_id = :users_type_id";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':users_type_id', $usuario['users_type_id']);
    $stmt->execute();
    
    $permissoes = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $permissoes[] = $row['nome'];
    }
    
    $_SESSION['permissoes'] = $permissoes;
    
    // Configura a sessão baseada no tipo de usuário
    $user_id = $usuario['id'];
    $user_type_id = $usuario['users_type_id'];
    
    // Configurações base da sessão
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $usuario['nome'];
    $_SESSION['user_email'] = $usuario['email'];
    $_SESSION['users_type_id'] = $user_type_id;
    $_SESSION['user_type'] = $usuario['tipo_usuario'] ?? '';
    $_SESSION['user_photo'] = $usuario['user_photo'] ?? 'default.png';
    $_SESSION['permissoes'] = $permissoes;
    $_SESSION['ultimo_acesso'] = time();
    $_SESSION['ip_login'] = $_SERVER['REMOTE_ADDR'];
    
    // Configurações específicas baseadas no tipo de usuário
    switch ($user_type_id) {
        case TIPO_SUPER:
        case TIPO_MASTER:
            // Verificar se é admin ou master ligado a um admin
            if ($usuario['admin_id']) {
                $sql = "SELECT id, nome_fantasia, logo, status 
                        FROM admin 
                        WHERE id = :admin_id AND status = 1";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':admin_id', $usuario['admin_id']);
                $stmt->execute();
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['company_name'] = $admin['nome_fantasia'];
                    $_SESSION['company_logo'] = $admin['logo'] ?? 'default_logo.png';
                }
            }
            break;
            
        case TIPO_GERENTE:
        case TIPO_INSPETOR:
        case TIPO_TECNICO:
        case TIPO_AUDITOR:
        case TIPO_FINANCEIRO:
            // Verificar associação a cliente
            if ($usuario['client_id']) {
                $sql = "SELECT id, nome_fantasia, logo, status 
                        FROM clients 
                        WHERE id = :client_id AND status = 1";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':client_id', $usuario['client_id']);
                $stmt->execute();
                $client = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($client) {
                    $_SESSION['client_id'] = $client['id'];
                    $_SESSION['company_name'] = $client['nome_fantasia'];
                    $_SESSION['company_logo'] = $client['logo'] ?? 'default_logo.png';
                }
            }
            
            // Verificar associação a master
            if ($usuario['master_id']) {
                $sql = "SELECT id, nome_fantasia, admin_id 
                        FROM admin 
                        WHERE id = :master_id AND status = 1";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':master_id', $usuario['master_id']);
                $stmt->execute();
                $master = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($master) {
                    $_SESSION['master_id'] = $master['id'];
                    $_SESSION['master_name'] = $master['nome_fantasia'];
                    if ($master['admin_id']) {
                        $_SESSION['admin_id'] = $master['admin_id'];
                    }
                }
            }
            break;
    }
    
    // Atualiza último login
    $sql = "UPDATE users SET ultimo_login = NOW() WHERE id = :id";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    
    // Registra login bem-sucedido no log
    registrarAcesso($user_id, 'Login bem-sucedido', $_SERVER['REMOTE_ADDR']);
    
    // Verifica se há necessidade de autenticação de dois fatores
    //if (verificaNecessidade2FA($user_id, $PDO)) {
    //    header('Location: verificacao_2fatores.php');
    //    exit;
    //}
    
    // Redireciona para a página inicial
    header('Location: ../index.php?flag=success&tip=Login realizado com sucesso.');
    exit;
    
} catch (PDOException $e) {
    // Registra erro no log do sistema (não mostrar detalhes ao usuário)
    logErro('Erro no processo de login: ' . $e->getMessage());
    var_dump($e->getMessage());
    die;
    header('Location: index.php?flag=erro&tip=Erro interno do sistema. Tente novamente mais tarde.');
    exit;
}
?>