<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';
include 'header.php';

// CRUD para Alunos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $aluno_id = $_POST['aluno_id'];
        $turma_id = $_POST['turma_id'];
        $status = $_POST['status'];
        $data = date('Y-m-d');
        $data_cadastro = date('Y-m-d');
        $hora_cadastro = date('H:m:s');
        $stmt = $pdo->prepare("INSERT INTO matriculas (matricula_aluno_id, matricula_turma_id, matricula_status, matricula_data, matricula_data_cadastro, matricula_hora_cadastro) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$aluno_id, $turma_id, $status, $data, $data_cadastro, $hora_cadastro]);
        header('Location: matriculas.php');
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $aluno_id = $_POST['aluno_id'];
        $turma_id = $_POST['turma_id'];
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE matriculas SET matricula_aluno_id = ?, matricula_turma_id = ?, matricula_status = ? WHERE matricula_id = ?");
        $stmt->execute([$aluno_id, $turma_id, $status, $id]);
        header('Location: matriculas.php');
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE matriculas SET matricula_status = '0' WHERE matricula_id = ?");
        $stmt->execute([$id]);
        header('Location: matriculas.php');
    }
}

$matriculas = $pdo->query("SELECT m.matricula_id, a.aluno_nome, t.turma_nome, m.matricula_status, m.matricula_data FROM matriculas m JOIN alunos a ON m.matricula_aluno_id = a.aluno_id JOIN turmas t ON m.matricula_turma_id = t.turma_id WHERE m.matricula_status = 'ATIVA'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Matrículas</h2>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addMatriculaModal">Adicionar Matrícula</button>

<table id="example" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Aluno</th>
            <th>Turma</th>
            <th>Status</th>
            <th>Data da matrícula</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($matriculas as $matricula): ?>
            <tr>
                <td><?= $matricula['matricula_id'] ?></td>
                <td><?= $matricula['aluno_nome'] ?></td>
                <td><?= $matricula['turma_nome'] ?></td>
                <td><?= $matricula['matricula_status'] ?></td>
                <td><?= date('d/m/Y', strtotime($matricula['matricula_data'])); ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editMatriculaModal<?= $matricula['matricula_id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteMatriculaModal<?= $matricula['matricula_id'] ?>">Desativar</button>
                </td>
            </tr>

            <!-- Modal Editar Aluno -->
            <div class="modal fade" id="editMatriculaModal<?= $matricula['matricula_id'] ?>" tabindex="-1" aria-labelledby="editMatriculaModalLabel<?= $matricula['matricula_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editMatriculaModalLabel<?= $matricula['matricula_id'] ?>">Editar Matrícula</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <input type="hidden" name="id" value="<?= $matricula['matricula_id'] ?>">

                                <div class="form-group">
                                    <label for="aluno_id">Aluno</label>
                                    <select class="form-control select2" name="aluno_id" id="aluno_id" required>
                                        <?php
                                        // Consulta para buscar os registros da tabela

                                        $sqlAlunos = "SELECT * FROM alunos";
                                        $stmtAlunos = $pdo->prepare($sqlAlunos);
                                        $stmtAlunos->execute();

                                        // Busca todos os registros como um array associativo
                                        $Alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

                                        // Verifica se há registros
                                        if (!empty($Alunos)) {
                                            // Itera sobre os registros e cria as opções
                                            foreach ($Alunos as $Aluno) {
                                                echo "<option value='{$Aluno['aluno_id']}'>{$Aluno['aluno_nome']}</option>";
                                            }
                                        } else {
                                            // Se não houver registros, exibe uma opção padrão
                                            echo "<option value=''>Nenhum registro encontrado</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="turma_id">Turma</label>
                                    <select class="form-control select2" name="turma_id" id="turma_id" required>
                                        <?php
                                        // Consulta para buscar os registros da tabela

                                        $sqlTurmas = "SELECT * FROM turmas t JOIN cursos c ON t.turma_curso_id = c.curso_id JOIN professores p ON t.turma_professor_id = p.professor_id WHERE t.turma_status = '1'";
                                        $stmtTurmas = $pdo->prepare($sqlTurmas);
                                        $stmtTurmas->execute();

                                        // Busca todos os registros como um array associativo
                                        $Turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

                                        // Verifica se há registros
                                        if (!empty($Turmas)) {
                                            // Itera sobre os registros e cria as opções
                                            foreach ($Turmas as $Turma) {
                                                echo "<option value='{$Turma['turma_id']}'>{$Turma['turma_nome']} | CURSO: {$Turma['curso_nome']} | PROFESSOR: {$Turma['professor_nome']}</option>";
                                            }
                                        } else {
                                            // Se não houver registros, exibe uma opção padrão
                                            echo "<option value=''>Nenhum registro encontrado</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control select2" name="status" id="status" required>
                                        <option value="ATIVA">ATIVA</option>;
                                        <option value="CONCLUÍDA">CONCLUÍDA</option>;
                                        <option value="CANCELADA">CANCELADA</option>;
                                    </select>
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
            <div class="modal fade" id="deleteTurmaModal<?= $turma['turma_id'] ?>" tabindex="-1" aria-labelledby="deleteTurmaModalLabel<?= $turma['turma_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteTurmaModalLabel<?= $turma['turma_id'] ?>">Desativar Turma</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Você tem certeza que deseja desativar a turma <strong><?= $turma['turma_nome'] ?></strong>?</p>
                                <input type="hidden" name="id" value="<?= $turma['turma_id'] ?>">
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
<div class="modal fade" id="addMatriculaModal" tabindex="-1" aria-labelledby="addMatriculaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMatriculaModalLabel">Adicionar Matrícula</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="aluno_id">Aluno</label>
                        <select class="form-control select2" name="aluno_id" id="aluno_id" required>
                            <?php
                            // Consulta para buscar os registros da tabela

                            $sqlAlunos = "SELECT * FROM alunos a WHERE a.aluno_status = '1'";
                            $stmtAlunos = $pdo->prepare($sqlAlunos);
                            $stmtAlunos->execute();

                            // Busca todos os registros como um array associativo
                            $Alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

                            // Verifica se há registros
                            if (!empty($Alunos)) {
                                // Itera sobre os registros e cria as opções
                                foreach ($Alunos as $Aluno) {
                                    echo "<option value='{$Aluno['aluno_id']}'>{$Aluno['aluno_nome']}</option>";
                                }
                            } else {
                                // Se não houver registros, exibe uma opção padrão
                                echo "<option value=''>Nenhum registro encontrado</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="turma_id">Turma</label>
                        <select class="form-control select2" name="turma_id" id="turma_id" required>
                            <?php
                            // Consulta para buscar os registros da tabela

                            $sqlTurmas = "SELECT * FROM turmas t JOIN cursos c ON t.turma_curso_id = c.curso_id JOIN professores p ON t.turma_professor_id = p.professor_id WHERE t.turma_status = '1'";
                            $stmtTurmas = $pdo->prepare($sqlTurmas);
                            $stmtTurmas->execute();

                            // Busca todos os registros como um array associativo
                            $Turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

                            // Verifica se há registros
                            if (!empty($Turmas)) {
                                // Itera sobre os registros e cria as opções
                                foreach ($Turmas as $Turma) {
                                    echo "<option value='{$Turma['turma_id']}'>{$Turma['turma_nome']} | CURSO: {$Turma['curso_nome']} | PROFESSOR: {$Turma['professor_nome']}</option>";
                                }
                            } else {
                                // Se não houver registros, exibe uma opção padrão
                                echo "<option value=''>Nenhum registro encontrado</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control select2" name="status" id="status" required>
                            <option value="ATIVA">ATIVA</option>;
                            <option value="CONCLUÍDA">CONCLUÍDA</option>;
                            <option value="CANCELADA">CANCELADA</option>;
                        </select>
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