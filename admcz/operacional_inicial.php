<?php
// Inicia sessões
session_start();
require 'functions/globals.php';
require 'functions/verifica-log.php';
require 'functions/check-oper.php';

// Chama a função para verificar se o usuário é administrador
checkOper();

$principal = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$pagina = filter_input(INPUT_GET, 'b', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// pega os dados do usuario
$idUsuario = $_SESSION['user_id'];

$PDO = db_connect();

$sql = "SELECT * FROM usuario u
        INNER JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id 
        WHERE u.id = :id";
$stmt = $PDO->prepare($sql);

$stmt->bindValue(':id', $idUsuario);
$stmt->execute();

$dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

$user_id = $_SESSION['user_id']; // Certifique-se de que o ID do usuário está na sessão

// Verificar o tipo de usuário na sessão
if ($_SESSION['tipo_usuario'] == 1) {
    $mostraGerente = 'sim';
    // Usuário é administrador, pode ver todos os processos, incluindo o nome do gerente da conta
    $sql = "SELECT pro.*, pat.*, emp.*, usuario.nome AS nome_gerente, pi.*
            FROM processos pro
            INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
            INNER JOIN processos_informacoes pi ON pro.id_processo = pi.id_processo
            INNER JOIN empresa emp ON pro.id_empresa = emp.id_empresa
            LEFT JOIN usuario ON emp.id_gerente_conta = usuario.id
            ORDER BY pro.data_add DESC";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);


} else {
    // Usuário é operacional, pode ver apenas os processos das empresas permitidas
    // Obter empresas_selecionadas do usuário logado
    $mostraGerente = 'nao';
    $sql_empresas = "SELECT empresas_selecionadas FROM usuario WHERE id = :user_id";
    $stmt_empresas = $PDO->prepare($sql_empresas);
    $stmt_empresas->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_empresas->execute();
    $empresas_selecionadas = $stmt_empresas->fetchColumn();

    // Verificar se empresas_selecionadas não está vazia
    if (!empty($empresas_selecionadas)) {
        // Converter a string de IDs de empresas em um array
        $empresas_ids = explode(",", $empresas_selecionadas);

        // Gerar uma lista de placeholders para a consulta (exemplo: ?, ?, ?)
        $placeholders = implode(",", array_fill(0, count($empresas_ids), "?"));

        // Consulta para obter processos apenas das empresas permitidas para o usuário logado
        $sql = "SELECT * FROM processos pro
                INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
                INNER JOIN processos_informacoes pi ON pro.id_processo = pi.id_processo
                INNER JOIN empresa emp ON pro.id_empresa = emp.id_empresa
                WHERE pro.id_empresa IN ($placeholders)
                ORDER BY pro.data_add DESC";
        $stmt = $PDO->prepare($sql);

        // Passar os IDs das empresas como parâmetros na execução da query
        $stmt->execute($empresas_ids);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        echo "Nenhuma empresa associada ao usuário logado.";
        $resultados = []; // Retorna um array vazio caso o usuário não tenha empresas associadas
    }
}

$contPC = 0;
$contDIR = 0;
$contDID = 0;
$contEF = 0;
foreach ($resultados as $resultado) {
    if (!is_null($resultado['presenca_carga'])){
        $contPC = $contPC + 1;
    }
    if (!is_null($resultado['registro_di'])){
        $contDIR = $contDIR + 1;
        $contPC = $contPC - 1;
    }
    if ($resultado['parametrizacao'] == 'ABC'){
        $contDID = $contDID + 1;
        $contDIR = $contDIR - 1;
    }
    if ($resultado['parametrizacao'] == 'ABC'){
        $contEF = $contEF + 1;
    }
}
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

    @keyframes alertaPisca {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.2;
        }

        100% {
            opacity: 1;
        }
    }

    .alerta {
        animation: alertaPisca 1s infinite;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <?php require 'layout/header.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php require 'layout/sidebar_operacional.php'; ?>
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
                                    <span class="info-box-text">Presença de Carga</span>
                                    <span class="info-box-number"><?=$contPC;?></span>
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
                                    <span class="info-box-text">DI Registrada</span>
                                    <span class="info-box-number"><?=$contDIR;?></span>
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
                                    <span class="info-box-text">DI Desembaraçada</span>
                                    <span class="info-box-number"><?=$contDID;?></span>
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
                                    <span class="info-box-text">Enviada para Faturamento</span>
                                    <span class="info-box-number"><?=$contEF;?></span>
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
                                <h3 class="card-title">Próximas chegadas:</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="listaProcDesemb" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Empresa</th>
                                            <th class="text-center">Modal</th>
                                            <th>Ref. Cliente</th>
                                            <th>Ref. Calvet</th>
                                            <th class="text-center">ETA</th>
                                            <th class="text-center">Última atividade</th>
                                            <th class="text-center">Atraso</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultados as $resultado) : ?>
                                        <?php  if (is_null($resultado['presenca_carga'])) : ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php if(!empty($resultado['logo'])) : ?>
                                                <img src="img/logos/<?= $resultado['logo']; ?>"
                                                    style="max-height: 30px;">
                                                <?php else : ?>
                                                <img src="img/logos/sem-foto.png" style="max-height: 30px;">
                                                <?php endif;?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($resultado['titulo_tipo']=='Importação Aérea' || $resultado['titulo_tipo']=='Exportação Aérea') : ?>
                                                <i class="fas fa-plane"></i>
                                                <?php elseif ($resultado['titulo_tipo']=='Importação Marítima' || $resultado['titulo_tipo']=='Exportação Marítima') : ?>
                                                <i class="fas fa-ship"></i>
                                                <?php else : ?>
                                                <i class="fas fa-truck"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['ref_cliente']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['ref_calvet']; ?>
                                            </td>
                                            <td class="text-center" data-order="<?= $resultado['eta']; ?>">
                                                <?= dateConvert($resultado['eta']); ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">
                                                    <?php
                                                    $idProcesso = $resultado['id_processo'];
                                                    $idEmpresa = $resultado['id_empresa'];
                                                    #Workflow
                                                    // Primeira consulta: Buscar processos_workflow
                                                    $sqlWF = "SELECT * FROM processos_workflow pwf
                                                    INNER JOIN processos_aux_workflow paw ON pwf.id_aux_wf = paw.id_aux_wf
                                                    INNER JOIN usuario usu ON pwf.id_usuario = usu.id
                                                    WHERE pwf.id_processo = :idProcesso AND pwf.id_empresa = :idEmpresa
                                                    ORDER BY pwf.id_wf DESC";
                                                    $stmtWF = $PDO->prepare($sqlWF);
                                                    $stmtWF->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
                                                    $stmtWF->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
                                                    $stmtWF->execute();
                                                    $resultadosWF = $stmtWF->fetchAll(PDO::FETCH_ASSOC);

                                                    echo $resultadosWF[0]['titulo_wf'] ?? '---';
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if($resultado['eta']< date('Y-m-d')): ?>
                                                <i class="fas fa-exclamation-triangle text-red alerta"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="aduaneiro.php?a=aduaneiro&b=processos_profile&idProcesso=<?=$resultado['id_processo'];?>&idEmpresa=<?=$resultado['id_empresa'];?>"
                                                    title="Visualizar Processo"><i class="fas fa-eye"></i></a>
                                                <a href="aduaneiro.php?a=aduaneiro&b=processos_acoes&idProcesso=<?=$resultado['id_processo'];?>&idEmpresa=<?=$resultado['id_empresa'];?>"
                                                    title="Editar Daos do Processo"><i class="fas fa-edit"></i></a>
                                                <i class="fas fa-trash"
                                                    title="Somente ADM pode excluir o processo!"></i>
                                            </td>
                                        </tr>
                                        <?php endif;?>
                                        <?php endforeach; ?>
                                    </tbody>
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
            "searching": true,
            "order": [
                [4, 'asc']
            ], // Ord
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