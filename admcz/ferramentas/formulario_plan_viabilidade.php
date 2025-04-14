<?php
$PDO = db_connect();
$visualizar = isset($_GET['visualizar']) ? $_GET['visualizar'] : null;
if ($visualizar==null) {
    if(isset($_GET['novo'])==1) {
        unset($_SESSION['idEstudo']);
    } else {
        $idEstudo = !isset($_SESSION['idEstudo']) ? null : $_SESSION['idEstudo'];
    }
} else {
    $idEstudo = $_GET['idEstudo'];
}
$passo = isset($_SESSION['passo']) ? $_SESSION['passo'] : null;
$editar_1 = isset($_SESSION['editar_1']) ? $_SESSION['editar_1'] : null;
$editar_2 = isset($_SESSION['editar_2']) ? $_SESSION['editar_2'] : null;
$editar_3 = isset($_SESSION['editar_3']) ? $_SESSION['editar_3'] : null;
$editar_4 = isset($_SESSION['editar_4']) ? $_SESSION['editar_4'] : null;
$editar_5 = isset($_SESSION['editar_5']) ? $_SESSION['editar_5'] : null;

$calculoArm = isset($_SESSION['valorArmazenagemTotal']) ? $_SESSION['valorArmazenagemTotal'] : null;

$sql = "SELECT pv.*, tc.real_venda FROM planilha_viabilidade pv
        LEFT JOIN taxa_cambial tc ON pv.moeda_padrao = tc.moeda 
        WHERE id_pv = :id_pv";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_pv', $idEstudo);
$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

$estadosCapitais = [
    "Acre" => "Rio Branco, AC",
    "Alagoas" => "Maceió, AL",
    "Amapá" => "Macapá, AP",
    "Amazonas" => "Manaus, AM",
    "Bahia" => "Salvador, BA",
    "Ceará" => "Fortaleza, CE",
    "Distrito Federal" => "Brasília, DF",
    "Espírito Santo" => "Vitória, ES",
    "Goiás" => "Goiânia, GO",
    "Maranhão" => "São Luís, MA",
    "Mato Grosso" => "Cuiabá, MT",
    "Mato Grosso do Sul" => "Campo Grande, MS",
    "Minas Gerais" => "Belo Horizonte, MG",
    "Pará" => "Belém, PA",
    "Paraíba" => "João Pessoa, PB",
    "Paraná" => "Curitiba, PR",
    "Pernambuco" => "Recife, PE",
    "Piauí" => "Teresina, PI",
    "Rio de Janeiro" => "Rio de Janeiro, RJ",
    "Rio Grande do Norte" => "Natal, RN",
    "Rio Grande do Sul" => "Porto Alegre, RS",
    "Rondônia" => "Porto Velho, RO",
    "Roraima" => "Boa Vista, RR",
    "Santa Catarina" => "Florianópolis, SC",
    "São Paulo" => "São Paulo, SP",
    "Sergipe" => "Aracaju, SE",
    "Tocantins" => "Palmas, TO"
];

$tabAtiva = isset($_GET['tabAtiva']) ? $_GET['tabAtiva'] : '';
?>
<style>
.img-flag-pais {
    width: 25px
}
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ferramentas
        <small>Viabilidade</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="#">Ferramentas</a></li>
        <li class="active">Viabilidade</li>
    </ol>
</section>

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

<?php if (!$idEstudo)  : ?>
<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-nomeEstudo').modal('show');
});
</script>
<?php endif; ?>

<!-- Main content -->
<section class="content">

    <?php if ($idEstudo)  : ?>
    <div class="row">
        <div class="col-md-12">
            <h3><?=$dados['nome_estudo'];?>
            </h3>
        </div>
    </div>
    <?php endif; ?>

    <!-- Linha de informações Iniciais -->
    <div class="row">
        <div class="col-md-12">
            <div
                class="box <?= !empty($dados['origem']) ? 'box-success' : 'box-default' ?> <?= $passo == 1 ? '' : 'collapsed-box' ?>">
                <div class=" box-header with-border">
                    <h3 class="box-title">Informações iniciais</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    <?php if (empty($dados['origem']) || $editar_1 == 1): ?>

                    <form name="ii-pv" action="cliente/grava_pv_info_iniciais.php" method="post">

                        <input type="hidden" name="id_estudo" value="<?=$idEstudo;?>">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="box-body">
                                    <label>Origem</label><br>
                                    <div class="form-group">
                                        <select id="frm_pais" name="origem" required class="form-control"
                                            style="width: 100%;">
                                            <option value="" selected>Selecione um país</option>
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
                                <!-- /.box-body -->
                            </div>

                            <div class="col-lg-4">
                                <div class="box-body">
                                    <label>Destino</label><br>
                                    <select name="destino" class="form-control" style="width: 100%;" required>
                                        <option value="" selected>Selecione uma capital/UF</option>
                                        <?php foreach ($estadosCapitais as $estado => $capital): ?>
                                        <option value="<?php echo $capital; ?>">
                                            <?php echo $capital; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- /.box-body -->
                            </div>

                            <div class="col-lg-4">
                                <div class="box-body">
                                    <label>Moeda negociada</label><br>
                                    <select class="form-control" name="moeda_padrao" style="width: 100%;" required>
                                        <option value="" selected>Selecione uma moeda padrão</option>
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="GBP">GBP (£)</option>
                                        <option value="BRL">BRL (R$)</option>
                                        <option value="JPY">JPY (J¥)</option>
                                        <option value="CAD">CAD (C$)</option>
                                        <option value="AUD">AUD (A$)</option>
                                        <option value="ARS">ARS (AR$)</option>
                                        <option value="CHF">CHF (Fr)</option>
                                        <option value="RMB">RMB (¥)</option>
                                    </select>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                        <!-- FIM da linha 1 -->

                        <!-- INICIO linha 2 -->
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="box-body">
                                    <label>Modal</label><br>
                                    <select id="frm_brand" name="modal" class="form-control"
                                        onchange="mostrarOcultarSelect()">
                                        <option value="">Selecione o modal</option>
                                        <option value="aer">Aéreo</option>
                                        <option value="mar">Marítimo</option>
                                    </select>
                                </div>
                                <!-- /.box-body -->
                            </div>

                            <div class="col-lg-4">
                                <div class="box-body">
                                    <label>Tipo de carga</label><br>
                                    <select id='tc_aer' name="tipo_carga_aer" class="form-control"
                                        style="display: none;">
                                        <option value="container_20" disabled>Conteiner 20'</option>
                                        <option value="container_40" disabled>Conteiner 40'</option>
                                        <option value="carga_solta">Carga Compartilhada</option>
                                    </select>

                                    <select id="tc_mar" class="form-control" name="tipo_carga_mar"
                                        style="display: none;width: 100%;" onchange="habilitarQtConteiner()">
                                        <option value="" selected>--Selecione--</option>
                                        <option value="container_20">Conteiner 20'</option>
                                        <option value="container_40">Conteiner 40'</option>
                                        <option value="carga_solta">Carga Compartilhada</option>
                                    </select>
                                </div>
                                <!-- /.box-body -->
                            </div>

                            <div class="col-lg-4">
                                <div class="box-body">
                                    <label>
                                        Quantidade de Conteiner
                                    </label>
                                    <br clear="all">
                                    <input type="number" class="form-control" id="qtConteiner" name="qtConteiner"
                                        disabled>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                        <!-- FIM da linha 2 -->

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="box-body">
                                    <label>
                                        Incoterms <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                data-target="#modal-default" title="Explicação dos tipos de Incoterms"
                                                style="cursor:pointer"></i></sup>
                                    </label>
                                    <br clear="all">
                                    <select id='aer' name="incoterms_aer" required class="form-control"
                                        style="display: none;">
                                        <option value="FCA">FCA</option>
                                        <option value="EXW">EXW</option>
                                        <option value="CPT">CPT</option>
                                        <option value="CIP">CIP</option>
                                        <option value="DAP">DAP</option>
                                    </select>

                                    <select id='mar' name="incoterms_mar" required class="form-control"
                                        style="display: none;">
                                        <option value="FOB">FOB</option>
                                        <option value="CFR">CFR</option>
                                        <option value="CIF">CIF</option>
                                        <option value="FCA">FCA</option>
                                        <option value="EXW">EXW</option>
                                        <option value="DAP">DAP</option>
                                    </select>

                                </div>
                                <!-- /.box-body -->
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <div class="box-body">
                                    <br clear="all">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Salvar
                                    </button>

                                    <a href="cliente/editar_pv_cancelar.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                        class="btn btn-danger">
                                        <i class="fa fa-times-circle-o"></i> Cancelar
                                    </a>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                </div>

                </form>

                <?php else: ?>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="box-body">
                            <label>Origem</label><br>
                            <p><?=$dados['origem'];?>
                            </p>
                        </div>
                        <!-- /.box-body -->
                    </div>

                    <div class="col-lg-4">
                        <div class="box-body">
                            <label>Destino</label><br>
                            <p><?=$dados['destino'];?>
                            </p>
                        </div>
                        <!-- /.box-body -->
                    </div>

                    <div class="col-lg-4">
                        <div class="box-body">
                            <label>Moeda negociada</label><br>
                            <p><?=$dados['moeda_padrao'];?>
                            </p>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <!-- FIM da linha 1 -->

                <!-- INICIO linha 2 -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="box-body">
                            <label>Modal</label><br>
                            <p>
                                <?= $dados['modal']=='aer' ? 'Aéreo' : 'Marítimo';?>
                            </p>
                        </div>
                        <!-- /.box-body -->
                    </div>

                    <div class="col-lg-4">
                        <div class="box-body">
                            <label>Tipo de carga</label><br>
                            <p>
                                <?php
                    if ($dados['tipo_carga'] == 'carga_solta') {
                        $tp = 'Carga Compartilhada';
                    } elseif ($dados['tipo_carga'] == 'container_20') {
                        $tp = "Container 20'";
                    } else {
                        $tp = "Container 40'"; // ou deixe em branco ou defina um valor padrão
                    }
                    ?>

                                <?= $tp; ?>
                            </p>
                        </div>
                        <!-- /.box-body -->
                    </div>

                    <div class="col-lg-4">
                        <div class="box-body">
                            <label>
                                Quantidade de Conteiner
                            </label>
                            <br clear="all">
                            <?=$dados['qtCont'];?>
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
                <!-- FIM da linha 2 -->

                <div class="row">
                    <div class="col-lg-4">
                        <div class="box-body">
                            <label>
                                Incoterms <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                        data-target="#modal-default" title="Explicação dos tipos de Incoterms"
                                        style="cursor:pointer"></i></sup>
                            </label>
                            <br clear="all">
                            <p><?=$dados['incoterm'];?>
                            </p>
                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-12 text-right">
                        <div class="box-body">
                            <br clear="all">
                            <a href="cliente/editar_pv_1.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                class="btn btn-warning">
                                <i class="fa fa-edit"></i> Editar
                            </a>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>

            <?php endif; ?>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    </div>
    <!-- FIM Linha de informações Iniciais -->

    <!-- Linha de ADIÇÕES -->
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT * FROM planilha_viabilidade_adicoes WHERE id_pv = :idEstudo";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idEstudo', $idEstudo, PDO::PARAM_INT);
$stmt->execute();
$resultadosAdic = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicialize arrays para armazenar os valores
$descricaoProduto = [];
$qt_produto = [];
$peso_liquido = [];
$valor_total_item = [];
$ncm = [];
$aliquota_icms = [];

// Defina um limite para o número de produtos que deseja processar
$limite = 5;

for ($i = 0; $i < $limite; $i++) {
    $id[$i] = $resultadosAdic[$i]['id'] ?? '';
    $descricaoProduto[$i] = $resultadosAdic[$i]['descricao_produto'] ?? '';
    $qt_produto[$i] = $resultadosAdic[$i]['qt'] ?? '0';
    $peso_liquido[$i] = $resultadosAdic[$i]['peso_liquido'] ?? '';
    // Formata o valor total do item
    $valor_total_item[$i] = isset($resultadosAdic[$i]['valor_total_item'])
                            ? number_format($resultadosAdic[$i]['valor_total_item'], 2, ',', '.')
                            : '';
    $ncm[$i] = $resultadosAdic[$i]['ncm'] ?? 'Selecione';
    $aliquota_icms[$i] = $resultadosAdic[$i]['aliquota_icms'] ?? '18';
}

$txtBox = count($resultadosAdic) > 0 ? 'box-success' : 'box-default';

?>
            <div class="box <?= $txtBox; ?> <?= $passo == 2 ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border">
                    <h3 class="box-title">Produto(s)</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    <!-- Botão para adicionar produto -->
                    <button class="btn btn-info" data-toggle="modal" data-target="#modalProduto">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Incluir Produto
                    </button>

                    <?php
$sqlProd = "SELECT * FROM planilha_viabilidade_adicoes 
WHERE id_pv = :id_pv";
$stmtProd = $PDO->prepare($sqlProd);
$stmtProd->bindParam(':id_pv', $idEstudo);
$stmtProd->execute();
$dadosProd = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
?>

                    <!-- Tabela de produtos cadastrados -->
                    <table id="listaProd" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Descrição do Produto</th>
                                <th>Quantidade</th>
                                <th>Peso Líquido (kg)</th>
                                <th>Valor Total (USD)</th>
                                <th>NCM</th>
                                <th>Alíquota ICMS (%)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dadosProd as $resultado) : ?>
                            <tr>
                                <td>
                                    <?= $resultado['descricao_produto']; ?>
                                </td>
                                <td class="text-center">
                                    <?= $resultado['qt']; ?>
                                </td>
                                <td class="text-center">
                                    <?= $resultado['peso_liquido']; ?>
                                </td>
                                <td class="text-center">
                                    <?= number_format($resultado['valor_total_item'], 2, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <?= $resultado['ncm']; ?>
                                </td>
                                <td class="text-center">
                                    <?= $resultado['aliquota_icms']; ?>
                                </td>
                                <td class="text-center">
                                    <a href="cliente/deletar_pv_produto.php?idProd=<?=$resultado['id'];?>&idPV=<?=$idEstudo;?>"
                                        onclick="return confirm('Deseja realmente excluir o Produto?')"><i
                                            class="fa fa-trash" title="Apagar"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="box-body">
                                <br clear="all">
                                <a href="cliente/salvar_pv_produtos.php?idPV=<?=$idEstudo;?>" class="btn btn-primary">
                                    <i class="fa fa-floppy-o" title="Salvar"></i> Salvar
                                </a>

                                <a href="cliente/editar_pv_cancelar.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                    class="btn btn-danger">
                                    <i class="fa fa-times-circle-o"></i> Cancelar
                                </a>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>


                </div>
                <!-- /.col -->
            </div>
            <!-- FIM de ADIÇÕES -->

            <!-- Linha de informações da mercadoria -->
            <div class="row">
                <div class="col-md-12">
                    <?php
                if(isset($dados['valor_mercadoria'])==false || $dados['valor_mercadoria']=='0.00' || is_null($dados['valor_mercadoria'])) {
                    $txtBox = 'box-default';
                } else {
                    $txtBox = 'box-success';
                }
?>
                    <div class="box <?= $txtBox; ?> <?= $passo == 3 ? '' : 'collapsed-box' ?>">
                        <div class="box-header with-border">
                            <h3 class="box-title">Informações da mercadoria</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <?php if ($dados['valor_mercadoria']=='0.00' || $editar_3 == 1): ?>

                            <?php
$sql = "SELECT SUM(qt) as total_qt, SUM(peso_liquido) as total_peso_liquido, SUM(valor_total_item) as total_valor_total_item 
FROM planilha_viabilidade_adicoes 
WHERE id_pv = :idEstudo";
                                $stmt = $PDO->prepare($sql);
                                $stmt->bindParam(':idEstudo', $idEstudo, PDO::PARAM_INT);
                                $stmt->execute();
                                $resultados = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Exibindo os resultados das somas
                                $total_qt = $resultados['total_qt'] ?? 0;
                                $total_peso_liquido = $resultados['total_peso_liquido'] ?? 0;
                                $total_valor_total_item = $resultados['total_valor_total_item'] ?? 0;
                                ?>

                            <form name="ii-pv" action="cliente/grava_pv_info_mercadorias.php" method="post">

                                <input type="hidden" name="id_estudo" value="<?=$idEstudo;?>">
                                <!-- Linha 1 -->
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Valor da mercadoria
                                                (<?=$dados['moeda_padrao'];?>)</label><br>
                                            <input type="text" class="form-control" name="valor_mercadoria"
                                                id="valorTotal"
                                                value="<?=number_format($total_valor_total_item, 2, ',', '.');?>"
                                                disabled>
                                            <input type="hidden" name="valor_mercadoria"
                                                value="<?=number_format($total_valor_total_item, 2, ',', '.');?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Peso líquido (kg)</label><br>
                                            <input type="text" class="form-control" name="peso_liquido"
                                                value="<?=$total_peso_liquido;?>" disabled>
                                            <input type="hidden" name="peso_liquido" value="<?=$total_peso_liquido;?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Peso bruto (kg)</label><br>
                                            <input type="text" class="form-control" name="peso_bruto">
                                        </div>
                                    </div>

                                </div>
                                <!-- FIM linha 1 -->

                                <!-- Linha 2 -->
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Valor da mercadoria em Real*</label><br>
                                            <?php
        $valor_mercadoria = $total_valor_total_item;
                                // Substitui a vírgula por um ponto
                                $real_com_ponto = isset($dados['real_venda']) ? str_replace(',', '.', $dados['real_venda']) : '';
                                // Converte a string para um número de ponto flutuante
                                $valor_do_real = floatval($real_com_ponto);
                                // Calcula o valor em Real
                                $valor_em_real = $valor_mercadoria * $valor_do_real;
                                // Formata o valor em Real para exibição
                                $valor_em_real_formatado = number_format($valor_em_real, 2, ',', '.');
                                ?>

                                            R$
                                            <?= $valor_em_real_formatado; ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Quantidade de produtos</label><br>
                                            <input type="number" class="form-control" name="qt_produtos"
                                                value="<?=$total_qt;?>" disabled>
                                            <input type="hidden" name="qt_produtos" value="<?=$total_qt;?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>CBM | LCL (m<sup>3</sup>) <sup><i class="fa fa-fw fa-question-circle"
                                                        data-toggle="modal" data-target="#modal-default-CBM"
                                                        title="Explicação sobre o cálculo do CBM"
                                                        style="cursor:pointer"></i></sup></label><br>
                                            <input type="text" class="form-control" name="cbm"
                                                <?= $dados['tipo_carga']=='carga_solta' ? "" : "disabled";?>>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM linha 2 -->

                                <!-- Linha 3 -->
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <div class="box-body">
                                            <br clear="all">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Salvar
                                            </button>

                                            <a href="cliente/editar_pv_cancelar.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                                class="btn btn-danger">
                                                <i class="fa fa-times-circle-o"></i> Cancelar
                                            </a>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                </div>
                                <!-- FIM linha 3 -->

                            </form>

                            <?php else : ?>

                            <!-- Linha 1 -->
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Valor da mercadoria</label><br>
                                        <p>
                                            <?php
                                        // Valor em dólares
                                        $valor = $dados['valor_mercadoria'];

                                // Formata o valor com duas casas decimais, usando ponto como separador decimal e vírgula como separador de milhar
                                $valor_formatado = number_format($valor, 2, ',', '.');

                                // Adiciona o símbolo da moeda
                                echo $valor_formatado . ' ' . $dados['moeda_padrao']; // Saída: $1,587.25 USD
                                ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Peso líquido (kg)</label><br>
                                        <p>
                                            <?=$dados['peso_liquido'];?>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Peso bruto (kg)</label><br>
                                        <p>
                                            <?=$dados['peso_bruto'];?>
                                        </p>
                                    </div>
                                </div>

                            </div>
                            <!-- FIM linha 1 -->

                            <!-- Linha 2 -->
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Valor da mercadoria em Real*</label><br>
                                        <?php
                                            $valor_mercadoria = isset($dados['valor_mercadoria']) ? $dados['valor_mercadoria'] : '0';
                                // Substitui a vírgula por um ponto
                                $real_com_ponto = str_replace(',', '.', $dados['real_venda']);
                                // Converte a string para um número de ponto flutuante
                                $valor_do_real = floatval($real_com_ponto);
                                // Calcula o valor em Real
                                $valor_em_realMerc = $valor_mercadoria * $valor_do_real;
                                // Formata o valor em Real para exibição
                                $valor_em_real_formatado = number_format($valor_em_realMerc, 2, ',', '.');
                                ?>

                                        <?php if (empty($dados['valor_mercadoria'])) : ?>
                                        Ao salvar esta etapa você terá o resultado.
                                        <?php else : ?>
                                        R$
                                        <?= $valor_em_real_formatado; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Quantidade de produtos</label><br>
                                        <p>
                                            <?=$dados['qt_produtos'];?>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>CBM | LCL (m<sup>3</sup>) <sup><i class="fa fa-fw fa-question-circle"
                                                    data-toggle="modal" data-target="#modal-default-CBM"
                                                    title="Explicação sobre o cálculo do CBM"
                                                    style="cursor:pointer"></i></sup></label><br>
                                        <p>
                                            <?=$dados['cbm'];?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- FIM linha 2 -->

                            <!-- Linha 3 -->
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <div class="box-body">
                                        <br clear="all">
                                        <a href="cliente/editar_pv_3.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                            class="btn btn-warning">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <!-- FIM linha 3 -->

                            </form>

                            <?php endif; ?>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- FIM de informações da mercadoria -->

            <!-- Linha de Valores LOG INT -->
            <div class="row">
                <div class="col-md-12">
                    <div
                        class="box <?= !is_null($dados['valor_log_int']) ? 'box-success' : 'box-default' ?> <?= $passo == 4 ? '' : 'collapsed-box' ?>">
                        <div class="box-header with-border">
                            <h3 class="box-title">Valores da Logística Internacional</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <?php if (is_null($dados['valor_log_int']) || $editar_4 == 1): ?>

                            <form name="li-pv" action="cliente/grava_pv_vli.php" method="post">

                                <input type="hidden" name="id_estudo" value="<?=$idEstudo;?>">

                                <!-- Linha 1 -->
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="box-body">
                                            <label>Você já tem uma previsão do valor do Frete Internacional?</label><br>
                                            <?php if ($dados['origem'] == 'China') : ?>
                                            <input type="radio" name="vfi_resumido" id="vfi_sim" value="1"
                                                onchange="mostrarOcultarVFI()"> Sim &nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="vfi_resumido" id="vfi_nao" value="0"
                                                onchange="mostrarOcultarVFI()"> Não, por favor calcule.

                                            <input type="hidden" name="tipo_carga" value="<?=$dados['tipo_carga']?>">
                                            <input type="hidden" name="modal" value="<?=$dados['modal']?>">
                                            <input type="hidden" name="incoterm" value="<?=$dados['incoterm']?>">
                                            <input type="hidden" name="cbm" value="<?=$dados['cbm']?>">
                                            <input type="hidden" name="peso_bruto" value="<?=$dados['peso_bruto']?>">
                                            <input type="hidden" name="qtCont" value="<?=$dados['qtCont']?>">
                                            <?php else : ?>
                                            <input type="radio" name="vfi_resumido" id="vfi_sim" value="1" checked
                                                onchange="mostrarOcultarVFI()"> Sim &nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="vfi_resumido" id="vfi_nao" value="0" disabled
                                                onchange="mostrarOcultarVFI()"> Não, por favor calcule
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM linha 1 -->

                                <!-- Linha 2 -->
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="box-body">
                                            <div class="col-md-4">
                                                <label>Valor do Frete Internacional
                                                    (<?=$dados['moeda_padrao'];?>)</label><br>
                                                <input type="text" class="form-control" name="vfi" id="valorTotalVFI"
                                                    required>
                                            </div>
                                            <div class="col-md-4">
                                                <label>
                                                    Valor de taxas
                                                    <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                            data-target="#modal-default-Taxas"
                                                            title="Explicação sobre as taxas"
                                                            style="cursor:pointer"></i></sup>
                                                </label><br>
                                                <input type="text" class="form-control" name="taxas"
                                                    id="valorTotalTaxas" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Valor do seguro</label><br>
                                                <input type="text" class="form-control" name="seguro"
                                                    id="valorTotalSeguro" required>
                                            </div>
                                        </div>
                                        <p id="mensagem" style="color: #999;"></p>
                                    </div>
                                </div>
                                <!-- FIM linha 2 -->

                                <!-- Linha 3 -->
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <div class="box-body">
                                            <br clear="all">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Salvar
                                            </button>

                                            <a href="cliente/editar_pv_cancelar.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                                class="btn btn-danger">
                                                <i class="fa fa-times-circle-o"></i> Cancelar
                                            </a>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                </div>
                                <!-- FIM linha 3 -->

                            </form>

                            <?php else : ?>

                            <?php
$incoterm = $dados['incoterm']; // Certifique-se de que o valor do Incoterm esteja disponível

                                if (!in_array($incoterm, ['CPT', 'CIP', 'DAP', 'CFR', 'CIF'])): ?>
                            <!-- Linha 1 -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="box-body">
                                        <label>Você já tem uma previsão do valor do Frete Internacional?</label><br>
                                        <?php if ($dados['vfi_resumido'] == 1) : ?>
                                        Sim
                                        <?php else : ?>
                                        Não, o sistema realizou o cálculo.
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- FIM linha 1 -->
                            <?php endif; ?>

                            <!-- Linha 2 -->
                            <div class="row">

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Valor Frete Internacional</label><br>
                                        <?php
                                                                    $valor_frete = floatval($dados['valor_frete_int_li']);
$valor_frete_formatado = number_format($valor_frete, 2, ',', '.');
echo $valor_frete_formatado;
?>
                                        <?=$dados['moeda_padrao'];?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Valor Taxas</label><br>
                                        <?php
                                                                    $valor_taxas_bruto = floatval($dados['taxa_exw_li']);
                                                                    $valor_cbm = $dados['cbm'];

                                                                    // Substitui a vírgula por ponto e converte para float
                                                                    $taxa_cbm = (float) str_replace(',', '.', $valor_cbm);

                                                                    //var_dump($valor_taxas_bruto);

                                                                    // Garante que o valor mínimo seja 1
                                                                    $taxa_cbm = ($taxa_cbm < 1) ? 1 : $taxa_cbm;
                                                                    
                                                                    $valor_taxas = $taxa_cbm * $valor_taxas_bruto;
$valor_taxas_formatado = number_format($valor_taxas, 2, ',', '.');
echo $valor_taxas_formatado;
?>
                                        <?=$dados['moeda_padrao'];?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Valor Seguro</label><br>
                                        <?php
                                                                    $valor_seguro = floatval($dados['valor_seguro_li']);
$valor_seguro_formatado = number_format($valor_seguro, 2, ',', '.');
echo $valor_seguro_formatado;
?>
                                        <?=$dados['moeda_padrao'];?>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="box-body">
                                        <label>Valor Total da Logística Internacional (All in)</label><br>
                                        <?php
                                                                    $valor_real = floatval($dados['valor_log_int']);
$valor_real_formatado = number_format($valor_real, 2, ',', '.');
?>
                                        <?=$valor_real_formatado;?>
                                        <?=$dados['moeda_padrao'];?>
                                        <small><?=$dados['txt_obs'];?></small><br><br>
                                        <b>Valor em Real</b><br>
                                        <?php
                                    $valor_em_dolar = floatval($dados['valor_log_int']);
$valor_do_real = $dados['real_venda'];
//var_dump($valor_do_real*$valor_em_dolar);
// Remover os pontos de milhar e substituir a vírgula decimal por ponto
//$valorNumerico = str_replace(',', '.', str_replace('.', '', $valor_do_real));
// Converter a string formatada para float
//$valorNumerico = floatval($valorNumerico);
$valor_em_realLI = $valor_em_dolar * $valor_do_real;
$valor_real_formatado = number_format($valor_em_realLI, 2, ',', '.');
echo 'R$ ' . $valor_real_formatado;
?>
                                    </div>
                                </div>
                            </div>
                            <!-- FIM linha 2 -->

                            <!-- Linha 3 -->
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <div class="box-body">
                                        <br clear="all">
                                        <a href="cliente/editar_pv_4.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                            class="btn btn-warning">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <!-- FIM linha 3 -->

                            </form>

                            <?php endif; ?>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- FIM de Valores LOG INT -->

            <!-- Linha de Despesas Aduaneiras -->
            <div class="row">
                <div class="col-md-12">
                    <?php
                if(isset($dados['valor_armazenagem'])==false || $dados['valor_armazenagem']=='0.00' || is_null($dados['valor_armazenagem'])) {
                    $txtBox4 = 'box-default';
                } else {
                    $txtBox4 = 'box-success';
                }
?>
                    <div class="box <?= $txtBox4 ?> <?= $passo == 5 ? '' : 'collapsed-box' ?>">
                        <div class="box-header with-border">
                            <h3 class="box-title">Despesas Aduaneiras</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-plus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <?php if (is_null($dados['valor_armazenagem']) || $dados['valor_armazenagem'] == '0.00' || $editar_5 == 1): ?>

                            <form name="da-pv" action="cliente/grava_pv_da.php" method="post">

                                <input type="hidden" name="id_estudo" value="<?=$idEstudo;?>">

                                <!-- Linha 1 -->
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Você já sabe sua estimativa de Armazenagem?</label><br>
                                            <input type="radio" name="estimativa_armazenagem" id="estimativaarm_sim"
                                                value="1" onchange="mostrarOcultarArmazenagem()"> Sim
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <?php if(is_null($calculoArm)): ?>
                                            <input type="radio" name="estimativa_armazenagem" id="estimativaarm_nao"
                                                value="0" onchange="mostrarOcultarArmazenagem()"> Não, quero que
                                            calcule.
                                            <?php else : ?>
                                            <input type="radio" name="estimativa_armazenagem" id="estimativaarm_nao"
                                                value="0" onchange="mostrarOcultarArmazenagem()" checked> Não, o sistema
                                            já
                                            calculou.
                                            <?php endif;?>
                                            <br>
                                            <input type="text" class="form-control" name="valor_armazenagem"
                                                id="valorTotalArm"
                                                value="<?= isset($calculoArm) ? number_format($calculoArm, 2, ',', '.') : '0,00'; ?>">

                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Frete Nacional (opcional) | R$</label><br>
                                            <input type="text" class="form-control" name="valor_frete_nacional"
                                                id="valorTotalFN">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Outras despesas | R$</label><br>
                                            <input type="text" class="form-control" name="outras_da" id="valorTotalODA">
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM linha 1 -->

                                <!-- Linha 2 -->
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Licença de Importação (opcional) | R$</label><br>
                                            <input type="text" class="form-control" name="licenca_importacao"
                                                id="valorTotalLI">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Anuência de Importação (opcional) | R$</label><br>
                                            <input type="text" class="form-control" name="anuencia_importacao"
                                                id="valorTotalAI">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Você possui os honorários do despachante? | R$</label><br>
                                            <input type="radio" name="estimativa_hd" id="estimativahd_sim" value="1"
                                                onchange="mostrarOcultarHD()"> Sim
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="estimativa_hd" id="estimativahd_nao" value="0"
                                                onchange="mostrarOcultarHD()"> Não

                                            <input type="text" class="form-control" name="honorario_despachante"
                                                id="valorTotalHD">
                                        </div>
                                    </div>
                                </div>
                                <!-- FIM linha 2 -->

                                <!-- Linha 3 -->
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Marinha Mercante</label><br>
                                            <?php
if($dados['modal']=='mar') {
    $valorMM = $valor_em_realLI * 0.08;
    $valor_real_formatado_MM = number_format($valorMM, 2, ',', '.');
    echo 'R$ ' . $valor_real_formatado_MM;
} else {
    $valor_real_formatado_MM = 0;
    echo 'R$ 0,00';
}
?>
                                            <input type="hidden" name="marinha_mercante"
                                                value="<?php echo $valor_real_formatado_MM; ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>
                                                Taxas no destino
                                                <sup><i class="fa fa-fw fa-question-circle" data-toggle="modal"
                                                        data-target="#modal-default-TaxasDA"
                                                        title="Explicação para taxas" style="cursor:pointer"></i></sup>
                                            </label><br>
                                            <?php
                                            if($dados['modal']=='aer') {
                                                $taxasDestino = 0 * $dados['real_venda'];
                                            } else {
                                                if ($dados['tipo_carga']!='carga_solta') {
                                                    $taxasDestino = (750 * $dados['qtCont']) * $dados['real_venda'];
                                                } else {
                                                    $taxasDestino = 250 * $dados['real_venda'];
                                                 
                                                }
                                            }
?>
                                            <input type="text" class="form-control" name="capatazia"
                                                value="<?php echo number_format($taxasDestino, 2, ',', '.');?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="box-body">
                                            <label>Valor total das despesas aduaneiras</label><br>
                                            Será calculado ao salvar.
                                        </div>
                                    </div>

                                </div>
                                <!-- FIM linha 3 -->

                                <!-- Linha 4 -->
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <div class="box-body">
                                            <br clear="all">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Salvar
                                            </button>

                                            <a href="cliente/editar_pv_cancelar.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                                class="btn btn-danger">
                                                <i class="fa fa-times-circle-o"></i> Cancelar
                                            </a>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                </div>
                                <!-- FIM linha 4 -->

                            </form>

                            <?php else : ?>


                            <!-- Linha 1 -->
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Você já sabe sua estimativa de Armazenagem?</label><br>
                                        <?php if($dados['estimativa_armazenagem']==1) {
                                            echo 'Sim';
                                        } else {
                                            echo 'Não, foi calculado pelo sistema';
                                        }?><br>
                                        R$
                                        <?= number_format($dados['valor_armazenagem'], 2, ',', '.');?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Frete Nacional (opcional)</label><br>
                                        R$
                                        <?= number_format($dados['valor_frete_nacional'], 2, ',', '.');?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Outras despesas</label><br>
                                        R$
                                        <?= number_format($dados['outras_da'], 2, ',', '.');?>
                                    </div>
                                </div>
                            </div>
                            <!-- FIM linha 1 -->

                            <!-- Linha 2 -->
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Licença de Importação (opcional)</label><br>
                                        R$
                                        <?= number_format($dados['licenca_importacao'], 2, ',', '.');?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Anuência de Importação (opcional)</label><br>
                                        R$
                                        <?= number_format($dados['anuencia_importacao'], 2, ',', '.');?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Honorário Despachante (opcional)</label><br>
                                        R$
                                        <?= number_format($dados['honorario_despachante'], 2, ',', '.');?>
                                    </div>
                                </div>
                            </div>
                            <!-- FIM linha 2 -->

                            <!-- Linha 3 -->
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Marinha Mercante</label><br>
                                        R$
                                        <?= number_format($dados['marinha_mercante'], 2, ',', '.');?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Taxas no destino</label><br>
                                        R$
                                        <?= number_format($dados['capatazia'], 2, ',', '.');?>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="box-body">
                                        <label>Valor total das despesas aduaneiras</label><br>
                                        <?php
                                        $va = $dados['valor_armazenagem'];
                                $vfn = $dados['valor_frete_nacional'];
                                $oda = $dados['outras_da'];
                                $li = $dados['licenca_importacao'];
                                $ai = $dados['anuencia_importacao'];
                                $hd = $dados['honorario_despachante'];
                                $mm = $dados['marinha_mercante'];
                                $valor_total_da = $va + $vfn + $oda + $li + $ai + $hd + $mm;
                                echo 'R$ '. number_format($valor_total_da, 2, ',', '.');
                                ?>
                                    </div>
                                </div>

                            </div>
                            <!-- FIM linha 3 -->

                            <!-- Linha 4 -->
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <div class="box-body">
                                        <br clear="all">
                                        <a href="cliente/editar_pv_5.php?visualizar=1&idEstudo=<?=$idEstudo;?>"
                                            class="btn btn-warning">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <!-- FIM linha 4 -->


                            <?php endif; ?>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- FIM de Despesas Aduaneiras -->

            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="box-body">
                        <br clear="all">
                        <a href="cliente.php?pg=plan_viabilidade_calculo&idEstudo=<?=$idEstudo;?>"
                            class="btn btn-lg btn-info">
                            <i class="fa fa-calculator"></i> FAZER CÁLCULO
                        </a>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>


</section>
<!-- /.content -->
</div>



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

<div class="modal fade" id="modal-nomeEstudo">
    <div class="modal-dialog boasPraticas">
        <div class="modal-content" style="background-color: #29363c;">
            <div class="modal-header" style="border-bottom-color: #555;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="color: #d8e6e7;">
                    <small>Para iniciar seu estudo de viabilidade, dê um nome para ele.</small><br>
                    Nome do Estudo:
                </h4>
            </div>
            <div class="modal-body" style="color: #d8e6e7;">
                <div class="row">
                    <form name="inicio-pv" action="cliente/grava_pv_nome_estudo.php" method="post">
                        <div class="col-lg-12">
                            <div class="box-body text-center">
                                <div>
                                    <input type="text" name="nome_estudo" placeholder="Digite aqui o nome do estudo"
                                        class="form-control" style="width:100%;">
                                </div>
                                <br clear="all">
                                <button type="submit" class="btn btn-default">
                                    <i class="fa fa-save"></i> Salvar
                                </button>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer text-left" style="color: #d8e6e7;text-align:left;">
                <h4><b>Aviso Importante:</b></h4>
                <p>
                    Informamos que o estudo a ser realizado e os cálculos apresentados são apenas estimativas. Os
                    valores
                    fornecidos são gerados com base na nossa metodologia de cálculo e devem ser utilizados apenas como
                    referência.
                </p>
            </div>
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-armazenagem">
    <div class="modal-dialog boasPraticas">
        <div class="modal-content" style="background-color: #29363c;">
            <div class="modal-header" style="border-bottom-color: #555;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="color: #d8e6e7;">
                    Faça o cálculo de estimativa da sua armazenagem agora mesmo!
                </h4>
            </div>
            <div class="modal-body" style="color: #d8e6e7;">

                <?php if($dados['modal']=='aer') : ?>

                <form role="form" name="cadastro-usuario-cliente" method="post"
                    action="cliente/calcula_armazenagem_a_da.php">

                    <input type="hidden" name="idEstudo" value="<?=$idEstudo;?>">

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="box-body">
                                <label>Terminal</label><br>
                                <div class="form-group">
                                    <?php
                                            try {
                                                $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                                // Consulta para selecionar nomes distintos dos terminais
                                                $sql = "SELECT * FROM armazenagem_aer_aux";
                                                $stmt = $PDO->query($sql);

                                                // Gerar o dropdown HTML
                                                echo '<select name="id_aeroporto" class="form-control" style="width:100%;">';
                                                echo '<option value="">Selecione</option>';
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo '<option value=' . $row["id_terminal_aer"] . '>' . htmlspecialchars($row["aeroporto"], ENT_QUOTES, 'UTF-8') . '</option>';
                                                }
                                                echo '</select>';

                                            } catch (PDOException $e) {
                                                echo 'Erro: ' . $e->getMessage();
                                            }
                    ?>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <div class="col-lg-12">
                            <div class="box-body">
                                <label>Valor CIF da mercadoria (em R$)</label><br>
                                <?php
                    $valorCIF = $valor_em_realMerc + $valor_em_realLI;
                    $valor_cif_formatado = number_format($valorCIF, 2, ',', '.');
                    ?>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                    <input type="text" id="valorCif" class="form-control"
                                        value="<?= $valor_cif_formatado;?>" name="cif" required>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <div class="col-lg-12">
                            <div class="box-body">
                                <label for="daterange-btn">Período</label><br>
                                <div class="input-group">
                                    <input type="text" id="daterange-btn" name="periodo" class="form-control"
                                        placeholder="Selecione o período" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <i class="fa fa-calendar"></i> Selecionar
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="box-body">
                                <label>Qual o tipo de carga?</label><br>
                                <div class="form-group">
                                    <span>
                                        <input type="radio" id="carga_geral" name="tipo_carga" value="carga_geral"
                                            checked>
                                        Carga Geral
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="box-body">
                                <label>Peso / Qtde.</label>
                                <br>
                                <div class="form-group">
                                    Peso bruto (Kg):<br>
                                    <input type="text" id="peso_bruto" name="peso_bruto" class="form-control"
                                        style="color: #29363c;" required>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-warning"><i class="fa fa-calculator"></i>
                            Calcular</button>
                    </div>
                </form>

                <?php else : ?>

                <!-- form start -->
                <form role="form" name="cadastro-usuario-cliente" method="post"
                    action="cliente/calcula_armazenagem_m_da.php">

                    <input type="hidden" name="idEstudo" value="<?=$idEstudo;?>">

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="box-body">
                                <label>Terminal</label><br>
                                <div class="form-group">
                                    <?php
                                    try {
                                        $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                        // Consulta para selecionar nomes distintos dos terminais
                                        $sql = "SELECT * FROM armazenagem_mar_aux";
                                        $stmt = $PDO->query($sql);

                                        // Gerar o dropdown HTML
                                        echo '<select name="id_terminal" class="form-control" style="width:100%;">';
                                        echo '<option value="">Selecione</option>';
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value=' . $row["id_terminal"] . '>' . htmlspecialchars($row["terminal"], ENT_QUOTES, 'UTF-8') . '</option>';
                                        }
                                        echo '</select>';

                                    } catch (PDOException $e) {
                                        echo 'Erro: ' . $e->getMessage();
                                    }
                    ?>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <div class="col-lg-12">
                            <div class="box-body">
                                <label>Valor CIF da mercadoria (em R$)</label><br>
                                <?php
                    $valor_em_realMerc = isset($valor_em_realMerc) ? $valor_em_realMerc : 0;
                    $valor_em_realLI = isset($valor_em_realLI) ? $valor_em_realLI : 0;
                    
                    $valorCIF = $valor_em_realMerc + $valor_em_realLI;
                    $valor_cif_formatado = number_format($valorCIF, 2, ',', '.');
                    ?>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                    <input type="text" id="valorCif" class="form-control"
                                        value="<?=$valor_cif_formatado;?>" name="cif" required>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <div class="col-lg-12">
                            <div class="box-body">
                                <label for="daterange-btn">Período</label><br>
                                <div class="input-group">
                                    <input type="text" id="daterange-btn" name="periodo" class="form-control"
                                        placeholder="Selecione o período" readonly>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <i class="fa fa-calendar"></i> Selecionar
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-lg-4">
                            <div class="box-body">
                                <label>Qual o tipo de carga?</label><br>
                                <div class="form-group">
                                    <span>
                                        <input type="radio" id="container_20" name="tipo_carga" value="container_20"
                                            onclick="toggleFields()">
                                        Contêiner de 20'
                                    </span>
                                    <br>
                                    <span>
                                        <input type="radio" id="container_40" name="tipo_carga" value="container_40"
                                            onclick="toggleFields()">
                                        Contêiner de 40'
                                    </span>
                                    <br>
                                    <span>
                                        <input type="radio" id="carga_sol" name="tipo_carga" value="carga_solta"
                                            onclick="toggleFields()">
                                        Carga Solta
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="box-body">
                                <label>Peso / Qtde.</label>
                                <br>
                                <div class="form-group">
                                    Peso bruto (Kg):<br>
                                    <input type="text" id="peso_bruto" name="peso_bruto" style="color: #29363c;"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    Qtde. de contêineres:<br>
                                    <input type="number" id="qtde_container" name="qtde_container"
                                        style="color: #29363c;" disabled>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <div class="col-lg-4">
                            <div class="box-body">
                                <label>Carga perigosa?</label>
                                <br>
                                <div class="form-group">
                                    <input type="radio" name="cp" value="1"> Sim<br>
                                    <input type="radio" name="cp" value="0" checked> Não
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>

                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-warning"><i class="fa fa-calculator"></i> Calcular</button>
                    </div>
                </form>

                <?php endif; ?>

            </div>
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-default-Taxas">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>ATENÇÃO</b></h4>
            </div>
            <div class="modal-body">
                <p>A taxa na origem em uma cotação de frete internacional refere-se aos custos adicionais cobrados no
                    país de origem da mercadoria antes do embarque. Esses encargos podem incluir despesas com manuseio
                    no porto ou aeroporto, documentação, taxas alfandegárias, inspeção, entre outros serviços
                    necessários para preparar o envio. Essas taxas variam de acordo com o local de origem, o tipo de
                    carga e o modal de transporte, e está presente sempre no Incoterm EXW.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-default-TaxasDA">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>ATENÇÃO</b></h4>
            </div>
            <div class="modal-body">
                <p>As taxas no destino, são os custos aplicados após a chegada da mercadoria no país de destino. Elas
                    incluem despesas com manuseio no porto ou aeroporto (Capatazia), taxa de liberação de BL ou AWB
                    (documentação), inspeções, transporte interno até o destinatário (quando aplicável) e outras
                    possíveis despesas. Esses encargos variam de acordo com o local, tipo de mercadoria e o modal
                    utilizado, e estão presentes em todos os Incoterms.
                    Se tiver algum valor que não contemple, adicione ao campo de “outras despesas".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal Swift COde -->
<div class="modal fade" id="modal-default-CBM" tabindex="-1" role="dialog" aria-labelledby="modal-default-CBMLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-default-CBMLabel"><b>CBM (Cubic Meter) - o que é e como calcular?</b>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    O <strong>CBM (Cubic Meter)</strong> é uma unidade de medida que representa o volume de um objeto. A
                    fórmula para
                    calcular o CBM é:
                </p>
                <p>
                    Fórmula para cálculo <strong>em metros:</strong>
                    <li>CBM=Comprimento (m)xLargura (m)xAltura (m)</li>
                </p>
                <p>
                    Fórmula para cálculo <strong>em centímetros:</strong><br>
                    Primeiro, é necessário converter o resultado de centímetros cúbicos para metros cúbicos, dividindo
                    por 1.000.000 (já que 1 m³ = 1.000.000 cm³).
                    <li>Volume em cm³=Comprimento (cm)xLargura (cm)xAltura (cm)</li>
                </p>
                <p>
                    <strong>Exemplo:</strong><br>
                    Se um objeto tem 100 cm de comprimento, 50 cm de largura e 30 cm de altura:<br>
                    <li>Volume em cm³=100x50x30=150.000 cm³</li>
                    <li>CBM em m³=150.000/1.000.000 = 0,15 m³</li>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para incluir produto -->
<div class="modal fade" id="modalProduto" tabindex="-1" role="dialog" aria-labelledby="modalProdutoLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalProdutoLabel"><b>Incluir Produto</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCadastrarProduto" method="post" action="cliente/grava_pv_produto.php">
                    <input type="hidden" name="id_pv" value="<?php echo $idEstudo;?>">
                    <div class="form-group">
                        <label for="descricao">Descrição do Produto</label>
                        <input type="text" name="descricao_produto" placeholder="Digite a descrição resumida"
                            class="form-control" id="descricao" required>
                    </div>
                    <div class="form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" name="qt_produto" class="form-control" id="quantidade" required>
                    </div>
                    <div class="form-group">
                        <label for="peso">Peso Líquido (kg)</label>
                        <input type="text" class="form-control" name="peso_liquido" id="pesoLiquido" required>
                    </div>
                    <div class="form-group">
                        <label>Valor total do item de embarque
                            (<?=$dados['moeda_padrao'];?>)</label>
                        <input type="text" class="form-control" name="valor_total_item" id="valorTotalItem1">
                    </div>
                    <div class="form-group">
                        <label for="ncm">NCM</label>
                        <?php
                        $sql = "SELECT ncm FROM ncm WHERE CHAR_LENGTH(ncm) = 10 ORDER BY ncm ASC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
                        <select name="ncm" required class="form-control select2" style="width: 100%;">
                            <?php foreach ($resultados as $resultado): ?>
                            <option value="<?php echo $resultado['ncm'];?>">
                                <?php echo $resultado['ncm'];?>
                            </option>
                            <?php endforeach;?>
                        </select>
                        <div class="form-group">
                            <label for="icms">Alíquota ICMS (%)</label>
                            <input type="number" class="form-control" name="aliquota_icms" id="icms" value="18"
                                required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                <i class="fa fa-ban"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-floppy-o"></i>
                                Salvar
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>Incoterms®2020</b><br>
                    <small>Termos Internacionais de Comércio (Incoterms) discriminados pela International Chamber of
                        Commerce (ICC) em sua Publicação nº 723-E, de 2020.</small>
                </h4>
            </div>
            <div class="modal-body">
                <h4>EXW</h4>
                <p>EX WORKS (named place of delivery) NA ORIGEM (local de entrega nomeado).</p>
                <p>O vendedor limita-se a colocar a mercadoria à disposição do comprador no estabelecimento do vendedor,
                    no prazo estabelecido, não se responsabilizando pelo desembaraço para exportação nem pelo
                    carregamento da mercadoria em qualquer veículo coletor.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Nota: em virtude de o comprador estrangeiro não dispor de condições legais para providenciar o
                    desembaraço para saída de bens do País, fica subentendido que esta providência é adotada pelo
                    vendedor, sob suas expensas e riscos, no caso da exportação brasileira</p>
                <hr>
                <h4>FCA</h4>
                <p>FREE CARRIER (named place of delivery) LIVRE NO TRANSPORTADOR (local de entrega nomeado).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando entrega a mercadoria,
                    desembaraçada para a exportação, ao transportador ou a outra pessoa indicada pelo comprador, no
                    local nomeado do país de origem.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento.</p>
                <hr>
                <h4>FAS</h4>
                <p>FREE ALONGSIDE SHIP (named port of shipment) LIVRE AO LADO DO NAVIO (porto de embarque nomeado).</p>
                <p>O vendedor encerra suas obrigações no momento em que a mercadoria é colocada, desembaraçada para
                    exportação, ao longo do costado do navio transportador indicado pelo comprador, no cais ou em
                    embarcações utilizadas para carregamento da mercadoria, no porto de embarque nomeado pelo comprador.
                </p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior).</p>
                <hr>
                <h4>FCB</h4>
                <p>FREE ON BOARD (named port of shipment) LIVRE A BORDO (porto de embarque nomeado).</p>
                <p>O vendedor encerra suas obrigações e responsabilidades quando a mercadoria, desembaraçada para a
                    exportação, é entregue, arrumada, a bordo do navio no porto de embarque, ambos indicados pelo
                    comprador, na data ou dentro do período acordado.</p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior)..</p>
                <hr>
                <h4>CFR</h4>
                <p>COST AND FREIGHT (named port of destination) CUSTO E FRETE (porto de destino nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FOB, o vendedor contrata e paga frete e
                    custos necessários para levar a mercadoria até o porto de destino combinado.</p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior).</p>
                <hr>
                <h4>CIF</h4>
                <p>COST, INSURANCE AND FREIGHT (named port of destination) CUSTO, SEGURO E FRETE (porto de destino
                    nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FOB, o vendedor contrata e paga frete,
                    custos e seguro relativos ao transporte da mercadoria até o porto de destino combinado.</p>
                <p>Utilizável exclusivamente no transporte aquaviário (marítimo ou hidroviário interior).</p>
                <hr>
                <h4>CPT</h4>
                <p>CARRIAGE PAID TO (named place of destination) TRANSPORTE PAGO ATÉ (local de destino nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FCA, o vendedor contrata e paga frete e
                    custos necessários para levar a mercadoria até o local de destino combinado.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <hr>
                <h4>CIP</h4>
                <p>CARRIAGE AND INSURANCE PAID TO (named place of destination) TRANSPORTE E SEGURO PAGOS ATÉ (local de
                    destino nomeado).</p>
                <p>Além de arcar com obrigações e riscos previstos para o termo FCA, o vendedor contrata e paga frete,
                    custos e seguro relativos ao transporte da mercadoria até o local de destino combinado.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <hr>
                <h4>DAP</h4>
                <p>DELIVERED AT PLACE (named place of destination) ENTREGUE NO LOCAL (local de destino nomeado).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando coloca a mercadoria à
                    disposição do comprador, na data ou dentro do período acordado, num local indicado no país de
                    destino, pronta para ser descarregada do veículo transportador e não desembaraçada para importação.
                </p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento.</p>
                <hr>
                <h4>DPU</h4>
                <p>DELIVERED AT PLACE UNLOADED (named place of destination) ENTREGUE NO LOCAL DESCARREGADO (local de
                    destino).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando a mercadoria é colocada à
                    disposição do comprador, na data ou dentro do período acordado, em local determinado no país de
                    destino, descarregada do veículo transportador mas não desembaraçada para importação.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento. Termo definido
                    em substituição ao DAT, com a diferença que o DAT determinava a “entrega” exclusivamente em
                    terminais de carga, podendo o DPU ser utilizado em terminais ou qualquer outro local determinado
                    (por exemplo o armazém do comprador).</p>
                <h4>DDP</h4>
                <p>DELIVERED DUTY PAID (named place of destination) ENTREGUE COM DIREITOS PAGOS (local de destino
                    nomeado).</p>
                <p>O vendedor completa suas obrigações e encerra sua responsabilidade quando a mercadoria é colocada à
                    disposição do comprador, na data ou dentro do período acordado, no local de destino designado no
                    país importador, não descarregada do meio de transporte. O vendedor, além do desembaraço, assume
                    todos os riscos e custos, inclusive impostos, taxas e outros encargos incidentes na importação.</p>
                <p>Utilizável em qualquer modalidade de transporte.</p>
                <p>Comprador e vendedor poderão utilizar transporte próprio em trechos do deslocamento. Nota: em razão
                    de o vendedor estrangeiro não dispor de condições legais para providenciar o desembaraço para
                    entrada de bens do País, <u>este termo não pode ser utilizado na importação brasileira</u>, devendo
                    ser escolhido o DPU ou DAP no caso de preferência por condição disciplinada pela ICC.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-informacao').modal('show');
});

$(document).ready(function() {
    $('.input-group.date').datepicker({
        format: 'dd/mm/yyyy', // Formato da data
        autoclose: true, // Fechar automaticamente ao selecionar a data
        todayHighlight: true // Destaque a data de hoje
    });
});

var currentDivId = 1;

function mostrarDiv() {
    var currentDiv = document.getElementById('row' + currentDivId);
    if (currentDiv) {
        currentDiv.style.display = 'block';
        currentDivId++;
    }
}

function ocultarDiv(id) {
    var div = document.getElementById(id);
    div.style.display = "none";
}

function mostrarOcultarTipoCarga() {
    var div1 = document.getElementById("div1");
    var div2 = document.getElementById("div2");

    if (document.getElementById("radioDiv1").checked) {
        div1.style.display = "block";
        div2.style.display = "none";
    } else {
        div1.style.display = "none";
        div2.style.display = "block";
    }

}

function mostrarOcultarSelect() {
    var select = document.getElementById("frm_brand");
    var select1 = document.getElementById("aer");
    var select11 = document.getElementById("tc_aer");
    var select2 = document.getElementById("mar");
    var select22 = document.getElementById("tc_mar");

    if (select.selectedIndex == 1) {
        select1.style.display = "block";
        select1.name = "incoterms"; // Nome para envio correto no POST
        select11.style.display = "block";
        select11.name = "tipo_carga"; // Nome para envio correto no POST
        select2.style.display = "none";
        select2.name = "incoterms_mar";
        select22.style.display = "none";
        select22.name = "tipo_carga_mar";
    } else if (select.selectedIndex == 2) {
        select1.style.display = "none";
        select1.name = "incoterms_aer";
        select11.style.display = "none";
        select11.name = "tipo_carga_aer";
        select2.style.display = "block";
        select2.name = "incoterms";
        select22.style.display = "block";
        select22.name = "tipo_carga"; // Nome para envio correto no POST
    } else {
        select1.style.display = "none";
        select11.style.display = "none";
        select11.name = "tipo_carga_aer";
        select2.style.display = "none";
        select22.style.display = "none";
        select22.name = "tipo_carga_mar";
    }
}

function mostrarOcultarVFI() {
    var vfiSim = document.getElementById('vfi_sim');
    var vfiNao = document.getElementById('vfi_nao');
    var valorTotalVFI = document.getElementById('valorTotalVFI');
    var valorTotalTaxas = document.getElementById('valorTotalTaxas');
    var valorTotalSeguro = document.getElementById('valorTotalSeguro');
    var mensagem = document.getElementById('mensagem');

    if (vfiNao.checked) {
        valorTotalVFI.disabled = true;
        valorTotalTaxas.disabled = true;
        valorTotalSeguro.disabled = true;
        valorTotalVFI.value = ''; // Limpar o valor quando desabilitado
        valorTotalTaxas.value = ''; // Limpar o valor quando desabilitado
        valorTotalSeguro.value = ''; // Limpar o valor quando desabilitado
        mensagem.innerHTML = 'Ao salvar você terá seu cálculo';
    } else {
        valorTotalVFI.disabled = false;
        valorTotalTaxas.disabled = false;
        valorTotalSeguro.disabled = false;
        mensagem.innerText = '';
    }
}

function mostrarOcultarArmazenagem() {
    var estimativaSim = document.getElementById('estimativaarm_sim');
    var estimativaNao = document.getElementById('estimativaarm_nao');
    var valorTotalArm = document.getElementById('valorTotalArm');

    if (estimativaNao.checked) {
        valorTotalArm.disabled = true;
        $('#modal-armazenagem').modal('show'); // Abrir a janela modal
    } else {
        valorTotalArm.disabled = false;
    }
}

function mostrarOcultarHD() {
    var sim = document.getElementById('estimativahd_sim');
    var nao = document.getElementById('estimativahd_nao');
    var campoHD = document.getElementById('valorTotalHD');

    if (sim.checked) {
        campoHD.removeAttribute('readonly');
        campoHD.value = '';
    } else if (nao.checked) {
        campoHD.setAttribute('readonly', 'readonly');
        campoHD.value = '950,00';
    }
}

function habilitarQtConteiner() {
    var tipoCarga = document.getElementById('tc_mar').value;
    var qtConteiner = document.getElementById('qtConteiner');

    if (tipoCarga === 'container_20' || tipoCarga === 'container_40') {
        qtConteiner.removeAttribute('disabled');
    } else {
        qtConteiner.setAttribute('disabled', 'disabled');
        qtConteiner.value = ''; // Clear the field if not needed
    }
}

// Chamando a função ao carregar a página para garantir a configuração inicial correta
document.addEventListener('DOMContentLoaded', function() {
    mostrarOcultarSelect();
    mostrarOcultarVFI();
    mostrarOcultarArmazenagem();
    mostrarOcultarHD();
    habilitarQtConteiner();
    // Initialize fields based on default selection
    toggleFields();
});

function removerNaoNumericos() {
    var campo = document.getElementById("valor_total");
    var campo2 = document.getElementById("valor_total_c");
    campo.value = campo.value.replace(/\D/g, "");
    campo2.value = campo2.value.replace(/\D/g, "");
}

function toggleFields() {
    const container20 = document.getElementById('container_20').checked;
    const container40 = document.getElementById('container_40').checked;
    const cargaSol = document.getElementById('carga_sol').checked;

    document.getElementById('qtde_container').disabled = !(container20 || container40);
    document.getElementById('peso_bruto').disabled = !cargaSol;
}

// Exemplo de uso: chamando a função ao digitar
var campo = document.getElementById("valor_total");
var campo2 = document.getElementById("valor_total_c");
campo.addEventListener("input", removerNaoNumericos);
campo2.addEventListener("input", removerNaoNumericos);
</script>