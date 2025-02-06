<?php
include 'db.php';
include 'header.php';

// CRUD para Professores
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Adicionar Professor
        $nome = $_POST['nome'];
        $stmt = $pdo->prepare("INSERT INTO professores (nome) VALUES (?)");
        $stmt->execute([$nome]);
    } elseif (isset($_POST['edit'])) {
        // Editar Professor
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $stmt = $pdo->prepare("UPDATE professores SET nome = ? WHERE id = ?");
        $stmt->execute([$nome, $id]);
    } elseif (isset($_POST['delete'])) {
        // Excluir Professor
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM professores WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Buscar todos os professores
$professores = $pdo->query("SELECT * FROM professores")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Professores</h2>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProfessorModal">Adicionar Professor</button>

<table class="table table-bordered">
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
            <td><?= htmlspecialchars($professor['id']) ?></td>
            <td><?= htmlspecialchars($professor['nome']) ?></td>
            <td>
                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editProfessorModal<?= $professor['id'] ?>">Editar</button>
                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteProfessorModal<?= $professor['id'] ?>">Excluir</button>
            </td>
        </tr>

        <!-- Modal Editar Professor -->
        <div class="modal fade" id="editProfessorModal<?= $professor['id'] ?>" tabindex="-1" aria-labelledby="editProfessorModalLabel" aria-hidden="true">
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
                            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($professor['nome']) ?>" required>
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
        <div class="modal fade" id="deleteProfessorModal<?= $professor['id'] ?>" tabindex="-1" aria-labelledby="deleteProfessorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteProfessorModalLabel">Excluir Professor</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $professor['id'] ?>">
                            <p>Tem certeza que deseja excluir o professor <strong><?= htmlspecialchars($professor['nome']) ?></strong>?</p>
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