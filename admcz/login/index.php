<?php
// Inicia a sessão para manter o token CSRF
session_start();

// Inclui funções de segurança
require_once '../functions/security.php';
require_once '../functions/globals.php';

// Gera um token CSRF para o formulário
$csrf_token = gerarCSRFToken();

// Recupera mensagens de erro ou sucesso
$flag = isset($_GET['flag']) ? $_GET['flag'] : '';
$tip = isset($_GET['tip']) ? $_GET['tip'] : '';

// Carrega configurações do sistema
$recaptcha_site_key = RECAPTCHA_SITE_KEY;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CheckZone - Acesso</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="../favicon.png" type="image/png" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="../framework/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="../framework/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../framework/dist/css/AdminLTE.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../framework/plugins/iCheck/square/blue.css">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <script type="text/javascript">
    var onloadCallback = function() {
        grecaptcha.render('html_element', {
            'sitekey': '<?php echo $recaptcha_site_key; ?>',
            'size': 'normal'
        });
    };
    </script>

    <style type="text/css">
    body {
        color: #aaabad;
        font-family: "Poppins", Sans-serif;
        letter-spacing: .6px;
    }

    .fundo-pagina {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        z-index: -1;
        background-image: url('../img/bg-login.jpg');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: bottom left;
    }

    .recaptcha-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: auto;
    }

    .btn-entrar {
        font-family: "Montserrat", Sans-serif;
        color: #fff;
        letter-spacing: 1px;
        text-shadow: 0 0 19px rgba(0, 0, 0, 0);
        background-color: #0b298c;
    }

    .btn-entrar:hover {
        background-color: #0039ff;
        color: #fff;
    }

    h3 {
        letter-spacing: .8px;
        text-shadow: 0 0 19px rgba(0, 0, 0, 0);
        color: #0b298c;
        font-size: 24px;
        text-align: center;
        padding-bottom: 5px;
        font-weight: bold;
    }

    .caixa-login {
        width: 380px;
        padding-top: 5px;
        padding-left: 20px;
        padding-right: 20px;
        padding-bottom: 20px;
        font-family: "Montserrat", Sans-serif;
        border-radius: 26px;
        border: 3px solid #0b298c;
        background-color: rgba(200, 200, 200, 0.7);
    }

    .link-especial a {
        color: #0b298c;
        text-decoration: none;
    }

    .link-especial a:hover {
        color: #86bc25;
    }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="fundo-pagina"></div>
    <div class="row" style="padding-top: 30px;">
        <div class="col-lg-2 text-center">
            <div class="login-logo">
                <a href="../index.php"><img src="../img/logo_completa.png" style="max-height: 250px;"></a>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="login-box caixa-login">
                <div>
                    <h3>ÁREA ADMINISTRATIVA</h3>

                    <?php if (isset($flag) && $flag == 'success') : ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fas fa-check"></i> Sucesso</h4>
                        <?= htmlspecialchars($tip); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($flag) && $flag == 'erro') : ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fas fa-ban"></i> Erro</h4>
                        <?= htmlspecialchars($tip); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($flag) && $flag == 'warning') : ?>
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fas fa-exclamation"></i> Atenç&atilde;o</h4>
                        <?= htmlspecialchars($tip); ?>
                    </div>
                    <?php endif; ?>

                    <form id="loginForm" name="acesso" action="verifica-usuario.php" method="post">
                        <!-- Token CSRF oculto para proteção -->
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="form-group has-feedback">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" name="senha" class="form-control" placeholder="Senha" required>
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 text-center link-especial">
                                <a href="../esqueci/" title="Caso não se lembre da senha, clique aqui!"><i
                                        class="fas fa-lightbulb" aria-hidden="true"></i> Esqueci a senha</a>
                            </div>
                        </div>

                        <br clear="all">

                        <div class="form-group recaptcha-container">
                            <div id="html_element" class="text-center"></div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <button type="submit" class="btn btn-entrar"><i class="fas fa-sign-in-alt"></i>
                                    Acessar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-1 text-right">
            <a href="../../index.html" class="btn btn-entrar">
                <i class="fas fa-home"></i> Voltar ao site</a>
        </div>
    </div>

    <!-- jQuery 3 -->
    <script src="../framework/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="../framework/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="../framework/plugins/iCheck/icheck.min.js"></script>
    <script>
    $(function() {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });

        // Validação do formulário antes do envio
        $('#loginForm').on('submit', function(e) {
            // Verifica se o captcha foi preenchido
            if (grecaptcha.getResponse() === '') {
                e.preventDefault();
                alert('Por favor, confirme o captcha!');
                return false;
            }
            return true;
        });
    });
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
</body>

</html>