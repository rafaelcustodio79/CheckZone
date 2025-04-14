<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$_SESSION['etapa'] = 5;

header('Location: ../frete.php?a=frete&b=frete_cotacao&flag=success&tip=Carga cadastrada com sucesso!');