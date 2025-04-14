<?php
$etapa_atual = isset($_SESSION['etapa_atual']) ? $_SESSION['etapa_atual'] : 1;

$idEmpresa = $_SESSION['empresa_id'];
$invoice_number = isset($_SESSION['invoice_number']) ? $_SESSION['invoice_number'] : null;

$PDO = db_connect();

if($invoice_number!=null || $invoice_number!='') {
    // Selecionando SENDER
    $sqlInvoice = "SELECT * FROM invoice_maker
                    WHERE invoice_number = :invoice_number";
    $stmtInvoice = $PDO->prepare($sqlInvoice);
    $stmtInvoice->bindValue(':invoice_number', $invoice_number);
    $stmtInvoice->execute();
    $resultadosInvoice = $stmtInvoice->fetch(PDO::FETCH_ASSOC);
    $idIM = $resultadosInvoice['id_im'];
    $currency = $resultadosInvoice['currency'];

    // Selecionando BANK
    $sqlbank = "SELECT * FROM invoice_maker_bank
                WHERE id_empresa = :idEmpresa
                ORDER BY beneficiary_name ASC";
    $stmtbank = $PDO->prepare($sqlbank);
    $stmtbank->bindValue(':idEmpresa', $idEmpresa);
    $stmtbank->execute();
    $resultadosbank = $stmtbank->fetchAll(PDO::FETCH_ASSOC);
}
// Selecionando SENDER
$sqlSender = "SELECT * FROM invoice_maker_sender
            WHERE id_empresa = :idEmpresa
            ORDER BY company ASC";
$stmtSender = $PDO->prepare($sqlSender);
$stmtSender->bindValue(':idEmpresa', $idEmpresa);
$stmtSender->execute();
$resultadosSender = $stmtSender->fetchAll(PDO::FETCH_ASSOC);

// Selecionando RECEIVER
$sqlReceiver = "SELECT * FROM invoice_maker_receiver
            WHERE id_empresa = :idEmpresa
            ORDER BY company ASC";
$stmtReceiver = $PDO->prepare($sqlReceiver);
$stmtReceiver->bindValue(':idEmpresa', $idEmpresa);
$stmtReceiver->execute();
$resultadosReceiver = $stmtReceiver->fetchAll(PDO::FETCH_ASSOC);

// Selecionando GOODS
$sqlGoods = "SELECT * FROM invoice_maker_goods
            WHERE id_empresa = :idEmpresa AND invoice_number = :invoice_number
            ORDER BY name ASC";
$stmtGoods = $PDO->prepare($sqlGoods);
$stmtGoods->bindValue(':idEmpresa', $idEmpresa);
$stmtGoods->bindValue(':invoice_number', $invoice_number);
$stmtGoods->execute();
$resultadosGoods = $stmtGoods->fetchAll(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css" type="text/css">
<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />


<style type="text/css">
.bs-stepper-label {
    font-size: 1em;
}

.bs-stepper-header {
    overflow-x: auto;
    /* Adiciona scroll horizontal */
    white-space: nowrap;
    /* Impede que os itens quebrem de linha */
}

.bs-stepper-header .step {
    display: inline-block;
    /* Deixa os steps alinhados horizontalmente */
}


.active .bs-stepper-circle {
    background-color: #046868;
}

@media (max-width: 768px) {
    .bs-stepper-header .step .bs-stepper-label {
        display: none;
        /* Esconde os labels em telas pequenas */
    }
}

.select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #aaa;
    border-radius: 4px;
    height: calc(2.25rem + 2px);
}
</style>

<?php if (isset($_GET['flag']) && $_GET['flag'] == 'success') : ?>
<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-sucesso').modal('show');
});
</script>
<?php endif; ?>

<?php if (isset($_GET['flag']) && $_GET['flag'] == 'erro') : ?>
<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-erro').modal('show');
});
</script>
<?php endif; ?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ferramentas</h1>
                <small>Invoice Maker</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item active">Invoice Maker</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Informações para montar a Invoice</h3>
                </div>
                <div class="card-body">
                    <div class="bs-stepper">
                        <!-- TITULO PASSOS -->
                        <div class="bs-stepper-header d-flex flex-wrap" role="tablist">
                            <!-- INVOICE DETAILS -->
                            <div class="step" data-target="#invoice-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="invoice-part"
                                    id="invoice-part-trigger">
                                    <span class="bs-stepper-circle">1</span>
                                    <span class="bs-stepper-label">INVOICE</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <!-- INVOICE SENDER -->
                            <div class="step" data-target="#sender-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="sender-part"
                                    id="sender-part-trigger">
                                    <span class="bs-stepper-circle">2</span>
                                    <span class="bs-stepper-label">EXPORTADOR</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <!-- INVOICE RECEIVER -->
                            <div class="step" data-target="#receiver-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="receiver-part"
                                    id="receiver-part-trigger">
                                    <span class="bs-stepper-circle">3</span>
                                    <span class="bs-stepper-label">IMPORTADOR</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <!-- INVOICE GOODS DESCRIPTION -->
                            <div class="step" data-target="#goods-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="goods-part"
                                    id="goods-part-trigger">
                                    <span class="bs-stepper-circle">4</span>
                                    <span class="bs-stepper-label">PRODUTOS</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <!-- INVOICE DADOS PAGAMENTO -->
                            <div class="step" data-target="#bank-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="bank-part"
                                    id="bank-part-trigger">
                                    <span class="bs-stepper-circle">5</span>
                                    <span class="bs-stepper-label">DADOS PAGAMENTO</span>
                                </button>
                            </div>
                        </div>
                        <!-- FIM TITULO PASSOS -->

                        <!-- INICIO CONTEÚDO PASSOS -->
                        <div class="bs-stepper-content">
                            <!-- INVOICE DETAILS -->
                            <div id="invoice-part" class="content" role="tabpanel"
                                aria-labelledby="invoice-part-trigger">
                                <form id="invoice-details" name="invoice-details" method="post">

                                    <div class="row">

                                        <div class="col-md-4 form-group">
                                            <label>INVOICE NUMBER</label>
                                            <input type="text" name="invoice_number" class="form-control"
                                                placeholder="Número da invoice" required>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>CURRENCY</label>
                                            <select class="form-control" name="currency" required>
                                                <option value="" selected>Selecione a Moeda</option>
                                                <option value="USD ($)">USD ($)</option>
                                                <option value="EUR (€)">EUR (€)</option>
                                                <option value="GBP (£)">GBP (£)</option>
                                                <option value="BRL (R$)">BRL (R$)</option>
                                                <option value="JPY (J¥)">JPY (J¥)</option>
                                                <option value="CAD (C$)">CAD (C$)</option>
                                                <option value="AUD (A$)">AUD (A$)</option>
                                                <option value="ARS (AR$)">ARS (AR$)</option>
                                                <option value="CHF (Fr)">CHF (Fr)</option>
                                                <option value="RMB (¥)">RMB (¥)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>REASON FOR EXPORT</label>
                                            <select class="form-control" name="reason_for_export" required>
                                                <option value="" selected>Qual a razão da exportação?</option>
                                                <option value="Sale">Sale</option>
                                                <option value="Sample">Sample</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-4 form-group">
                                            <label>INCOTERMS</label>
                                            <select name="incoterms" id="incoterms" class="form-control" required>
                                                <option value="" selected>Selecione o Incoterms</option>
                                                <option value="CPT">CPT</option>
                                                <option value="CFR">CFR</option>
                                                <option value="DAP">DAP</option>
                                                <option value="DPU">DPU</option>
                                                <option value="CIP">CIP</option>
                                                <option value="CIF">CIF</option>
                                                <option value="EXW">EXW</option>
                                                <option value="FCA">FCA</option>
                                                <option value="FAS">FAS</option>
                                                <option value="FOB">FOB</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label>TERMS OF PAYMENT</label>
                                            <select name="terms_payment" class="form-control">
                                                <option value="" selected>Selecione os termos de pagamento</option>
                                                <option value="100% in advance">100% in advance</option>
                                                <option value="Payment upon receiving">Payment upon receiving</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>COUNTRY OF ORIGIN</label>
                                            <select name="country_origin" required class="form-control"
                                                style="width: 100%;">
                                                <option value="" selected>Qual o país de origem?</option>
                                                <option value="Afeganistao">Afeganistão</option>
                                                <option value="Africa do Sul">África do Sul</option>
                                                <option value="Albania">Albânia</option>
                                                <option value="Alemanha">Alemanha</option>
                                                <option value="Andorra">Andorra</option>
                                                <option value="Angola">Angola</option>
                                                <option value="Arabia Saudita">Arábia Saudita</option>
                                                <option value="Argelia">Argélia</option>
                                                <option value="Argentina">Argentina</option>
                                                <option value="Armenia">Armênia</option>
                                                <option value="Australia">Austrália</option>
                                                <option value="Austria">Áustria</option>
                                                <option value="Azerbaijao">Azerbaijão</option>
                                                <option value="Bahamas">Bahamas</option>
                                                <option value="Bangladesh">Bangladesh</option>
                                                <option value="Belgica">Bélgica</option>
                                                <option value="Bielorrusia">Bielorrusia</option>
                                                <option value="Bolivia">Bolívia</option>
                                                <option value="Bosnia e Herzegovina">Bósnia e Herzegovina</option>
                                                <option value="Botsuana">Botsuana</option>
                                                <option value="Brasil">Brasil</option>
                                                <option value="Brunei">Brunei</option>
                                                <option value="Bulgaria">Bulgária</option>
                                                <option value="Burkina Fasso">Burkina Fasso</option>
                                                <option value="Butao">Butão</option>
                                                <option value="Camaroes">Camarões</option>
                                                <option value="Camboja">Camboja</option>
                                                <option value="Canada">Canadá</option>
                                                <option value="Cazaquistao">Cazaquistão</option>
                                                <option value="Chile">Chile</option>
                                                <option value="China">China</option>
                                                <option value="Chipre">Chipre</option>
                                                <option value="Colombia">Colômbia</option>
                                                <option value="Coreia do Norte">Coréia do Norte</option>
                                                <option value="Coreia do Sul">Coréia do Sul</option>
                                                <option value="Costa do Marfim">Costa do Marfim</option>
                                                <option value="Costa Rica">Costa Rica</option>
                                                <option value="Croacia">Croácia</option>
                                                <option value="Cuba">Cuba</option>
                                                <option value="Dinamarca">Dinamarca</option>
                                                <option value="Egito">Egito</option>
                                                <option value="El Salvador">El Salvador</option>
                                                <option value="Emirados Arabes Unidos">Emirados Árabes Unidos</option>
                                                <option value="Equador">Equador</option>
                                                <option value="Escocia">Escócia</option>
                                                <option value="Eslovaquia">Eslováquia</option>
                                                <option value="Eslovenia">Eslovênia</option>
                                                <option value="Espanha">Espanha</option>
                                                <option value="Estados Unidos">Estados Unidos</option>
                                                <option value="Estonia">Estônia</option>
                                                <option value="Etiopia">Etiópia</option>
                                                <option value="Finlandia">Finlândia</option>
                                                <option value="Franca">França</option>
                                                <option value="Gabao">Gabão</option>
                                                <option value="Gambia">Gâmbia</option>
                                                <option value="Gana">Gana</option>
                                                <option value="Georgia">Geórgia</option>
                                                <option value="Granada">Granada</option>
                                                <option value="Grecia">Grécia</option>
                                                <option value="Groelandia">Groelândia</option>
                                                <option value="Guatemala">Guatemala</option>
                                                <option value="Guine Equatorial">Guiné Equatorial</option>
                                                <option value="Guine-Bissau">Guiné-bissau</option>
                                                <option value="Guine">Guiné</option>
                                                <option value="Haiti">Haiti</option>
                                                <option value="Holanda">Holanda</option>
                                                <option value="Honduras">Honduras</option>
                                                <option value="Hong Kong">Hong Kong</option>
                                                <option value="Hungria">Hungria</option>
                                                <option value="India">Índia</option>
                                                <option value="Indonesia">Indonésia</option>
                                                <option value="Inglaterra">Inglaterra</option>
                                                <option value="Ira">Irã</option>
                                                <option value="Iraque">Iraque</option>
                                                <option value="Irlanda">Irlanda</option>
                                                <option value="Islandia">Islândia</option>
                                                <option value="Israel">Israel</option>
                                                <option value="Italia">Itália</option>
                                                <option value="Jamaica">Jamaica</option>
                                                <option value="Japao">Japão</option>
                                                <option value="Jordania">Jordânia</option>
                                                <option value="Kosovo">Kosovo</option>
                                                <option value="Laos">Laos</option>
                                                <option value="Letonia">Letonia</option>
                                                <option value="Libia">Líbia</option>
                                                <option value="Liechtenstein">Liechtenstein</option>
                                                <option value="Lituania">Lituânia</option>
                                                <option value="Luxemburgo">Luxemburgo</option>
                                                <option value="Macedonia do Norte">Macedônia do Norte</option>
                                                <option value="Madagascar">Madagascar</option>
                                                <option value="Malasia">Malásia</option>
                                                <option value="Malawi">Malawi</option>
                                                <option value="Maldivas">Maldivas</option>
                                                <option value="Malta">Malta</option>
                                                <option value="Marrocos">Marrocos</option>
                                                <option value="Mexico">México</option>
                                                <option value="Mocambique">Moçambique</option>
                                                <option value="Moldavia">Moldávia</option>
                                                <option value="Mongolia">Mongólia</option>
                                                <option value="Montenegro">Montenegro</option>
                                                <option value="Nepal">Nepal</option>
                                                <option value="Nicaragua">Nicarágua</option>
                                                <option value="Niger">Niger</option>
                                                <option value="Nigeria">Nigéria</option>
                                                <option value="Noruega">Noruega</option>
                                                <option value="Nova Zelandia">Nova Zelândia</option>
                                                <option value="Oma">Omã</option>
                                                <option value="Pais de Gales">País de Gales</option>
                                                <option value="Palau">Palau</option>
                                                <option value="Paquistao">Paquistão</option>
                                                <option value="Paraguai">Paraguai</option>
                                                <option value="Peru">Peru</option>
                                                <option value="Polonia">Polônia</option>
                                                <option value="Porto Rico">Porto Rico</option>
                                                <option value="Portugal">Portugal</option>
                                                <option value="Qatar">Qatar</option>
                                                <option value="Reino Unido">Reino Unido</option>
                                                <option value="Republica Tcheca">República Tcheca</option>
                                                <option value="Romenia">Romênia</option>
                                                <option value="Ruanda">Ruanda</option>
                                                <option value="Russia">Rússia</option>
                                                <option value="San Marino">San Marino</option>
                                                <option value="Senegal">Senegal</option>
                                                <option value="Servia">Sérvia</option>
                                                <option value="Singapura">Singapura</option>
                                                <option value="Siria">Síria</option>
                                                <option value="Somalia">Somália</option>
                                                <option value="Sudao">Sudão</option>
                                                <option value="Suecia">Suécia</option>
                                                <option value="Suica">Suíça</option>
                                                <option value="Suriname">Suriname</option>
                                                <option value="Tanzania">Tanzânia</option>
                                                <option value="Togo">Togo</option>
                                                <option value="Tonga">Tonga</option>
                                                <option value="Trinidad e Tobago">Trinidad & Tobago</option>
                                                <option value="Tunisia">Tunísia</option>
                                                <option value="Turquia">Turquia</option>
                                                <option value="Tuvalu">Tuvalu</option>
                                                <option value="Ucrania">Ucrânia</option>
                                                <option value="Uganda">Uganda</option>
                                                <option value="Uruguai">Uruguai</option>
                                                <option value="Uzbequistao">Uzbequistão</option>
                                                <option value="Venezuela">Venezuela</option>
                                                <option value="Vietna">Vietnã</option>
                                                <option value="Zambia">Zâmbia</option>
                                                <option value="Zimbabue">Zimbábue</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label>SHIPPING COST</label>
                                            <input type="text" id="shipping_cost" class="form-control"
                                                placeholder="Digite o valor do Frete" name="shipping_cost" disabled>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>INSURANCE</label>
                                            <input type="text" id="insurance" class="form-control"
                                                placeholder="Digite o valor do Seguro" name="insurance" disabled>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>OTHERS EXPANSES</label>
                                            <input type="text" id="others_expanses" class="form-control"
                                                placeholder="Digite o valor de outras despesas da origem"
                                                name="others_expanses" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 form-group text-right">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i>
                                                Salvar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- FIM INVOICE DETAILS -->

                            <!-- INVOICE SENDER -->
                            <div id="sender-part" class="content" role="tabpanel" aria-labelledby="sender-part-trigger">
                                <form id="sender-details" name="sender-details" method="post">
                                    <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">

                                    <input type="hidden" name="invoice_number" value="<?php echo $invoice_number;?>">

                                    <input type="hidden" name="idIM" value="<?php echo $idIM;?>">

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Escolha o Exportador</label><br>
                                            <select class="form-control select2" name="id_im_sender"
                                                style="width: 100%;">
                                                <option value="" selected>--Selecione--</option>
                                                <?php foreach ($resultadosSender as $resultado) : ?>
                                                <option value="<?=$resultado['id_im_sender'];?>">
                                                    <?=$resultado['company'];?>
                                                </option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-info" data-toggle="modal"
                                                data-target="#modalSender">
                                                <i class="fas fa-plus"></i> <b>Novo</b> Exportador
                                            </button>
                                        </div>
                                        <!--
                                        <div class="col-md-3 form-group">
                                            <label>O Exportador é o Fabricante?</label><br>
                                            <input type="radio" name="sendermanufacture" value="sim" checked> Sim
                                            <input type="radio" name="sendermanufacture" value="nao"> Não
                                        </div>
                                        -->
                                    </div>



                                    <div class="row">
                                        <div class="col-md-12 form-group text-right">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i>
                                                Salvar e continuar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- FIM INVOICE SENDER -->

                            <!-- INVOICE RECEIVER -->
                            <div id="receiver-part" class="content" role="tabpanel"
                                aria-labelledby="receiver-part-trigger">
                                <form id="receiver-details" name="receiver-details" method="post">
                                    <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">

                                    <input type="hidden" name="invoice_number" value="<?php echo $invoice_number;?>">

                                    <input type="hidden" name="idIM" value="<?php echo $idIM;?>">

                                    <div class="row">
                                        <div class="col-md-8 form-group">
                                            <label>Escolha o Importador</label><br>
                                            <select class="form-control select2" name="id_im_receiver"
                                                style="width: 100%;">
                                                <option value="" selected>--Selecione--</option>
                                                <?php foreach ($resultadosReceiver as $resultado) : ?>
                                                <option value="<?=$resultado['id_im_receiver'];?>">
                                                    <?=$resultado['company'];?>
                                                </option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-info" data-toggle="modal"
                                                data-target="#modalReceiver">
                                                <i class="fa fa-plus"></i> <b>Novo</b> Importador
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 form-group text-right">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i>
                                                Salvar e continuar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- FIM INVOICE RECEIVER -->

                            <!-- GOODS DESCRIPTION -->
                            <div id="goods-part" class="content" role="tabpanel" aria-labelledby="goods-part-trigger">
                                <form id="goods-details" name="goods-details" method="post"
                                    action="cliente/ferramentas/grava_invoice_maker_goods.php">
                                    <div class="row">
                                        <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">

                                        <input type="hidden" name="invoice_number"
                                            value="<?php echo $invoice_number;?>">

                                        <input type="hidden" name="idIM" value="<?php echo $idIM;?>">

                                        <div class="col-md-4 form-group">
                                            <label for="name">NAME</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Nome do produto" required>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="description">DETAILED DESCRIPTION <sup><i
                                                        class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                        data-target="#modal-default-DESC"
                                                        title="Explicação sobre a descrição do produto"
                                                        style="cursor:pointer"></i></sup></label>
                                            <input type="text" class="form-control" name="description" id="description"
                                                placeholder="Descrição detahada do produto" required>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="color">SPECIFICATION <sup><i class="fa fa-fw fa-question-circle"
                                                        data-toggle="modal" data-target="#modal-default-SPEC"
                                                        title="Explicação sobre as especificações"
                                                        style="cursor:pointer"></i></sup></label>
                                            <input type="text" class="form-control"
                                                placeholder="Especificações do seu produto" name="color" id="color">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="hs_code">NCM</label>
                                            <?php
                                            $sql = "SELECT ncm FROM ncm WHERE CHAR_LENGTH(ncm) = 10 ORDER BY ncm ASC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
                                            <select name="hs_code" id="hs_code" required class="form-control select2"
                                                style="width: 100%;">
                                                <option value="" selected>--Selecione--</option>
                                                <?php foreach ($resultados as $resultado): ?>
                                                <option value="<?php echo $resultado['ncm'];?>">
                                                    <?php echo $resultado['ncm'];?>
                                                </option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>

                                        <div class="col-md-1 form-group">
                                            <label for="qty">QTY</label>
                                            <input type="number" class="form-control" placeholder="Qtde." name="qty"
                                                id="qty" data-toggle="tooltip" title="Quantidade de produtos" required>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="unit_weight">UNIT WEIGHT (KG)</label>
                                            <input type="text" class="form-control" placeholder="Peso Liquido Unitário"
                                                name="unit_weight" id="unit_weight" required>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="unit_price">UNIT PRICE (<?=$currency;?>)</label>
                                            <input type="text" class="form-control" name="unit_price"
                                                placeholder="Valor Unitário" id="unit_price" required>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="nw_kg">NW KG (opcional)</label>
                                            <input type="text" placeholder="Peso liquido Total" class="form-control"
                                                name="nw_kg" id="nw_kg">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="gw_kg">GW KG (opcional)</label>
                                            <input type="text" placeholder="Peso Bruto Total" class="form-control"
                                                name="gw_kg" id="gw_kg">
                                        </div>

                                        <div class="col-md-1 form-group">
                                            <label for="no_boxes">Nº BOXES</label>
                                            <input type="number" placeholder="Qtde." class="form-control"
                                                name="no_boxes" id="no_boxes" data-toggle="tooltip"
                                                title="Número de caixas desse produto" required>
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <button class="btn btn-info" type="submit"><i class="fa fa-plus"></i>
                                                Adicionar produto acima</button>
                                        </div>
                                        <div class="col-md-12 form-group text-right">
                                            <a href="cliente/ferramentas/grava_invoice_maker_4.php?&idIM=<?=$idIM;?>&invoice=<?=$invoice_number;?>"
                                                class="btn btn-primary">
                                                <i class="fas fa-save"></i> Salvar e continuar
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                <!-- Tabela de Itens Cadastrados -->
                                <table id="goodsTable"
                                    class="table table-bordered table-striped table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th>NAME</th>
                                            <th>DETAILED DESCRIPTION</th>
                                            <th>NCM</th>
                                            <th>QTY</th>
                                            <th>SPECIFICATION</th>
                                            <th>UNIT WEIGHT (KG)</th>
                                            <th>UNIT PRICE</th>
                                            <th>TOTAL PRODUCT COST</th>
                                            <th>NW KG</th>
                                            <th>GW KG</th>
                                            <th>No. of boxes</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                try {

                    foreach ($resultadosGoods as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['hs_code']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['qty']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['color']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['unit_weight']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['unit_price']) . "</td>";
                        $totalCost = $row['unit_price'] * $row['qty'];
                        echo "<td>" . htmlspecialchars(number_format($totalCost, 2, ',', '.')) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nw_kg']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['gw_kg']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['no_boxes']) . "</td>";
                        echo "<td><a href='ferramentas/deletar_prod_im.php?idProd=".$row['id_im_goods']."&idIM=".$row['id_im']."&invoice=".$row['invoice_number']."'><i class='fa fa-trash-o'></i></a></td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                }
?>
                                    </tbody>
                                </table>

                            </div>
                            <!-- FIM GOODS DESCRIPTION -->

                            <!-- INVOICE DATAILS OF BANK -->
                            <div id="bank-part" class="content" role="tabpanel" aria-labelledby="bank-part-trigger">
                                <form id="bank-details" name="bank-details" method="post"
                                    action="cliente/ferramentas/grava_invoice_maker_bank.php">
                                    <div class="row">
                                        <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">

                                        <input type="hidden" name="invoice_number"
                                            value="<?php echo $invoice_number;?>">

                                        <input type="hidden" name="idIM" value="<?php echo $idIM;?>">

                                        <div class="col-md-4 form-group">
                                            <label for="beneficiary_name">BENEFICIARY NAME</label>
                                            <input type="text" class="form-control" name="beneficiary_name"
                                                placeholder="Insira o nome da empresa que receberá o pagamento"
                                                required>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>BENEFICIARY ACCOUNT NUMBER</label>
                                            <input type="text" class="form-control" name="beneficiary_an"
                                                placeholder="Insira o número da conta bancária para onde o pagamento será enviado"
                                                required>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label>BENEFICIARY ADDRESS</label>
                                            <input type="text" class="form-control" name="beneficiary_address"
                                                placeholder="Forneça o endereço da empresa que receberá o pagamento"
                                                required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>
                                                SWIFT CODE
                                                <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                        data-target="#modal-default-CS"
                                                        title="Explicação sobre o código Swift"
                                                        style="cursor:pointer"></i></sup>
                                            </label>
                                            <input type="text" class="form-control" name="swift_code"
                                                placeholder="Código Swift" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>BENEFICIARY BANK</label>
                                            <input type="text" class="form-control" name="beneficiary_bank"
                                                placeholder="Insira o nome do banco onde a empresa beneficiária mantém sua conta"
                                                required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>REMARK</label>
                                            <input type="text" class="form-control" name="remark"
                                                placeholder="Adicione uma observação ou comentário relacionado à transação. (Opcional)">
                                        </div>
                                        <div class="col-md-12 form-group text-right">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i>
                                                Salvar e finalizar</button>
                                        </div>
                                    </div>
                                </form>
                                <p>
                                    <i class="fa fa-exclamation-triangle"></i>&nbsp;
                                    Atenção: As informações desta etapa devem ser fornecidas pelo seu fornecedor, de
                                    quem você está adquirindo os produtos.
                                </p>
                            </div>
                            <!-- FIM DATAILS OF BANK -->


                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!-- /.row -->
    <p>
        <a href="cliente/ferramentas/invoice_maker_reiniciar.php" class="btn btn-danger">
            <i class="fa fa-refresh"></i>
            Reiniciar Invoice Maker
        </a>
    </p>
</section>
<!-- /.content -->

<!-- Modal para Cadastrar SENDER -->
<div class="modal fade" id="modalSender" tabindex="-1" role="dialog" aria-labelledby="modalSenderLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalSenderLabel"><b>Incluir Empresa</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="addSender" method="post" action="cliente/ferramentas/grava_invoice_maker_sender.php">
                    <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" class="form-control" id="company" name="company"
                            placeholder="Nome completo do seu fornecedor internacional" required>
                    </div>
                    <div class="form-group">
                        <label for="vat_number">Vat Number (opcional)</label>
                        <input type="text" class="form-control" id="vat_number" name="vat_number"
                            placeholder="Número de identificação do seu fornecedor internacional">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address"
                            placeholder="Endereço completo do seu fornecedor internacional" required>
                    </div>
                    <div class="form-group">
                        <label for="post_code">Post Code</label>
                        <input type="text" class="form-control" id="post_code" name="post_code"
                            placeholder="Preencher com o ZIP Code, equivalente ao CEP do seu fornecedor interncaional"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city"
                            placeholder="Cidade do seu fornecedor internacional" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" name="country"
                            placeholder="Pais do seu fornecedor internacional" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Nome da pessoa de contato do seu fornecedor internacional" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Email do fornecedor internacional" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                        Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cadastrar RECEIVER -->
<div class="modal fade" id="modalReceiver" tabindex="-1" role="dialog" aria-labelledby="modalReceiverLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalReceiverLabel"><b>Incluir Empresa</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="addReceiver" method="post" action="cliente/ferramentas/grava_invoice_maker_receiver.php">
                    <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" class="form-control" id="company" name="company"
                            placeholder="Nome da sua empresa" required>
                    </div>
                    <div class="form-group">
                        <label for="vat_number">Vat Number</label>
                        <input type="text" class="form-control" id="vat_number" name="vat_number"
                            placeholder="Preencha com seu CNPJ" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address"
                            placeholder="Preencha com seu endereço completo" required>
                    </div>
                    <div class="form-group">
                        <label for="mesmoEnd">O endereço fiscal é o mesmo do endereço de entrega?</label><br>
                        <input type="radio" name="mesmoEnd" value="Sim" id="endereco_sim"> Sim
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="mesmoEnd" value="Não" id="endereco_nao"> Não
                    </div>
                    <div class="form-group">
                        <label for="address_delivery">Address Delivery</label>
                        <input type="text" class="form-control" id="address_delivery" name="address_delivery"
                            placeholder="Preencha com o endereço completo de entrega" required>
                    </div>
                    <div class="form-group">
                        <label for="post_code">Post Code</label>
                        <input type="text" class="form-control" id="post_code" name="post_code"
                            placeholder="Preencha com seu CEP" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city"
                            placeholder="Preencha com a cidade da sua empresa" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" id="country" name="country"
                            placeholder="Preencha com o pais da sua empresa" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Preencha com seu nome ou do contato responsavel pela importaçao da sua empresa"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Informe seu e-mail ou o do responsavel pela importação da sua empresa"
                            required>
                    </div>
                    <!-- Adicione outros campos necessários aqui -->
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                        Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cadastrar DELIVERY -->
<div class="modal fade" id="modalDelivery" tabindex="-1" role="dialog" aria-labelledby="modalDeliveryLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalDeliveryLabel"><b>Incluir Empresa</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="addDelivery" method="post" action="cliente/ferramentas/grava_invoice_maker_delivery.php">
                    <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" class="form-control" id="company" name="company" required>
                    </div>
                    <div class="form-group">
                        <label for="vat_number">Vat Number</label>
                        <input type="text" class="form-control" id="vat_number" name="vat_number" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="post_code">Post Code</label>
                        <input type="text" class="form-control" id="post_code" name="post_code" required>
                    </div>
                    <div class="form-group">
                        <label for="city_country">City / Country</label>
                        <input type="text" class="form-control" id="city_country" name="city_country" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <!-- Adicione outros campos necessários aqui -->
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                        Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Swift COde -->
<div class="modal fade" id="modal-default-CS" tabindex="-1" role="dialog" aria-labelledby="modal-default-CSLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-default-CSLabel"><b>O que é código SWIFT (ou SWIFT/BIC)?</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    O código SWIFT (ou SWIFT/BIC) é um identificador alfanumérico usado para identificar bancos e outras
                    instituições financeiras em transações internacionais. Ele é composto por 8 ou 11 caracteres,
                    conforme o formato:
                </p>
                <ul>
                    <li><b>Os primeiros 4 caracteres</b> representam o código do banco;</li>
                    <li><b>Os 2 caracteres seguintes</b> indicam o país do banco;</li>
                    <li><b>Os 2 próximos caracteres</b> correspondem à localização da instituição;</li>
                    <li><b>Os últimos 3 caracteres</b> representam o código do banco;</li>
                    <li><b>Os primeiros 4 caracteres</b> (opcionais) identificam a agência.</li>
                </ul>
                <p>
                    Esse código é essencial para transferências internacionais de dinheiro, garantindo que os fundos
                    sejam enviados para o banco correto em outro país.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Swift COde -->
<div class="modal fade" id="modal-default-DESC" tabindex="-1" role="dialog" aria-labelledby="modal-default-DESCLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-default-DESCLabel"><b>DETAILED DESCRIPTION | Descrição do Produto</b>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Este campo deve conter um resumo detalhado sobre o produto, incluindo suas características gerais,
                    como função, categoria, e possíveis usos. É importante fornecer uma visão clara do que o produto é e
                    para que serve.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Swift COde -->
<div class="modal fade" id="modal-default-SPEC" tabindex="-1" role="dialog" aria-labelledby="modal-default-SPECLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-default-SPECLabel"><b>SPECIFICATION | Especificações do Produto</b>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Neste campo, inclua informações técnicas e detalhadas sobre o produto, como dimensões, materiais,
                    peso, cor, modelo, capacidade, e qualquer outra característica técnica relevante que ajude a definir
                    o produto de forma mais precisa.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa o Stepper
    var stepper = new Stepper(document.querySelector('.bs-stepper'));

    // Definir a etapa inicial com base na sessão
    var etapaAtual = <?php echo $etapa_atual; ?>;
    stepper.to(etapaAtual);

    // Função para enviar formulário e avançar no stepper
    function enviarFormulario(formId, urlGravacao) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            fetch(urlGravacao, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Exibir Toastr de sucesso
                        toastr.success('Dados salvos com sucesso!', 'Sucesso');

                        // Avançar para a próxima etapa
                        stepper.next();
                    } else {
                        // Exibir Toastr de erro
                        toastr.error('Erro ao salvar os dados: ' + data.error, 'Erro');
                    }
                })
                .catch(error => console.error('Erro:', error));
        });
    }

    // Associar o envio dos formulários a seus arquivos de gravação
    enviarFormulario('invoice-details', 'ferramentas/grava_invoice_maker_1.php');
    enviarFormulario('sender-details', 'ferramentas/grava_invoice_maker_2.php');
    enviarFormulario('receiver-details', 'ferramentas/grava_invoice_maker_3.php');
    //enviarFormulario('bank-details', 'ferramentas/grava_invoice_maker_5.php');

    document.getElementById('salvarBtn').addEventListener('click', function() {
        window.location.href =
            'ferramentas/grava_invoice_maker_4.php'; // Redireciona para a página aaa.php
    });

    // Inicialmente oculta a div "fabricante"
    // document.getElementById("fabricante").style.display = "none";

    // // Adiciona o evento de clique aos inputs de radio
    // const radios = document.getElementsByName("sendermanufacture");
    // radios.forEach(function(radio) {
    //     radio.addEventListener("change", function() {
    //         if (this.value === "nao") {
    //             // Exibe a div quando "Não" for selecionado
    //             document.getElementById("fabricante").style.display = "block";
    //         } else {
    //             // Oculta a div quando "Sim" for selecionado
    //             document.getElementById("fabricante").style.display = "none";
    //         }
    //     });
    // });

    $('[data-toggle="tooltip"]').tooltip();

    $('#goodsTable').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
    });

    function toggleAddressDelivery() {
        var addressDelivery = document.getElementById('address_delivery');
        var enderecoSim = document.getElementById('endereco_sim');

        if (enderecoSim.checked) {
            // Se "Sim" estiver marcado, desabilitar o campo de endereço de entrega
            addressDelivery.disabled = true;
            addressDelivery.removeAttribute('required');
            addressDelivery.value = ''; // Limpa o campo de entrega
        } else {
            // Se "Não" estiver marcado, habilitar e exigir o campo de endereço de entrega
            addressDelivery.disabled = false;
            addressDelivery.setAttribute('required', true);
        }
    }

    // Adiciona os eventos de mudança aos botões de rádio
    document.getElementById('endereco_sim').addEventListener('change', toggleAddressDelivery);
    document.getElementById('endereco_nao').addEventListener('change', toggleAddressDelivery);
});
</script>