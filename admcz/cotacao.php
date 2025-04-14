<?php
// Inicia sessões
session_start();
require 'functions/globals.php';
require 'functions/verifica-log.php';

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
    <title>COMEX MANAGER | Sistema de Gerenciamento de Comércio Exterior | Cotação</title>

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
    .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active,
    .fundo-celula-1 {
        background-color: #046868 !important;
        color: #fff;
    }

    .titulo-modal {
        background-color: #046868 !important;
        color: #fff;
    }

    .fundo-celula-2 {
        background-color: #539898 !important;
        color: #fff;
    }

    .img-zoom {
        transition: transform 0.3s ease;
    }

    .img-zoom:hover {
        transform: scale(3);
        /* Ajuste o valor para o nível de zoom desejado */
    }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <?php require 'layout/header.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php
        switch ($_SESSION['tipo_usuario']) {
            case 1:
                require 'layout/sidebar.php';
                break;
            case 2:
                require 'layout/sidebar_cliente.php';
                break;
            case 3:
                require 'layout/sidebar_operacional.php';
                break;
            default:
                require 'layout/sidebar_admin.php';
        }
?>
        <!-- /.main-sidebar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- PAGINA INICIAL -->
            <?php
   require $principal . '/' . $pagina . '.php';
?>
            <!-- FIM PAGINA INICIAL -->
        </div>
        <!-- /.content-wrapper -->

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
    document.addEventListener("DOMContentLoaded", function() {
        const select = document.getElementById("agente_carga");
        const logoImg = document.getElementById("logo_empresa");

        if (!select || !logoImg) {
            console.error("Elemento não encontrado. Verifique o ID do select ou da imagem.");
            return;
        }

        select.addEventListener("change", function() {
            const selectedOption = select.options[select.selectedIndex];
            const logoUrl = selectedOption.getAttribute("data-logo");

            if (logoUrl && logoUrl.trim() !== "") {
                logoImg.src = logoUrl;
                logoImg.classList.remove("d-none"); // Exibe a logo
            } else {
                logoImg.classList.add("d-none"); // Oculta a logo se não houver imagem
            }
        });
    });

    $(document).ready(function() {

        $.fn.dataTable.ext.type.order['date-br-pre'] = function(data) {
            if (!data) return 0; // Se a célula estiver vazia, retorna 0
            var partes = data.split('/');
            return new Date(parseInt(partes[2]), parseInt(partes[1]) - 1, parseInt(partes[0])).getTime();
        };


        if ($("#id_aux_wf").length) {
            $("#id_aux_wf").on("change", function() {
                var campoDI = $("#campo_di");
                if ($(this).val() === "10") {
                    campoDI.show();
                } else {
                    campoDI.hide();
                }
            });
        }
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

        $('#telefone_empresa').inputmask({
            mask: ['(99) 9999-9999', '(99) 9 9999-9999'],
            keepStatic: true
        });

        $('#cnpj').inputmask('99.999.999/9999-99');

        $("#valorTotal").inputmask('decimal', {
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

        $('.summernote').summernote({
            placeholder: 'Escreva aqui...',
            tabsize: 2,
            height: 200, // Altura do editor
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

    });


    $(function() {
        $("#listCotacoes").DataTable({
            "responsive": true,
            "lengthChange": false,
            "searching": true,
            "paging": true,
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
                }
            ],
            "order": [
                [4, 'desc']
            ], // Ordenando pela coluna da data
            "columnDefs": [{
                    targets: 4,
                    type: 'date-br'
                } // Define o tipo de ordenação para a data
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            }
        }).buttons().container().appendTo('#listCotacoes_wrapper .col-md-6:eq(0)');
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

    function formatCurrency(value) {
        // Remove todos os caracteres que não sejam números
        let cleanValue = value.replace(/\D/g, '');

        // Adiciona o ponto decimal manualmente
        let integerPart = cleanValue.slice(0, -2);
        let decimalPart = cleanValue.slice(-2);

        // Formata a parte inteira com separadores de milhar
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Retorna o valor formatado
        return integerPart + ',' + decimalPart;
    }

    function applyCurrencyFormatting(event) {
        let value = event.target.value;
        event.target.value = formatCurrency(value);
    }

    // Função para adicionar os listeners a múltiplos campos
    function addCurrencyListeners(ids) {
        ids.forEach(id => {
            let element = document.getElementById(id);

            element.addEventListener('input', applyCurrencyFormatting);
            element.addEventListener('blur', applyCurrencyFormatting);
        });
    }

    // Adiciona os listeners aos campos
    addCurrencyListeners(['valor_honorario', 'valor_servico', 'valor_reajuste']);
    </script>

</body>

</html>