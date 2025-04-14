<?php
$cEmail = isset($_COOKIE['email']) ? $_COOKIE['email'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSV | Gerenciador de Conteúdo | Redefinir Senha</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="../plugins/toastr/toastr.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../css/adminlte.css">

    <!-- Estilos -->
    <style type="text/css">
    .recaptcha-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: inherit;
    }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="../index.php"><img src="../img/logo-padrao.png" style="max-width: 350px;"></a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Ingresa tu nueva contraseña.</p>

                <form action="atualizar_senha.php" method="post">
                    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="nueva contraseña">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Redefinir Senha</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../js/adminlte.min.js"></script>
    <!-- Google Recaptcha -->
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
    <!-- Toastr -->
    <script src="../plugins/toastr/toastr.min.js"></script>

    <script>
    var onloadCallback = function() {
        grecaptcha.render('html_element', {
            'sitekey': '6LcT50QpAAAAADa8j77FK3XK1E0vnfLYtyDWhHdK'
        });
    };

    // Execute o código ao carregar a página
    $(document).ready(function() {
        // Verifique se o parâmetro 'flag' está presente na URL
        var flagParam = '<?php echo isset($_GET['flag']) ? $_GET['flag'] : ''; ?>';
        var tipParam = '<?php echo isset($_GET['tip']) ? $_GET['tip'] : ''; ?>';

        // Se 'flag' estiver presente e for igual a 'erro', exiba a notificação de erro
        if (flagParam === 'erro') {
            toastr.error(tipParam);
        }
        if (flagParam === 'warning') {
            toastr.warning(tipParam);
        }
    });
    </script>
</body>

</html>