<?php
// Inclui o arquivo de inicialização
require 'functions/globals.php';

// Inicia a sessão para garantir que as mensagens sejam mantidas
session_start();

$PDO = db_connect();

// Consulta os planos ordenando por destaque primeiro
$query = "SELECT * FROM plans ORDER BY destaque DESC, preco ASC";
$stmt = $PDO->prepare($query);
$stmt->execute();
$plans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html class="wide wow-animation" lang="pt-br">

<head>
    <title>CheckZone - Vistoria Online | Planos</title>
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
    .pricing-card {
        border-radius: 10px;
        border: 2px solid #ddd;
        padding: 15px;
        transition: 0.3s;
        background: #fff;
    }

    h4+*,
    .h4+* {
        margin-top: 0px !important;
    }

    .pricing-card:hover {
        transform: scale(1.05);
    }

    .border-primary {
        border: 3px solid #007bff !important;
    }

    .border-secondary {
        border: 2px solid #ccc !important;
    }

    .text-primary {
        color: #007bff !important;
    }

    .text-secondary {
        color: #666 !important;
    }

    .text-muted {
        color: #bfbfbf !important;
    }

    .card-header {
        font-size: 1.5rem;
        font-weight: bold;
        padding: 5px;
        text-transform: uppercase;
    }

    .badge {
        font-size: 1rem;
        padding: 5px 10px;
    }

    .bg-green {
        background-color: #28a745 !important;
        /* Verde para "Teste 30 dias" */
    }

    .bg-blue {
        background-color: #007bff !important;
        /* Azul para "+ Completo" */
    }

    .bg-gray {
        background-color: #6c757d !important;
        /* Cinza para "Básico" */
    }

    .bg-dark-gray {
        background-color: #333 !important;
        /* Cinza para "Básico" */
    }

    .card-price {
        font-size: 2.1rem;
        font-weight: bold;
    }

    .btn-block {
        width: 100%;
    }

    .btn-primary {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: #fff !important;
    }

    .btn-primary:hover {
        background-color: #013164 !important;
        border-color: #013164 !important;
        color: #fff !important;
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
                    <h1 class="h2">Planos</h1>
                </div>
            </div>
        </section>
        <section class="section section-xs bg-default">
            <div class="container">
                <div class="row justify-content-center justify-content-xl-start text-center text-xl-start">
                    <div class="col-md-10 col-xl-8 col-xxl-7">
                        <h2>Quer máxima agilidade nas suas vistorias? Conheça os nossos planos.</h2>
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-lg-10 col-xl-12">
                        <div class="row row-20 row-md-40">

                            <div class="row justify-content-center">
                                <?php foreach ($plans as $plan): ?>
                                <?php 
                                    // Se for destaque, estilo azul, senão estilo cinza
                                    $cardBorder = ($plan['destaque'] == 1) ? 'border-primary shadow-lg' : 'border-secondary';
                                    $buttonClass = ($plan['destaque'] == 1) ? 'btn-primary' : 'btn-secondary';
                                    $iconColor = ($plan['destaque'] == 1) ? 'text-primary' : 'text-secondary';
                                ?>
                                <div class="col-md-3 mb-4">
                                    <div class="card pricing-card <?= $cardBorder ?> text-center">
                                        <div class="card-header bg-light">
                                            <h4 class="card-title"><?= htmlspecialchars($plan['nome_plano']) ?></h4>
                                            <span
                                                class="badge bg-<?= $plan['badge_color'] ?>"><?= htmlspecialchars($plan['badge_label']) ?></span>
                                        </div>
                                        <div class="card-body" style="text-align: left;">
                                            <ul class="list-unstyled mt-3 mb-4">
                                                <li>
                                                    <i
                                                        class="fa-solid fa-shield-halved <?= ($plan['cnpj']) ? $iconColor : 'text-muted' ?>"></i>
                                                    CNPJ
                                                </li>
                                                <li>
                                                    <i
                                                        class="fa-solid fa-shield-halved <?= ($plan['checklists']) ? $iconColor : 'text-muted' ?>"></i>
                                                    <?= htmlspecialchars($plan['checklists']) ?> Checklists
                                                </li>
                                                <li>
                                                    <i
                                                        class="fa-solid fa-shield-halved <?= ($plan['inspetores']) ? $iconColor : 'text-muted' ?>"></i>
                                                    <?= htmlspecialchars($plan['inspetores']) ?> Inspetores
                                                </li>
                                                <li class="<?= ($plan['upload_fotos']) ? '' : 'text-muted' ?>">
                                                    <i
                                                        class="fa-solid fa-shield-halved <?= ($plan['upload_fotos']) ? $iconColor : 'text-muted' ?>"></i>
                                                    Upload
                                                    de fotos
                                                </li>
                                                <li class="<?= ($plan['upload_videos']) ? '' : 'text-muted' ?>">
                                                    <i
                                                        class="fa-solid fa-shield-halved <?= ($plan['upload_videos']) ? $iconColor : 'text-muted' ?>"></i>
                                                    Upload
                                                    de vídeos
                                                </li>
                                                <li class="<?= ($plan['alarme']) ? '' : 'text-muted' ?>">
                                                    <i
                                                        class="fa-solid fa-shield-halved <?= ($plan['alarme']) ? $iconColor : 'text-muted' ?>"></i>
                                                    Alarme
                                                </li>
                                                <li class="<?= ($plan['gera_relatorios']) ? '' : 'text-muted' ?>">
                                                    <i
                                                        class="fa-solid fa-shield-halved <?= ($plan['gera_relatorios']) ? $iconColor : 'text-muted' ?>"></i>
                                                    Gera
                                                    relatórios
                                                </li>
                                            </ul>
                                            <h3 class="card-price">
                                                <?= ($plan['preco'] == 0) ? '<strong>Gratuito</strong>' : 'R$ ' . number_format($plan['preco'], 2, ',', '.') . ' /mês' ?>
                                            </h3>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($plan['validade']) ?></small>
                                            <div class="mt-3 text-center">
                                                <a href="contratar.php?plano_id=<?= $plan['id'] ?>"
                                                    class="btn <?= $buttonClass ?> btn-block">Quero contratar</a>
                                                <a href="#" class="small">Saber mais detalhes</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
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

</body>

</html>