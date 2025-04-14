<?php
session_start();
require '../functions/globals.php';
require '../vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$PDO = db_connect();
$user_id = $_SESSION['user_id']; // ID do usuário logado

// Busca o secret do banco de dados
$sql = "SELECT google_2fa_secret FROM users WHERE id = :id";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não houver secret, redireciona para configuração do 2FA
if (empty($user['google_2fa_secret'])) {
    header('Location: configura-2fa.php');
    exit;
}

// Captura o código inserido pelo usuário
$codigoInserido = $_POST['2fa_code'];

// Valida o código usando a biblioteca Google Authenticator
$g = new GoogleAuthenticator();
if ($g->checkCode($user['google_2fa_secret'], $codigoInserido)) {
    // Código 2FA válido, permite o login
    $_SESSION['2fa_verified'] = true;
    header('Location: ../index.php?flag=success&tip=Login realizado com sucesso.');
exit;
} else {
    // Código 2FA inválido
    header('Location: verificacao_2fatores.php?flag=erro&tip=Código 2FA inválido!');
    exit;
}