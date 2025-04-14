<?php
session_start();

unset($_SESSION['etapa_atual']); // Remove a variável de sessão
unset($_SESSION['active_step']); // Remove a variável de sessão

header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_criar');
exit();