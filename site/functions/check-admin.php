<?php
function checkAdmin()
{
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 1) {
        // muda o valor de logged_in para false
        $_SESSION['logged_in'] = false;

        // finaliza a sessão
        session_destroy();

        // redireciona para a página de login
        header("Location: ../login/index.php?flag=erro&tip=Você não possui privilégios de Administrador para acessar a página!"); // Redireciona para a página de login se não for administrador
        exit;
    }
}
