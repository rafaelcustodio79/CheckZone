<?php
$PDO = db_connect();

$sql = "SELECT id, tipo_pessoa, razao_social, nome_fantasia, logo, cnpj, cpf, telefone_principal, email_principal, status
        FROM clients
        ORDER BY id DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT *
        FROM plans
        ORDER BY id DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$planos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT *
        FROM users
        WHERE user_status = 1
        ORDER BY id DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Configurações</h1>
                <small>Clientes</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item">Configurações</li>
                    <li class="breadcrumb-item active">Clientes</li>
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
                        <h3 class="card-title">Listando Clientes</h3>
                        <div class="ml-auto">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#modalCadastrarEmpresa">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="listaEmpresas" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Logo</th>
                                    <th class="text-center">Tipo</th>
                                    <th>Nome</th>
                                    <th class="text-center" style="min-width: 115px;">CNPJ / CPF</th>
                                    <th>Email Principal</th>
                                    <th class="text-center" style="min-width: 95px;">Telefone</th>
                                    <th class="text-center" style="width: 40px;">Status</th>
                                    <th class="text-center" style="width: 40px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($empresas as $resultado) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if(!empty($resultado['company_logo'])) : ?>
                                        <img src="img/logos/<?= $resultado['company_logo']; ?>"
                                            style="max-height: 40px;">
                                        <?php else : ?>
                                        <img src="img/logos/sem-foto.png" style="max-height: 40px;">
                                        <?php endif;?>
                                    </td>
                                    <td>
                                        <?= $resultado['tipo_pessoa']; ?>
                                    </td>
                                    <td>
                                        <?php if($resultado['tipo_pessoa']=='PJ') : ?>
                                        <strong><?= $resultado['nome_fantasia']; ?></strong>
                                        <br>
                                        <small><?= $resultado['razao_social']; ?></small>
                                        <?php else :?>
                                        <strong><?= $resultado['nome_fantasia']; ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($resultado['tipo_pessoa']=='PJ') : ?>
                                        <?= $resultado['cnpj']; ?>
                                        <?php else :?>
                                        <?= $resultado['cpf']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $resultado['email_principal']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $resultado['telefone_principal']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            if ($resultado['status'] == 1) {
                                                echo "<a href='config.php?a=config&b=empresas_ativar&id={$resultado['id']}&acao=0'><i class='fas fa-check-circle text-green' title='Ativa'></i></a>";
                                            } else {
                                                echo "<a href='config.php?a=config&b=empresas_ativar&id={$resultado['id']}&acao=1'><i class='fas fa-times-circle text-danger' title='Inativa'></i></a>";
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="config.php?a=config&b=empresas_profile&id=<?= $resultado['id']; ?>"><i
                                                class="fas fa-eye" title="Visualizar"></i></a>
                                        <a href="javascript:void(0);" class="edit-user"
                                            onclick="editarEmpresa(<?= $resultado['id'] ?>)">
                                            <i class="fas fa-edit" title="Editar"></i>
                                        </a>

                                        <a href="config.php?a=config&b=empresas_deletar&id=<?= $resultado['id']; ?>&arquivo=<?= $resultado['logo']; ?>"
                                            onclick="return confirm('Deseja realmente excluir o Cliente?')"><i
                                                class="fas fa-trash" title="Apagar"></i></a>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="modalCadastrarEmpresaLabel">Cadastrar Empresa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="empresaAdd" method="post" action="config/empresas_salvar.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="razao_social">Razão Social</label>
                                <input type="text" class="form-control" placeholder="Digite a Razão Social da Empresa"
                                    id="razao_social" name="razao_social">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome_fantasia">Nome Fantasia</label>
                                <input type="text" class="form-control" placeholder="Digite o Nome Fantasia da Empresa"
                                    id="nome_fantasia" name="nome_fantasia">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cnpj">CNPJ</label>
                                <input type="text" class="form-control" placeholder="Digite aqui o CNPJ da Empresa"
                                    id="cnpj" name="cnpj">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefone_principal">Telefone Principal</label>
                                <input type="text" class="form-control"
                                    placeholder="Digite o telefone principal da Empresa" id="telefone_principal"
                                    name="telefone_principal">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email_principal">Email Principal</label>
                                <input type="email" class="form-control" placeholder="email@servidor.com"
                                    id="email_principal" name="email_principal">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cep">
                                    CEP
                                </label>
                                <input class="form-control" type="text" placeholder="Digite o cep aqui" name="cep"
                                    id="cep" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endereco">
                                    Endereço
                                </label>
                                <input class="form-control" type="text" placeholder="Endereço" name="endereco"
                                    id="endereco" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="numero">
                                    Número
                                </label>
                                <input class="form-control" type="text" placeholder="Número" name="numero"
                                    id="numero" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="complemento">
                                    Complemento
                                </label>
                                <input class="form-control" type="text" placeholder="Complemento" name="complemento"
                                    id="complemento" />
                                </ </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bairro">
                                    Bairro
                                </label>
                                <input class="form-control" type="text" placeholder="Bairro" name="bairro"
                                    id="bairro" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cidade">
                                    Cidade
                                </label>
                                <input class="form-control" type="text" placeholder="Cidade" name="cidade"
                                    id="cidade" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estado">
                                    Estado (UF)
                                </label>
                                <input class="form-control" type="text" placeholder="UF" name="estado" id="estado" />
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="master_id">Usuário Principal</label>
                                <select class="form-control select2bs4" id="master_id" name="master_id">
                                    <option value="">Selecione</option>
                                    <?php foreach ($usuarios as $usuario) :?>
                                    <option value="<?= $usuario['id'] ?>"><?= $usuario['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plano_id">Plano</label>
                                <select class="form-control" id="plano_id" name="plano_id">
                                    <option value="">Selecione</option>
                                    <option value="0">Sem Plano - ADM</option>
                                    <?php foreach ($planos as $plano) :?>
                                    <option value="<?= $plano['id'] ?>"><?= $plano['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status_empresa">Status</label>
                                <select class="form-control" id="status_empresa" name="status_empresa">
                                    <option value="">Selecione</option>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo">
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
            <div class="modal-header titulo-modal">
                <h5 class="modal-title" id="editUserModalLabel">Editar Empresa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="empresaEditar" method="post" action="config/empresas_salvar_alteracao.php"
                enctype="multipart/form-data">
                <input type="hidden" name="id_empresa" id="edit_id_empresa">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="razao_social">Razão Social</label>
                                <input type="text" class="form-control" placeholder="Digite a Razão Social da Empresa"
                                    id="edit_razao_social" name="razao_social">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome_fantasia">Nome Fantasia</label>
                                <input type="text" class="form-control" placeholder="Digite o Nome Fantasia da Empresa"
                                    id="edit_nome_fantasia" name="nome_fantasia">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cnpj">CNPJ</label>
                                <input type="text" class="form-control" placeholder="Digite aqui o CNPJ da Empresa"
                                    id="edit_cnpj" name="cnpj">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefone_principal">Telefone Principal</label>
                                <input type="text" class="form-control"
                                    placeholder="Digite o telefone principal da Empresa" id="edit_telefone_principal"
                                    name="telefone_principal">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email_principal">Email Principal</label>
                                <input type="email" class="form-control" placeholder="email@servidor.com"
                                    id="edit_email_principal" name="email_principal">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cep">
                                    CEP
                                </label>
                                <input class="form-control" type="text" placeholder="Digite o cep aqui" name="cep"
                                    id="edit_cep" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endereco">
                                    Endereço
                                </label>
                                <input class="form-control" type="text" placeholder="Endereço" name="endereco"
                                    id="edit_endereco" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="numero">
                                    Número
                                </label>
                                <input class="form-control" type="text" placeholder="Número" name="numero"
                                    id="edit_numero" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="complemento">
                                    Complemento
                                </label>
                                <input class="form-control" type="text" placeholder="Complemento" name="complemento"
                                    id="edit_complemento" />
                                </ </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bairro">
                                    Bairro
                                </label>
                                <input class="form-control" type="text" placeholder="Bairro" name="bairro"
                                    id="edit_bairro" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cidade">
                                    Cidade
                                </label>
                                <input class="form-control" type="text" placeholder="Cidade" name="cidade"
                                    id="edit_cidade" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estado">
                                    Estado (UF)
                                </label>
                                <input class="form-control" type="text" placeholder="UF" name="estado"
                                    id="edit_estado" />
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="master_id">Usuário Principal</label>
                                <select class="form-control select2bs4" id="edit_master_id" name="master_id">
                                    <?php foreach ($usuarios as $usuario) :?>
                                    <option value="<?= $usuario['id'] ?>"><?= $usuario['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plano_id">Plano</label>
                                <select class="form-control" id="edit_plano_id" name="plano_id">
                                    <option value="0">Sem Plano - ADM</option>
                                    <?php foreach ($planos as $plano) :?>
                                    <option value="<?= $plano['id'] ?>"><?= $plano['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status_empresa">Status</label>
                                <select class="form-control" id="edit_status_empresa" name="status_empresa">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
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
                    <button type="button" class="btn btn-primary" onclick="updateEmpresa()"><i class="fas fa-save"></i>
                        Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarEmpresa(userId) {
    console.log("Abrindo modal para a empresa com ID: " + userId);

    // Abre o modal
    $('#editUserModal').modal('show');

    // Faz a requisição AJAX para buscar os dados da empresa
    $.ajax({
        url: 'config/empresas_buscar.php',
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
            $('#edit_id_empresa').val(response.id);
            $('#edit_razao_social').val(response.razao_social);
            $('#edit_nome_fantasia').val(response.nome_fantasia);
            $('#edit_cnpj').val(response.cnpj);
            $('#edit_telefone_principal').val(response.telefone_principal);
            $('#edit_email_principal').val(response.email_principal);
            $('#edit_cep').val(response.cep);
            $('#edit_endereco').val(response.endereco);
            $('#edit_numero').val(response.numero);
            $('#edit_complemento').val(response.complemento);
            $('#edit_bairro').val(response.bairro);
            $('#edit_cidade').val(response.cidade);
            $('#edit_estado').val(response.estado);
            $('#edit_status_empresa').val(response.status_empresa);
            $('#edit_master_id').val(response.master_id);
            $('#edit_plano_id').val(response.plano_id);
        },
        error: function(xhr, status, error) {
            console.error("Erro ao buscar empresa:", error);
            alert("Erro ao carregar os dados da empresa. Veja o console para mais detalhes.");
        }
    });
}

function updateEmpresa() {
    var formData = new FormData(document.querySelector('form[name="empresaEditar"]'));

    fetch('config/empresas_salvar_alteracao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Empresa atualizada com sucesso!');
                location.reload(); // Recarrega a página para exibir os novos dados
            } else {
                alert('Erro ao atualizar a empresa: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atualizar a empresa.');
        });
}
</script>