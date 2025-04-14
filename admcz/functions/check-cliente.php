<?php
function checkCliente()
{
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 2) {
        // inicia a sessão
        session_start();

        // muda o valor de logged_in para false
        $_SESSION['logged_in'] = false;
        
        // finaliza a sessão
        session_destroy();
        
        // redireciona para a página de login
        header("Location: login/index.php?flag=erro&tip=Faça o LOGIN para acessar essa página!"); // Redireciona para a página de login se não for administrador
        exit;
    }
}