<?php
// Inicia sessões
session_start();
require 'functions/globals.php';
require 'functions/verifica-log.php';
require 'functions/check-cliente.php';

// Chama a função para verificar se o usuário é administrador
checkCliente();

$principal = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$pagina = filter_input(INPUT_GET, 'b', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// pega os dados do usuario
$idUsuario = $_SESSION['user_id'];
$idEmpresa = $_SESSION['empresa_id'];

$PDO = db_connect();

$sql = "SELECT * FROM usuario u
        INNER JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id 
        WHERE u.id = :id AND u.id_empresa = :idEmpresa";
$stmt = $PDO->prepare($sql);
$stmt->bindValue(':id', $idUsuario, PDO::PARAM_INT);
$stmt->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmt->execute();
$dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Consulta principal para contar o total de processos
$sqlProcessos = "SELECT *
                FROM processos pro
                INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
                WHERE pro.id_empresa = :idEmpresa
                ORDER BY pro.data_add DESC";
$stmtProcessos = $PDO->prepare($sqlProcessos);
$stmtProcessos->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtProcessos->execute();
$resultadosProcessos = $stmtProcessos->fetchAll(PDO::FETCH_ASSOC);
$totalProcessos = count($resultadosProcessos);

// Consulta para contar os processos com categoria 1
$sqlCountCat1 = "SELECT COUNT(*) as count_cat1
                 FROM processos pro
                 INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
                 WHERE pro.id_empresa = :idEmpresa AND pat.categoria = 1";
$stmtCat1 = $PDO->prepare($sqlCountCat1);
$stmtCat1->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtCat1->execute();
$countCat1 = $stmtCat1->fetch(PDO::FETCH_ASSOC)['count_cat1'];

// Consulta para contar os processos com categoria 2
$sqlCountCat2 = "SELECT COUNT(*) as count_cat2
                 FROM processos pro
                 INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
                 WHERE pro.id_empresa = :idEmpresa AND pat.categoria = 2";
$stmtCat2 = $PDO->prepare($sqlCountCat2);
$stmtCat2->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtCat2->execute();
$countCat2 = $stmtCat2->fetch(PDO::FETCH_ASSOC)['count_cat2'];

// Calcular a porcentagem de categoria 1
$percentCat1 = ($totalProcessos > 0) ? ($countCat1 / $totalProcessos) * 100 : 0;

// Calcular a porcentagem de categoria 2
$percentCat2 = ($totalProcessos > 0) ? ($countCat2 / $totalProcessos) * 100 : 0;

// Consulta para contar os processos no Canal Verde
$sqlCountCVerde = "SELECT COUNT(*) as canal_verde
                 FROM processos pro
                 INNER JOIN processos_informacoes pi ON pro.id_processo = pi.id_processo
                 WHERE pro.id_empresa = :idEmpresa AND pi.parametrizacao = 'Canal Verde'";
$stmtCVerde = $PDO->prepare($sqlCountCVerde);
$stmtCVerde->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtCVerde->execute();
$countCVerde = $stmtCVerde->fetch(PDO::FETCH_ASSOC)['canal_verde'];

// Consulta para contar os processos no Canal Vermelho
$sqlCountCVermelho = "SELECT COUNT(*) as canal_vermelho
                 FROM processos pro
                 INNER JOIN processos_informacoes pi ON pro.id_processo = pi.id_processo
                 WHERE pro.id_empresa = :idEmpresa AND pi.parametrizacao = 'Canal Vermelho'";
$stmtCVermelho = $PDO->prepare($sqlCountCVermelho);
$stmtCVermelho->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtCVermelho->execute();
$countCVermelho = $stmtCVermelho->fetch(PDO::FETCH_ASSOC)['canal_vermelho'];

// Consulta para contar os processos no Canal Amarelo
$sqlCountCAmarelo = "SELECT COUNT(*) as canal_amarelo
                 FROM processos pro
                 INNER JOIN processos_informacoes pi ON pro.id_processo = pi.id_processo
                 WHERE pro.id_empresa = :idEmpresa AND pi.parametrizacao = 'Canal Amarelo'";
$stmtCAmarelo = $PDO->prepare($sqlCountCAmarelo);
$stmtCAmarelo->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtCAmarelo->execute();
$countCAmarelo = $stmtCAmarelo->fetch(PDO::FETCH_ASSOC)['canal_amarelo'];

// Consulta para contar os processos no Canal Cinza
$sqlCountCCinza = "SELECT COUNT(*) as canal_cinza
                 FROM processos pro
                 INNER JOIN processos_informacoes pi ON pro.id_processo = pi.id_processo
                 WHERE pro.id_empresa = :idEmpresa AND pi.parametrizacao = 'Canal Cinza'";
$stmtCCinza = $PDO->prepare($sqlCountCCinza);
$stmtCCinza->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtCCinza->execute();
$countCCinza = $stmtCCinza->fetch(PDO::FETCH_ASSOC)['canal_cinza'];

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>COMEX MANAGER | Sistema de Gerenciamento de Comércio Exterior | Home</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="css/adminlte.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <style type="text/css">
    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
    .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
        background-color: #046868;
        color: #fff;
    }

    .titulo-modal {
        background-color: #046868 !important;
        color: #fff;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <?php require 'layout/header.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php require 'layout/sidebar_cliente.php'; ?>
        <!-- /.main-sidebar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Página Inicial</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active"><a href="index_adm.php"><i class="fa fa-home"></i>
                                        Home</a></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-ccomex"><i class="fas fa-cube"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Importações</span>
                                    <span class="info-box-number"><?=$countCat1;?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-ccomex"><i class="fas fa-paper-plane"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Exportações</span>
                                    <span class="info-box-number"><?=$countCat2;?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-ccomex"><i class="fas fa-cube"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">% Importações</span>
                                    <span class="info-box-number">
                                        <?=number_format($percentCat1, 2, ',', '.');?>
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-ccomex"><i class="fas fa-paper-plane"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">% Exportações</span>
                                    <span class="info-box-number">
                                        <?=number_format($percentCat2, 2, ',', '.');?>
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><img src="img/simbolo.png" alt="Símbolo CCOMEX"></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Canal Verde</span>
                                    <span class="info-box-number"><?=$countCVerde;?></span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: 0%"></div>
                                    </div>
                                    <span class="progress-description">
                                        0% dos desembaraços
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><img src="img/simbolo.png" alt="Símbolo CCOMEX"></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Canal Amarelo</span>
                                    <span class="info-box-number"><?=$countCAmarelo;?></span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: 0%"></div>
                                    </div>
                                    <span class="progress-description">
                                        0% dos desembaraços
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><img src="img/simbolo.png" alt="Símbolo CCOMEX"></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Canal Vermelho</span>
                                    <span class="info-box-number"><?=$countCVermelho;?></span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: 0%"></div>
                                    </div>
                                    <span class="progress-description">
                                        0% dos desembaraços
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="info-box bg-prata">
                                <span class="info-box-icon"><img src="img/simbolo.png" alt="Símbolo CCOMEX"></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Canal Cinza</span>
                                    <span class="info-box-number"><?=$countCCinza;?></span>

                                    <div class="progress">
                                        <div class="progress-bar" style="width: 0%"></div>
                                    </div>
                                    <span class="progress-description">
                                        0% dos desembaraços
                                    </span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->


                </div>
            </div>

            <div class="content">
                <div class="container-fluid">

                    <div class="row">

                        <div class="col-md-12 card">
                            <div class="card-header">
                                <h3 class="card-title">Processos em desembaraço:</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="listaProcDesemb" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data criado</th>
                                            <th>Referência Cliente</th>
                                            <th>Referência Calvet</th>
                                            <th>Tipo do processo</th>
                                            <th>Status do processo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultadosProcessos as $processo) : ?>
                                        <tr>
                                            <td>
                                                <?=dateConvert($processo['data_add']);?>
                                            </td>
                                            <td>
                                                <?=$processo['ref_cliente'];?>
                                            </td>
                                            <td>
                                                <?=$processo['ref_calvet'];?>
                                            </td>
                                            <td>
                                                <?=$processo['titulo_tipo'];?>
                                            </td>
                                            <td>
                                                <?=$processo['status_processo'];?>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Footer -->
        <?php require 'layout/footer.php'; ?>
        <!-- /.main-footer -->
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="js/adminlte.min.js"></script>

    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- InputMask -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/inputmask/jquery.inputmask.min.js"></script>

    <!-- Page specific script -->
    <script>
    // Execute o código ao carregar a página
    $(document).ready(function() {
        // Verifique se o parâmetro 'flag' está presente na URL
        var flagParam =
            '<?php echo isset($_GET['flag']) ? $_GET['flag'] : ''; ?>';
        var tipParam =
            '<?php echo isset($_GET['tip']) ? $_GET['tip'] : ''; ?>';

        // Se 'flag' estiver presente e for igual a 'erro', exiba a notificação de erro
        if (flagParam == 'erro') {
            toastr.error(tipParam);
        }
        if (flagParam == 'warning') {
            toastr.warning(tipParam);
        }
        if (flagParam == 'success') {
            toastr.success(tipParam);
        }

    });

    $(function() {
        $("#listaProcDesemb").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": [{
                    extend: 'copy',
                    text: 'Copiar'
                },
                {
                    extend: 'csv',
                    text: 'CSV'
                },
                {
                    extend: 'excel',
                    text: 'Excel'
                },
                {
                    extend: 'pdf',
                    text: 'PDF'
                },
                {
                    extend: 'print',
                    text: 'Imprimir'
                },
                {
                    extend: 'colvis',
                    text: 'Visibilidade das colunas'
                }
            ],
        }).buttons().container().appendTo('#listaProcDesemb_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });

    $(function() {
        $("#listaAssoc").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "searching": true,
            "paging": true,
            "info": true,
            "order": [
                [0, 'desc']
            ], // Ordenar por ID em ordem decrescente (coluna 0)
            "buttons": ["csv", "pdf", "excel", "print"]
        });

        $("#planAssinaturas").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#planAssinaturas_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });

        // Summernote
        //$('#textoMateria').summernote()

        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        $('[data-mask]').inputmask();

        $('#cpf').on('blur', function() {
            const cpf = $(this).val();
            if (!validarCPF(cpf)) {
                $('#cpfError').show();
                $(this).focus();
            } else {
                $('#cpfError').hide();
            }
        });

        $('#cep').on('blur', function() {
            const cep = $(this).val().replace(/\D/g, '');
            if (cep !== "") {
                const validacep = /^[0-9]{8}$/;
                if (validacep.test(cep)) {
                    $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                        preencherEndereco(data);
                    });
                } else {
                    alert("Formato de CEP inválido.");
                    limparEndereco();
                }
            } else {
                limparEndereco();
            }
        });

        //$('#modal-aviso').modal('show');

    });

    function voltarPagina() {
        window.history.back();
    }

    function generateAccessCode() {
        var codigo = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < 8; i++) {
            codigo += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        document.getElementById('codigo_acesso').value = codigo;
    }
    </script>

</body>

</html>