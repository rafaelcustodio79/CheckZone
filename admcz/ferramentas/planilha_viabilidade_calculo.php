<?php
$idEstudo = isset($_GET['idEstudo']) ? $_GET['idEstudo'] : null;

$PDO = db_connect();

$sql = "SELECT pv.*, tc.real_venda FROM planilha_viabilidade pv
        LEFT JOIN taxa_cambial tc ON pv.moeda_padrao = tc.moeda 
        WHERE id_pv = :id_pv";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_pv', $idEstudo);
$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

$sqlProd = "SELECT * FROM planilha_viabilidade_adicoes 
WHERE id_pv = :id_pv";
$stmtProd = $PDO->prepare($sqlProd);
$stmtProd->bindParam(':id_pv', $idEstudo);
$stmtProd->execute();
$dadosProd = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
$qtProdutos = count($dadosProd);
?>
<style>
#listaNcm {
    display: none;
}

.d-flex {
    display: flex;
}

.justify-content-between {
    justify-content: space-between;
}

.align-items-center {
    align-items: center;
}

.logo-left {
    margin-right: auto;
    max-width: 80px;
}
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ferramentas
        <small>Viabilidade</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Ferramentas</a></li>
        <li><a href="#">Viabilidade</a></li>
        <li class="active">Cálculo</li>
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

<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-aviso').modal('show');
});
</script>

<!-- Main content -->
<section class="content">

    <!-- Linha do Botões -->
    <div class="row">
        <div class="col-md-12">
            <a href="cliente.php?pg=plan_viabilidade_calculo&idEstudo=<?=$idEstudo;?>" class="btn btn-info">Resumo</a>

            <?php for ($i = 1; $i <= $qtProdutos; $i++) :
                // Gerar o link para cada produto
                $url = "cliente.php?pg=plan_viabilidade_calculo_produto&idProduto=$i&idEstudo=$idEstudo";
                ?>
            <a href="<?= $url ?>" class="btn btn-default">Produto
                <?= $i ?></a>
            <?php endfor; ?>

            <?php if ($dados['modal']=='aer' && $dados['valor_mercadoria'] < 3000) : ?>
            <a href="cliente.php?pg=plan_viabilidade_calculo_fxs&idEstudo=<?=$idEstudo;?>"
                class="btn btn-default">Formal x
                Simplificada</a>
            <?php elseif ($dados['modal']=='aer' && $dados['valor_mercadoria']>=3000) : ?>
            <button type="button" class="btn btn-default" disabled title="Simplificada somente até 3000 USD">Formal x
                Simplificada</button>
            <?php else : ?>
            <button type="button" class="btn btn-default" disabled title="Simplificada somente com modal Aéreo">Formal x
                Simplificada</button>
            <?php endif; ?>
        </div>
    </div>
    <!-- FIM linha dos botões -->

    <br clear="all">

    <div class="row">
        <!-- left column -->
        <div class="col-md-12 card-resumo">
            <!-- general form elements -->
            <div class="box box-warning">
                <div class="box-header with-border d-flex justify-content-between align-items-center">
                    <img src="assets/img/logos/6497410a123da.png" alt="Logo" class="logo-left">
                    <span class="label label-warning text-right">
                        Data do estudo:
                        <?php
    $dataOriginal = $dados['data_add'];
$dateTime = new DateTime($dataOriginal);
$dataFormatada = $dateTime->format('d/m/Y');
echo $dataFormatada;
?>
                    </span>
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    <!-- Linha 1 do estudo -->
                    <div class="row" style="border-bottom: solid 1px #cacaca;border-top: solid 1px #cacaca;">
                        <br clear="all" />
                        <div class="col-xs-6">
                            <b>Nome do Estudo: </b>
                            <?php echo $dados['nome_estudo']; ?><br>
                            <b>Origem: </b>
                            <?php echo $dados['origem']; ?><br>
                            <b>Incoterm: </b>
                            <?php echo $dados['incoterm']; ?><br>
                            <b>Taxa Cambial: </b>
                            <?php echo $dados['real_venda']; ?>
                        </div>
                        <div class="col-xs-6">
                            <b>Transporte: </b>
                            <?= $dados['modal']== 'mar' ? 'Marítimo' : 'Aéreo'; ?><br>
                            <b>Peso líquido: </b>
                            <?php echo $dados['peso_liquido']; ?>
                            kg<br>
                            <b>Valor da mercadoria: </b>
                            <?php echo number_format($dados['valor_mercadoria'], 2, ',', '.') . ' ' . $dados['moeda_padrao']; ?><br>
                            <b>Valor Frete: </b>
                            <?php echo number_format($dados['valor_log_int'], 2, ',', '.') . ' ' . $dados['moeda_padrao']; ?>
                        </div>
                        <br clear="all" /><br clear="all" />
                    </div>
                    <!-- FIM linha 1 do estudo -->

                    <br>

                    <!-- Linha 2 do estudo -->
                    <div class="row" style="border-bottom: solid 1px #cacaca;">
                        <div class="col-xs-12">
                            <h3>Discriminação de valores</h3>
                            <table class="table table-condensed table-hover table-striped" style="max-width: 600px;">
                                <tr>
                                    <th>Valor da mercadoria</th>
                                    <td style="width: 200px;">
                                        <span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            <?php
                                        $valor_mercadoria = $dados['valor_mercadoria'];
// Substitui a vírgula por um ponto
$real_com_ponto = str_replace(',', '.', $dados['real_venda']);
// Converte a string para um número de ponto flutuante
$valor_do_real = floatval($real_com_ponto);
// Calcula o valor em Real
$valor_em_real = $valor_mercadoria * $valor_do_real;
// Formata o valor em Real para exibição
$valor_em_real_formatado = number_format($valor_em_real, 2, ',', '.');
?>

                                            R$
                                            <?= $valor_em_real_formatado; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Valor Logística Internacional</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            <?php
                                        $valor_frete = $dados['valor_log_int'];
// Substitui a vírgula por um ponto
$real_com_ponto2 = str_replace(',', '.', $dados['real_venda']);
// Converte a string para um número de ponto flutuante
$valor_do_real2 = floatval($real_com_ponto2);
// Calcula o valor em Real
$valor_em_real2 = $valor_frete * $valor_do_real2;
// Formata o valor em Real para exibição
$valor_em_real_formatado2 = number_format($valor_em_real2, 2, ',', '.');
?>

                                            R$
                                            <?= $valor_em_real_formatado2; ?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>
                                        <h3>Valor aduaneiro</h3>
                                    </th>
                                    <td>
                                        <h3 class="text-success">
                                            <?php
                                            $valorTotalDV = $valor_em_real + $valor_em_real2;
echo 'R$ ' . number_format($valorTotalDV, 2, ',', '.');
?>
                                        </h3>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- FIM linha 2 do estudo -->

                    <br>

                    <!-- Linha 3 do estudo -->
                    <div class="row" style="border-bottom: solid 1px #cacaca;">
                        <div class="col-xs-12">
                            <h3>Impostos pagos na Nacionalização Formal</h3>
                            <?php
                            $sql = "SELECT * FROM planilha_viabilidade_adicoes pva
                                    INNER JOIN ncm n ON pva.ncm = n.ncm 
                                    WHERE pva.id_pv = :id_pv";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_pv', $idEstudo);
$stmt->execute();
$dadosAdic = $stmt->fetchAll(PDO::FETCH_ASSOC);

$real_venda = (float) str_replace(",", ".", $dados['real_venda']);

$peso_liquido_total = 0;
$valor_log_int_moeda_ext = (float) $dados['valor_log_int']; // Defina o valor_log_int de acordo com sua necessidade
$valor_log_int = $valor_log_int_moeda_ext * $real_venda; // Defina o valor_log_int de acordo com sua necessidade

$valor_total_mercadoriasFinal = 0;
$sqlNcm = "SELECT COUNT(DISTINCT ncm) AS distinct_ncm_count
           FROM planilha_viabilidade_adicoes
           WHERE id_pv = :id_pv";
$stmtNcm = $PDO->prepare($sqlNcm);
$stmtNcm->bindParam(':id_pv', $idEstudo);
$stmtNcm->execute();
$dadosNcm = $stmtNcm->fetch(PDO::FETCH_ASSOC);

$quantidadeNcmDistintos = $dadosNcm['distinct_ncm_count'];

$sql = "SELECT * FROM planilha_viabilidade_siscomex_aux 
        WHERE adicoes = :totalAdicoes";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':totalAdicoes', $quantidadeNcmDistintos);
$stmt->execute();
$dadosAd = $stmt->fetch(PDO::FETCH_ASSOC);
$taxa_siscomex = $dadosAd['valor_tx_siscomex'];
$taxa_marinha = $dados['marinha_mercante'];

// Primeiro loop para calcular o peso líquido total e o valor total das mercadorias
foreach($dadosAdic as $dado) {
    $peso_liquido_total += (float) str_replace(',', '.', $dado['peso_liquido']);
    $valor_total_mercadoriasFinal += (float) $dado['valor_total_item'];
}

$total_ii = 0; // Inicializando a variável para somar todos os valores de II
$total_pis = 0; // Inicializando a variável para somar todos os valores de PIS
$total_cofins = 0; // Inicializando a variável para somar todos os valores de COFINS
$total_ipi = 0; // Inicializando a variável para somar todos os valores de IPI
$total_icms = 0; // Inicializando a variável para somar todos os valores de ICMS

// Segundo loop para fazer o rateio e calcular II, PIS, COFINS, IPI e ICMS
foreach($dadosAdic as $dado) {
    $peso_liquido = (float) str_replace(',', '.', $dado['peso_liquido']);
    
    $valor_total_item = (float) $dado['valor_total_item'] * $real_venda;
    $valor_total_mercadorias = (float) $valor_total_mercadoriasFinal * $real_venda;
    
    // Fazendo o rateio
    $rateio = ($peso_liquido / $peso_liquido_total) * $valor_log_int;
    $rateio_siscomex = ($valor_total_item / $valor_total_mercadorias) * $taxa_siscomex;
    $rateio_marinha = ($valor_total_item / $valor_total_mercadorias) * $taxa_marinha;

    // Calculando o II
    $aliquota_ii = $dado['ii'];
    $valor_ii = 0;
    if ($aliquota_ii !== '0' && $aliquota_ii !== 'NT') {
        $aliquota_ii = (float) str_replace(",", ".", $aliquota_ii); // Substituindo vírgula por ponto decimal
        $valor_ii = ($rateio + $valor_total_item) * ($aliquota_ii / 100);
        $total_ii += $valor_ii;
    }
    $passagem[] = $valor_ii;

    // Calculando o PIS
    $aliquota_pis = $dado['pis'];
    if($aliquota_pis == 'Reduz') {
        $aliquota_pis = 2.1;
        $txt_pis = 'Possível redução. Consultar!';
    }
    $valor_pis = 0;
    if ($aliquota_pis !== '0' && $aliquota_pis !== 'NT') {
        $aliquota_pis = (float) str_replace(",", ".", $aliquota_pis); // Substituindo vírgula por ponto decimal
        $valor_pis = ($rateio + $valor_total_item) * ($aliquota_pis / 100);
        $total_pis += $valor_pis;
    }

    // Calculando o COFINS
    $aliquota_cofins = $dado['cofins'];
    if($aliquota_cofins == 'Reduz') {
        $aliquota_cofins = 9.65;
        $txt_cofins = 'Possível redução. Consultar!';
    }
    $valor_cofins = 0;
    if ($aliquota_cofins !== '0' && $aliquota_cofins !== 'NT') {
        $aliquota_cofins = (float) str_replace(",", ".", $aliquota_cofins); // Substituindo vírgula por ponto decimal
        $valor_cofins = ($rateio + $valor_total_item) * ($aliquota_cofins / 100);
        $total_cofins += $valor_cofins;
    }

    // Calculando o IPI
    $aliquota_ipi = $dado['ipi'];
    $valor_ipi = 0;
    if ($aliquota_ipi !== '0' && $aliquota_ipi !== 'NT') {
        $aliquota_ipi = (float) str_replace(",", ".", $aliquota_ipi); // Substituindo vírgula por ponto decimal
        $valor_ipi = (($rateio + $valor_total_item + $valor_ii) * $aliquota_ipi) / 100;
        $total_ipi += $valor_ipi;
    }

    // Calculando a base de cálculo do ICMS
    $base_calculo_icms = $rateio + $valor_total_item + $valor_ii + $valor_ipi + $valor_pis + $valor_cofins + $rateio_siscomex + $rateio_marinha;
    
    // Calculando o ICMS
    $aliquota_icms = $dado['aliquota_icms'];
    if ($aliquota_icms !== '0' && $aliquota_icms !== 'NT') {
        $aliquota_icms = (float) str_replace(",", ".", $aliquota_icms); // Substituindo vírgula por ponto decimal
        $valor_icms = ($base_calculo_icms / (1 - ($aliquota_icms / 100))) * ($aliquota_icms / 100);
        $total_icms += $valor_icms;
    }
}
?>
                            <table class="table table-condensed table-hover table-striped" style="max-width: 700px;">
                                <tr>
                                    <th>Imposto de Importação</th>
                                    <td style="width: 300px;">
                                        <span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            <?= "R$ " . number_format($total_ii, 2, ',', '.');?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>IPI</th>
                                    <td>
                                        <span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            <?= "R$ " . number_format($total_ipi, 2, ',', '.');?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>ICMS</th>
                                    <td>
                                        <span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            <?= "R$ " . number_format($total_icms, 2, ',', '.');?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>PIS</th>
                                    <td>
                                        <span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            <?= "R$ " . number_format($total_pis, 2, ',', '.');?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>COFINS</th>
                                    <td>
                                        <span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            <?= "R$ " . number_format($total_cofins, 2, ',', '.');?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <h3>Total dos impostos</h3>
                                    </th>
                                    <td>
                                        <?php
                                    $total_impostos = $total_ii + $total_ipi + $total_icms + $total_pis + $total_cofins;
?>
                                        <h3 class="text-success">
                                            <?= "R$ " . number_format($total_impostos, 2, ',', '.');?>
                                        </h3>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- FIM linha 3 do estudo -->

                    <br>

                    <!-- Linha 4 do estudo -->
                    <div class="row" style="border-bottom: solid 1px #cacaca;">
                        <div class="col-xs-12">
                            <h3>Despesas Aduaneiras</h3>

                            <table class="table table-condensed table-hover table-striped" style="max-width: 600px;">
                                <tr>
                                    <th>Armazenagem</th>
                                    <td style="width: 200px;">
                                        <span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['valor_armazenagem'], 2, ',', '.');?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Frete Nacional</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['valor_frete_nacional'], 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>Outras despesas</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['outras_da'], 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>Licença de Importação</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['licenca_importacao'], 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>Anuência de Importação</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['anuencia_importacao'], 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>Honorários Despachante</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['honorario_despachante'], 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>Marinha Mercante</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['marinha_mercante'], 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>Taxas no destino</th>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($dados['capatazia'], 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>Taxa SISCOMEX</th>
                                    <?php
$totalAdicoes = $quantidadeNcmDistintos;
$sql = "SELECT * FROM planilha_viabilidade_siscomex_aux 
                WHERE adicoes = :totalAdicoes";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':totalAdicoes', $totalAdicoes);
$stmt->execute();
$dadosAd = $stmt->fetch(PDO::FETCH_ASSOC);

$valorTx = $dadosAd['valor_tx_siscomex'];
?>
                                    <td><span class="text-info" style="font-weight: bold; font-size: 16px;">
                                            R$
                                            <?=number_format($valorTx, 2, ',', '.');?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>
                                        <h3>Total das despesas aduaneiras</h3>
                                    </th>
                                    <?php
$valorTotalDA = $dados['valor_armazenagem'] + $dados['valor_frete_nacional'] + $dados['outras_da'] + $dados['licenca_importacao'] + $dados['anuencia_importacao'] + $dados['honorario_despachante'] + $dados['marinha_mercante'] + $dados['capatazia'] + $valorTx;
?>
                                    <td>
                                        <h3 class="text-success">
                                            R$
                                            <?=number_format($valorTotalDA, 2, ',', '.');?>
                                        </h3>
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>
                    <!-- FIM linha 4 do estudo -->

                    <br><br>

                    <!-- Linha 5 do estudo -->
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="label label-success" style="font-size: 22px;">Custo Total da Importação</span>
                            <?php
                            $total_geral = $valorTotalDV + $total_impostos + $valorTotalDA;
?>
                            <h3 class="text-success">
                                <?= "R$ " . number_format($total_geral, 2, ',', '.');?>
                            </h3>
                        </div>
                    </div>
                    <!-- FIM linha 5 do estudo -->

                </div>

            </div>
            <!-- /.box -->
        </div>
        <!--/.col (left) -->
    </div>
    <!-- /.row -->
    <p class="text-center">
        <a href="#" class="btn btn-primary btnImprimir">
            <i class="fa fa-file-pdf-o"></i> Gerar PDF
        </a>
    </p>
</section>
<!-- /.content -->

</div>
<!-- /.content-wrapper -->
</section>

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

<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">AAA</h4>
            </div>
            <div class="modal-body">
                <h4>EXW</h4>
                <p>EX WORKS (named place of delivery) NA ORIGEM (local de entrega nomeado).</p>
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

<div class="modal fade" id="modal-aviso">
    <div class="modal-dialog boasPraticas">
        <div class="modal-content" style="background-color: #29363c;">
            <div class="modal-header" style="border-bottom-color: #555;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="color: #d8e6e7;">
                    Atenção:
                </h4>
            </div>
            <div class="modal-body" style="color: #d8e6e7;">
                <div class="row">
                    <div class="modal-body">
                        <p>
                            Lembrando que os estudos e cálculos apresentados são estimativas preliminares. Os valores
                            indicados são gerados pela nossa base de cálculo e servem apenas como uma
                            referência inicial.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Inclua jQuery e Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-informacao').modal('show');
});

var printButtons = document.querySelectorAll('.btnImprimir');

printButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
        // Impede que o link navegue para outra página
        event.preventDefault();

        // Seleciona a carteirinha mais próxima do botão clicado
        var card = button.parentElement.previousElementSibling.querySelector(
            '.card-resumo');

        // Ajustar o estilo da carteirinha para o PDF
        card.style.fontSize = '14px'; // Diminui o tamanho da fonte para o PDF
        card.style.margin = '20px'; // Aumenta as margens no PDF

        // Configurações para o PDF
        var opt = {
            margin: [0.5, 0.5, 0.5, 0.5], // Definir margens maiores
            filename: 'Resumo_estudo_viabilidade.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 1.3, // Reduzir o scale para melhorar a renderização
                useCORS: true // Usar CORS se houver imagens externas
            },
            jsPDF: {
                unit: 'in',
                format: 'A4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['avoid-all', 'css', 'legacy'] // Evitar cortes no conteúdo
            }
        };

        // Gera o PDF
        html2pdf().from(card).set(opt).save().then(function() {
            // Reverte os ajustes de estilo após a geração do PDF
            card.style.fontSize = '';
            card.style.margin = '';
        });
    });
});
</script>