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
        $turma_id = $_POST['turma_id'];
        $dia_semana = $_POST['dia_semana'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $data_cadastro = date('Y-m-d');
        $hora_cadastro = date('H:m:s');

        $stmt = $pdo->prepare("INSERT INTO horarios (horario_turma_id, horario_dia_semana, horario_hora_inicio, horario_hora_fim, horario_data_cadastro, horario_hora_cadastro) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$turma_id, $dia_semana, $hora_inicio, $hora_fim, $data_cadastro, $hora_cadastro]);
        header('Location: horarios.php');
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $turma_id = $_POST['turma_id'];
        $dia_semana = $_POST['dia_semana'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fim = $_POST['hora_fim'];
        $stmt = $pdo->prepare("UPDATE horarios SET horario_turma_id = ?, horario_dia_semana = ?, horario_hora_inicio = ?, horario_hora_fim = ? WHERE horario_id = ?");
        $stmt->execute([$turma_id, $dia_semana, $hora_inicio, $hora_fim, $id]);
        header('Location: horarios.php');
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE horarios SET horario_status = '0' WHERE horario_id = ?");
        $stmt->execute([$id]);
        header('Location: horarios.php');
    }
}

$horarios = $pdo->query("SELECT h.horario_id, h.horario_turma_id, t.turma_nome, c.curso_nome, p.professor_nome, h.horario_dia_semana, h.horario_hora_inicio, h.horario_hora_fim FROM horarios h JOIN turmas t ON h.horario_turma_id = t.turma_id JOIN cursos c ON t.turma_curso_id = c.curso_id JOIN professores p ON c.curso_id = p.professor_id WHERE h.horario_status = '1'")->fetchAll(PDO::FETCH_ASSOC);
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
                <td><?= date('H:m:s', strtotime($horario['horario_hora_inicio'])) ?></td>
                <td><?= date('H:m:s', strtotime($horario['horario_hora_fim'])) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editHorarioModal<?= $horario['horario_id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteHorarioModal<?= $horario['horario_id'] ?>">Desativar</button>
                </td>
            </tr>

            <!-- Modal Editar Horário -->
            <div class="modal fade" id="editHorarioModal<?= $horario['horario_id'] ?>" tabindex="-1" aria-labelledby="editHorarioModalLabel<?= $horario['horario_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editHorarioModalLabel<?= $horario['horario_id'] ?>">Editar Horário</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $horario['horario_id'] ?>">

                                <div class="form-group">
                                    <label for="horario_turma_id">Turma</label>

                                    <select class="form-control select2" name="turma_id" id="turma_id" required>
                                        <option value="">-- Selecione uma turma --</option>
                                        <?php
                                        // Consulta para buscar os registros da tabela

                                        $sqlTurmas = "SELECT * FROM turmas INNER JOIN cursos ON turmas.turma_curso_id = cursos.curso_id";
                                        $stmtTurmas = $pdo->prepare($sqlTurmas);
                                        $stmtTurmas->execute();

                                        // Busca todos os registros como um array associativo
                                        $Turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

                                        // Verifica se há registros
                                        if (!empty($Turmas)) {
                                            // Itera sobre os registros e cria as opções
                                            foreach ($Turmas as $Turma) {
                                                echo "<option value='{$Turma['turma_id']}'>{$Turma['turma_nome']} - {$Turma['curso_nome']}</option>";
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
                                    <label for="hora_inicio">Hora inicial</label>
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="<?= $horario['horario_hora_inicio'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="hora_fim">Hora final</label>
                                    <input type="time" class="form-control" id="hora_fim" name="hora_fim" value="<?= $horario['horario_hora_fim'] ?>" required>
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
            <div class="modal fade" id="deleteHorarioModal<?= $horario['horario_id'] ?>" tabindex="-1" aria-labelledby="deleteHorarioModalLabel<?= $horario['horario_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteHorarioModalLabel<?= $horario['horario_id'] ?>">Desativar Horário</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Você tem certeza que deseja desativar o horário <strong><?= $horario['horario_id'] ?></strong>?</p>
                                <input type="hidden" name="id" value="<?= $horario['horario_id'] ?>">
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

                        <select class="form-control select2" name="turma_id" id="turma_id" required>
                            <option value="">-- Selecione uma turma --</option>
                            <?php
                            // Consulta para buscar os registros da tabela

                            $sqlTurmas = "SELECT * FROM turmas INNER JOIN cursos ON turmas.turma_curso_id = cursos.curso_id";
                            $stmtTurmas = $pdo->prepare($sqlTurmas);
                            $stmtTurmas->execute();

                            // Busca todos os registros como um array associativo
                            $Turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);

                            // Verifica se há registros
                            if (!empty($Turmas)) {
                                // Itera sobre os registros e cria as opções
                                foreach ($Turmas as $Turma) {
                                    echo "<option value='{$Turma['turma_id']}'>{$Turma['turma_nome']} - {$Turma['curso_nome']}</option>";
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
                        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
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