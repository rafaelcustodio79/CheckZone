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

<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
    $('#modal-aviso').modal('show');
});
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ferramentas</h1>
                <small>Viabilidade</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item">
                        <a href="../ferramentas.php?a=ferramentas&b=viabilidade_listar">Viabilidade</a>
                    </li>
                    <li class="breadcrumb-item active">Cálculo</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Linha do Botões -->
        <div class="row">
            <div class="col-md-12">
                <a href="ferramentas.php?a=ferramentas&b=viabilidade_calculo&idEstudo=<?=$idEstudo;?>"
                    class="btn btn-info">Resumo</a>

                <?php for ($i = 1; $i <= $qtProdutos; $i++) :
                // Gerar o link para cada produto
                $url = "ferramentas.php?a=ferramentas&b=viabilidade_calculo_produto&idProduto=$i&idEstudo=$idEstudo";
                ?>
                <a href="<?= $url ?>" class="btn btn-default">Produto
                    <?= $i ?></a>
                <?php endfor; ?>

                <?php if ($dados['modal']=='aer' && $dados['valor_mercadoria'] < 3000) : ?>
                <a href="ferramentas.php?a=ferramentas&b=viabilidade_calculo_fxs&idEstudo=<?=$idEstudo;?>"
                    class="btn btn-default">Formal x
                    Simplificada</a>
                <?php elseif ($dados['modal']=='aer' && $dados['valor_mercadoria']>=3000) : ?>
                <button type="button" class="btn btn-default" disabled title="Simplificada somente até 3000 USD">Formal
                    x
                    Simplificada</button>
                <?php else : ?>
                <button type="button" class="btn btn-default" disabled
                    title="Simplificada somente com modal Aéreo">Formal x
                    Simplificada</button>
                <?php endif; ?>
            </div>
        </div>
        <!-- FIM linha dos botões -->

        <br>

        <div class="row" id="viabilidadeResumo">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <img src="img/logos/6587985.png" alt="Logo" class="logo-left">
                        <span class="badge badge-info text-right">
                            Data do estudo:
                            <?php
                                $dataOriginal = $dados['data_add'];
                                $dateTime = new DateTime($dataOriginal);
                                $dataFormatada = $dateTime->format('d/m/Y');
                                echo $dataFormatada;
                            ?>
                        </span>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">

                        <!-- Linha 1 do estudo -->
                        <div class="row" style="border-bottom: solid 1px #cacaca;">
                            <div class="col-lg-6">
                                <b>Nome do Estudo: </b>
                                <?php echo $dados['nome_estudo']; ?><br>
                                <b>Origem: </b>
                                <?php echo $dados['origem']; ?><br>
                                <b>Incoterm: </b>
                                <?php echo $dados['incoterm']; ?><br>
                                <b>Taxa Cambial: </b>
                                <?php echo $dados['real_venda']; ?>
                            </div>
                            <div class="col-lg-6">
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
                        </div>
                        <!-- FIM linha 1 do estudo -->

                        <br>

                        <!-- Linha 2 do estudo -->
                        <div class="row" style="border-bottom: solid 1px #cacaca;">
                            <div class="col-xs-12">
                                <h4><strong>Discriminação de valores</strong></h4>
                                <table class="table table-condensed table-hover table-striped" style="width: 500px;">
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
                                            <h4>Valor aduaneiro</h4>
                                        </th>
                                        <td>
                                            <h4 class="text-success">
                                                <?php
                                                $valorTotalDV = $valor_em_real + $valor_em_real2;
                                                echo '<strong>R$ ' . number_format($valorTotalDV, 2, ',', '.'). '</strong>';
                                                ?>
                                            </h4>
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
                                <h4><strong>Impostos pagos na Nacionalização Formal</strong></h4>
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
                                <table class="table table-condensed table-hover table-striped" style="width: 500px;">
                                    <tr>
                                        <th>Imposto de Importação</th>
                                        <td style="width: 200px;">
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
                                            <h4>Total dos impostos</h4>
                                        </th>
                                        <td>
                                            <?php
                                            $total_impostos = $total_ii + $total_ipi + $total_icms + $total_pis + $total_cofins;
                                            ?>
                                            <h4 class="text-success">
                                                <strong><?= "R$ " . number_format($total_impostos, 2, ',', '.');?></strong>
                                            </h4>
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
                                <h4><strong>Despesas Aduaneiras</strong></h4>

                                <table class="table table-condensed table-hover table-striped" style="width: 500px;">
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
                                            <h4>Total das despesas aduaneiras</h4>
                                        </th>
                                        <?php
                                        $valorTotalDA = $dados['valor_armazenagem'] + $dados['valor_frete_nacional'] + $dados['outras_da'] + $dados['licenca_importacao'] + $dados['anuencia_importacao'] + $dados['honorario_despachante'] + $dados['marinha_mercante'] + $dados['capatazia'] + $valorTx;
                                        ?>
                                        <td>
                                            <h4 class="text-success">
                                                <strong>R$
                                                    <?=number_format($valorTotalDA, 2, ',', '.');?>
                                                </strong>
                                            </h4>
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
                                <span class="badge badge-info" style="font-size: 22px;">Custo Total da
                                    Importação</span>
                                <?php
                                $total_geral = $valorTotalDV + $total_impostos + $valorTotalDA;
                                ?>
                                <h3 class="text-info">
                                    <strong><?= "R$ " . number_format($total_geral, 2, ',', '.');?></strong>
                                </h3>
                            </div>
                        </div>
                        <!-- FIM linha 5 do estudo -->

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        <p class="text-center">
            <a href="#" class="btn btn-primary btnImprimirCalculo">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </a>
        </p>
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