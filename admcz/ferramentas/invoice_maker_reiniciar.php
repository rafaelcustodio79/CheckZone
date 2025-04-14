<?php
session_start();

unset($_SESSION['etapa_atual']); // Remove a variável de sessão
unset($_SESSION['active_step']); // Remove a variável de sessão

header('Location: ../ferramentas.php?a=ferramentas&b=invoice_maker_criar');
exit();