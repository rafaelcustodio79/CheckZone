<?php
$PDO = db_connect();

$sql = "SELECT *, emp.id_empresa AS idEmpresa FROM empresa emp
        LEFT JOIN honorarios hon ON emp.id_empresa = hon.id_empresa
        WHERE emp.id_tipo_empresa = 2
        ORDER BY emp.id_empresa DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Listando Empresas</h3>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="listaBanners" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Logo</th>
                                    <th>Nome Empresa</th>
                                    <th class="text-center">CNPJ</th>
                                    <th>Email Principal</th>
                                    <th class="text-center">Telefone</th>
                                    <th class="text-center" style="width: 50px;">Dados</th>
                                    <th class="text-center" style="width: 100px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $resultado) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if(!empty($resultado['logo'])) : ?>
                                        <img src="img/logos/<?= $resultado['logo']; ?>" style="max-height: 30px;">
                                        <?php else : ?>
                                        <img src="img/logos/sem-foto.png" style="max-height: 30px;">
                                        <?php endif;?>
                                    </td>
                                    <td>
                                        <?= $resultado['nome_empresa']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $resultado['cnpj']; ?>
                                    </td>
                                    <td>
                                        <?= $resultado['email_principal']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $resultado['telefone_empresa']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            if (isset($resultado['id_honorario'])) {
                                                echo "<i class='fas fa-check-circle text-green' title='Possui honorários cadastrado!'></i>";
                                            } else {
                                                echo "<i class='fas fa-times-circle text-danger' title='Não possui informações...'></i></a>";
                                            }
                                    ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($resultado['id_honorario'])) : ?>
                                        <i class="fas fa-plus-circle text-muted"
                                            title="Já existe honorários cadastrados"></i>
                                        <?php else : ?>
                                        <a
                                            href="config.php?a=config&b=honorarios_abertura&id=<?= $resultado['idEmpresa']; ?>">
                                            <i class="fas fa-plus-circle" title="Adicionar dados!"></i>
                                        </a>
                                        <?php endif;?>

                                        <a
                                            href="config.php?a=config&b=honorarios_criar&idHonorario=<?= $resultado['id_honorario']; ?>&idEmpresa=<?= $resultado['idEmpresa']; ?>"><i
                                                class="fas fa-folder-open" title="Abrir/Editar/Cadastrar"></i></a>
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

function openEditModal(userId) {
    // Aqui você pode adicionar lógica para buscar os dados do usuário se necessário.
    console.log("Abrindo modal para o usuário com ID: " + userId);

    // Exemplo de como abrir o modal (se estiver usando Bootstrap 4 ou 5):
    $('#editUserModal').modal('show');

    // Se precisar carregar dados via AJAX, pode fazer algo como:
    $.ajax({
        url: 'config/empresas_buscar.php',
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
            // $('#edit_certificacoes').val(userData.certificacoes);
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