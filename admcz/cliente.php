<?php
// Inicia sessões
session_start();
require 'functions/globals.php';
require 'functions/verifica-log.php';
require 'functions/check-cliente.php';

// Chama a função para verificar se o usuário é administrador
checkCliente();

$pga= filter_input(INPUT_GET, 'a', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$pgb= filter_input(INPUT_GET, 'b', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// pega os dados do usuario
$idUsuario = $_SESSION['user_id'];
$idEmpresa = $_SESSION['empresa_id'];

$PDO = db_connect();

$sql = "SELECT * FROM usuario u
        INNER JOIN tipo_usuario tu ON u.id_tipo_usuario = tu.id 
        INNER JOIN empresa e ON u.id_empresa = e.id_empresa
        WHERE u.id = :id AND u.id_empresa = :idEmpresa";
$stmt = $PDO->prepare($sql);
$stmt->bindValue(':id', $idUsuario, PDO::PARAM_INT);
$stmt->bindValue(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmt->execute();
$dadosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
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

    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

            <?php 
        require 'cliente/'.$pga.'/'.$pgb.'.php';
        ?>

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

    <!-- date-range-picker -->
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- BS Stepper -->
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <!-- PDF html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.world.js"></script>

    <!-- Page specific script -->
    <script>
    document.querySelectorAll('.btnImprimir, .btnImprimirContrato').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            console.log("Botão clicado:", button);

            // Identifica se é o botão da carteirinha ou do contrato
            //const isCarteirinhaButton = button.classList.contains('btnImprimir');
            const isContratoButton = button.classList.contains('btnImprimir');

            // Seleciona o card correspondente
            let card = null;
            if (isContratoButton) {
                card = document.querySelector('.card-invoice');
                console.log("Selecionado card contrato:", card);
            }

            // Verifica se o card existe antes de continuar
            if (!card) {
                console.error("Card não encontrado.");
                return;
            }

            // Configuração para o contrato
            if (isContratoButton) {
                card.style.fontSize = '11px';
                card.style.margin = '5px';

                const optContrato = {
                    margin: [0.2, 0.2, 0.2, 0.2],
                    filename: 'invoice_file.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 1.1,
                        useCORS: true
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'A4',
                        orientation: 'portrait'
                    },
                    pagebreak: {
                        mode: ['avoid-all', 'css', 'legacy']
                    }
                };

                html2pdf().from(card).set(optContrato).save().then(() => {
                    console.log("PDF do contrato gerado.");
                    // Restaura estilo após salvar
                    card.style.fontSize = '';
                    card.style.margin = '';
                });
            }
        });
    });

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

    // Código de jQuery para o Incoterms
    $('#incoterms').change(function() {
        var selectedIncoterm = $(this).val();

        // Habilitar e definir obrigatoriedade dos campos com base no Incoterm selecionado
        if (selectedIncoterm === "CPT" || selectedIncoterm === "CFR" || selectedIncoterm === "DPU") {
            $('#shipping_cost').prop('disabled', false).prop('required', true);
            $('#insurance').prop('disabled', true).prop('required', false).val('');
            $('#others_expanses').prop('disabled', true).prop('required', false).val('');
        } else if (selectedIncoterm === "CIP" || selectedIncoterm === "CIF" || selectedIncoterm === "DAP") {
            $('#shipping_cost').prop('disabled', false).prop('required', true);
            $('#insurance').prop('disabled', false).prop('required', true);
            $('#others_expanses').prop('disabled', true).prop('required', false).val('');
        } else if (selectedIncoterm === "FCA" || selectedIncoterm === "FOB" || selectedIncoterm === "EXW") {
            $('#shipping_cost').prop('disabled', true).prop('required', false).val('');
            $('#insurance').prop('disabled', true).prop('required', false).val('');
            $('#others_expanses').prop('disabled', false).prop('required', true);
        } else {
            $('#shipping_cost').prop('disabled', true).prop('required', false).val('');
            $('#insurance').prop('disabled', true).prop('required', false).val('');
            $('#others_expanses').prop('disabled', true).prop('required', false).val('');
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
        $("#listaProcCli").DataTable({
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
        }).buttons().container().appendTo('#listaProcCli_wrapper .col-md-6:eq(0)');
        $('#listaMoedas').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
        $("#listaInvoice").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "searching": true,
            "paging": true,
            "info": true,
            "order": [
                [5, 'desc'],
                [0, 'desc']
            ], // Ordenar por ID em ordem decrescente (coluna 0)
            "buttons": ["csv", "pdf", "excel", "print"]
        });

        $("#valorCif").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: false, // Evita forçar dígitos adicionais
            clearMaskOnLostFocus: false, // Mantém o valor formatado
            removeMaskOnSubmit: true, // Facilita o envio no formato correto
            onBeforeMask: function(value, opts) {
                // Garante que valores existentes sejam formatados corretamente
                return parseFloat(value).toFixed(opts.digits);
            },
        });

        $("#unit_price").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: false, // Evita forçar dígitos adicionais
            clearMaskOnLostFocus: false, // Mantém o valor formatado
            removeMaskOnSubmit: true, // Facilita o envio no formato correto
            onBeforeMask: function(value, opts) {
                // Garante que valores existentes sejam formatados corretamente
                return parseFloat(value).toFixed(opts.digits);
            },
        });

        $("#shipping_cost").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: false, // Evita forçar dígitos adicionais
            clearMaskOnLostFocus: false, // Mantém o valor formatado
            removeMaskOnSubmit: true, // Facilita o envio no formato correto
            onBeforeMask: function(value, opts) {
                // Garante que valores existentes sejam formatados corretamente
                return parseFloat(value).toFixed(opts.digits);
            },
        });

        $("#others_expanses").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: false, // Evita forçar dígitos adicionais
            clearMaskOnLostFocus: false, // Mantém o valor formatado
            removeMaskOnSubmit: true, // Facilita o envio no formato correto
            onBeforeMask: function(value, opts) {
                // Garante que valores existentes sejam formatados corretamente
                return parseFloat(value).toFixed(opts.digits);
            },
        });

        $("#insurance").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: false, // Evita forçar dígitos adicionais
            clearMaskOnLostFocus: false, // Mantém o valor formatado
            removeMaskOnSubmit: true, // Facilita o envio no formato correto
            onBeforeMask: function(value, opts) {
                // Garante que valores existentes sejam formatados corretamente
                return parseFloat(value).toFixed(opts.digits);
            },
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

    $(function() {
        'use strict';

        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        };

        var mode = 'index';
        var intersect = true;

        var $visitorsChart = $('#visitors-chart');

        // Função para criar o gráfico
        function createChart(labels, data, borderColor, label) {
            new Chart($visitorsChart, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label, // Label dinâmico do PHP
                        data: data,
                        backgroundColor: 'transparent',
                        borderColor: borderColor,
                        pointBorderColor: borderColor,
                        pointBackgroundColor: borderColor,
                        fill: false
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: mode,
                        intersect: intersect
                    },
                    hover: {
                        mode: mode,
                        intersect: intersect
                    },
                    legend: {
                        display: true // Exibe a legenda
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: true,
                                lineWidth: '4px',
                                color: 'rgba(0, 0, 0, .2)',
                                zeroLineColor: 'transparent'
                            },
                            ticks: {
                                beginAtZero: true,
                                suggestedMax: 200
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });
        }

        // Recupera os dados do servidor
        fetch('freight_index_dados.php?tipo=2') // URL para o script PHP correto
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Erro:', data.error);
                    return;
                }

                alert(data);

                // Extrai os dados do PHP
                const labels = data.data.map(item => item.y); // Datas formatadas
                const values = data.data.map(item => item.item1); // Valores
                const borderColor = data.cor; // Cor dinâmica
                const label = data.label; // Label dinâmico

                // Cria o gráfico
                createChart(labels, values, borderColor, label);
            })
            .catch(error => console.error('Erro ao buscar os dados:', error));
    });

    // Configuração do mapa com cores personalizadas
    $('#world-map').vectorMap({
        map: 'world_en',
        backgroundColor: 'transparent',
        color: '#f4f3f0', // Cor padrão
        hoverColor: '#c9dfaf',
        colors: {
            br: '#00ff00', // Brasil em verde
            cn: '#ff0000' // China em vermelho
        },
        borderColor: '#818181', // Cor das bordas dos países
        borderOpacity: 0.25,
        borderWidth: 1,
        onRegionLabelShow: function(e, el, code) {
            // Personalização do tooltip
            if (code == 'br') {
                el.html('Brasil - Destacado');
            } else if (code === 'cn') {
                el.html('China - Destacado');
            }
        }
    });
    </script>

</body>

</html>