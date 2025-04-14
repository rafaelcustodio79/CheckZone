<?php
// Inclui o arquivo de inicialização
require '../functions/globals.php';

// Inicia a sessão para garantir que as mensagens sejam mantidas
session_start();

// Verifica o captcha
if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
    header('Location: index.php?flag=erro&tip=Por favor, confirme o captcha!');
    exit;
}

// Resgata variáveis do formulário com proteção contra XSS
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

// Validação básica dos dados
if (empty($email) || empty($senha)) {
    header('Location: index.php?flag=erro&tip=Preencha todos os campos!');
    exit;
}

$PDO = db_connect();

// Consulta SQL segura usando consultas preparadas
$sql = "SELECT id, email, senha_hash, users_type_id, admin_id, client_id, user_status FROM users 
        WHERE email = :email";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o usuário existe e se a senha está correta
if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    header('Location: index.php?flag=erro&tip=Usuário ou senha inválidos!');
    exit;
}
// Verifica se o usuário está ativo
if ($usuario['user_status'] != 1) {
    header('Location: index.php?flag=warning&tip=Usuário inativo. Entre em contato com o Administrador do sistema!');
    exit;
}

// Verifica se o usuário é ADMINISTRADOR ou CLIENTE
if($usuario['users_type_id'] == 1 && $usuario['admin_id'] == 1) {

    $user_id = $usuario['id'];

    $sql = "SELECT * FROM users u
            INNER JOIN admin a ON a.id = u.admin_id 
            WHERE u.id = :user_id";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Configura a sessão do usuário
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['user_name'] = $usuario['nome'];
    $_SESSION['users_type_id'] = $usuario['users_type_id'];
    $_SESSION['user_photo'] = $usuario['user_photo'];
    $_SESSION['company_id'] = $usuario['admin_id'];
    $_SESSION['company_name'] = $usuario['nome_fantasia'];
    $_SESSION['company_logo'] = $usuario['logo'];
    
    header('Location: ../index.php?flag=success&tip=Login realizado com sucesso.');
    //header('Location: verificacao_2fatores.php');
    exit;
    
} elseif($usuario['client_id'] != 0  && is_null($usuario['admin_id'])) {
    $user_id = $usuario['id'];

    $sql = "SELECT * FROM users u
            INNER JOIN clientes c ON u.client_id = c.id 
            WHERE u.id = :user_id";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    // Configura a sessão do usuário
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['user_name'] = $usuario['nome'];
    $_SESSION['users_type_id'] = $usuario['users_type_id'];
    $_SESSION['user_photo'] = $usuario['user_photo'];
    $_SESSION['company_id'] = $usuario['client_id'];
    $_SESSION['company_name'] = $usuario['nome_fantasia'];
    $_SESSION['company_logo'] = $usuario['logo'];

    header('Location: ../index.php?flag=success&tip=Login realizado com sucesso.');
    //header('Location: verificacao_2fatores.php');
    exit;
}