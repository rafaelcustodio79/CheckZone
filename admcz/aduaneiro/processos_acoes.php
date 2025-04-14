<?php
$PDO = db_connect();
$user_id = $_SESSION['user_id']; // Certifique-se de que o ID do usuário está na sessão
$idProcesso = isset($_GET['idProcesso']) ? $_GET['idProcesso'] : null;
$idEmpresa = isset($_GET['idEmpresa']) ? $_GET['idEmpresa'] : null;

// Usuário é administrador, pode ver todos os processos, incluindo o nome do gerente da conta
$sql = "SELECT pro.*, pat.*, emp.*, proi.*, usuario.nome AS nome_gerente
        FROM processos pro
        INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
        INNER JOIN empresa emp ON pro.id_empresa = emp.id_empresa
        LEFT JOIN usuario ON emp.id_gerente_conta = usuario.id
        LEFT JOIN processos_informacoes proi ON pro.id_processo = proi.id_processo
        WHERE pro.id_processo = :idProcesso AND pro.id_empresa = :idEmpresa";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
$stmt->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmt->execute();
$resultados = $stmt->fetch(PDO::FETCH_ASSOC);

$folderName = $resultados['ref_calvet'].'='.$resultados['ref_cliente'];

$anoCorrente = date("Y");

$sqlDrive = "SELECT * FROM pasta_drive WHERE id_empresa = :id_empresa AND ano = :ano";
$stmtDrive = $PDO->prepare($sqlDrive);
$stmtDrive->bindParam(':id_empresa', $idEmpresa, PDO::PARAM_INT);
$stmtDrive->bindParam(':ano', $anoCorrente, PDO::PARAM_INT);
$stmtDrive->execute();
$resultadosDrive = $stmtDrive->fetch(PDO::FETCH_ASSOC);
$idPastaRaiz = isset($resultadosDrive['id_pasta_drive']) ?? null;

#Rastreabilidade
$sqlR = "SELECT * FROM processos_rastreabilidade pror
        INNER JOIN empresa emp ON pror.id_empresa = emp.id_empresa
        INNER JOIN usuario usu ON pror.id_usuario = usu.id
        LEFT JOIN processos_aux_workflow paw ON pror.id_aux_wf = paw.id_aux_wf
        WHERE pror.id_empresa = :idEmpresa AND pror.id_processo = :idProcesso
        ORDER BY pror.id_pr DESC";
$stmtR = $PDO->prepare($sqlR);
$stmtR->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtR->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
$stmtR->execute();
$resultadosR = $stmtR->fetchAll(PDO::FETCH_ASSOC);

#Workflow
// Primeira consulta: Buscar processos_workflow
$sqlWF = "SELECT * FROM processos_workflow pwf
          WHERE id_processo = :idProcesso AND id_empresa = :idEmpresa
          ORDER BY pwf.id_wf ASC";
$stmtWF = $PDO->prepare($sqlWF);
$stmtWF->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
$stmtWF->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
$stmtWF->execute();
$resultadosWF = $stmtWF->fetchAll(PDO::FETCH_ASSOC);

// Extrair todos os id_aux_wf encontrados
$idAuxWfArray = array_column($resultadosWF, 'id_aux_wf');

// Definir a condição do canal com base em $resultados['parametrizacao']
$canal = $resultados['parametrizacao'];
switch ($canal) {
    case 'Canal Verde':
        $mostraCanal = 1;
        break;
    case 'Canal Amarelo':
        $mostraCanal = 2;
        break;
    case 'Canal Vermelho':
        $mostraCanal = 3;
        break;
    case 'Canal Cinza':
        $mostraCanal = 4;
        break;
    default:
        $mostraCanal = 0;
}

// Montar a consulta da tabela processos_aux_workflow com filtro NOT IN
$sqlAWF = "SELECT * FROM processos_aux_workflow paw
           WHERE canal = ?";

// Verificar se temos id_aux_wf para excluir
if (!empty($idAuxWfArray)) {
    // Criar placeholders para o array de ids
    $placeholders = implode(',', array_fill(0, count($idAuxWfArray), '?'));
    $sqlAWF .= " AND paw.id_aux_wf NOT IN ($placeholders)";
}

$sqlAWF .= " ORDER BY paw.id_aux_wf ASC";
$stmtAWF = $PDO->prepare($sqlAWF);

// Bind do canal (agora como primeiro parâmetro posicional)
$stmtAWF->bindValue(1, $mostraCanal, PDO::PARAM_INT);

// Bind dos valores de id_aux_wf, se houver
if (!empty($idAuxWfArray)) {
    foreach ($idAuxWfArray as $index => $idAuxWf) {
        $stmtAWF->bindValue($index + 2, $idAuxWf, PDO::PARAM_INT); // Começa no índice 2
    }
}

// Executar a segunda consulta
$stmtAWF->execute();
$resultadosAWF = $stmtAWF->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
.bg-1,
.bg-1>a {
    background-color: #013e51;
    color: #FFF;

}

.bg-2,
.bg-2>a {
    background-color: #a0c194;
    color: #000;

}

.bg-3,
.bg-3>a {
    background-color: #40aea5;
    color: #000;

}

.bg-4,
.bg-4>a {
    background-color: #127676;
    color: #FFF;

}

.badge-ref {
    background-color: #3c5252;
    color: #FFF;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Processo</h1>
                <p>
                    Ref. Cliente: <strong class="badge badge-info"><?=$resultados['ref_cliente'];?></strong>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Ref. Calvet: <strong class="badge badge-ref"><?=$resultados['ref_calvet'];?></strong>
                </p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Aduaneiro</li>
                    <li class="breadcrumb-item">Processos</li>
                    <li class="breadcrumb-item active">Ações</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">

        <p>Ações</p>

        <div class="row mb-2">
            <a href="#" data-target="#modal-info" data-toggle="modal" class="btn btn-app bg-1">
                <i class="fas fa-info-circle"></i> Informações
            </a>
            <a href="#" data-target="#modal-workflow" data-toggle="modal" class="btn btn-app bg-2">
                <i class="fas fa-history"></i> Histórico
            </a>
            <a href="#" data-target="#modal-coment" data-toggle="modal" class="btn btn-app bg-3">
                <i class="fas fa-comments"></i> Comentários
            </a>
            <a href="#" data-target="#modal-documentacao" data-toggle="modal" class="btn btn-app bg-4">
                <i class="fas fa-folder"></i> Documentação
            </a>
            <a href="aduaneiro.php?a=aduaneiro&b=processos_profile&idProcesso=<?=$idProcesso;?>&idEmpresa=<?=$idEmpresa;?>"
                class="btn btn-app bg-purple">
                <i class="fas fa-eye"></i> Visualizar
            </a>
        </div>


    </div>
</div>

<div class="content">
    <div class="container-fluid">

        <div class="row">

            <div class="col-md-12 card">
                <div class="card-header">
                    <h3 class="card-title">Últimos registros das atividades:</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="listaRegistroAtividades" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tipo de atividade</th>
                                <th>Registrado em</th>
                                <th>Registrado por</th>
                                <th>Tipo</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultadosR as $rastreabilidade) : ?>
                            <tr>
                                <td><?=$rastreabilidade['local'];?></td>
                                <td><?=dateConvert($rastreabilidade['data_hora']);?></td>
                                <td><?=$rastreabilidade['nome'];?></td>
                                <td><?=$resultados['titulo_tipo'];?></td>
                                <td class="text-center">
                                    <?php if ($rastreabilidade['id_aux_wf']) : ?>
                                    <?php 
        $idAuxWf = $rastreabilidade['id_aux_wf'];
        $txtAtividade = "Tem certeza que deseja excluir a atividade: " . $rastreabilidade['titulo_wf'] . "?";
    ?>
                                    <a href="aduaneiro/processos_workflow_deletar_item.php?idPr=<?=$rastreabilidade['id_pr'];?>&idProcesso=<?=$idProcesso;?>&idEmpresa=<?=$idEmpresa;?>&id_aux_wf=<?= $idAuxWf; ?>"
                                        onclick="return confirm('<?= $txtAtividade; ?>');"
                                        title="<?=$rastreabilidade['titulo_wf'];?>">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
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

<!-- Modal -->
<div class="modal fade" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="modal-infoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="modal-infoLabel">
                    Dados do processo |
                    &nbsp;
                    <strong class="badge badge-info"><?=$resultados['ref_cliente'];?></strong>
                    &nbsp;
                    <strong class="badge badge-ref"><?=$resultados['ref_calvet'];?></strong>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="empresaAdd" method="post" action="aduaneiro/processos_informacoes_salvar.php">

                <input type="hidden" name="idUsuario" value="<?=$user_id;?>">
                <input type="hidden" name="idProcesso" value="<?=$idProcesso;?>">
                <input type="hidden" name="idEmpresa" value="<?=$idEmpresa;?>">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <input type="text" name="tipo" id="tipo" class="form-control"
                                    value="<?=$resultados['titulo_tipo']?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incoterms">Incoterm</label>
                                <select id='incoterms' name="incoterms" required class="form-control">
                                    <?php if ($resultados['incoterms']) : ?>
                                    <option value="<?=$resultados['incoterms'];?>"><?=$resultados['incoterms'];?>
                                    </option>
                                    <?php else : ?>
                                    <option value="" selected>Selecione</option>
                                    <?php endif; ?>
                                    <option value="FCA">FCA</option>
                                    <option value="EXW">EXW</option>
                                    <option value="CPT">CPT</option>
                                    <option value="CIP">CIP</option>
                                    <option value="DAP">DAP</option>
                                    <option value="FOB">FOB</option>
                                    <option value="CFR">CFR</option>
                                    <option value="CIF">CIF</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pais_origem">Origem</label>
                                <select id='pais_origem' name="pais_origem" required class="form-control">
                                    <?php if ($resultados['pais_origem']) : ?>
                                    <option value="<?=$resultados['pais_origem'];?>"><?=$resultados['pais_origem'];?>
                                    </option>
                                    <?php else : ?>
                                    <option value="" selected>Selecione</option>
                                    <?php endif; ?>
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pais_destino">Destino</label>
                                <select id='pais_destino' name="pais_destino" required class="form-control">
                                    <?php if ($resultados['pais_destino']) : ?>
                                    <option value="<?=$resultados['pais_destino'];?>"><?=$resultados['pais_destino'];?>
                                    </option>
                                    <?php else : ?>
                                    <option value="" selected>Selecione</option>
                                    <?php endif; ?>
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

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="porto_descarga">Aeroporto/Porto Descarga</label>
                                <select class="form-control" id="porto_descarga" name="porto_descarga">
                                    <?php if ($resultados['porto_descarga']) : ?>
                                    <option value="<?=$resultados['porto_descarga'];?>">
                                        <?=$resultados['porto_descarga'];?>
                                    </option>
                                    <?php else : ?>
                                    <option value="" selected>Selecione</option>
                                    <?php endif; ?>
                                    <?php if ($resultados['id_tipo_processo']== 1 || $resultados['id_tipo_processo']== 4) : ?>
                                    <!-- Aeroportos -->
                                    <option>Aeroporto Internacional de São Paulo/Guarulhos – Governador André Franco
                                        Montoro (GRU) – São Paulo</option>
                                    <option>Aeroporto Internacional do Galeão – Antônio Carlos Jobim (GIG) – Rio de
                                        Janeiro</option>
                                    <option>Aeroporto Internacional de Brasília – Presidente Juscelino Kubitschek (BSB)
                                        – Brasília</option>
                                    <option>Aeroporto Internacional de Viracopos (VCP) – Campinas, São Paulo</option>
                                    <option>Aeroporto Internacional Tancredo Neves/Confins (CNF) – Belo Horizonte
                                    </option>
                                    <option>Aeroporto Internacional de Salvador – Deputado Luís Eduardo Magalhães (SSA)
                                        – Salvador</option>
                                    <option>Aeroporto Internacional de Recife/Guararapes – Gilberto Freyre (REC) –
                                        Recife</option>
                                    <option>Aeroporto Internacional de Fortaleza – Pinto Martins (FOR) – Fortaleza
                                    </option>
                                    <option>Aeroporto Internacional Salgado Filho (POA) – Porto Alegre</option>
                                    <option>Aeroporto Internacional Eduardo Gomes (MAO) – Manaus</option>
                                    <option>Aeroporto Internacional Afonso Pena (CWB) – Curitiba</option>
                                    <option>Aeroporto Internacional de Belém/Val-de-Cans – Júlio Cezar Ribeiro (BEL) –
                                        Belém</option>
                                    <option>Aeroporto Internacional Hercílio Luz (FLN) – Florianópolis</option>
                                    <option>Aeroporto Internacional Marechal Rondon (CGB) – Cuiabá</option>
                                    <option>Aeroporto Internacional de Natal – Governador Aluízio Alves (NAT) – Natal
                                    </option>
                                    <option>Aeroporto Internacional Zumbi dos Palmares (MCZ) – Maceió</option>
                                    <option>Aeroporto Internacional Marechal Cunha Machado (SLZ) – São Luís</option>
                                    <option>Aeroporto Internacional Presidente Castro Pinto (JPA) – João Pessoa</option>
                                    <option>Aeroporto Internacional Eurico de Aguiar Salles (VIX) – Vitória</option>
                                    <option>Aeroporto Internacional Santa Genoveva (GYN) – Goiânia</option>
                                    <option>Aeroporto Internacional de Foz do Iguaçu/Cataratas (IGU) – Foz do Iguaçu
                                    </option>
                                    <option>Aeroporto Internacional de Campo Grande (CGR) – Campo Grande</option>
                                    <option>Aeroporto Internacional de Porto Velho – Governador Jorge Teixeira de
                                        Oliveira (PVH) – Porto Velho</option>
                                    <option>Aeroporto Internacional de Rio Branco – Plácido de Castro (RBR) – Rio Branco
                                    </option>
                                    <option>Aeroporto Internacional de Boa Vista – Atlas Brasil Cantanhede (BVB) – Boa
                                        Vista</option>
                                    <option>Aeroporto Internacional de Macapá – Alberto Alcolumbre (MCP) – Macapá
                                    </option>
                                    <option>Aeroporto Internacional de Tabatinga (TBT) – Tabatinga</option>
                                    <option>Aeroporto Internacional de Tefé (TFF) – Tefé</option>
                                    <option>Aeroporto Internacional de Corumbá (CMG) – Corumbá</option>
                                    <?php endif; ?>

                                    <?php if ($resultados['id_tipo_processo']== 2 || $resultados['id_tipo_processo']== 5) : ?>
                                    <!-- Portos -->
                                    <option>Porto de Santos (SP)</option>
                                    <option>Porto de Paranaguá (PR)</option>
                                    <option>Porto de Rio Grande (RS)</option>
                                    <option>Porto de Itajaí (SC)</option>
                                    <option>Porto de Navegantes (SC)</option>
                                    <option>Porto de Suape (PE)</option>
                                    <option>Porto de Salvador (BA)</option>
                                    <option>Porto de Pecém (CE)</option>
                                    <option>Porto de Manaus (AM)</option>
                                    <option>Porto de Vila do Conde (PA)</option>
                                    <option>Porto de Vitória (ES)</option>
                                    <option>Porto do Rio de Janeiro (RJ)</option>
                                    <option>Porto de Itaguaí (RJ)</option>
                                    <option>Porto de São Francisco do Sul (SC)</option>
                                    <option>Porto de Imbituba (SC)</option>
                                    <option>Porto de Fortaleza (CE)</option>
                                    <option>Porto de Aratu (BA)</option>
                                    <option>Porto de Maceió (AL)</option>
                                    <option>Porto de Recife (PE)</option>
                                    <option>Porto de Cabedelo (PB)</option>
                                    <option>Porto de São Luís (MA)</option>
                                    <option>Porto de Macapá (AP)</option>
                                    <option>Porto de Santarém (PA)</option>
                                    <option>Porto de Corumbá (MS)</option>
                                    <option>Porto de Porto Velho (RO)</option>
                                    <option>Porto de Santana (AP)</option>
                                    <option>Porto de Altamira (PA)</option>
                                    <option>Porto de Belém (PA)</option>
                                    <option>Porto de Antonina (PR)</option>
                                    <option>Porto de Pelotas (RS)</option>
                                    <option>Porto de São Sebastião (SP)</option>
                                    <option>Porto de Barra dos Coqueiros (SE)</option>
                                    <option>Porto de Angra dos Reis (RJ)</option>
                                    <option>Porto de Rio Grande da Serra (SP)</option>
                                    <option>Porto de Laguna (SC)</option>
                                    <option>Porto de Caravelas (BA)</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="porto_destino">Aeroporto/Porto Destino</label>
                                <select class="form-control" id="porto_destino" name="porto_destino">
                                    <?php if ($resultados['porto_destino']) : ?>
                                    <option value="<?=$resultados['porto_destino'];?>">
                                        <?=$resultados['porto_destino'];?>
                                    </option>
                                    <?php else : ?>
                                    <option value="" selected>Selecione</option>
                                    <?php endif; ?>
                                    <!-- Aeroportos -->
                                    <option>Aeroporto Internacional de São Paulo/Guarulhos – Governador André Franco
                                        Montoro (GRU) – São Paulo</option>
                                    <option>Aeroporto Internacional do Galeão – Antônio Carlos Jobim (GIG) – Rio de
                                        Janeiro</option>
                                    <option>Aeroporto Internacional de Brasília – Presidente Juscelino Kubitschek (BSB)
                                        – Brasília</option>
                                    <option>Aeroporto Internacional de Viracopos (VCP) – Campinas, São Paulo</option>
                                    <option>Aeroporto Internacional Tancredo Neves/Confins (CNF) – Belo Horizonte
                                    </option>
                                    <option>Aeroporto Internacional de Salvador – Deputado Luís Eduardo Magalhães (SSA)
                                        – Salvador</option>
                                    <option>Aeroporto Internacional de Recife/Guararapes – Gilberto Freyre (REC) –
                                        Recife</option>
                                    <option>Aeroporto Internacional de Fortaleza – Pinto Martins (FOR) – Fortaleza
                                    </option>
                                    <option>Aeroporto Internacional Salgado Filho (POA) – Porto Alegre</option>
                                    <option>Aeroporto Internacional Eduardo Gomes (MAO) – Manaus</option>
                                    <option>Aeroporto Internacional Afonso Pena (CWB) – Curitiba</option>
                                    <option>Aeroporto Internacional de Belém/Val-de-Cans – Júlio Cezar Ribeiro (BEL) –
                                        Belém</option>
                                    <option>Aeroporto Internacional Hercílio Luz (FLN) – Florianópolis</option>
                                    <option>Aeroporto Internacional Marechal Rondon (CGB) – Cuiabá</option>
                                    <option>Aeroporto Internacional de Natal – Governador Aluízio Alves (NAT) – Natal
                                    </option>
                                    <option>Aeroporto Internacional Zumbi dos Palmares (MCZ) – Maceió</option>
                                    <option>Aeroporto Internacional Marechal Cunha Machado (SLZ) – São Luís</option>
                                    <option>Aeroporto Internacional Presidente Castro Pinto (JPA) – João Pessoa</option>
                                    <option>Aeroporto Internacional Eurico de Aguiar Salles (VIX) – Vitória</option>
                                    <option>Aeroporto Internacional Santa Genoveva (GYN) – Goiânia</option>
                                    <option>Aeroporto Internacional de Foz do Iguaçu/Cataratas (IGU) – Foz do Iguaçu
                                    </option>
                                    <option>Aeroporto Internacional de Campo Grande (CGR) – Campo Grande</option>
                                    <option>Aeroporto Internacional de Porto Velho – Governador Jorge Teixeira de
                                        Oliveira (PVH) – Porto Velho</option>
                                    <option>Aeroporto Internacional de Rio Branco – Plácido de Castro (RBR) – Rio Branco
                                    </option>
                                    <option>Aeroporto Internacional de Boa Vista – Atlas Brasil Cantanhede (BVB) – Boa
                                        Vista</option>
                                    <option>Aeroporto Internacional de Macapá – Alberto Alcolumbre (MCP) – Macapá
                                    </option>
                                    <option>Aeroporto Internacional de Tabatinga (TBT) – Tabatinga</option>
                                    <option>Aeroporto Internacional de Tefé (TFF) – Tefé</option>
                                    <option>Aeroporto Internacional de Corumbá (CMG) – Corumbá</option>

                                    <!-- Portos -->
                                    <option>Porto de Santos (SP)</option>
                                    <option>Porto de Paranaguá (PR)</option>
                                    <option>Porto de Rio Grande (RS)</option>
                                    <option>Porto de Itajaí (SC)</option>
                                    <option>Porto de Navegantes (SC)</option>
                                    <option>Porto de Suape (PE)</option>
                                    <option>Porto de Salvador (BA)</option>
                                    <option>Porto de Pecém (CE)</option>
                                    <option>Porto de Manaus (AM)</option>
                                    <option>Porto de Vila do Conde (PA)</option>
                                    <option>Porto de Vitória (ES)</option>
                                    <option>Porto do Rio de Janeiro (RJ)</option>
                                    <option>Porto de Itaguaí (RJ)</option>
                                    <option>Porto de São Francisco do Sul (SC)</option>
                                    <option>Porto de Imbituba (SC)</option>
                                    <option>Porto de Fortaleza (CE)</option>
                                    <option>Porto de Aratu (BA)</option>
                                    <option>Porto de Maceió (AL)</option>
                                    <option>Porto de Recife (PE)</option>
                                    <option>Porto de Cabedelo (PB)</option>
                                    <option>Porto de São Luís (MA)</option>
                                    <option>Porto de Macapá (AP)</option>
                                    <option>Porto de Santarém (PA)</option>
                                    <option>Porto de Corumbá (MS)</option>
                                    <option>Porto de Porto Velho (RO)</option>
                                    <option>Porto de Santana (AP)</option>
                                    <option>Porto de Altamira (PA)</option>
                                    <option>Porto de Belém (PA)</option>
                                    <option>Porto de Antonina (PR)</option>
                                    <option>Porto de Pelotas (RS)</option>
                                    <option>Porto de São Sebastião (SP)</option>
                                    <option>Porto de Barra dos Coqueiros (SE)</option>
                                    <option>Porto de Angra dos Reis (RJ)</option>
                                    <option>Porto de Rio Grande da Serra (SP)</option>
                                    <option>Porto de Laguna (SC)</option>
                                    <option>Porto de Caravelas (BA)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="terminal_descarga">Terminal Descarga</label>
                                <input type="text" class="form-control" id="terminal_descarga" name="terminal_descarga"
                                    value="<?=$resultados['terminal_descarga'];?>">
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="n_li">Nº LI</label>
                                <input type="text" class="form-control" id="n_li" name="n_li"
                                    value="<?=$resultados['n_li'];?>">
                            </div>
                        </div>

                        <div class=" col-md-6">
                            <div class="form-group">
                                <label for="status_li">Status LI</label>
                                <input type="text" class="form-control" id="status_li" name="status_li"
                                    value="<?=$resultados['status_li'];?>">
                            </div>
                        </div>

                        <div class=" col-md-4">
                            <div class="form-group">
                                <label for="n_di">Nº DI</label>
                                <input type="text" class="form-control" id="n_di" name="n_di"
                                    value="<?=$resultados['n_di'];?>" readonly>
                            </div>
                        </div>

                        <div class=" col-md-4">
                            <div class="form-group">
                                <label for="registro_di">Data registro DI</label>
                                <input type="date" class="form-control" id="registro_di" name="registro_di"
                                    value="<?=$resultados['registro_di'];?>" readonly>
                            </div>
                        </div>

                        <div class=" col-md-4">
                            <div class="form-group">
                                <label for="parametrizacao">Parametrização</label>
                                <select class="form-control" id="parametrizacao" name="parametrizacao">
                                    <?php if ($resultados['parametrizacao']) : ?>
                                    <option value="<?=$resultados['parametrizacao'];?>">
                                        <?=$resultados['parametrizacao'];?>
                                    </option>
                                    <?php else : ?>
                                    <option value="" selected>Selecione</option>
                                    <?php endif; ?>
                                    <option value="Canal Verde">Canal Verde</option>
                                    <option value="Canal Amarelo">Canal Amarelo</option>
                                    <option value="Canal Vermelho">Canal Vermelho</option>
                                    <option value="Canal Cinza">Canal Cinza</option>
                                </select>
                            </div>
                        </div>

                        <div class=" col-md-12">
                            <div class="form-group">
                                <label for="observacoes">Observações</label>
                                <input type="text" class="form-control" id="observacoes" name="observacoes"
                                    value="<?=$resultados['observacoes'];?>">
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descricao_produto">Descrição do produto</label>
                                <input type="text" class="form-control" id="descricao_produto" name="descricao_produto"
                                    value="<?=$resultados['descricao_produto'];?>">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="exportador">Exportador</label>
                                <input type="text" class="form-control" id="exportador" name="exportador"
                                    value="<?=$resultados['exportador'];?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="n_invoice">Nº da Invoice</label>
                                <input type="text" class="form-control" id="n_invoice" name="n_invoice"
                                    value="<?=$resultados['n_invoice'];?>">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="carga">Carga</label>
                                <select class="form-control" id="carga" name="carga">
                                    <?php if ($resultados['carga']) : ?>
                                    <option value="<?=$resultados['carga'];?>">
                                        <?=$resultados['carga'];?>
                                    </option>
                                    <?php else : ?>
                                    <option value="" selected>Selecione</option>
                                    <?php endif; ?>
                                    <option value="Container">Container</option>
                                    <option value="Amarrado">Amarrado</option>
                                    <option value="Baú de Madeira">Baú de Madeira</option>
                                    <option value="Baú de Metal">Baú de Metal</option>
                                    <option value="Barrica de Ferro">Barrica de Ferro</option>
                                    <option value="Barrica de Fibra de Vidro">Barrica de Fibra de Vidro
                                    </option>
                                    <option value="Barrica de Plástico">Barrica de Plástico</option>
                                    <option value="Big Bag">Big Bag</option>
                                    <option value="Bloco">Bloco</option>
                                    <option value="Bobina">Bobina</option>
                                    <option value="Bombona">Bombona</option>
                                    <option value="Botijão">Botijão</option>
                                    <option value="Caixa de Isopor">Caixa de Isopor</option>
                                    <option value="Caixa de Madeira">Caixa de Madeira</option>
                                    <option value="Caixa de Metal">Caixa de Metal</option>
                                    <option value="Caixa de Papelão">Caixa de Papelão</option>
                                    <option value="Caixa de Plástico">Caixa de Plástico</option>
                                    <option value="Canudo">Canudo</option>
                                    <option value="Carretel">Carretel</option>
                                    <option value="Cilindro">Cilindro</option>
                                    <option value="Engradado">Engradado</option>
                                    <option value="Engradado de Madeira">Engradado de Madeira</option>
                                    <option value="Engradado de Plástico">Engradado de Plástico</option>
                                    <option value="Envelope">Envelope</option>
                                    <option value="Estrado">Estrado</option>
                                    <option value="Fardo">Fardo</option>
                                    <option value="Frasco">Frasco</option>
                                    <option value="Galão">Galão</option>
                                    <option value="Granel">Granel</option>
                                    <option value="Lata">Lata</option>
                                    <option value="Maleta">Maleta</option>
                                    <option value="Pacote">Pacote</option>
                                    <option value="Pallet">Pallet</option>
                                    <option value="Pallet de Madeira">Pallet de Madeira</option>
                                    <option value="Pallet de Plástico">Pallet de Plástico</option>
                                    <option value="Peça">Peça</option>
                                    <option value="Rolo">Rolo</option>
                                    <option value="Saco de Aniagem">Saco de Aniagem</option>
                                    <option value="Saco de Lona">Saco de Lona</option>
                                    <option value="Saco de Nylon">Saco de Nylon</option>
                                    <option value="Saco de Papel">Saco de Papel</option>
                                    <option value="Saco de Papelão">Saco de Papelão</option>
                                    <option value="Saco de Plástico">Saco de Plastico</option>
                                    <option value="Sacola">Sacola</option>
                                    <option value="San Bag">San Bag</option>
                                    <option value="Tambor de Metal">Tambor de Metal</option>
                                    <option value="Tambor de Papel">Tambor de Papel</option>
                                    <option value="Tambor de Plástico">Tambor de Plástico</option>
                                    <option value="Outros/Diversos">Outros/Diversos</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantidade">Quantidade</label>
                                <input type="number" step=1 class="form-control" id="quantidade" name="quantidade"
                                    value="<?=$resultados['quantidade'];?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome_navio">Nome do Navio</label>
                                <input type="text" class="form-control" id="nome_navio" name="nome_navio"
                                    value="<?=$resultados['quantidade'];?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bl">BL</label>
                                <input type="text" class="form-control" id="bl" name="bl"
                                    value="<?=$resultados['bl'];?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="eta">ETA</label>
                                <input type="date" class="form-control" id="eta" name="eta"
                                    value="<?=$resultados['eta'];?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="confirmacao_chegada">Confirmação de Chegada</label>
                                <input type="date" class="form-control" id="confirmacao_chegada"
                                    name="confirmacao_chegada" value="<?=$resultados['confirmacao_chegada'];?>">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vencimento_armazenagem">Vencimento Armazenagem</label>
                                <input type="date" class="form-control" id="vencimento_armazenagem"
                                    name="vencimento_armazenagem" value="<?=$resultados['vencimento_armazenagem'];?>">
                            </div>
                        </div>

                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fechar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                        Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Comentário -->
<div class="modal fade" id="modal-coment" tabindex="-1" role="dialog" aria-labelledby="modal-comentLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="modal-comentLabel">
                    Comentários |
                    &nbsp;
                    <strong class="badge badge-info"><?=$resultados['ref_cliente'];?></strong>
                    &nbsp;
                    <strong class="badge badge-ref"><?=$resultados['ref_calvet'];?></strong>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="editUser" method="post" action="aduaneiro/processos_comentarios_salvar.php">

                <input type="hidden" name="idUsuario" value="<?=$user_id;?>">
                <input type="hidden" name="idProcesso" value="<?=$idProcesso;?>">
                <input type="hidden" name="idEmpresa" value="<?=$idEmpresa;?>">

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-body">
                                <label for="comentario">Adicione o seu comentário</label>
                                <textarea id="comentario" name="comentario" class="summernote"></textarea>
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fechar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Histórico -->
<div class="modal fade" id="modal-workflow" tabindex="-1" role="dialog" aria-labelledby="modal-workflowLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="modal-workflowLabel">
                    Histórico / Workflow |
                    &nbsp;
                    <strong class="badge badge-info"><?=$resultados['ref_cliente'];?></strong>
                    &nbsp;
                    <strong class="badge badge-ref"><?=$resultados['ref_calvet'];?></strong>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="editUser" method="post" action="aduaneiro/processos_workflow_salvar.php">

                <input type="hidden" name="idUsuario" value="<?=$user_id;?>">
                <input type="hidden" name="idProcesso" value="<?=$idProcesso;?>">
                <input type="hidden" name="idEmpresa" value="<?=$idEmpresa;?>">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="box-body">
                                <label for="acao">Selecione uma Ação</label>
                                <select class="form-control" id="id_aux_wf" name="id_aux_wf" required>
                                    <option value="" selected>---</option>
                                    <?php foreach($resultadosAWF as $auxwf) : ?>
                                    <option value="<?=$auxwf['id_aux_wf'];?>"><?=$auxwf['titulo_wf'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="box-body">
                                <label for="data">Data</label>
                                <input type="date" class="form-control" id="data_acao" name="data_acao">
                            </div>
                        </div>
                    </div>

                    <!-- Campo de texto oculto -->
                    <div class="row" id="campo_di" style="display: none; padding-top: 10px;">
                        <div class="col-md-6">
                            <div class="box-body">
                                <label for="numero_di">Número da DI</label>
                                <input type="text" class="form-control" id="numero_di" name="numero_di"
                                    placeholder="Digite o nº da DI">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fechar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Documentação -->
<div class="modal fade" id="modal-documentacao" tabindex="-1" role="dialog" aria-labelledby="modal-documentacaoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="modal-documentacaoLabel">
                    Documentação Google Drive |
                    &nbsp;
                    <strong class="badge badge-info"><?=$resultados['ref_cliente'];?></strong>
                    &nbsp;
                    <strong class="badge badge-ref"><?=$resultados['ref_calvet'];?></strong>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php
    $apiKey = $googleApiKey;
    $folderName = $resultados['ref_calvet'].'='.$resultados['ref_cliente']; // Nome da pasta específica

    $parentFolderId = $idPastaRaiz;

    // Buscando a pasta pelo nome dentro da pasta raiz
    $url = "https://www.googleapis.com/drive/v3/files?q=name='" . urlencode($folderName) . "'%20and%20mimeType='application/vnd.google-apps.folder'%20and%20'$parentFolderId'%20in%20parents&key=" . $apiKey . "&fields=files(id,name,mimeType,webViewLink)";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Se a pasta for encontrada, buscar os arquivos nela
    if (isset($data['files'][0])) {
        $folderId = $data['files'][0]['id']; // ID da pasta encontrada
        $folderData = file_get_contents("https://www.googleapis.com/drive/v3/files?q='$folderId'%20in%20parents&key=" . $apiKey . "&fields=files(id,name,mimeType,webViewLink)");
        $folderContent = json_decode($folderData, true);
    }
?>

            <div class="container mt-4">
                <div class="row">
                    <?php if (isset($folderContent['files'])): ?>
                    <?php foreach ($folderContent['files'] as $file): ?>
                    <?php
            $fileName = htmlspecialchars($file['name']);
            $fileId = $file['id'];
            $fileLink = htmlspecialchars($file['webViewLink']) . "?usp=sharing";
            $isFolder = $file['mimeType'] === 'application/vnd.google-apps.folder';
            $icon = $isFolder ? 'fas fa-folder' : 'fas fa-file';
            $target = 'target="_blank"';
        ?>

                    <div class="col-md-3 mb-4">
                        <a href="<?php echo $fileLink; ?>" <?php echo $target; ?>
                            class="file-block d-block text-decoration-none">
                            <div class="file-icon text-center">
                                <i class="<?php echo $icon; ?> fa-3x text-secondary"></i>
                            </div>
                            <div class="file-name text-center mt-2">
                                <?php echo $fileName; ?>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <p>Nenhum arquivo ou pasta encontrado na pasta "<?php echo $folderName; ?>".</p>
                    <?php endif; ?>
                </div>
            </div>

            <style>
            .file-block {
                padding: 10px;
                background-color: #f8f9fa;
                border: 1px solid #e9ecef;
                border-radius: 8px;
                transition: background-color 0.3s;
            }

            .file-block:hover {
                background-color: #e2e6ea;
            }

            .file-icon i {
                color: #5f6368;
            }

            .file-name {
                font-size: 14px;
                color: #202124;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            </style>




        </div>
    </div>
</div>

<script>
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

function toggleGerenteConta() {
    var tipoEmpresa = document.getElementById("id_tipo_empresa").value;
    var gerenteConta = document.getElementById("gerenteConta");
    if (tipoEmpresa == "2") {
        gerenteConta.style.display = "block";
    } else {
        gerenteConta.style.display = "none";
    }
}

function openEditModal(userId) {
    // Aqui você pode adicionar lógica para buscar os dados do usuário se necessário.
    console.log("Abrindo modal para o usuário com ID: " + userId);

    // Exemplo de como abrir o modal (se estiver usando Bootstrap 4 ou 5):
    $('#editUserModal').modal('show');

    // Se precisar carregar dados via AJAX, pode fazer algo como:
    $.ajax({
        url: 'aduaneiro/empresas_buscar.php',
        method: 'GET',
        data: {
            id: userId
        },
        success: function(response) {
            // Assuming the response is a JSON object with user data
            var userData = JSON.parse(response);

            // Populate the modal fields with the user data
            $('#id_empresa').val(userData.id_empresa);
            $('#edit_id_tipo_empresa').val(userData.id_tipo_empresa);
            $('#edit_email_principal').val(userData.email_principal);
            $('#edit_nome_empresa').val(userData.nome_empresa);
            $('#edit_segmento_empresa').val(userData.segmento_empresa);
            $('#edit_cnpj').val(userData.cnpj);
            $('#edit_localizacao').val(userData.localizacao);
            $('#edit_idUsuario').val(userData.id_gerente_conta);
            // $('#edit_especializacoes').val(userData.especializacoes);
            // $('#edit_pontos_fortes').val(userData.pontos_fortes);
            $('#edit_website').val(userData.website);
            $('#edit_telefone_empresa').val(userData.telefone_empresa);

        },
        error: function() {
            alert('Error fetching user data.');
        }
    });
}
</script>