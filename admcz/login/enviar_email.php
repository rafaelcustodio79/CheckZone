<?php
// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

// Verifica o captcha
if (isset($_POST['g-recaptcha-response'])) {
    $captcha_data = $_POST['g-recaptcha-response'];
}

// Se nenhum valor foi recebido, o usuário não realizou o captcha
if (!$captcha_data) {
    header('Location: index.php?flag=erro&tip=Por favor, confirma o captcha!');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Verifica se o email existe na tabela de usuários
    $stmt = $PDO->prepare("SELECT * FROM usuario WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Gera um token de recuperação
        $token = bin2hex(random_bytes(50));

        // Armazena o token no banco de dados
        $stmt = $PDO->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        $stmt->execute(['email' => $email, 'token' => $token]);

        // Envia um email com o link de redefinição de senha
        $resetLink = "http://www.voleysur.web10f82.kinghost.net/gercont/login/redefinir-senha.php?token=" . $token;
        $message = "Clique no link para redefinir sua senha: " . $resetLink;

        // Use a função mail() ou uma biblioteca de envio de email
        mail($email, "Redefinição de senha", $message);

        header('Location: index.php?flag=success&tip=Um link de redefinição de senha foi enviado para seu email.');
    } else {
        header('Location: index.php?flag=warning&tip=Email não encontrado!');
    }
}
