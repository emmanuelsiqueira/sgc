<?php
include 'db.php';
include 'header.php';

// CRUD para Cursos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Adicionar Cursos
        $nome = $_POST['nome'];
        $carga_horaria = $_POST['carga_horaria'];
        $stmt = $pdo->prepare("INSERT INTO cursos (nome,carga_horaria) VALUES (?)");
        $stmt->execute([$nome,$carga_horaria]);
    } elseif (isset($_POST['edit'])) {
        // Editar Cursos
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $carga_horaria = $_POST['carga_horaria'];
        $stmt = $pdo->prepare("UPDATE cursos SET nome = ?,carga_horaria = ? WHERE id = ?");
        $stmt->execute([$nome, $carga_horaria, $id]);
    } elseif (isset($_POST['delete'])) {
        // Excluir Cursos
        $id = $_POST['id'];
        $softdelete = $_POST['softdelete'];
        $stmt = $pdo->prepare("UPDATE cursos SET softdelete = ? WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Buscar todos os cursos
$cursos = $pdo->query("SELECT * FROM cursos")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Cursos</h2>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addCursoModal">Adicionar Curso</button>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Carga Horária</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cursos as $curso): ?>
        <tr>
            <td><?= htmlspecialchars($curso['id']) ?></td>
            <td><?= htmlspecialchars($curso['nome']) ?></td>
            <td><?= htmlspecialchars($curso['carga_horaria']) ?></td>
            <td>
                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editCursoModal<?= $curso['id'] ?>">Editar</button>
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteCursoModal<?= $curso['id'] ?>">Excluir</button>
            </td>
        </tr>

        <!-- Modal Editar Curso -->
        <div class="modal fade" id="editCursoModal<?= $curso['id'] ?>" tabindex="-1" aria-labelledby="editCursoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCursoModalLabel">Editar Curso</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $curso['id'] ?>">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($curso['nome']) ?>" required>
                            <div class="form-group">
                                <label for="carga-horaria">Carga Horária</label>
                                <input type="text" class="form-control" id="carga_horaria" name="carga_horaria" value="<?= htmlspecialchars($curso['carga_horaria']) ?>" required>
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

        <!-- Modal Excluir Curso -->
        <div class="modal fade" id="deleteCursoModal<?= $curso['id'] ?>" tabindex="-1" aria-labelledby="deleteCursoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteCursoModalLabel">Excluir Curso</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="1">
                            <p>Tem certeza que deseja excluir o curso <strong><?= htmlspecialchars($curso['nome']) ?></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" name="delete">Excluir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal Adicionar Curso -->
<div class="modal fade" id="addCursoModal" tabindex="-1" aria-labelledby="addCursoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCursoModalLabel">Adicionar Curso</h5>
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
                        <label for="nome">Carga Horária</label>
                        <input type="text" class="form-control" id="carga-horaria" name="carga-horaria" required>
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

<?php include 'footer.php'; ?>