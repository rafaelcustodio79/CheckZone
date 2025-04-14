<?php
// Inicia sessões
session_start();
require 'functions/globals.php';
require 'functions/verifica-log.php';

$principal = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$pagina = filter_input(INPUT_GET, 'b', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CheckZone | Sistema de checklist online | ADM | Configurações</title>

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
    <!-- Biblioteca para medir força da senha -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <style type="text/css">
    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
    .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
        background-color: #a7bf13;
        color: #fff;
    }

    .titulo-modal {
        background-color: #a7bf13 !important;
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
        <?php require 'layout/sidebar.php'; ?>
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

        $('#telefone_principal').inputmask({
            mask: ['(99) 9999-9999', '(99) 9 9999-9999'],
            keepStatic: true
        });

        $('#edit_telefone_usuario').inputmask({
            mask: ['(99) 9999-9999', '(99) 9 9999-9999'],
            keepStatic: true
        });

        $('#cnpj').inputmask('99.999.999/9999-99');

        function verificarForcaSenha(senha) {
            let resultado = zxcvbn(senha);
            let forca = resultado.score;
            let cor = ["bg-danger", "bg-warning", "bg-info", "bg-primary", "bg-success"];
            let textos = ["Muito fraca", "Fraca", "Média", "Forte", "Muito forte"];

            $("#senha-strength-bar").removeClass().addClass("progress-bar " + cor[forca]);
            $("#senha-strength-bar").css("width", (forca + 1) * 20 + "%");
            $("#senha-status").text(textos[forca]);
        }

        function verificarForcaSenhaEditar(senha) {
            let resultado = zxcvbn(senha);
            let forca = resultado.score;
            let cor = ["bg-danger", "bg-warning", "bg-info", "bg-primary", "bg-success"];
            let textos = ["Muito fraca", "Fraca", "Média", "Forte", "Muito forte"];

            $("#edit-senha-strength-bar").removeClass().addClass("progress-bar " + cor[forca]);
            $("#edit-senha-strength-bar").css("width", (forca + 1) * 20 + "%");
            $("#edit-senha-status").text(textos[forca]);
        }

        function gerarSenhaAleatoria() {
            let caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*";
            let senha = "";
            for (let i = 0; i < 12; i++) {
                senha += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
            }
            $("#senha").val(senha);
            verificarForcaSenha(senha);
        }

        $("#gerar_senha").click(function() {
            gerarSenhaAleatoria();
        });

        $("#toggle_senha").click(function() {
            let senhaInput = $("#senha");
            let tipo = senhaInput.attr("type") === "password" ? "text" : "password";
            senhaInput.attr("type", tipo);
            $(this).text(tipo === "password" ? "👁️" : "🙈");
        });

        function verificarConfirmacaoSenha() {
            let senha = $("#senha").val();
            let confirmarSenha = $("#confirmar_senha").val();

            if (senha === confirmarSenha && senha !== "") {
                $("#senha-match").text("✔ Senhas coincidem").css("color", "green");
            } else {
                $("#senha-match").text("✖ Senhas não coincidem").css("color", "red");
            }
        }

        function verificarConfirmacaoSenhaEditar() {
            let senha = $("#edit_senha").val();
            let confirmarSenha = $("#edit_confirmar_senha").val();

            if (senha === confirmarSenha && senha !== "") {
                $("#edit-senha-match").text("✔ Senhas coincidem").css("color", "green");
            } else {
                $("#edit-senha-match").text("✖ Senhas não coincidem").css("color", "red");
            }
        }

        $("#senha").on("input", function() {
            verificarForcaSenha($(this).val());
            verificarConfirmacaoSenha();
        });

        $("#edit_senha").on("input", function() {
            verificarForcaSenhaEditar($(this).val());
            verificarConfirmacaoSenhaEditar();
        });

    });

    $(function() {
        var table = $("#listaEmpresas").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "searching": true,
            "paging": true,
            "info": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "order": [
                [1, 'desc']
            ], // Ordenar por ID em ordem decrescente
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" // URL corrigida
            }
        });

        // Adiciona os botões na parte superior da tabela
        table.buttons().container().appendTo('#listaEmpresas_wrapper .col-md-6:eq(0)');
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

        $('#cnpj').on('blur', function() {
            const cnpj = $(this).val();
            if (!validarCNPJ(cnpj)) {
                $('#cnpjError').show();
                $(this).focus();
            } else {
                $('#cnpjError').hide();
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

        function preencherEndereco(data) {
            $('#endereco').val(data.logradouro);
            $('#bairro').val(data.bairro);
            $('#cidade').val(data.localidade);
            $('#estado').val(data.uf);
        }

        $('#edit_cep').on('blur', function() {
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

        function preencherEndereco(data) {
            $('#edit_endereco').val(data.logradouro);
            $('#edit_bairro').val(data.bairro);
            $('#edit_cidade').val(data.localidade);
            $('#edit_estado').val(data.uf);
        }
    });

    function voltarPagina() {
        window.history.back();
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
    addCurrencyListeners(['valor_honorario', 'valor_servico', 'valor_reajuste', 'valor_fi']);
    </script>

</body>

</html>