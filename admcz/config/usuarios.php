<?php
$PDO = db_connect();

$sql = "SELECT *, u.id AS userId FROM users u
        INNER JOIN clients c ON c.id = u.client_id
        ORDER BY u.id DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT *
        FROM clients
        WHERE status = 1
        ORDER BY id DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT *
        FROM users_type
        ORDER BY id DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$usersType = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Configurações</h1>
                <small>Usuários</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Configurações</li>
                    <li class="breadcrumb-item active">Usuários</li>
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
                        <h3 class="card-title">Listando Usuários</h3>
                        <div class="ml-auto">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#modalCadastrarUsuario">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="listaUsuarios" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Foto</th>
                                    <th>Nome</th>
                                    <th>Empresa</th>
                                    <th>Email</th>
                                    <th class="text-center">Telefone</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center" style="width: 50px;">Ativo</th>
                                    <th class="text-center" style="width: 100px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $resultado) : ?>
                                <tr>
                                    <td class="text-center">
                                        <img src="img/users/<?= $resultado['user_photo']??'default.png'; ?>"
                                            class="img-circle elevation-2 img-zoom" alt="Foto do usuário"
                                            style="max-height: 30px;">
                                    </td>
                                    <td>
                                        <?= $resultado['nome']; ?>
                                    </td>
                                    <td>
                                        <?= $resultado['nome_fantasia']; ?><br>
                                        <small><?= $resultado['razao_social']; ?></small>
                                    </td>
                                    <td>
                                        <?= $resultado['email']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $resultado['telefone_usuario']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        switch ($resultado['users_type_id']) {
                                            case 1:
                                                echo '<span class="badge badge-danger">SUPER USUÁRUIO</span>';
                                                break;
                                            case 2:
                                                echo '<span class="badge badge-success">MASTER</span>';
                                                break;
                                            case 3:
                                                echo '<span class="badge badge-info">GERENTE</span>';
                                                break;
                                            case 4:
                                                echo '<span class="badge badge-primary">INSPETOR</span>';
                                                break;
                                            case 5:
                                                echo '<span class="badge badge-secondary">TÉCNICO</span>';
                                                break;
                                            case 6:
                                                echo '<span class="badge badge-warning">AUDITOR</span>';
                                                break;
                                            case 7:
                                                echo '<span class="badge badge-dark">FINANCEIRO</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            if ($resultado['user_status'] == 1) {
                                                echo "<a href='config.php?a=config&b=usuarios_ativar&id={$resultado['id']}&acao=0'><i class='fas fa-check-circle text-green' title='Ativa'></i></a>";
                                            } else {
                                                echo "<a href='config.php?a=config&b=usuarios_ativar&id={$resultado['id']}&acao=1'><i class='fas fa-times-circle text-danger' title='Inativa'></i></a>";
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center" style="width: 100px;">
                                        <a href="javascript:void(0);" class="edit-user"
                                            onclick="editarUsuario(<?= $resultado['userId'] ?>)">
                                            <i class="fas fa-edit" title="Editar"></i>
                                        </a>
                                        <a href="config.php?a=config&b=usuarios_deletar&id=<?= $resultado['userId']; ?>&arquivo=<?= $resultado['user_photo']; ?>"
                                            onclick="return confirm('Deseja realmente excluir o Usuário?')"><i
                                                class="fas fa-trash" title="Deletar"></i></a>
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
<div class="modal fade" id="modalCadastrarUsuario" tabindex="-1" role="dialog"
    aria-labelledby="modalCadastrarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="modalCadastrarUsuarioLabel">Cadastrar Usuário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="addUser" method="post" action="config/usuarios_salvar.php" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome"
                                    placeholder="Digite seu nome">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="telefone_usuario">Telefone</label>
                                <input type="text" class="form-control" id="telefone_usuario" name="telefone_usuario"
                                    placeholder="Digite seu telefone (Whatsapp)">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="email@servidor.com">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">

                                <label for="company">Empresa</label>
                                <select class="form-control select2bs4" id="company_id" name="company_id">
                                    <option value="">Selecione</option>
                                    <?php foreach ($empresas as $empresa) :?>
                                    <option value="<?= $empresa['id'] ?>"><?= $empresa['nome_fantasia'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_type">Tipo de Usuário</label>
                                <select class="form-control" id="user_type_id" name="user_type_id">
                                    <option value="">Selecione</option>
                                    <?php foreach ($usersType as $type) :?>
                                    <option value="<?= $type['id'] ?>"><?= $type['tipo_usuario'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo de senha -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="senha">Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="senha" name="senha">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="button" id="toggle_senha">👁️</button>
                                        <button class="btn btn-primary" type="button" id="gerar_senha">🔄</button>
                                    </div>
                                </div>
                                <small id="senha-status" class="form-text"></small>
                                <div class="progress">
                                    <div id="senha-strength-bar" class="progress-bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo de confirmação de senha -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirmar_senha">Confirmar Senha</label>
                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha">
                                <small id="senha-match" class="form-text"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>
                        Fechar</button>
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
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="usuarioEditar" method="post" action="config/usuarios_salvar_alteracao.php"
                enctype="multipart/form-data">
                <input type="hidden" name="id_usuario" id="edit_id_usuario">

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="edit_nome" name="nome" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="telefone_usuario">Telefone</label>
                                <input type="text" class="form-control" id="edit_telefone_usuario"
                                    name="telefone_usuario">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="company_id">Empresa</label>
                                <select class="form-control select2bs4" id="edit_company_id" name="company_id">
                                    <option value="<?= $empresa['id'] ?>"><?= $empresa['nome_fantasia'] ?></option>
                                    <?php foreach ($empresas as $empresa) : ?>
                                    <option value="<?= $empresa['id'] ?>"><?= $empresa['nome_fantasia'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_type_id">Tipo de Usuário</label>
                                <select class="form-control" id="edit_user_type_id" name="user_type_id">
                                    <?php foreach ($usersType as $type) :?>
                                    <option value="<?= $type['id'] ?>"><?= $type['tipo_usuario'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo de senha -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="senha">Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="edit_senha" name="senha">
                                </div>
                                <small id="edit-senha-status" class="form-text"></small>
                                <div class="progress">
                                    <div id="edit-senha-strength-bar" class="progress-bar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo de confirmação de senha -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirmar_senha">Confirmar Senha</label>
                                <input type="password" class="form-control" id="edit_confirmar_senha"
                                    name="confirmar_senha">
                                <small id="edit-senha-match" class="form-text"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                                <small id="logoHelp" class="form-text text-muted">Só selecione uma imagem se quiser
                                    alterar a foto do usuário.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_status">Status</label>
                                <select class="form-control" id="edit_user_status" name="user_status">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="updateUsuario()"><i class="fas fa-save"></i>
                        Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarUsuario(userId) {
    console.log("Abrindo modal para o usuário com ID: " + userId);

    // Abre o modal
    $('#editUserModal').modal('show');

    // Faz a requisição AJAX para buscar os dados da empresa
    $.ajax({
        url: 'config/usuarios_buscar.php',
        method: 'GET',
        data: {
            id: userId
        },
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                alert('Erro: ' + response.error);
                return;
            }

            // Preenche os campos do modal com os dados recebidos
            $('#edit_id_usuario').val(response.id);
            $('#edit_nome').val(response.nome);
            $('#edit_email').val(response.email);
            $('#edit_telefone_usuario').val(response.telefone_usuario);
            $('#edit_user_status').val(response.user_status);
            $('#edit_company_id').val(response.company_id);
            $('#edit_user_type_id').val(response.users_type_id);
        },
        error: function(xhr, status, error) {
            console.error("Erro ao buscar usuário:", error);
            alert("Erro ao carregar os dados do usuário. Veja o console para mais detalhes.");
        }
    });
}

function updateUsuario() {
    var formData = new FormData(document.querySelector('form[name="usuarioEditar"]'));

    fetch('config/usuarios_salvar_alteracao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Usuário atualizado com sucesso!');
                location.reload(); // Recarrega a página para exibir os novos dados
            } else {
                alert('Erro ao atualizar a usuário: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atualizar a usuário.');
        });
}
</script>