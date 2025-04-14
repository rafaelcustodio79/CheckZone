<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CotSYS - Acesso</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="../framework/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../framework/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="../framework/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../framework/dist/css/AdminLTE.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../framework/plugins/iCheck/square/blue.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->

    <script type="text/javascript">
        var onloadCallback = function() {
            grecaptcha.render('html_element', {
                'sitekey': '6LfJ9iolAAAAAJlNpwTdT5LfclOPdq2c9cC1Cxku'
            });
        };
    </script>

    <style type="text/css">
        body {

            color: #000;
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
            background-image: url('../assets/img/fundo_login_luiza.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: bottom right;
        }

        .recaptcha-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .btn-entrar {
            font-family: "Montserrat", Sans-serif;
            font-size: 18px;
            font-weight: 500;
            color: #fff;
            letter-spacing: .6px;
            text-shadow: 0 0 19px rgba(0, 0, 0, 0);
            background-color: #9B1D16;
        }

        .btn-entrar:hover {
            background-color: #5773a2;
            color: #fff;
        }

        .caixa-login {
            width: 420px;
            border-radius: 10px;
            background-color: #ffafaa;
            padding: 30px;
            font-family: "Montserrat", Sans-serif;
            text-shadow: 0 0 19px rgba(0, 0, 0, 0);
            border-radius: 26px 26px 26px 26px;
        }
    </style>


</head>

<body class="hold-transition login-page">
    <div class="fundo-pagina"></div>
    <div class="row" style="padding-top: 30px;">
        <div class="col-lg-6 text-left">
            <div class="login-logo">
                <a href="../index.php"><img src="../assets/img/logos/sistema/logo-logix-b.png" style="max-width: 300px;"></a>
            </div>
            <!-- /.login-logo -->
        </div>
        <div class="col-lg-6 text-right" style="padding-top: 20px;padding-right:50px;">
            <button type="button" class="btn btn-danger" onclick="abrirRegistrar();"><i class="fa fa-user-plus" aria-hidden="true"></i> CADASTRE-SE</button>
        </div>
    </div>
    <div class="login-box caixa-login">
        <div>
            <img src="../assets/img/logos/logo-teste-login.png">

            <?php if (isset($_GET['flag']) && $_GET['flag'] == 'success') : ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Sucesso</h4>
                    <?= $_GET['tip']; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['flag']) && $_GET['flag'] == 'erro') : ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Erro</h4>
                    <?= $_GET['tip']; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['flag']) && $_GET['flag'] == 'warning') : ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-exclamation"></i> Atenç&atilde;o</h4>
                    <?= $_GET['tip']; ?>
                </div>
            <?php endif; ?>

            <form name="acesso" action="verifica-usuario.php" method="post">
                <div class="form-group has-feedback">
                    <input type="email" name="email" class="form-control input-lg" placeholder="Email" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="senha" class="form-control input-lg" placeholder="Senha" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback text-left">
                    <i class="fa fa-lightbulb-o" aria-hidden="true"></i> <a href="../esqueci/">Esqueci minha senha</a>
                </div>

                <div class="form-group recaptcha-container">
                    <div id="html_element" class="text-center"></div>
                </div>

                <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-12 text-center">
                        <button type="submit" class="btn btn-lg btn-entrar" style="color: 171c5a;">Entrar</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
    <p style="text-align:center;color:#fff;">Ainda não tem cadastro?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../registrar/" style="color: #ffafaf;">Cadastrar-se</a></p>

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
        });

        function abrirRegistrar() {
            window.location.href = "../registrar/";
        }
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
</body>


</html>
