<?php
include 'db.php';
include 'header.php';

// CRUD para Cursos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Adicionar Cursos
        $nome = $_POST['nome'];
        $carga_horaria = $_POST['carga_horaria'];
        $data_cadastro = date('Y-m-d');
        $hora_cadastro = date('H:m:s');
        $stmt = $pdo->prepare("INSERT INTO cursos (curso_nome,curso_carga_horaria,curso_data_cadastro,curso_hora_cadastro) VALUES (?,?,?,?)");
        $stmt->execute([$nome,$carga_horaria,$data_cadastro,$hora_cadastro]);
        header('Location: cursos.php');
    } elseif (isset($_POST['edit'])) {
        // Editar Cursos
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $carga_horaria = $_POST['carga_horaria'];
        $stmt = $pdo->prepare("UPDATE cursos SET curso_nome = ?,curso_carga_horaria = ? WHERE curso_id = ?");
        $stmt->execute([$nome, $carga_horaria, $id]);
        header('Location: cursos.php');
    } elseif (isset($_POST['delete'])) {
        // Excluir Cursos
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE cursos SET curso_status = ? WHERE curso_id = ?");
        $stmt->execute([$id]);
        header('Location: cursos.php');
    }
}

// Buscar todos os cursos
$cursos = $pdo->query("SELECT * FROM cursos WHERE curso_status = '1'")->fetchAll(PDO::FETCH_ASSOC);
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
            <td><?= htmlspecialchars($curso['curso_id']) ?></td>
            <td><?= htmlspecialchars($curso['curso_nome']) ?></td>
            <td><?= htmlspecialchars($curso['curso_carga_horaria']) ?></td>
            <td>
                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editCursoModal<?= $curso['curso_id'] ?>">Editar</button>
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteCursoModal<?= $curso['curso_id'] ?>">Desativar</button>
            </td>
        </tr>

        <!-- Modal Editar Curso -->
        <div class="modal fade" id="editCursoModal<?= $curso['curso_id'] ?>" tabindex="-1" aria-labelledby="editCursoModalLabel" aria-hidden="true">
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
                            <input type="hidden" name="id" value="<?= $curso['curso_id'] ?>">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($curso['curso_nome']) ?>" required>
                            <div class="form-group">
                                <label for="carga-horaria">Carga Horária</label>
                                <input type="text" class="form-control" id="carga_horaria" name="carga_horaria" value="<?= htmlspecialchars($curso['curso_carga_horaria']) ?>" required>
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
        <div class="modal fade" id="deleteCursoModal<?= $curso['curso_id'] ?>" tabindex="-1" aria-labelledby="deleteCursoModalLabel<?= $curso['curso_id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteCursoModalLabel<?= $curso['curso_id'] ?>">Desativar Curso</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $curso['curso_id'] ?>">
                            <p>Tem certeza que deseja desativar o curso <strong><?= htmlspecialchars($curso['curso_nome']) ?></strong>?</p>
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
                        <input type="text" class="form-control" id="carga_horaria" name="carga_horaria" required>
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