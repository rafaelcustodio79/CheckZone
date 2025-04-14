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
    <!-- daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- BS Stepper -->
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>

    <style type="text/css">
    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
    .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
        background-color: #046868;
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
        <?php require 'layout/sidebar_operacional.php'; ?>
        <!-- /.main-sidebar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <?php 
            require $principal.'/'.$pagina.'.php';
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

    <script>
    document.querySelectorAll('.btnImprimirInvoice, .btnImprimirCalculo').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            console.log("Botão clicado:", button);

            // Identifica qual botão foi clicado
            const isInvoiceButton = button.classList.contains('btnImprimirInvoice');
            const isCalculoButton = button.classList.contains('btnImprimirCalculo');

            // Seleciona o card correspondente
            let targetElement = null;
            if (isInvoiceButton) {
                targetElement = document.querySelector('.card-invoice');
                console.log("Selecionado card invoice:", targetElement);
            } else if (isCalculoButton) {
                targetElement = document.getElementById('viabilidadeResumo');
                console.log("Selecionado viabilidadeResumo:", targetElement);
            }

            // Verifica se o elemento foi encontrado
            if (!targetElement) {
                console.error("⚠️ ERRO: Elemento não encontrado.");
                return;
            }

            // Ajuste de estilo antes de gerar o PDF
            targetElement.style.fontSize = '11px';
            targetElement.style.margin = '5px';

            // Configurações de geração do PDF
            const pdfOptions = {
                margin: [0.2, 0.2, 0.2, 0.2],
                filename: isInvoiceButton ? 'invoice_file.pdf' : 'resumo_viabilidade_calculo.pdf',
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

            // Gera o PDF
            html2pdf().from(targetElement).set(pdfOptions).save().then(() => {
                console.log("PDF gerado:", pdfOptions.filename);
                // Restaura o estilo original
                targetElement.style.fontSize = '';
                targetElement.style.margin = '';
            });
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
        $("#listaMoedas").DataTable({
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
        }).buttons().container().appendTo('#listaMoedas_wrapper .col-md-6:eq(0)');
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

        $("#valorTotalVFI").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });
        $("#valorTotalTaxas").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });

        $("#valorTotalSeguro").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });

        $("#valorTotalArm").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });

        $("#valorTotalFN").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });

        $("#valorTotalODA").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });

        $("#valorTotalLI").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });

        $("#valorTotalAI").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
        });

        $("#valorTotalHD").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
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

        $("#valorCif2").inputmask({
            alias: "numeric",
            radixPoint: ",", // Define a vírgula como separador decimal
            groupSeparator: ".", // Define o ponto como separador de milhar
            autoGroup: true, // Mantém a separação de milhares ativa
            digits: 2, // Garante duas casas decimais
            digitsOptional: false, // Torna obrigatórias as casas decimais
            allowMinus: false, // Impede números negativos (opcional)
            rightAlign: false, // Evita que o número fique desalinhado
            placeholder: "0", // Mantém um placeholder visível
            clearMaskOnLostFocus: false, // Mantém o valor quando perde o foco
            unmaskAsNumber: true, // Garante que o valor seja salvo corretamente no formato numérico
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

        $("#valorTotalItem1").inputmask('decimal', {
            radixPoint: ',',
            groupSeparator: '.',
            autoGroup: true,
            digits: 2,
            digitsOptional: false,
            placeholder: '0',
            enforceDigitsOnBlur: true,
            clearMaskOnLostFocus: false,
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

    function updateProgressBar(percent) {
        const progressBar = document.getElementById('progress-bar');
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        progressBar.querySelector('.sr-only').textContent = percent + '% Complete';
    }

    function hideProgressBar() {
        const progressBox = document.querySelector('.box.box-solid');
        progressBox.style.display = 'none';
    }

    function showTable() {
        document.getElementById('listaNcm').style.display = 'table';
    }

    function loadTableData() {
        let percent = 0;
        const interval = setInterval(() => {
            percent += 10;
            updateProgressBar(percent);
            if (percent >= 100) {
                clearInterval(interval);
                hideProgressBar();
                showTable();
                initializeDataTable();
            }
        }, 200);
    }

    function initializeDataTable() {
        $("#listaNcm").DataTable({
            "responsive": true,
            "paging": true,
            "lengthChange": true,
            "autoWidth": false,
            "searching": true,
            "pageLength": 25, // Definir o número padrão de registros por página como 30
            "buttons": [{
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel'
                },
                {
                    extend: 'pdf',
                    orientation: 'landscape',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF'
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    text: '<i class="fa fa-print"></i> Imprimir'
                }
            ]
        }).buttons().container().appendTo('#listaNcm_wrapper .col-md-6:eq(0)');
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadTableData();
    });

    //Date range as a button
    $('#daterange-btn').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY', // Formato de data
                separator: ' - ',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'De',
                toLabel: 'Até',
                customRangeLabel: 'Personalizado',
                daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho',
                    'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                ],
                firstDay: 0 // Primeiro dia da semana (0 = Domingo, 1 = Segunda-feira, etc.)
            },
            alwaysShowCalendars: true, // Sempre mostrar os calendários
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function(start, end) {
            $('#daterange-btn span').html(start.format('DD/MM/YYYY') + ' - ' + end.format(
                'DD/MM/YYYY'));
        }
    );

    //Date picker
    $('#datepicker').datepicker({
        autoclose: true,
    })
    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
            format: 'MM/DD/YYYY hh:mm A'
        }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
        }
    )
    </script>

</body>

</html>