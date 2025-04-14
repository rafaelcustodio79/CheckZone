<?php
session_start();
require '../functions/globals.php';
require '../vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$PDO = db_connect();
$user_id = $_SESSION['user_id']; // ID do usuário logado

// Busca o secret do banco de dados
$sql = "SELECT google_2fa_secret FROM users WHERE id = :id";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CheckZone | Sistema de checklist online | ADM | Verificação 2FA</title>

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
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="../index.php"><img src="../img/logo-padrao.png" style="max-width: 300px;"></a>
            </div>
            <div class="card-body">

                <?php
                // Se o secret estiver vazio, o usuário ainda não ativou o 2FA
                if (empty($user['google_2fa_secret'])) {
                    // Gerar a chave secreta
                    $g = new GoogleAuthenticator();
                    $secret = $g->generateSecret();

                    // Atualiza o secret no banco de dados
                    $sql = "UPDATE users SET google_2fa_secret = :secret_code WHERE id = :id";
                    $stmt = $PDO->prepare($sql);
                    $stmt->bindParam(':secret_code', $secret);
                    $stmt->bindParam(':id', $user_id);
                    $stmt->execute();

                    // Gerar o QR Code
                    $qrCodeUrl = GoogleQrUrl::generate('CheckZone', $secret);
                    ?>

                <!-- Exibe o QR Code para o usuário escanear -->
                <p class="login-box-msg">Escaneie o QR Code com o Google Authenticator:</p>
                <div class="text-center">
                    <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code Google Authenticator" class="img-fluid" />
                    <br>
                </div>

                <?php
                } else {
                    // Usuário já tem 2FA ativado, pedir o código
                    echo '<p class="login-box-msg">Insira o código gerado pelo Google Authenticator:</p>';
                }
?>

                <!-- Formulário para inserir o código 2FA -->
                <form action="verifica-2fa.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="2fa_code" class="form-control" placeholder="Código 2FA" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-key"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Verificar Código</button>
                        </div>
                    </div>
                </form>

                <!-- Botão de Logout -->
                <p class="mt-3">
                    <a href="logout.php" class="text-center">Sair</a>
                </p>
            </div>
        </div>
    </div>

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
    // Execute o código ao carregar a página
    $(document).ready(function() {
        // Verifique se o parâmetro 'flag' está presente na URL
        var flagParam =
            '<?php echo isset($_GET['flag']) ? $_GET['flag'] : ''; ?>';
        var tipParam =
            '<?php echo isset($_GET['tip']) ? $_GET['tip'] : ''; ?>';

        // Se 'flag' estiver presente e for igual a 'erro', exiba a notificação de erro
        if (flagParam === 'erro') {
            toastr.error(tipParam);
        }
        if (flagParam === 'warning') {
            toastr.warning(tipParam);
        }
        if (flagParam === 'success') {
            toastr.success(tipParam);
        }
    });
    </script>
</body>

</html>