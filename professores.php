<?php
include 'db.php';
include 'header.php';

// CRUD para Professores
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Adicionar Professor
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $data_cadastro = date('Y-m-d');
        $hora_cadastro = date('H:m:s');
        $stmt = $pdo->prepare("INSERT INTO professores (professor_nome,professor_telefone,professor_email,professor_data_cadastro,professor_hora_cadastro) VALUES (?,?,?,?,?)");
        $stmt->execute([$nome, $telefone, $email, $data_cadastro, $hora_cadastro]);
        header('Location: professores.php');
    } elseif (isset($_POST['edit'])) {
        // Editar Professor
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $stmt = $pdo->prepare("UPDATE professores SET professor_nome = ?, professor_telefone = ?, professor_email = ? WHERE professor_id = ?");
        $stmt->execute([$nome, $telefone, $email, $id]);
        header('Location: professores.php');
    } elseif (isset($_POST['delete'])) {
        // Excluir Professor
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE professores SET professor_status = '0' WHERE professor_id = ?");
        $stmt->execute([$id]);
        header('Location: professores.php');
    }
}

// Buscar todos os professores
$professores = $pdo->query("SELECT * FROM professores WHERE professor_status = '1'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Professores</h2>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProfessorModal">Adicionar Professor</button>

<table id="example" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($professores as $professor): ?>
            <tr>
                <td><?= htmlspecialchars($professor['professor_id']) ?></td>
                <td><?= htmlspecialchars($professor['professor_nome']) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editProfessorModal<?= $professor['professor_id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteProfessorModal<?= $professor['professor_id'] ?>">Desativar</button>
                </td>
            </tr>

            <!-- Modal Editar Professor -->
            <div class="modal fade" id="editProfessorModal<?= $professor['professor_id'] ?>" tabindex="-1" aria-labelledby="editProfessorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editProfessorModalLabel">Editar Professor</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $professor['professor_id'] ?>">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($professor['professor_nome']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="telefone">Telefone</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($professor['professor_telefone']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($professor['professor_email']) ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                <button type="submit" class="btn btn-primary" name="edit">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Excluir Professor -->
            <div class="modal fade" id="deleteProfessorModal<?= $professor['professor_id'] ?>" tabindex="-1" aria-labelledby="deleteProfessorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteProfessorModalLabel">Desativar Professor</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $professor['professor_id'] ?>">
                                <p>Tem certeza que deseja desativar o professor <strong><?= htmlspecialchars($professor['professor_nome']) ?></strong>?</p>
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

<!-- Modal Adicionar Professor -->
<div class="modal fade" id="addProfessorModal" tabindex="-1" aria-labelledby="addProfessorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProfessorModalLabel">Adicionar Professor</h5>
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
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
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