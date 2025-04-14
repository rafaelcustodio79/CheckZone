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
    $token = $_POST['token'];
    $password = md5($_POST['password']);

    // Verifica se o token é válido
    $stmt = $PDO->prepare("SELECT * FROM password_resets WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $reset = $stmt->fetch();

    if ($reset) {
        $email = $reset['email'];

        // Atualiza a senha do usuário
        $stmt = $PDO->prepare("UPDATE usuario SET senha = :senha WHERE email = :email");
        $stmt->execute(['senha' => $password, 'email' => $email]);

        // Remove o token de recuperação
        $stmt = $PDO->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);

        header('Location: index.php?flag=success&tip=Contraseña cambiada correctamente.');
    } else {
        header('Location: index.php?flag=warning&tip=Operação não realizada!');
    }
}
