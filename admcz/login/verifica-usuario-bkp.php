<?php
// inclui o arquivo de inicialização
require '../functions/globals.php';

// Verifica o captcha
if (isset($_POST['g-recaptcha-response'])) {
    $captcha_data = $_POST['g-recaptcha-response'];
}

// Se nenhum valor foi recebido, o usuário não realizou o captcha
if (!$captcha_data) {
    header('Location: index.php?flag=erro&tip=Por favor, confirma o captcha!');
    exit;
}

// resgata variáveis do formulário
$email = isset($_POST['email']) ? $_POST['email'] : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';
//$hash = $senha;

$PDO = db_connect();

$sql = "SELECT * FROM usuario WHERE email = :email AND senha = :senha";
$stmt = $PDO->prepare($sql);

$stmt->bindParam(':email', $email);
$stmt->bindValue(':senha', md5($senha));
$stmt->execute();

$dadosUsuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($dadosUsuario) <= 0) {
    header('Location: index.php?flag=erro&tip=Usuário ou senha inválidos!');
} else {
    if ($dadosUsuario[0]['ativo'] == 0) {
        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $dadosUsuario[0]['id'];
        $_SESSION['empresa_id'] = $dadosUsuario[0]['id_empresa'];
        $_SESSION['tipo_usuario'] = $dadosUsuario[0]['id_tipo_usuario'];

        // Cria um cookie
        setcookie('user_id', $dadosUsuario[0]["id"]);
        setcookie('email', $dadosUsuario[0]["email"]);
        setcookie('senha', $dadosUsuario[0]["senha"]);

        header('Location: ../index.php');
    } else {
        header('Location: index.php?flag=warning&tip=Aguardando liberaçao de Demonstraçao!');
    }
}
