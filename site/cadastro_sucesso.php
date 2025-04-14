<?php
// Inclui o arquivo de inicialização
require 'functions/globals.php';

// Inicia a sessão para garantir que as mensagens sejam mantidas
session_start();

// Verifica se existe mensagem de sucesso
if (!isset($_SESSION['success_message'])) {
    // Se não houver mensagem de sucesso, redireciona para a página inicial
    header("Location: index.php");
    exit;
}

// Armazena a mensagem e limpa da sessão
$success_message = $_SESSION['success_message'];
unset($_SESSION['success_message']);

// Conecta ao banco de dados para buscar os detalhes do plano gratuito
$PDO = db_connect();
$query = "SELECT * FROM plans WHERE preco = 0 LIMIT 1";
$stmt = $PDO->prepare($query);
$stmt->execute();
$plano = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html class="wide wow-animation" lang="pt-br">

<head>
    <title>CheckZone - Vistoria Online | Cadastro Realizado</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <link rel="stylesheet" type="text/css"
        href="//fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,300;8..144,500" display="swap" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/fonts.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
    .success-card {
        border-radius: 10px;
        border: 3px solid #28a745;
        padding: 30px;
        transition: 0.3s;
        background: #fff;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .success-icon {
        font-size: 60px;
        color: #28a745;
        margin-bottom: 20px;
    }

    .text-success {
        color: #28a745 !important;
    }

    .features-list {
        margin-top: 20px;
        text-align: left;
    }

    .features-list li {
        margin-bottom: 10px;
    }

    .btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff !important;
        padding: 10px 20px;
        font-size: 16px;
    }

    .btn-success:hover {
        background-color: #218838 !important;
        border-color: #218838 !important;
    }

    .countdown {
        font-size: 16px;
        color: #666;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="preloader-text">
            <span class="preloader-char">carregando</span>
        </div>
    </div>
    <div class="page">

        <!-- HEADER -->
        <?php require_once('header.php'); ?>
        <!-- HEADER -->

        <section class="section section-xs novi-background bg-primary">
            <div class="container">
                <div class="page-title">
                    <h1 class="h2">Cadastro Realizado</h1>
                </div>
            </div>
        </section>
        <section class="section section-xs bg-default">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="success-card text-center">
                            <i class="fa-solid fa-circle-check success-icon"></i>
                            <h2 class="text-success">Parabéns!</h2>
                            <p class="lead"><?= htmlspecialchars($success_message) ?></p>

                            <?php if($plano): ?>
                            <div class="card pricing-card border-primary shadow-lg text-center mt-4">
                                <div class="card-header bg-light">
                                    <h4 class="card-title"><?= htmlspecialchars($plano['nome_plano']) ?></h4>
                                    <span
                                        class="badge bg-<?= $plano['badge_color'] ?>"><?= htmlspecialchars($plano['badge_label']) ?></span>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-price">
                                        <strong>Gratuito</strong>
                                    </h3>
                                    <small class="text-muted"><?= htmlspecialchars($plano['validade']) ?></small>

                                    <ul class="list-unstyled features-list mt-3">
                                        <?php if($plano['cnpj']): ?>
                                        <li><i class="fa-solid fa-shield-halved text-primary"></i> CNPJ</li>
                                        <?php endif; ?>
                                        <li><i class="fa-solid fa-shield-halved text-primary"></i>
                                            <?= htmlspecialchars($plano['checklists']) ?> Checklists</li>
                                        <li><i class="fa-solid fa-shield-halved text-primary"></i>
                                            <?= htmlspecialchars($plano['inspetores']) ?> Inspetores</li>
                                        <?php if($plano['upload_fotos']): ?>
                                        <li><i class="fa-solid fa-shield-halved text-primary"></i> Upload de fotos</li>
                                        <?php endif; ?>
                                        <?php if($plano['upload_videos']): ?>
                                        <li><i class="fa-solid fa-shield-halved text-primary"></i> Upload de vídeos</li>
                                        <?php endif; ?>
                                        <?php if($plano['alarme']): ?>
                                        <li><i class="fa-solid fa-shield-halved text-primary"></i> Alarme</li>
                                        <?php endif; ?>
                                        <?php if($plano['gera_relatorios']): ?>
                                        <li><i class="fa-solid fa-shield-halved text-primary"></i> Gera relatórios</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mt-4">
                                <p>Você já pode começar a usar nossa plataforma!</p>
                                <a href="dashboard.php" class="btn btn-success">Acessar Meu Painel</a>
                            </div>

                            <div class="countdown mt-4">
                                <p>Redirecionando para o seu painel em <span id="contador">10</span> segundos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <?php require_once('footer.php');?>
        <!-- FOOTER -->
    </div>
    <div class="snackbars" id="form-output-global"></div>
    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>

    <script>
    // Script para redirecionar após contagem regressiva
    document.addEventListener("DOMContentLoaded", function() {
        let segundos = 30;
        const contadorElement = document.getElementById('contador');

        const intervalo = setInterval(function() {
            segundos--;
            contadorElement.textContent = segundos;

            if (segundos <= 0) {
                clearInterval(intervalo);
                window.location.href = 'dashboard.php';
            }
        }, 1000);
    });
    </script>
</body>

</html>