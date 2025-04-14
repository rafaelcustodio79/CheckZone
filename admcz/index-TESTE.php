<?php
// Inicia sessões
session_start();
require 'functions/globals.php';
require 'functions/verifica-log.php';

// pega os dados do usuario
$idUsuario = $_SESSION['user_id'];

$PDO = db_connect();
$sql = "SELECT * FROM usuario u
        INNER JOIN empresa emp ON u.id_empresa = emp.id_empresa
        LEFT JOIN banco b ON b.id_usuario = u.id
        WHERE u.id = :id
        ORDER BY b.data_mov DESC
        LIMIT 1";

$stmt = $PDO->prepare($sql);

$stmt->bindValue(':id', $idUsuario);
$stmt->execute();

$dadosUsuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>COMEX MANAGER - Sistema de Cota&ccedil;&atilde;o | Home</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- favicon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="framework/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="framework/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="framework/bower_components/Ionicons/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="framework/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="framework/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="framework/dist/css/skins/_all-skins.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="framework/bower_components/morris.js/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="framework/bower_components/jvectormap/jquery-jvectormap.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="framework/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="framework/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="framework/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
        rel="stylesheet">
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->

    <!-- favicons ================================================== -->
    <link href="../favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="../favicon.ico" rel="icon" type="image/x-icon">

    <style type="text/css">
        /* Estilo para o contêiner que envolve a imagem e o texto */
        .image-container {
            display: flex;
            /* Use flexbox para alinhamento vertical */
            align-items: center;
            /* Centraliza verticalmente os itens */
        }

        /* Estilo para a imagem */
        .image-container img {
            max-width: 25px;
            /* Largura máxima da imagem */
            margin-right: 10px;
            /* Espa&ccedil;amento à direita da imagem */
        }

        .menu-container a {
            display: block;
            padding: 8px;
            margin-bottom: 8px;
            background-color: #252f3e;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        .menu-container a.active,
        .menu-container a:hover {
            background-color: #ffbf32;
            color: #323b48;
        }

        .video-container {
            display: none;

            height: 100%;
        }

        .video-container.active {
            display: block;
        }

        .modal-dialog {
            width: 800px;
            margin: 30px auto;
        }
    </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- HEADER -->
        <?php require 'layout/header.php'; ?>
        <!-- FIM HEADER -->

        <!-- SIDEBAR -->
        <?php
        switch ($dadosUsuario[0]['id_tipo_usuario']) {
            case 1:
                $sideBar = 'layout/sidebar_adm.php';
                break;
            case 2:
                $sideBar = 'layout/sidebar_cliente.php';
                break;
            case 3:
                $sideBar = 'layout/sidebar_agente.php';
                break;
        }
require $sideBar;
?>
        <!-- FIM SIDEBAR -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- PAGINA INICIAL -->
            <?php
    switch ($dadosUsuario[0]['id_tipo_usuario']) {
        case 1:
            $homePage = 'adm/index.php';
            break;
        case 2:
            $homePage = 'cliente/index.php';
            break;
        case 3:
            $homePage = 'agente/index.php';
            break;
    }
require $homePage;
?>
            <!-- FIM PAGINA INICIAL -->
        </div>
        <!-- /.content-wrapper -->

        <!-- FOOTER -->
        <?php require 'layout/footer.php'; ?>
        <!-- FIM FOOTER -->



    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="framework/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="framework/bower_components/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.7 -->
    <script src="framework/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Morris.js charts -->
    <script src="framework/bower_components/raphael/raphael.min.js"></script>
    <script src="framework/bower_components/morris.js/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="framework/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="framework/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="framework/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="framework/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="framework/bower_components/moment/min/moment.min.js"></script>
    <script src="framework/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="framework/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="framework/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="framework/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- DataTables -->
    <script src="framework/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="framework/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="framework/bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="framework/dist/js/adminlte.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="framework/dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="framework/dist/js/demo.js"></script>
    <script>
        $(function() {
            $('#tabelaInicial').DataTable()
        })

        $('#bp-checkbox').change(function() {
            var isChecked = $(this).is(":checked") ? 0 : 1;
            console.log('Checkbox changed:', isChecked);

            $.ajax({
                url: 'cliente/atualizar_boas_praticas.php',
                type: 'POST',
                data: {
                    boas_praticas: isChecked
                },
                success: function(response) {
                    console.log('Boas práticas atualizado com sucesso:', response);
                    $("#modal-boasPraticas").modal(
                        'hide'); // Fechar o modal após a atualiza&ccedil;&atilde;o
                },
                error: function(error) {
                    console.error('Erro ao atualizar boas práticas:', error);
                }
            });
        });
    </script>
</body>


</html>