<?php
include 'db.php';
include 'header.php';

// CRUD para Alunos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $horario_id = $_POST['horario_id'];
        $dia_semana = $_POST['dia_semana'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $data_cadastro = date('Y-m-d');
        $hora_cadastro = date('H:m:s');

        $stmt = $pdo->prepare("INSERT INTO horarios (horario_turma_id,horario_dia_semana, horario_hora_inicio, horario_hora_fim, horario_data_cadastro,horario_hora_cadastro) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$horario_id, $dia_semana, $hora_inicio, $hora_fim, $data_cadastro, $hora_cadastro]);
        header('Location: horarios.php');
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $horario_turma_id = $_POST['horario_turma_id'];
        $dia_semana = $_POST['dia_semana'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $stmt = $pdo->prepare("UPDATE horarios SET horario_id = ?, horario_turma_id = ?, horario_dia_semana = ?, horario_hora_inicio = ?, horario_hora_fim = ? WHERE horario__id = ?");
        $stmt->execute([$id, $horario_turma_id, $dia_semana, $hora_inicio, $hora_fim]);
        header('Location: horarios.php');
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE horarios SET horario_status = '0' WHERE horario_id = ?");
        $stmt->execute([$id]);
        header('Location: horarios.php');
    }
}

$horarios = $pdo->query("SELECT horario_id, turma_id, turma_nome, curso_id, curso_nome, professor_id, professor_nome, horario_hora_inicio, horario_hora_fim, horario_status FROM horarios INNER JOIN turmas ON horarios.horario_turma_id = turmas.turma_id INNER JOIN cursos ON horarios.horario_turma_id = cursos.curso_id INNER JOIN professores ON horarios.horario_turma_id = professores.professor_id WHERE horario_status = '1'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Horários</h2>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addHorarioModal">Adicionar Horário</button>

<table id="example" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Turma</th>
            <th>Curso</th>
            <th>Professor</th>
            <th>Dia da semana</th>
            <th>Hora Inicial</th>
            <th>Hora Final</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($horarios as $horario): ?>
            <tr>
                <td><?= $horario['horario_id'] ?></td>
                <td><?= $horario['turma_nome'] ?></td>
                <td><?= $horario['curso_nome'] ?></td>
                <td><?= $horario['professor_nome'] ?></td>
                <td><?= $horario['horario_dia_semana'] ?></td>
                <td><?= $horario['horario_hora_inicio'] ?></td>
                <td><?= date('H:m:s', strtotime($horario['horario_hora_inicial'])) ?></td>
                <td><?= date('H:m:s', strtotime($horario['horario_hora_fim'])) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editTurmaModal<?= $turma['turma_id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteTurmaModal<?= $turma['turma_id'] ?>">Desativar</button>
                </td>
            </tr>

            <!-- Modal Editar Aluno -->
            <div class="modal fade" id="editTurmaModal<?= $horarios['horario_id'] ?>" tabindex="-1" aria-labelledby="editTurmaModalLabel<?= $horarios['horario_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTurmaModalLabel<?= $horarios['horario_id'] ?>">Editar Horário</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $horarios['horario_id'] ?>">
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
            <div class="modal fade" id="deleteHorarioModal<?= $horarios['horario_id'] ?>" tabindex="-1" aria-labelledby="deleteHorarioModalLabel<?= $horarios['horario_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteHorarioModalLabel<?= $horarios['horario_id'] ?>">Desativar Horário</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Você tem certeza que deseja desativar o horário <strong><?= $horarios['horario_id'] ?></strong>?</p>
                                <input type="hidden" name="id" value="<?= $horarios['horario_id'] ?>">
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
<div class="modal fade" id="addHorarioModal" tabindex="-1" aria-labelledby="addHorarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHorarioModalLabel">Adicionar Horário</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">

                        <label for="horario_turma_id">Turma</label>
                        <select class="form-control select2" name="horario_turma_id" id="horario_turma_id" required>
                            <option value="">-- Selecione uma turma --</option>
                            <?php
                            // Verifica se há registros
                            if (!empty($horarios)) {
                                // Itera sobre os registros e cria as opções
                                foreach ($horarios as $horario) {
                                    echo "<option value='{$horario['horario_turma_id']}'>{$horario['turma_nome']}</option>";
                                }
                            } else {
                                // Se não houver registros, exibe uma opção padrão
                                echo "<option value=''>Nenhum registro encontrado</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dia_semana">Dia da semana</label>
                        <select class="form-control select2" name="dia_semana" id="dia_semana" required>
                        <option value="SEGUNDA-FEIRA">SEGUNDA-FEIRA</option>
                        <option value="TERÇA-FEIRA">TERÇA-FEIRA</option>
                        <option value="QUARTA-FEIRA">QUARTA-FEIRA</option>
                        <option value="QUINTA-FEIRA">QUINTA-FEIRA</option>
                        <option value="SEXTA-FEIRA">SEXTA-FEIRA</option>
                        <option value="SÁBADO">SÁBADO</option>
                        <option value="DOMINGO">DOMINGO</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="hora_inicial">Hora inicial</label>
                        <input type="time" class="form-control" id="hora_inicial" name="hora_inicial" required>
                    </div>
                  
                    <div class="form-group">
                        <label for="hora_fim">Hora inicial</label>
                        <input type="time" class="form-control" id="hora_fim" name="hora_fim" required>
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