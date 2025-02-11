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
        $nome = $_POST['nome'];
        $curso_id = $_POST['curso_id'];
        $professor_id = $_POST['professor_id'];
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];
        $data_fim = $_POST['data_fim'];
        $data_cadastro = date('Y-m-d');
        $hora_cadastro = date('H:m:s');

        $stmt = $pdo->prepare("INSERT INTO turmas (turma_nome,turma_curso_id,turma_professor_id,turma_data_inicio,turma_data_fim,turma_data_cadastro,turma_hora_cadastro) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$nome, $curso_id, $professor_id, $data_inicio, $data_fim, $data_cadastro, $hora_cadastro]);
        header('Location: turmas.php');
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $curso_id = $_POST['curso_id'];
        $professor_id = $_POST['professor_id'];
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];
        $stmt = $pdo->prepare("UPDATE turmas SET turma_nome = ?, turma_curso_id = ?, turma_professor_id = ?, turma_data_inicio = ?, turma_data_fim = ? WHERE turma_id = ?");
        $stmt->execute([$nome, $curso_id, $professor_id, $data_inicio, $data_fim, $id]);
        header('Location: turmas.php');
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE turmas SET turma_status = '0' WHERE turma_id = ?");
        $stmt->execute([$id]);
        header('Location: turmas.php');
    }
}

$turmas = $pdo->query("SELECT turma_id,turma_nome,curso_id,curso_nome,professor_nome,turma_data_inicio,turma_data_fim FROM turmas INNER JOIN cursos ON turmas.turma_curso_id = cursos.curso_id INNER JOIN professores ON turmas.turma_professor_id = professores.professor_id WHERE turma_status = '1'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Turmas</h2>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addTurmaModal">Adicionar Turma</button>

<table id="example" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Curso</th>
            <th>Professor</th>
            <th>Data Inicial</th>
            <th>Data Final</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($turmas as $turma): ?>
            <tr>
                <td><?= $turma['turma_id'] ?></td>
                <td><?= $turma['turma_nome'] ?></td>
                <td><?= $turma['curso_nome'] ?></td>
                <td><?= $turma['professor_nome'] ?></td>

                <td><?= date('d/m/Y', strtotime($turma['turma_data_inicio'])); ?></td>
                <td><?= date('d/m/Y', strtotime($turma['turma_data_fim'])); ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editTurmaModal<?= $turma['turma_id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteTurmaModal<?= $turma['turma_id'] ?>">Desativar</button>
                </td>
            </tr>

            <!-- Modal Editar Aluno -->
            <div class="modal fade" id="editTurmaModal<?= $turma['turma_id'] ?>" tabindex="-1" aria-labelledby="editTurmaModalLabel<?= $turma['turma_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTurmaModalLabel<?= $turma['turma_id'] ?>">Editar Turma</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $turma['turma_id'] ?>">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= $turma['turma_nome'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="curso_id">Curso</label>
                                    <select class="form-control select2" name="curso_id" id="curso_id" required>
                                        <?php
                                        // Consulta para buscar os registros da tabela
                                        
                                        $sqlCursos = "SELECT * FROM cursos";
                                        $stmtCursos = $pdo->prepare($sqlCursos);
                                        $stmtCursos->execute();

                                        // Busca todos os registros como um array associativo
                                        $Cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

                                        // Verifica se há registros
                                        if (!empty($Cursos)) {
                                            // Itera sobre os registros e cria as opções
                                            foreach ($Cursos as $Curso) {
                                                echo "<option value='{$Curso['curso_id']}'>{$Curso['curso_nome']}</option>";
                                            }
                                        } else {
                                            // Se não houver registros, exibe uma opção padrão
                                            echo "<option value=''>Nenhum registro encontrado</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="professor_id">Professor</label>
                                    <select class="form-control select2" name="professor_id" id="professor_id" required>
                                    <?php
                                        // Consulta para buscar os registros da tabela
                                        $sqlProfessores = "SELECT * FROM professores";
                                        $stmtProfessores = $pdo->prepare($sqlProfessores);
                                        $stmtProfessores->execute();

                                        // Busca todos os registros como um array associativo
                                        $Professores = $stmtProfessores->fetchAll(PDO::FETCH_ASSOC);

                                        // Verifica se há registros
                                        if (!empty($Professores)) {
                                            // Itera sobre os registros e cria as opções
                                            foreach ($Professores as $Professor) {
                                                echo "<option value='{$Professor['professor_id']}'>{$Professor['professor_nome']}</option>";
                                            }
                                        } else {
                                            // Se não houver registros, exibe uma opção padrão
                                            echo "<option value=''>Nenhum registro encontrado</option>";
                                        }
                                        ?>
                
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="data_inicio">Data inicial</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= $turma['turma_data_inicio'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="data_fim">Data final</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= $turma['turma_data_fim'] ?>" required>
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
<div class="modal fade" id="addTurmaModal" tabindex="-1" aria-labelledby="addTurmaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTurmaModalLabel">Adicionar Turma</h5>
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

                        <label for="curso_id">Curso</label>

                        <select class="form-control select2" name="curso_id" id="curso_id" required>
                            <option value="">-- Selecione um curso --</option>
                            <?php
                            // Verifica se há registros
                            if (!empty($turmas)) {
                                // Itera sobre os registros e cria as opções
                                foreach ($turmas as $turma) {
                                    echo "<option value='{$turma['curso_id']}'>{$turma['curso_nome']}</option>";
                                }
                            } else {
                                // Se não houver registros, exibe uma opção padrão
                                echo "<option value=''>Nenhum registro encontrado</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <label for="professor_id">Professor</label>

                        <select class="form-control" name="professor_id" id="professor_id" required>
                            <option value="">-- Selecione um professor --</option>
                            <?php
                            // Verifica se há registros
                            if (!empty($turmas)) {
                                // Itera sobre os registros e cria as opções
                                foreach ($turmas as $turma) {
                                    echo "<option value='{$turma['professor_id']}'>{$turma['professor_nome']}</option>";
                                }
                            } else {
                                // Se não houver registros, exibe uma opção padrão
                                echo "<option value=''>Nenhum registro encontrado</option>";
                            }
                            ?>
                        </select>
                    </div>



                    <div class="form-group">
                        <label for="data_inicio">Data inicial</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="data_fim">Data final</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" required>
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