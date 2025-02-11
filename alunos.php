<?php
include 'db.php';
include 'header.php';

/*
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
*/

// CRUD para Alunos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nome = $_POST['nome'];
        $data_nascimento = $_POST['data_nascimento'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $cep = $_POST['cep'];
        $logradouro = $_POST['logradouro'];
        $numero = $_POST['numero'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $uf = $_POST['uf'];
        $extensao = pathinfo($_FILES['foto']['name']);
        $extensao = "." . $extensao['extension'];
        $foto = $pdo->lastInsertId() . date('YmdHms') . $extensao;
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/$foto");
        $data_cadastro = date('Y-m-d');
        $hora_cadastro = date('H:m:s');

        $stmt = $pdo->prepare("INSERT INTO alunos (aluno_nome,aluno_data_nascimento,aluno_email,aluno_telefone,aluno_cep,aluno_logradouro,aluno_numero,aluno_bairro,aluno_cidade,aluno_uf,aluno_foto,aluno_data_cadastro,aluno_hora_cadastro) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$nome, $data_nascimento, $email, $telefone, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $foto, $data_cadastro, $hora_cadastro]);
        header('Location: alunos.php');
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $data_nascimento = $_POST['data_nascimento'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $cep = $_POST['cep'];
        $logradouro = $_POST['logradouro'];
        $numero = $_POST['numero'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $uf = $_POST['uf'];
        $extensao = pathinfo($_FILES['foto']['name']);
        $extensao = "." . $extensao['extension'];
        $foto = $pdo->lastInsertId() . date('YmdHms') . $extensao;

        if ($foto == "") {
            $stmt = $pdo->prepare("UPDATE alunos SET aluno_nome = ?, aluno_data_nascimento = ?, aluno_email = ?, aluno_telefone = ?,aluno_cep = ?, aluno_logradouro = ?, aluno_numero = ?, aluno_bairro = ?, aluno_cidade = ?, aluno_uf = ? WHERE aluno_id = ?");
            $stmt->execute([$nome, $data_nascimento, $email, $telefone, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $id]);
            unlink($foto);
            header('Location: alunos.php');
        } else {
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/$foto");
            $stmt = $pdo->prepare("UPDATE alunos SET aluno_nome = ?, aluno_data_nascimento = ?, aluno_email = ?, aluno_telefone = ?, aluno_cep = ?, aluno_logradouro = ?, aluno_numero = ?, aluno_bairro = ?, aluno_cidade = ?, aluno_uf = ?, aluno_foto = ? WHERE aluno_id = ?");
            $stmt->execute([$nome, $data_nascimento, $email, $telefone, $cep, $logradouro, $numero, $bairro, $cidade, $uf, $foto, $id]);
            header('Location: alunos.php');
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE alunos SET aluno_status = '0' WHERE aluno_id = ?");
        $stmt->execute([$id]);
        header('Location: alunos.php');
    }
}

$alunos = $pdo->query("SELECT * FROM alunos WHERE aluno_status = '1'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Alunos</h2>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addAlunoModal">Adicionar Aluno</button>

<table id="example" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Foto</th>
            <th>Data de Nascimento</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alunos as $aluno): ?>
            <tr>
                <td><?= $aluno['aluno_id'] ?></td>
                <td><?= $aluno['aluno_nome'] ?></td>
                <td><img src="uploads/<?= $aluno['aluno_foto'] ?>" width="50"></td>
                <td><?= date('d/m/Y', strtotime($aluno['aluno_data_nascimento'])); ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editAlunoModal<?= $aluno['aluno_id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteAlunoModal<?= $aluno['aluno_id'] ?>">Desativar</button>
                </td>
            </tr>

            <!-- Modal Editar Aluno -->
            <div class="modal fade" id="editAlunoModal<?= $aluno['aluno_id'] ?>" tabindex="-1" aria-labelledby="editAlunoModalLabel<?= $aluno['aluno_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editAlunoModalLabel<?= $aluno['aluno_id'] ?>">Editar Aluno</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $aluno['aluno_id'] ?>">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= $aluno['aluno_nome'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="data_nascimento">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= $aluno['aluno_data_nascimento'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $aluno['aluno_email'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="telefone">Telefone</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $aluno['aluno_telefone'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="cep">CEP</label>
                                    <input type="text" class="form-control" id="cep" name="cep" size="10" maxlength="9" value="<?= $aluno['aluno_cep'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="endereco">Logradouro</label>
                                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?= $aluno['aluno_logradouro'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="numero">Número</label>
                                    <input type="text" class="form-control" id="numero" name="numero" value="<?= $aluno['aluno_numero'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= $aluno['aluno_bairro'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?= $aluno['aluno_cidade'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="uf">UF</label>
                                    <input type="text" class="form-control" id="uf" name="uf" value="<?= $aluno['aluno_uf'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="foto">Foto</label>
                                    <input type="file" class="form-control" id="foto" name="foto" value="<?= $aluno['aluno_foto'] ?>" />
                                    <small>Deixe em branco para manter a foto atual.</small>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary" name="edit">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Excluir Aluno -->
            <div class="modal fade" id="deleteAlunoModal<?= $aluno['aluno_id'] ?>" tabindex="-1" aria-labelledby="deleteAlunoModalLabel<?= $aluno['aluno_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteAlunoModalLabel<?= $aluno['aluno_id'] ?>">Desativar Aluno</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Você tem certeza que deseja excluir o aluno <strong><?= $aluno['aluno_nome'] ?></strong>?</p>
                                <input type="hidden" name="id" value="<?= $aluno['aluno_id'] ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger" name="delete">Desativar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </tbody>
</table>



<!-- Modal Adicionar Aluno -->
<div class="modal fade" id="addAlunoModal" tabindex="-1" aria-labelledby="addAlunoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAlunoModalLabel">Adicionar Aluno</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required>
                    </div>
                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" class="form-control" id="cep" name="cep" size="10" maxlength="9" onblur="buscarCep()" required>
                    </div>
                    <div class="form-group">
                        <label for="endereco">Logradouro</label>
                        <input type="text" class="form-control" id="rua" name="logradouro" required>
                    </div>
                    <div class="form-group">
                        <label for="numero">Número</label>
                        <input type="text" class="form-control" id="numero" name="numero" required>
                    </div>
                    <div class="form-group">
                        <label for="bairro">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" required>
                    </div>
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" class="form-control" id="cidade" name="cidade" required>
                    </div>
                    <div class="form-group">
                        <label for="uf">UF</label>
                        <input type="text" class="form-control" id="uf" name="uf" required>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <div>
                            <input type="file" class="form-control" id="foto" name="foto" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary" name="add">Salvar</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv', 'pdf'
            ],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "Nada encontrado",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Nenhum registro disponível",
                "infoFiltered": "(filtrado de _MAX_ registros no total)",
                "search": "Pesquisar:",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                }
            }
        });
    });
</script>


<?php include 'footer.php'; ?>