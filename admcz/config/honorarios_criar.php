<?php
$idHonorario = filter_var($_GET['idHonorario'] ?? null, FILTER_VALIDATE_INT);
$idEmpresa = filter_var($_GET['idEmpresa'] ?? null, FILTER_VALIDATE_INT);

if (!$idEmpresa) {
    header('Location: config.php?a=config&b=empresas&flag=erro&tip=ID da empresa inválido.');
    exit;
}

$PDO = db_connect();

$sql = "SELECT * FROM empresa emp
        LEFT JOIN honorarios hon ON emp.id_empresa = hon.id_empresa
        WHERE emp.id_empresa = :idEmpresa
        ORDER BY emp.id_empresa DESC";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idEmpresa', $idEmpresa);
$stmt->execute();
$resultadosHon = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Configurações</h1>
                <small>Honorários</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Configurações</li>
                    <li class="breadcrumb-item active">Honorários</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <h3>Honorários para
            <?=$resultadosHon['nome_empresa'];?>
        </h3>
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Cadastrando <b>SERVIÇOS</b>
                        </h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <form name="cadHonorarioServico" method="post" action="config/honorarios_servicos_salvar.php">
                            <input type="hidden" name="idEmpresa" value="<?=$idEmpresa;?>">
                            <input type="hidden" name="idHonorario" value="<?=$idHonorario;?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="servico">Serviço</label>
                                        <select class="form-control" id="servico" name="servico" required>
                                            <option value="">Selecione um Serviço</option>
                                            <option value="Honorário de Importação">Honorário de Importação</option>
                                            <option value="Honorário de Exportação">Honorário de Exportação</option>
                                            <option value="Honorário de Admissão Temporária">Honorário de Admissão
                                                Temporária</option>
                                            <option value="Sindaerj (SDA)">Sindaerj (SDA)</option>
                                            <option value="GRH">GRH</option>
                                            <option value="Taxa de Expediente">Taxa de Expediente</option>
                                            <option value="Emissão de Licença de Importação">Emissão de Licença de
                                                Importação</option>
                                            <option
                                                value="Liberação Portos, Aerop. e Fronteiras fora do Rio de Janeiro">
                                                Liberação Portos, Aerop. e Fronteiras fora do Rio de Janeiro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="forma">Forma de cálculo</label>
                                        <select class="form-control" id="fc_tipo" name="fc_tipo" required>
                                            <option value="">Selecione</option>
                                            <option value="Fixo">Fixo</option>
                                            <option value="Mínimo">Mínimo</option>
                                            <option value="Máximo">Máximo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="forma">Base de cálculo</label>
                                        <select class="form-control" id="base_calculo" name="base_calculo" required>
                                            <option value="">Selecione</option>
                                            <option value="sal_minimo">Salário Mínimo</option>
                                            <option value="ipca">IPCA</option>
                                            <option value="igpm">IGP-M</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" id="fc_valor_container" style="display: none;">
                                    <div class="form-group">
                                        <label for="forma">Forma de cálculo (x Sal. Min.)</label>
                                        <input type="number" step="0.1" class="form-control" id="fc_valor"
                                            name="fc_valor" placeholder="Digite o valor">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label id="label_valor_servico" for="valor_servico">Valor do Serviço
                                            (R$)</label>
                                        <input type="text" class="form-control" id="valor_servico" name="valor_servico"
                                            placeholder="0,00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary float-right"><i
                                            class="fas fa-save"></i> Salvar</button>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            Calculadora de <b>ISS</b>
                        </h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <form name="cadHonorario" method="post" action="config/honorarios_salvar_alteracao.php">
                            <input type="hidden" name="idEmpresa" value="<?=$idEmpresa;?>">
                            <input type="hidden" name="idHonorario" value="<?=$idHonorario;?>">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="valor_honorario">Valor do Honorário (R$)</label>
                                        <input type="text" class="form-control" id="valor_honorario"
                                            name="valor_honorario" placeholder="Digite o valor do Honorário" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="percent_iss">Porcentagem ISS</label>
                                        <select class="form-control" id="percent_iss" name="percent_iss" required>
                                            <option value="1">1%</option>
                                            <option value="2">2%</option>
                                            <option value="3">3%</option>
                                            <option value="4">4%</option>
                                            <option value="5" selected>5%</option>
                                            <option value="6">6%</option>
                                            <option value="7">7%</option>
                                            <option value="8">8%</option>
                                            <option value="9">9%</option>
                                            <option value="10">10%</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary float-right"><i
                                            class="fas fa-calculator"></i> Calcular</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-12">
                <table id="listaBanners" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th class="text-center" style="width: 180px;">Forma de cálculo</th>
                            <th class="text-center" style="width: 180px;">Base de cálculo</th>
                            <th class="text-center" style="width: 160px;">Forma de cálculo (x Sal. Min.)</th>
                            <th class="text-center">Valor do Serviço</th>
                            <th class="text-center" style="width: 30px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                        $sql = "SELECT * FROM honorarios_aux_servicos
                                        WHERE id_empresa = :idEmpresa
                                        ORDER BY servico ASC";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':idEmpresa', $idEmpresa);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
                        <?php foreach ($resultados as $resultado) : ?>
                        <tr>
                            <td>
                                <?= $resultado['servico']; ?>
                            </td>
                            <td class="text-center">
                                <?= $resultado['fc_tipo']; ?>
                            </td>
                            <td class="text-center">
                                <?= $resultado['base_calculo']; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($resultado['fc_valor']==0) : ?>
                                ---
                                <?php else : ?>
                                <?= number_format($resultado['fc_valor'], 2, ',', '.'); ?>
                                <?php endif;?>
                            </td>
                            <td class="text-right">
                                R$
                                <?= number_format($resultado['valor_servico'], 2, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <a href="config.php?a=config&b=honorarios_deletar_servico&idServico=<?= $resultado['id_servico']; ?>&idEmpresa=<?= $idEmpresa; ?>&idHonorario=<?= $idHonorario; ?>"
                                    onclick="return confirm('Deseja realmente excluir esse Serviço?')"><i
                                        class="fas fa-trash" title="Apagar"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="fundo-celula-1">
                            <td colspan="4" class="text-right">
                                <strong>HONORÁRIO</strong>
                            </td>
                            <td class="text-right">
                                <strong>
                                    R$
                                    <?= number_format($resultadosHon['valor_honorarios'], 2, ',', '.'); ?>
                                </strong>
                            </td>
                        </tr>
                        <tr class="fundo-celula-2">
                            <td colspan="4" class="text-right">
                                <strong>ISS</strong>
                            </td>
                            <td class="text-right">
                                <?php
                                $iss = ($resultadosHon['valor_honorarios'] * $resultadosHon['iss'])/100;
?>
                                <strong>
                                    R$
                                    <?= number_format($iss, 2, ',', '.'); ?>
                                </strong>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

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
</script>