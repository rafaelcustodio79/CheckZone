<?php

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ferramentas</h1>
                <small>Cálculo de armazenagem</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Ferramentas</li>
                    <li class="breadcrumb-item">Armazenagem</li>
                    <li class="breadcrumb-item active">Cálculo</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <!-- left column -->
            <div class="col-md-7">
                <!-- general form elements -->
                <div class="card card-info">
                    <div class="card-header with-border">
                        <h3 class="card-title">Estimativa de Armazenagem - AEROPORTUÁRIA</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" name="cadastro-usuario-cliente" method="post"
                        action="ferramentas/calcula_armazenagem_a.php">

                        <input type="hidden" name="idEmpresa" value="<?= $dadosUsuario[0]['id_empresa']; ?>">

                        <div class="row">

                            <div class="col-lg-6">
                                <div class="card-body">
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
                                <!-- /.card-body -->
                            </div>

                            <div class="col-lg-6">
                                <div class="card-body">
                                    <label>Valor CIF da mercadoria (em R$)</label><br>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                        <input type="text" id="valorCif" class="form-control"
                                            placeholder="Digite o valor" name="cif" required>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>

                            <div class="col-lg-8">
                                <div class="card-body">
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
                                <!-- /.card-body -->
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-lg-4">
                                <div class="card-body">
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
                            <div class="col-lg-4">
                                <div class="card-body">
                                    <label>Peso / Qtde.</label>
                                    <br>
                                    <div class="form-group">
                                        Peso bruto (Kg):<br>
                                        <input type="text" id="peso_bruto" name="peso_bruto" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>

                        </div>

                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-calculator"></i>
                                Calcular</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <!--/.col (left) -->

            <div class="col-md-5">

                <div class="box">
                    <?php

                if (isset($_SESSION['dados'])) {
                    $dados = $_SESSION['dados'];

                    // Exibir os primeiros elementos
                    echo '<div class="card-header">';
                    echo '<h3 class="card-title">Estimativa de Custo para: <span class="text-info"><strong>' . htmlspecialchars($dados[0]) . '</strong></span></h3><br>';
                    echo '<small>' . htmlspecialchars($dados[1]) . ' <br> ' . htmlspecialchars($dados[2]) . '</small>';
                    echo '</div>';
                    echo '<!-- /.card-header -->';
                    echo '<div class="card-body no-padding">';
                    echo '<p style="padding-left:5px;">' . htmlspecialchars($dados[3]) . '</p>';

                    // Iniciar a tabela
                    echo '<table class="table table-striped">';
                    
                    echo '<tr>';
                    echo '<th>Valor CIF da carga</th>';
                    echo '<td class="text-center">'.htmlspecialchars($dados[4]).'</td>';
                    echo '</tr>';
                    
                    echo '<tr>';
                    echo '<th>Peso bruto</th>';
                    echo '<td class="text-center">'.htmlspecialchars($dados[5]).'</td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th>Custo da Armazenagem</th>';
                    echo '<td class="text-center">'.htmlspecialchars($dados[6]).'</td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th>Custo da Capatazia</th>';
                    echo '<td class="text-center">'.htmlspecialchars($dados[7]).'</td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '<th style="font-size:18px; font: weight 900px;">Custo Total</th>';
                    echo '<td  style="font-size:22px; font: weight 900px;" class="text-center text-info"><b>'.htmlspecialchars($dados[8]).'</b></td>';
                    echo '</tr>';

                    // Fechar a tabela
                    echo '</table>';

                    // Fechar a card-body div
                    echo '</div>';

                    // Limpar os dados da sessão após exibir
                    unset($_SESSION['dados']);
                } else {
                    echo "<p style='padding:20px;'>Faça a seleção dos campos acima para exibir o cálculo.</p>";
                }
?>

                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->

        </div>
        <!-- /.row -->

        <br clear="all">

        <div class="row">

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->


<script>
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

function removerNaoNumericos() {
    var campo = document.getElementById("valor_total");
    var campo2 = document.getElementById("valor_total_c");
    campo.value = campo.value.replace(/\D/g, "");
    campo2.value = campo2.value.replace(/\D/g, "");
}

// Exemplo de uso: chamando a função ao digitar
var campo = document.getElementById("valor_total");
var campo2 = document.getElementById("valor_total_c");
campo.addEventListener("input", removerNaoNumericos);
campo2.addEventListener("input", removerNaoNumericos);

function toggleFields() {
    const container20 = document.getElementById('container_20').checked;
    const container40 = document.getElementById('container_40').checked;
    const cargaSol = document.getElementById('carga_sol').checked;

    document.getElementById('qtde_container').disabled = !(container20 || container40);
    document.getElementById('peso_bruto').disabled = !cargaSol;
}

// Initialize fields based on default selection
toggleFields();
</script>