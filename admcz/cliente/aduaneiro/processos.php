<?php
$PDO = db_connect();
$idUsuario = $_SESSION['user_id']; 
$idEmpresa = $_SESSION['empresa_id'];


$sql = "SELECT pro.*, pat.*, emp.*
        FROM processos pro
        INNER JOIN processos_aux_tipos pat ON pro.id_tipo_processo = pat.id_tipo_processo
        INNER JOIN empresa emp ON pro.id_empresa = emp.id_empresa
        WHERE pro.id_empresa = :idEmpresa
        ORDER BY pro.data_add DESC";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Aduaneiro</h1>
                <small>Processos</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Aduaneiro</li>
                    <li class="breadcrumb-item active">Processos</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Listando Processos</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="listaProcCli" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Ref. Calvet</th>
                                    <th class="text-center">Ref. Cliente</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Última atividade</th>
                                    <th class="text-center" style="width: 100px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $resultado) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?= $resultado['ref_calvet']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $resultado['ref_cliente']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $resultado['titulo_tipo']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $resultado['status_processo']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $idProcesso = $resultado['id_processo'];
                                        $idEmpresa = $resultado['id_empresa'];
                                        #Workflow
                                        // Primeira consulta: Buscar processos_workflow
                                        $sqlWF = "SELECT * FROM processos_workflow pwf
                                        INNER JOIN processos_aux_workflow paw ON pwf.id_aux_wf = paw.id_aux_wf
                                        INNER JOIN usuario usu ON pwf.id_usuario = usu.id
                                        WHERE pwf.id_processo = :idProcesso AND pwf.id_empresa = :idEmpresa
                                        ORDER BY pwf.id_wf DESC";
                                        $stmtWF = $PDO->prepare($sqlWF);
                                        $stmtWF->bindParam(':idProcesso', $idProcesso, PDO::PARAM_INT);
                                        $stmtWF->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
                                        $stmtWF->execute();
                                        $resultadosWF = $stmtWF->fetchAll(PDO::FETCH_ASSOC);

                                        echo $resultadosWF[0]['titulo_wf'] ?? '---';
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="cliente.php?a=aduaneiro&b=processos_profile&idProcesso=<?= $resultado['id_processo']; ?>&idEmpresa=<?= $resultado['id_empresa']; ?>"><i
                                                class="fas fa-eye" title="Visualizar"></i></a>
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
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Modal -->
<div class="modal fade" id="modalCadastrarEmpresa" tabindex="-1" role="dialog"
    aria-labelledby="modalCadastrarEmpresaLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="modalCadastrarEmpresaLabel">Novo Processo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="empresaAdd" method="post" action="aduaneiro/processos_salvar.php">
                <input type="hidden" name="idUsuario" value="<?php echo $_SESSION['user_id'];?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php
// Verificar se o usuário é administrador
if ($_SESSION['tipo_usuario'] == 1) {
    // Administrador, pode ver todas as empresas com id_tipo_empresa = 2
    $sqlCli = "SELECT * FROM empresa WHERE id_tipo_empresa = 2 ORDER BY nome_empresa ASC";
    $stmtCli = $PDO->prepare($sqlCli);
    $stmtCli->execute();
    $resultadosCli = $stmtCli->fetchAll(PDO::FETCH_ASSOC);

} else {
    // Usuário não é administrador, filtrar empresas permitidas para ele
    if (!empty($empresas_ids)) {
        // Gerar placeholders para a consulta com os IDs de empresas permitidas
        $placeholders = implode(",", array_fill(0, count($empresas_ids), "?"));

        // Consulta para obter empresas permitidas com id_tipo_empresa = 2
        $sqlCli = "SELECT * FROM empresa WHERE id_empresa IN ($placeholders) AND id_tipo_empresa = 2 ORDER BY nome_empresa ASC";
        $stmtCli = $PDO->prepare($sqlCli);

        // Executar a consulta com os IDs das empresas permitidas
        $stmtCli->execute($empresas_ids);
        $resultadosCli = $stmtCli->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $resultadosCli = []; // Caso não haja empresas associadas para o usuário
    }
}

?>
                                <label for="cliente">Cliente</label>
                                <select class="form-control" name="idCliente">
                                    <option value="" selected>Selecione um cliente</option>
                                    <?php foreach ($resultadosCli as $resultadoCli) : ?>
                                    <option value="<?= $resultadoCli['id_empresa']; ?>">
                                        <?= htmlspecialchars($resultadoCli['nome_empresa']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ref_cliente">Referência Cliente</label>
                                <input type="text" class="form-control" id="ref_cliente" name="ref_cliente">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ref_calvet">Referência Calvet</label>
                                <input type="text" class="form-control" id="ref_calvet" name="ref_calvet">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php
                                $sqlTipoP = "SELECT * FROM processos_aux_tipos ORDER BY id_tipo_processo ASC";
$stmtTipoP = $PDO->prepare($sqlTipoP);
$stmtTipoP->execute();
$resultadosTipoP = $stmtTipoP->fetchAll(PDO::FETCH_ASSOC);
?>
                                <label for="idTipoProcesso">Tipo de Processo</label>
                                <select class="form-control" name="idTipoProcesso">
                                    <option value="" selected>Selecione um Tipo</option>
                                    <?php foreach ($resultadosTipoP as $resultadoTipoP) : ?>
                                    <option value="<?= $resultadoTipoP['id_tipo_processo']; ?>">
                                        <?= $resultadoTipoP['titulo_tipo']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
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


<!-- Modal structure for editing user -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Empresa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="editUser" method="post" action="aduaneiro/empresas_salvar_alteracao.php"
                enctype="multipart/form-data">
                <input type="hidden" name="id_empresa" id="id_empresa">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nome_empresa">Nome da Empresa</label>
                                <input type="text" class="form-control" id="edit_nome_empresa" name="nome_empresa">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="segmento_empresa">Segmento</label>
                                <input type="text" class="form-control" id="edit_segmento_empresa"
                                    name="segmento_empresa">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cnpj">CNPJ</label>
                                <input type="text" class="form-control" id="edit_cnpj" name="cnpj">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefone_empresa">Telefone</label>
                                <input type="text" class="form-control" id="edit_telefone_empresa"
                                    name="telefone_empresa">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email_principal">Email Principal</label>
                                <input type="email" class="form-control" id="edit_email_principal"
                                    name="email_principal">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="localizacao">Localização</label>
                        <input type="text" class="form-control" id="edit_localizacao" name="localizacao">
                    </div>

                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="text" class="form-control" id="edit_website" name="website">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="id_tipo_empresa">Tipo de Empresa</label>
                                <select class="form-control" id="edit_id_tipo_empresa" name="id_tipo_empresa">
                                    <option value="1">Administrador</option>
                                    <option value="2">Cliente</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?php
        $sqlOper = "SELECT * FROM usuario WHERE id_tipo_usuario = 3 ORDER BY nome ASC";
$stmtOper = $PDO->prepare($sqlOper);
$stmtOper->execute();
$resultadosOper = $stmtOper->fetchAll(PDO::FETCH_ASSOC);
?>
                                <label for="idUsuario">Gerente da Conta</label>
                                <select class="form-control" id="edit_idUsuario" name="idUsuario">
                                    <option value="">Selecione um gerente</option>
                                    <?php foreach ($resultadosOper as $resultadoOper) : ?>
                                    <option value="<?= $resultadoOper['id']; ?>">
                                        <?= $resultadoOper['nome']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                                <small id="logoHelp" class="form-text text-muted">Só selecione uma imagem se quiser
                                    alterar a logo da empresa.</small>
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