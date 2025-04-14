<?php
$active_step = isset($_SESSION['active_step']) ? $_SESSION['active_step'] : 1; // Define o passo ativo, 1 se não estiver definido
unset($_SESSION['etapa_atual']);

$idEmpresa = $_SESSION['empresa_id'];
// Sanitiza o valor de idIM recebido via GET
$idIM = filter_input(INPUT_GET, 'idIM', FILTER_SANITIZE_NUMBER_INT);
$invoice = filter_input(INPUT_GET, 'invoice', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$PDO = db_connect();

$sql = "SELECT im.*, 
        ims.*, ims.company AS empresaSender, ims.vat_number AS vatSender, ims.address AS addressSender, ims.post_code AS postCodeSender, ims.city_country AS cityCountrySender, ims.name_im_sender AS nomeCttSender, ims.email_im_sender AS emailCttSender,
        imf.*, imf.company AS empresaManufacture, imf.vat_number AS vatManufacture, imf.address AS addressManufacture, imf.post_code AS postCodeManufacture, imf.city_country AS cityCountryManufacture, imf.name_im_sender AS nomeCttManufacture, imf.email_im_sender AS emailCttManufacture,
        imr.*, imr.company AS empresaReceiver, imr.vat_number AS vatReceiver, imr.address AS addressReceiver, imr.post_code AS postCodeReceiver, imr.city_country AS cityCountryReceiver,
        imb.* 
        FROM invoice_maker im
        INNER JOIN invoice_maker_sender ims ON im.id_im_sender = ims.id_im_sender
        LEFT JOIN invoice_maker_sender imf ON im.id_im_manufacture = imf.id_im_sender
        INNER JOIN invoice_maker_receiver imr ON im.id_im_receiver = imr.id_im_receiver
        LEFT JOIN  invoice_maker_bank imb ON im.id_im = imb.id_im AND im.invoice_number = imb.invoice_number
        WHERE im.id_empresa = :id_empresa AND im.id_im = :id_im AND im.invoice_number = :invoice_number";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_empresa', $idEmpresa);
$stmt->bindParam(':id_im', $idIM);
$stmt->bindParam(':invoice_number', $invoice);
$stmt->execute();
$resultados = $stmt->fetch(PDO::FETCH_ASSOC);

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
$stmtGoods->bindValue(':invoice_number', $invoice);
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
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Clique nos <b>passos</b> para editar as informações que deseja!</h3>
                </div>
                <div class="card-body p-0">
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
                                <form id="invoice-details" name="invoice-details" method="post"
                                    action="cliente/ferramentas/editar_im_1.php">

                                    <input type="hidden" name="idIM" value="<?php echo $idIM;?>" />

                                    <div class="row">

                                        <div class="col-md-4 form-group">
                                            <label>INVOICE NUMBER</label>
                                            <input type="text" name="invoice_number" class="form-control"
                                                value="<?=$invoice;?>" readonly>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>CURRENCY</label>
                                            <select class="form-control" name="currency" required>
                                                <option value="<?=$resultados['currency'];?>" selected>
                                                    <?=$resultados['currency'];?>
                                                </option>
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
                                                <option value="<?=$resultados['reason_for_export'];?>" selected>
                                                    <?=$resultados['reason_for_export'];?>
                                                </option>
                                                <option value="Sale">Sale</option>
                                                <option value="Sample">Sample</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-md-4 form-group">
                                            <label>INCOTERMS</label>
                                            <select name="incoterms" id="incoterms" class="form-control" required>
                                                <option value="<?=$resultados['incoterms'];?>" selected>
                                                    <?=$resultados['incoterms'];?>
                                                </option>
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
                                                <option value="<?=$resultados['terms_payment'];?>" selected>
                                                    <?=$resultados['terms_payment'];?>
                                                </option>
                                                <option value="100% in advance">100% in advance</option>
                                                <option value="Payment upon receiving">Payment upon receiving</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>COUNTRY OF ORIGIN</label>
                                            <select name="country_origin" required class="form-control"
                                                style="width: 100%;">
                                                <option value="<?=$resultados['country_origin'];?>" selected>
                                                    <?=$resultados['country_origin'];?>
                                                </option>
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
                                                value="<?= $resultados['shipping_cost']; ?>" name="shipping_cost">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>INSURANCE</label>
                                            <input type="text" id="insurance" class="form-control"
                                                value="<?= $resultados['insurance']; ?>" name="insurance">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>OTHERS EXPANSES</label>
                                            <input type="text" id="others_expanses" class="form-control"
                                                value="<?= $resultados['others_expanses']; ?>" name="others_expanses">
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 form-group text-right">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-save"></i> Salvar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- FIM INVOICE DETAILS -->

                            <!-- INVOICE SENDER -->
                            <div id="sender-part" class="content" role="tabpanel" aria-labelledby="sender-part-trigger">
                                <form id="sender-details" name="sender-details" method="post"
                                    action="cliente/ferramentas/editar_im_2.php">

                                    <input type="hidden" name="invoice_number" id="invoice_number"
                                        value="<?php echo $invoice;?>">

                                    <input type="hidden" name="idIM" value="<?php echo $idIM;?>" />

                                    <div class="row">
                                        <div class="col-md-8 form-group">
                                            <label>Escolha o Exportador</label><br>
                                            <select class="form-control select2" name="id_im_sender"
                                                style="width: 100%;">
                                                <option value="<?=$resultados['id_im_sender'];?>" selected>
                                                    <?=$resultados['empresaSender'];?>
                                                </option>
                                                <?php foreach ($resultadosSender as $resultado) : ?>
                                                <option value="<?=$resultado['id_im_sender'];?>">
                                                    <?=$resultado['company'];?>
                                                </option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 form-group text-right">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-save"></i> Salvar
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <!-- FIM INVOICE SENDER -->

                            <!-- INVOICE RECEIVER -->
                            <div id="receiver-part" class="content" role="tabpanel"
                                aria-labelledby="receiver-part-trigger">
                                <form id="receiver-details" name="receiver-details" method="post"
                                    action="cliente/ferramentas/editar_im_3.php">

                                    <input type="hidden" name="invoice_number" id="invoice_number"
                                        value="<?php echo $invoice;?>">

                                    <input type="hidden" name="idIM" value="<?php echo $idIM;?>" />

                                    <div class="col-md-8 form-group">
                                        <label>Escolha o Importador</label><br>
                                        <select class="form-control select2" name="id_im_receiver" style="width: 100%;">
                                            <option value="<?=$resultados['id_im_receiver'];?>" selected>
                                                <?=$resultados['empresaReceiver'];?>
                                            </option>
                                            <?php foreach ($resultadosReceiver as $resultado) : ?>
                                            <option value="<?=$resultado['id_im_receiver'];?>">
                                                <?=$resultado['company'];?>
                                            </option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>

                                    <div class="col-md-12 form-group text-right">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-save"></i> Salvar
                                        </button>
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

                                        <input type="hidden" name="invoice_number" value="<?php echo $invoice;?>">

                                        <input type="hidden" name="idIM" value="<?php echo $idIM;?>">

                                        <input type="hidden" name="pagina" value="editar">

                                        <div class="col-md-4 form-group">
                                            <label for="name">NAME</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Nome do produto" required>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="description">DETAILED DESCRIPTION</label>
                                            <input type="text" class="form-control" name="description" id="description"
                                                placeholder="Descrição detahada do produto" required>
                                        </div>

                                        <div class="col-md-4 form-group">
                                            <label for="color">SPECIFICATION</label>
                                            <input type="text" class="form-control"
                                                placeholder="Especificações do seu produto" name="color" id="color">
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label for="hs_code">NCM</label>
                                            <?php
                                            $sql = "SELECT ncm FROM ncm WHERE CHAR_LENGTH(ncm) = 10 ORDER BY ncm ASC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultadosNcm = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
                                            <select name="hs_code" id="hs_code" required class="form-control select2"
                                                style="width: 100%; border:#cacaca;height:155px !important;">
                                                <option value="" selected>--Selecione--</option>
                                                <?php foreach ($resultadosNcm as $resultado): ?>
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
                                            <label for="unit_price">UNIT PRICE</label>
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
                                                Adicionar</button>
                                        </div>
                                        <div class="col-md-12 form-group text-right">
                                            <a href="cliente/ferramentas/editar_im_4.php?&idIM=<?=$idIM;?>&invoice=<?=$invoice;?>"
                                                class="btn btn-primary">
                                                <i class="fas fa-save"></i> Salvar
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                <!-- Tabela de Itens Cadastrados -->
                                <table id="goodsTable"
                                    class="table table-condensed table-bordered table-striped table-hover table-responsive"
                                    style="font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>NAME</th>
                                            <th>DETAILED DESCRIPTION</th>
                                            <th>NCM</th>
                                            <th>QTY</th>
                                            <th>SPECIFICATION</th>
                                            <th class='text-center'>UNIT WEIGHT (KG)</th>
                                            <th class='text-center'>UNIT PRICE</th>
                                            <th class='text-center'>TOTAL PRODUCT COST</th>
                                            <th class='text-center'>NW KG</th>
                                            <th class='text-center'>GW KG</th>
                                            <th class='text-center'>No. of boxes</th>
                                            <th class="text-center"></th>
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
                        echo "<td class='text-center'>" . htmlspecialchars($row['unit_weight']) . "</td>";
                        echo "<td class='text-center'>" . htmlspecialchars($row['unit_price']) . "</td>";
                        $totalCost = $row['unit_price'] * $row['qty'];
                        echo "<td class='text-center'>" . htmlspecialchars(number_format($totalCost, 2, ',', '.')) . "</td>";
                        echo "<td class='text-center'>" . htmlspecialchars($row['nw_kg']) . "</td>";
                        echo "<td class='text-center'>" . htmlspecialchars($row['gw_kg']) . "</td>";
                        echo "<td class='text-center'>" . htmlspecialchars($row['no_boxes']) . "</td>";
                        echo "<td class='text-center'><a href='cliente/ferramentas/deletar_prod_im_editar.php?idProd=".$row['id_im_goods']."&idIM=".$row['id_im']."&invoice=".$row['invoice_number']."'><i class='fas fa-trash'></i></a></td>";
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
                                    action="ciente/ferramentas/editar_im_5.php">
                                    <div class="row">
                                        <input type="hidden" name="id_empresa" value="<?= $idEmpresa;?>">

                                        <input type="hidden" name="invoice_number" value="<?php echo $invoice;?>">

                                        <input type="hidden" name="idIM" value="<?php echo $idIM;?>">

                                        <div class="col-md-4 form-group">
                                            <label for="beneficiary_name">BENEFICIARY NAME</label>
                                            <input type="text" class="form-control" name="beneficiary_name"
                                                value="<?=$resultados['beneficiary_name'];?>" required>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label>BENEFICIARY ACCOUNT NUMBER</label>
                                            <input type="text" class="form-control" name="beneficiary_an"
                                                value="<?=$resultados['beneficiary_an'];?>" required>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label>BENEFICIARY ADDRESS</label>
                                            <input type="text" class="form-control" name="beneficiary_address"
                                                value="<?=$resultados['beneficiary_address'];?>" required>
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
                                                value="<?=$resultados['swift_code'];?>" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>BENEFICIARY BANK</label>
                                            <input type="text" class="form-control" name="beneficiary_bank"
                                                value="<?=$resultados['beneficiary_bank'];?>" required>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>REMARK</label>
                                            <input type="text" class="form-control" name="remark"
                                                value="<?=$resultados['remark'];?>">
                                        </div>
                                        <div class="col-md-12 form-group text-right">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-save"></i>
                                                Salvar
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
</section>
<!-- /.content -->

<p class="text-center">
    <a href="cliente.php?a=ferramentas&b=invoice_maker_gerar&idIM=<?=$idIM;?>&invoice=<?=$invoice;?>"
        class="btn btn-success"><i class="fa fa-eye"></i> Visualizar</a>
</p>

<div class="modal modal-success fade" id="modal-sucesso">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><i class="fa fa-fw fa-check"></i> Sucesso!</b></h4>
            </div>
            <div class="modal-body">
                <p><?= $_GET['tip']; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal modal-danger fade" id="modal-erro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b><i class="fa fa-fw fa-warning"></i> Erro</b></h4>
            </div>
            <div class="modal-body">
                <p><?= $_GET['tip']; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

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
                <form name="addSender" method="post" action="ferramentas/grava_invoice_maker_sender.php">
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
                        <label for="city_country">City / Country</label>
                        <input type="text" class="form-control" id="city_country" name="city_country"
                            placeholder="Cidade e Pais do seu fornecedor internacional" required>
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
                    <!-- Adicione outros campos necessários aqui -->
                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>
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
                <form name="addReceiver" method="post" action="ferramentas/grava_invoice_maker_receiver.php">
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
                        <label for="mesmoEnd">O endereço fiscal é o mesmo do endereço de entrega?</label><br>
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
                        <label for="city_country">City / Country</label>
                        <input type="text" class="form-control" id="city_country" name="city_country"
                            placeholder="Preencha com a cidade e pais da sua empresa" required>
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
                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>
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
                <form name="addDelivery" method="post" action="ferramentas/grava_invoice_maker_delivery.php">
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
                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa o Stepper
    var stepper = new Stepper(document.querySelector('.bs-stepper'), {
        linear: false // Define como não linear para permitir navegação entre quaisquer passos
    });

    // Verifica o parâmetro 'active_step' na URL
    const
        activeStep = <?php echo json_encode(isset($_SESSION['active_step']) ? $_SESSION['active_step'] : 1); ?>;

    // Define o passo ativo
    stepper.to(activeStep);

    // Eventos de clique para navegar para um passo específico
    document.querySelector('#invoice-part-trigger').addEventListener('click', function() {
        stepper.to(1); // Vai para o passo 1
    });

    document.querySelector('#sender-part-trigger').addEventListener('click', function() {
        stepper.to(2); // Vai para o passo 2
    });

    document.querySelector('#receiver-part-trigger').addEventListener('click', function() {
        stepper.to(3); // Vai para o passo 3
    });

    document.querySelector('#goods-part-trigger').addEventListener('click', function() {
        stepper.to(4); // Vai para o passo 4
    });

    document.querySelector('#bank-part-trigger').addEventListener('click', function() {
        stepper.to(5); // Vai para o passo 5
    });

    // Botão "Next" - avança para o próximo passo
    document.querySelector('#nextBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Impede o comportamento padrão do botão
        stepper.next(); // Avança para o próximo passo
    });

    // Botão "Prev" - volta para o passo anterior
    document.querySelector('#prevBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Impede o comportamento padrão do botão
        stepper.previous(); // Volta para o passo anterior
    });

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

    // Código de jQuery para o Incoterms
    $('#incoterms').change(function() {
        var selectedIncoterm = $(this).val();

        // Habilitar e definir obrigatoriedade do campo shipping_cost
        if (selectedIncoterm === "CPT" || selectedIncoterm === "CFR" || selectedIncoterm ===
            "DAP" || selectedIncoterm === "DPU") {
            $('#shipping_cost').prop('disabled', false).prop('required', true);
            $('#insurance').prop('disabled', true).prop('required', false).val('');
        } else if (selectedIncoterm === "CIP" || selectedIncoterm === "CIF") {
            $('#shipping_cost').prop('disabled', false).prop('required', true);
            $('#insurance').prop('disabled', false).prop('required', true);
        } else {
            $('#shipping_cost').prop('disabled', true).prop('required', false).val('');
            $('#insurance').prop('disabled', true).prop('required', false).val('');
        }
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