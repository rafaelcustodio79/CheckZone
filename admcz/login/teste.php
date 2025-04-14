<?php
$to = 'orion5.suporte@gmail.com';
$subject = 'Teste de Email';
$message = 'Este é um teste para verificar a função mail() do PHP.';
$headers = 'From: webmaster@seusite.com' . "\r\n" .
           'Reply-To: webmaster@seusite.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo 'Email enviado com sucesso.';
} else {
    echo 'Falha ao enviar email.';
}
