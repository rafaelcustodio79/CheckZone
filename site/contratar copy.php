<?php
// Include the backend file
require_once 'contratar_backend.php';

// If no plan is selected, redirect to plans page
if (!$selectedPlan) {
    // If no plan_id in URL, check if we have one in session
    if (!isset($_SESSION['selected_plan_id'])) {
        header('Location: planos.php');
        exit;
    } else {
        $selectedPlan = getPlanById($_SESSION['selected_plan_id']);
        if (!$selectedPlan) {
            header('Location: planos.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html class="wide wow-animation" lang="pt-br">

<head>
    <title>CheckZone - Vistoria Online | Contratação de Plano</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <link rel="stylesheet" type="text/css"
        href="//fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,300;8..144,500" display="swap" />
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/fonts.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="preloader">
        <div class="preloader-text">
            <span class="preloader-char">c</span>
            <span class="preloader-char">a</span>
            <span class="preloader-char">r</span>
            <span class="preloader-char">r</span>
            <span class="preloader-char">e</span>
            <span class="preloader-char">g</span>
            <span class="preloader-char">a</span>
            <span class="preloader-char">n</span>
            <span class="preloader-char">d</span>
            <span class="preloader-char">o</span>
        </div>
    </div>
    <div class="page">

        <!-- HEADER -->
        <?php require_once('header.php'); ?>
        <!-- HEADER -->

        <section class="section section-xs novi-background bg-primary">
            <div class="container">
                <div class="page-title">
                    <h1 class="h2">Contratação de Plano</h1>
                </div>
            </div>
        </section>
        <section class="section section-xs bg-default">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="form-section">
                            <h3 class="text-center mb-4">Faça o Seu cadastro ou da sua Empresa!</h3>

                            <div class="alert alert-info mb-4">
                                <p class="mb-0">Você selecionou o plano
                                    <strong><?php echo $selectedPlan['nome_plano']; ?></strong> -
                                    <?php if($selectedPlan['preco'] > 0): ?>
                                    <strong>R$
                                        <?php echo number_format($selectedPlan['preco'], 2, ',', '.'); ?>/<?php echo $selectedPlan['recorrencia'] === 'mensal' ? 'mês' : 'ano'; ?></strong>
                                    <?php else: ?>
                                    <strong>Teste Gratuito</strong>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="cadastroForm">
                                <input type="hidden" name="step" value="registration">

                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <h4>Dados da Empresa</h4>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-wrap">
                                            <label for="razao_social">Razão Social <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control" id="razao_social"
                                                name="razao_social" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome_fantasia">Nome Fantasia <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="nome_fantasia" name="nome_fantasia" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cnpj">CNPJ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation" id="cnpj"
                                                name="cnpj" required>
                                            <div class="invalid-feedback" id="cnpjError">CNPJ inválido</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefone">Telefone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="telefone" name="telefone" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-input form-control-has-validation"
                                                id="email" name="email" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cep">CEP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation" id="cep"
                                                name="cep" required>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="endereco">Endereço <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="endereco" name="endereco" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="numero">Número <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="numero" name="numero" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="complemento">Complemento</label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="complemento" name="complemento">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="bairro">Bairro <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="bairro" name="bairro" required>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="cidade">Cidade <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="cidade" name="cidade" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="estado">Estado <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="estado" name="estado" required maxlength="2">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12 mb-4">
                                        <h4>Dados do Responsável</h4>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome_responsavel">Nome Completo <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="nome_responsavel" name="nome_responsavel" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="senha">Senha <span class="text-danger">*</span></label>
                                            <input type="password" class="form-input form-control-has-validation"
                                                id="senha" name="senha" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirmar_senha">Confirmar Senha <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" class="form-input form-control-has-validation"
                                                id="confirmar_senha" name="confirmar_senha" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cargo">Cargo <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation" id="cargo"
                                                name="cargo" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cpf_responsavel">CPF <span class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="cpf_responsavel" name="cpf_responsavel" required>
                                            <div class="invalid-feedback" id="cpfError">CPF inválido</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefone_responsavel">Telefone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-input form-control-has-validation"
                                                id="telefone_responsavel" name="telefone_responsavel" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email_responsavel">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-input form-control-has-validation"
                                                id="email_responsavel" name="email_responsavel" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12 mb-4">
                                        <h4>Termos e Condições</h4>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="termos"
                                                    name="termos" required>
                                                <label class="form-check-label" for="termos">
                                                    Li e concordo com os <a href="#" target="_blank">Termos de Uso</a> e
                                                    <a href="#" target="_blank">Política de Privacidade</a> <span
                                                        class="text-danger">*</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-md">Cadastrar e ir para
                                            pagamento</button>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- FOOTER -->
        <?php require_once('footer.php');?>
        <!-- FOOTER -->
    </div>
    <div class="snackbars" id="form-output-global"></div>
    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>
    <!-- Incluindo jQuery e o script de máscara -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js">
    </script>

    <script>
    $(document).ready(function() {
        // Aplicando máscaras
        $('#cnpj').mask('00.000.000/0000-00');
        $('#cpf_responsavel').mask('000.000.000-00');
        $('#telefone, #telefone_responsavel').mask('(00) 00000-0000');
        $('#cep').mask('00000-000');

        // Validação de CNPJ
        function validarCNPJ(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g, '');

            if (cnpj == '') return false;
            if (cnpj.length != 14) return false;

            // Elimina CNPJs inválidos conhecidos
            if (cnpj == "00000000000000" ||
                cnpj == "11111111111111" ||
                cnpj == "22222222222222" ||
                cnpj == "33333333333333" ||
                cnpj == "44444444444444" ||
                cnpj == "55555555555555" ||
                cnpj == "66666666666666" ||
                cnpj == "77777777777777" ||
                cnpj == "88888888888888" ||
                cnpj == "99999999999999")
                return false;

            // Valida DVs
            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0, tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;

            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) pos = 9;
            }

            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0)) return false;

            tamanho = tamanho + 1;
            numeros = cnpj.substring(0, tamanho);
            soma = 0;
            pos = tamanho - 7;

            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) pos = 9;
            }

            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1)) return false;

            return true;
        }

        // Validação de CPF
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');

            if (cpf == '') return false;
            if (cpf.length != 11) return false;

            // Elimina CPFs inválidos conhecidos
            if (cpf == "00000000000" ||
                cpf == "11111111111" ||
                cpf == "22222222222" ||
                cpf == "33333333333" ||
                cpf == "44444444444" ||
                cpf == "55555555555" ||
                cpf == "66666666666" ||
                cpf == "77777777777" ||
                cpf == "88888888888" ||
                cpf == "99999999999")
                return false;

            // Valida 1o dígito
            add = 0;
            for (i = 0; i < 9; i++)
                add += parseInt(cpf.charAt(i)) * (10 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(9)))
                return false;

            // Valida 2o dígito
            add = 0;
            for (i = 0; i < 10; i++)
                add += parseInt(cpf.charAt(i)) * (11 - i);
            rev = 11 - (add % 11);
            if (rev == 10 || rev == 11)
                rev = 0;
            if (rev != parseInt(cpf.charAt(10)))
                return false;

            return true;
        }

        // Validar CNPJ ao perder o foco
        $('#cnpj').blur(function() {
            var cnpj = $(this).val();
            if (cnpj && !validarCNPJ(cnpj)) {
                $('#cnpjError').show();
                $(this).addClass('is-invalid');
            } else {
                $('#cnpjError').hide();
                $(this).removeClass('is-invalid');
            }
        });

        // Validar CPF ao perder o foco
        $('#cpf_responsavel').blur(function() {
            var cpf = $(this).val();
            if (cpf && !validarCPF(cpf)) {
                $('#cpfError').show();
                $(this).addClass('is-invalid');
            } else {
                $('#cpfError').hide();
                $(this).removeClass('is-invalid');
            }
        });

        // Preenchimento automático a partir do CEP
        $('#cep').blur(function() {
            var cep = $(this).val().replace(/\D/g, '');

            if (cep.length != 8) return;

            // Exibir indicador de carregamento
            $('#endereco, #bairro, #cidade, #estado').val('Carregando...');

            $.getJSON("https://viacep.com.br/ws/" + cep + "/json/", function(
                data) {
                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                    $('#numero').focus();
                } else {
                    // Limpar campos em caso de erro
                    $('#endereco, #bairro, #cidade, #estado').val('');
                    alert("CEP não encontrado.");
                }
            }).fail(function() {
                // Limpar campos em caso de falha
                $('#endereco, #bairro, #cidade, #estado').val('');
                alert("Erro ao buscar o CEP. Verifique sua conexão.");
            });
        });

        // Validação ao enviar o formulário
        $('#cadastroForm').submit(function(e) {
            var cnpj = $('#cnpj').val();
            var cpf = $('#cpf_responsavel').val();
            var senha = $('#senha').val();
            var confirmar_senha = $('#confirmar_senha').val();
            var formValido = true;

            // Validar CNPJ
            if (!validarCNPJ(cnpj)) {
                $('#cnpj').addClass('is-invalid');
                $('#cnpjError').show();
                formValido = false;
            } else {
                $('#cnpj').removeClass('is-invalid');
                $('#cnpjError').hide();
            }

            // Validar CPF
            if (!validarCPF(cpf)) {
                $('#cpf_responsavel').addClass('is-invalid');
                $('#cpfError').show();
                formValido = false;
            } else {
                $('#cpf_responsavel').removeClass('is-invalid');
                $('#cpfError').hide();
            }

            // Validar senhas iguais
            if (senha !== confirmar_senha) {
                $('#confirmar_senha').addClass('is-invalid');
                if (!$('#senhaError').length) {
                    $('#confirmar_senha').after(
                        '<div class="invalid-feedback" id="senhaError">As senhas não conferem</div>'
                    );
                }
                $('#senhaError').show();
                formValido = false;
            } else {
                $('#confirmar_senha').removeClass('is-invalid');
                $('#senhaError').hide();
            }

            if (!formValido) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>

</html>