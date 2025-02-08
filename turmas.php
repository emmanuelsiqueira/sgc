<?php
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

        $stmt = $pdo->prepare("INSERT INTO turmas (nome,curso_id,professor_id,data_inicio,data_fim) VALUES (?,?,?,?,?)");
        $stmt->execute([$nome, $curso_id, $professor_id, $data_inicio, $data_fim]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $curso_id = $_POST['curso_id'];
        $professor_id = $_POST['professor_id'];
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];
        $stmt = $pdo->prepare("UPDATE turmas SET nome = ?, curso_id = ?, professor_id = ?, data_inicio = ?, data_fim = ? WHERE id = ?");
        $stmt->execute([$nome, $curso_id, $professor_id, $data_inicio, $data_fim, $id]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE turmas SET estado = '0' WHERE id = ?");
        $stmt->execute([$id]);
    }
}

$turmas = $pdo->query("SELECT * FROM turmas WHERE estado = '1'")->fetchAll(PDO::FETCH_ASSOC);
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
                <td><?= $turma['id'] ?></td>
                <td><?= $turma['nome'] ?></td>
                <td><?= $turma['curso_id'] ?></td>
                <td><?= $turma['professor_id'] ?></td>

                <td><?= date('d/m/Y', strtotime($turma['data_inicio'])); ?></td>
                <td><?= date('d/m/Y', strtotime($turma['data_fim'])); ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editTurmaModal<?= $turma['id'] ?>">Editar</button>
                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteTurmaModal<?= $turma['id'] ?>">Desativar</button>
                </td>
            </tr>

            <!-- Modal Editar Aluno -->
            <div class="modal fade" id="editTurmaModal<?= $turma['id'] ?>" tabindex="-1" aria-labelledby="editTurmaModalLabel<?= $turma['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTurmaModalLabel<?= $turma['id'] ?>">Editar Turma</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $turma['id'] ?>">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= $turma['nome'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="curso_id">Curso</label>
                                    <input type="" class="form-control" id="curso_id" name="curso_id" value="<?= $turma['curso_id'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="professor_id">Professor</label>
                                    <input type="text" class="form-control" id="professor" name="professor" value="<?= $turma['professor_id'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="data_inicio">Data inicial</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= $turma['data_inicio'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="data_fim">Data final</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= $turma['data_fim'] ?>" required>
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
            <div class="modal fade" id="deleteTurmaModal<?= $turma['id'] ?>" tabindex="-1" aria-labelledby="deleteTurmaModalLabel<?= $turma['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteTurmaModalLabel<?= $turma['id'] ?>">Desativar Turma</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Você tem certeza que deseja desativar a turma <strong><?= $turma['nome'] ?></strong>?</p>
                                <input type="hidden" name="id" value="<?= $turma['id'] ?>">
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
                            class Curso
                            {
                                private $pdo;

                                // Construtor que recebe a conexão PDO
                                public function __construct($pdo)
                                {
                                    $this->pdo = $pdo;
                                }

                                // Método para buscar todos os cursos
                                public function listarCursos()
                                {
                                    $result = [];
                                    $sql = "SELECT * FROM cursos";
                                    $stmt = $this->pdo->query($sql);

                                    // Verifica se a consulta retornou resultados
                                    if ($stmt) {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $result[] = $row;
                                        }
                                    }

                                    return $result;
                                }
                            }

                            // Uso da classe
                            try {
                                // Conexão com o banco de dados usando PDO
                                $pdo = new PDO('mysql:host=localhost;dbname=sgc', 'root', '');
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                // Instanciando a classe Curso
                                $curso = new Curso($pdo);

                                // Obtendo a lista de cursos
                                $cursos = $curso->listarCursos();

                                // Exibindo os cursos como opções em um select
                                foreach ($cursos as $curso) {
                                    echo '<option value="' . $curso['id'] . '">' . $curso['id'] . " - " . $curso['nome'] . '</option>';
                                }
                            } catch (PDOException $e) {
                                echo 'Erro de conexão: ' . $e->getMessage();
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <label for="professor_id">Professor</label>

                        <select class="form-control" name="professor_id" id="professor_id" required>
                            <option value="">-- Selecione um professor --</option>
                            <?php
                            class Professor
                            {
                                private $pdo;

                                // Construtor que recebe a conexão PDO
                                public function __construct($pdo)
                                {
                                    $this->pdo = $pdo;
                                }

                                // Método para buscar todos os cursos
                                public function listarProfessores()
                                {
                                    $result = [];
                                    $sql = "SELECT * FROM professores";
                                    $stmt = $this->pdo->query($sql);

                                    // Verifica se a consulta retornou resultados
                                    if ($stmt) {
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $result[] = $row;
                                        }
                                    }

                                    return $result;
                                }
                            }

                            // Uso da classe
                            try {
                                // Conexão com o banco de dados usando PDO
                                $pdo = new PDO('mysql:host=localhost;dbname=sgc', 'root', '');
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                // Instanciando a classe Curso
                                $professor = new Professor($pdo);

                                // Obtendo a lista de cursos
                                $professores = $professor->listarProfessores();

                                // Exibindo os cursos como opções em um select
                                foreach ($professores as $professor) {
                                    echo '<option value="' . $professor['id'] . '">' . $professor['id'] . " - " . $professor['nome'] . '</option>';
                                }
                            } catch (PDOException $e) {
                                echo 'Erro de conexão: ' . $e->getMessage();
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