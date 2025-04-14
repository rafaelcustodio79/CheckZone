<?php
// Include globals.php which already has DB connection functions and utilities
require_once 'functions/globals.php';

// Get the plan ID from the query string
$plano_id = isset($_GET['plano_id']) ? (int)$_GET['plano_id'] : null;

// Get the selected plan
$selectedPlan = null;
if ($plano_id) {
    $selectedPlan = getPlanById($plano_id);
    
    if (!$selectedPlan) {
        header('Location: planos.php');
        exit;
    }
    
    // Store the selected plan in session
    $_SESSION['selected_plan_id'] = $plano_id;
    $selectedPlan = getPlanById($_SESSION['selected_plan_id']);
}

// Exibir mensagem de erro, se houver
$mensagemErro = exibeMensagemFlash('error');

if (!empty($_SESSION['error_message'])) {
    var_dump($_SESSION['error_message']);
    unset($_SESSION['error_message']);
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

    <style>
    /* Estilo para o seletor de tipo de pessoa */
    .toggle-switch-container {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 20px;
    }

    .toggle-switch {
        position: relative;
        width: 380px;
        height: 50px;
        background-color: #f0f0f0;
        border-radius: 25px;
        overflow: hidden;
        cursor: pointer;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-switch-label {
        position: relative;
        z-index: 1;
        float: left;
        width: 50%;
        line-height: 50px;
        text-align: center;
        font-weight: 600;
        transition: color 0.3s;
        cursor: pointer;
    }

    .toggle-switch-slider {
        position: absolute;
        top: 3px;
        left: 3px;
        width: calc(50% - 6px);
        height: calc(100% - 6px);
        border-radius: 25px;
        background-color: #007bff;
        transition: transform 0.3s ease-in-out;
    }

    /* Quando o PF estiver selecionado, mover o slider */
    #pf:checked~.toggle-switch-slider {
        transform: translateX(100%);
    }

    /* Estilo para os labels com base no estado */
    #pj:checked~label[for="pj"] {
        color: white;
    }

    #pf:checked~label[for="pf"] {
        color: white;
    }

    /* Estilo para os labels não selecionados */
    #pj:not(:checked)~label[for="pj"],
    #pf:not(:checked)~label[for="pf"] {
        color: #333;
    }

    /* Destacar o cabeçalho do formulário ativo */
    #secao-pj h4,
    #secao-pf h4 {
        padding: 10px;
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
    }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="preloader-text">
            <span class="preloader-char">carregando</span>
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
                <div class="row justify-content-center justify-content-xl-start text-center text-xl-start">
                    <div class="col-md-10 col-xl-8 col-xxl-7">
                        <h2>Faça o Seu cadastro ou da sua Empresa!</h2>
                    </div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-lg-10 col-xl-12">
                        <div class="row row-20 row-md-40">

                            <div class="row justify-content-center">
                                <!-- Exibir mensagem de erro se houver -->
                                <?php if (!empty($mensagemErro)): ?>
                                <div class="col-md-12">
                                    <?php echo $mensagemErro; ?>
                                </div>
                                <?php endif; ?>

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

                                <form method="post" action="efetuar_contratacao.php" id="cadastroForm">
                                    <input type="hidden" name="step" value="registration">
                                    <input type="hidden" name="plano_id" value="<?=$plano_id; ?>">

                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="form-group tipo-pessoa">
                                                <label class="mb-2 fw-bold">Tipo de Cadastro:</label>
                                                <div class="toggle-switch-container">
                                                    <div class="toggle-switch">
                                                        <input type="radio" class="toggle-switch-input"
                                                            name="tipo_pessoa" id="pj" value="PJ" checked>
                                                        <input type="radio" class="toggle-switch-input"
                                                            name="tipo_pessoa" id="pf" value="PF">
                                                        <label for="pj" class="toggle-switch-label">Pessoa
                                                            Jurídica</label>
                                                        <label for="pf" class="toggle-switch-label">Pessoa
                                                            Física</label>
                                                        <span class="toggle-switch-slider"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Seção PJ - Dados da Empresa -->
                                    <div id="secao-pj">
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
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="cnpj" name="cnpj" required>
                                                    <div class="invalid-feedback" id="cnpjError">CNPJ inválido</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="telefone">Telefone <span
                                                            class="text-danger">*</span></label>
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
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="cep" name="cep" required>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="endereco">Endereço <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="endereco" name="endereco" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="numero">Número <span
                                                            class="text-danger">*</span></label>
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
                                                    <label for="bairro">Bairro <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="bairro" name="bairro" required>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="cidade">Cidade <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="cidade" name="cidade" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="estado">Estado <span
                                                            class="text-danger">*</span></label>
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
                                                    <label for="nome_completo">Nome Completo <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="nome_completo" name="nome_completo" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cargo">Cargo <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="cargo" name="cargo" required>
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

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="senha_pj">Senha <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password"
                                                        class="form-input form-control-has-validation" id="senha_pj"
                                                        name="senha" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="confirmar_senha_pj">Confirmar Senha <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password"
                                                        class="form-input form-control-has-validation"
                                                        id="confirmar_senha_pj" name="confirmar_senha" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Seção PF - Dados Pessoais -->
                                    <div id="secao-pf" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <h4>Dados Pessoais</h4>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nome_completo_pf">Nome Completo <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="nome_completo_pf" name="nome_completo_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cpf">CPF <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="cpf" name="cpf" required>
                                                    <div class="invalid-feedback" id="cpfError">CPF inválido</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email_pf">E-mail <span
                                                            class="text-danger">*</span></label>
                                                    <input type="email" class="form-input form-control-has-validation"
                                                        id="email_pf" name="email_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="telefone_pf">Telefone <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control" id="telefone_pf"
                                                        name="telefone_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cep_pf">CEP <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="cep_pf" name="cep_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="endereco_pf">Endereço <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="endereco_pf" name="endereco_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="numero_pf">Número <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="numero_pf" name="numero_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="complemento_pf">Complemento</label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="complemento_pf" name="complemento_pf">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bairro_pf">Bairro <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="bairro_pf" name="bairro_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="cidade_pf">Cidade <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="cidade_pf" name="cidade_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="estado_pf">Estado <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-input form-control-has-validation"
                                                        id="estado_pf" name="estado_pf" required maxlength="2">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="senha_pf">Senha <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password"
                                                        class="form-input form-control-has-validation" id="senha_pf"
                                                        name="senha_pf" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="confirmar_senha_pf">Confirmar Senha <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password"
                                                        class="form-input form-control-has-validation"
                                                        id="confirmar_senha_pf" name="confirmar_senha_pf" required>
                                                </div>
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
                                                        Li e concordo com os <a href="#" target="_blank">Termos de
                                                            Uso</a> e
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos de seleção de tipo (PJ ou PF)
        const radioPJ = document.getElementById('pj');
        const radioPF = document.getElementById('pf');

        // Seções do formulário
        const secaoPJ = document.getElementById('secao-pj');
        const secaoPF = document.getElementById('secao-pf');

        // Função para alternar entre os formulários
        function toggleFormularios() {
            if (radioPJ.checked) {
                secaoPJ.style.display = 'block';
                secaoPF.style.display = 'none';

                // Ativar validação nos campos PJ e desativar nos campos PF
                toggleRequiredFields(secaoPJ, true);
                toggleRequiredFields(secaoPF, false);
            } else {
                secaoPJ.style.display = 'none';
                secaoPF.style.display = 'block';

                // Ativar validação nos campos PF e desativar nos campos PJ
                toggleRequiredFields(secaoPJ, false);
                toggleRequiredFields(secaoPF, true);
            }
        }

        // Função para alternar os atributos required dos campos
        function toggleRequiredFields(section, isRequired) {
            const requiredFields = section.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (isRequired) {
                    field.setAttribute('required', '');
                } else {
                    field.removeAttribute('required');
                }
            });
        }

        // Adicionar evento de alteração aos radios
        radioPJ.addEventListener('change', toggleFormularios);
        radioPF.addEventListener('change', toggleFormularios);

        // Configuração inicial
        toggleFormularios();

        // Validação de CNPJ e CPF pode ser adicionada aqui
    });

    $(document).ready(function() {
        // Aplicando máscaras
        $('#cnpj').mask('00.000.000/0000-00');
        $('#cpf').mask('000.000.000-00');
        $('#telefone, #telefone_responsavel, #telefone_pf').mask('(00) 00000-0000');
        $('#cep, #cep_pf').mask('00000-000');

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
        $('#cpf').blur(function() {
            var cpf = $(this).val();
            if (cpf && !validarCPF(cpf)) {
                $('#cpfError').show();
                $(this).addClass('is-invalid');
            } else {
                $('#cpfError').hide();
                $(this).removeClass('is-invalid');
            }
        });

        // Preenchimento automático a partir do CEP (funciona para ambos PJ e PF)
        $('#cep, #cep_pf').blur(function() {
            var cep = $(this).val().replace(/\D/g, '');

            if (cep.length != 8) return;

            // Identificar qual conjunto de campos deve ser preenchido
            var prefixo = this.id === 'cep' ? '' : '_pf';

            // Exibir indicador de carregamento
            $('#endereco' + prefixo + ', #bairro' + prefixo + ', #cidade' + prefixo + ', #estado' +
                prefixo).val('Carregando...');

            $.getJSON("https://viacep.com.br/ws/" + cep + "/json/", function(data) {
                if (!data.erro) {
                    $('#endereco' + prefixo).val(data.logradouro);
                    $('#bairro' + prefixo).val(data.bairro);
                    $('#cidade' + prefixo).val(data.localidade);
                    $('#estado' + prefixo).val(data.uf);
                    $('#numero' + prefixo).focus();
                } else {
                    // Limpar campos em caso de erro
                    $('#endereco' + prefixo + ', #bairro' + prefixo + ', #cidade' + prefixo +
                        ', #estado' + prefixo).val('');
                    alert("CEP não encontrado.");
                }
            }).fail(function() {
                // Limpar campos em caso de falha
                $('#endereco' + prefixo + ', #bairro' + prefixo + ', #cidade' + prefixo +
                    ', #estado' + prefixo).val('');
                alert("Erro ao buscar o CEP. Verifique sua conexão.");
            });
        });

        // Validação ao enviar o formulário
        $('#cadastroForm').submit(function(e) {
            // Detecta se está no modo PF ou PJ
            var tipoCadastro = $('input[name="tipo_pessoa"]:checked').val();
            var formValido = true;

            if (tipoCadastro === 'PJ') {
                var cnpj = $('#cnpj').val();
                var senha = $('#senha_pj').val();
                var confirmar_senha = $('#confirmar_senha_pj').val();

                // Validar CNPJ
                if (!validarCNPJ(cnpj)) {
                    $('#cnpj').addClass('is-invalid');
                    $('#cnpjError').show();
                    formValido = false;
                } else {
                    $('#cnpj').removeClass('is-invalid');
                    $('#cnpjError').hide();
                }
            } else {
                var cpf = $('#cpf').val();
                var senha = $('#senha_pf').val();
                var confirmar_senha = $('#confirmar_senha_pf').val();

                // Validar CPF
                if (!validarCPF(cpf)) {
                    $('#cpf').addClass('is-invalid');
                    $('#cpfError').show();
                    formValido = false;
                } else {
                    $('#cpf').removeClass('is-invalid');
                    $('#cpfError').hide();
                }
            }

            // Validar senhas iguais
            if (senha !== confirmar_senha) {
                if (tipoCadastro === 'PJ') {
                    $('#confirmar_senha_pj').addClass('is-invalid');
                    if (!$('#senhaErrorPJ').length) {
                        $('#confirmar_senha_pj').after(
                            '<div class="invalid-feedback" id="senhaErrorPJ">As senhas não conferem</div>'
                        );
                    }
                    $('#senhaErrorPJ').show();
                } else {
                    $('#confirmar_senha_pf').addClass('is-invalid');
                    if (!$('#senhaErrorPF').length) {
                        $('#confirmar_senha_pf').after(
                            '<div class="invalid-feedback" id="senhaErrorPF">As senhas não conferem</div>'
                        );
                    }
                    $('#senhaErrorPF').show();
                }
                formValido = false;
            }

            if (!formValido) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>

</html>