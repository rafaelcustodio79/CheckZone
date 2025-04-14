<?php
require '../vendor/autoload.php'; // Inclua o autoloader do Composer

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

// Crie uma nova instância do GoogleAuthenticator
$g = new GoogleAuthenticator();

// Gerar uma chave secreta (essa chave deve ser armazenada no banco de dados para cada usuário)
$secret = $g->generateSecret();
echo $secret;
