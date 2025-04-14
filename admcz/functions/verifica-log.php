
<?php
// Verifica se existe os dados da sessão de login 
if(isLoggedIn()==false) { 
    // Usuário não logado! Redireciona para a página de login 
    header("Location: login/"); 
    exit;
}
?>
