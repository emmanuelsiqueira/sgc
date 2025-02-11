<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';
include 'header.php';

// Aniversariantes do Mês
$mes_atual = date('m');
$aniversariantes = $pdo->query("SELECT aluno_nome, aluno_data_nascimento FROM alunos WHERE MONTH(aluno_data_nascimento) = $mes_atual")->fetchAll(PDO::FETCH_ASSOC);

// Buscar dados para os gráficos
$alunos = $pdo->query("SELECT COUNT(*) as total FROM alunos")->fetch(PDO::FETCH_ASSOC);
$cursos = $pdo->query("SELECT COUNT(*) as total FROM cursos")->fetch(PDO::FETCH_ASSOC);
$professores = $pdo->query("SELECT COUNT(*) as total FROM professores")->fetch(PDO::FETCH_ASSOC);
$matriculas = $pdo->query("SELECT COUNT(*) as total FROM matriculas")->fetch(PDO::FETCH_ASSOC);
?>

<h2>Dashboard</h2>

<div class="row">
    <div class="col-md-6">
        <h4>Aniversariantes do Mês</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de Nascimento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($aniversariantes as $aniversariante): ?>
                <tr>
                    <td><?= $aniversariante['aluno_nome'] ?></td>
                    <td><?= date('d/m/Y', strtotime($aniversariante['aluno_data_nascimento'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <h4>Gráficos</h4>
        <canvas id="myChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Alunos', 'Cursos', 'Professores', 'Matrículas'],
            datasets: [{
                label: 'Quantidade',
                data: [
                    <?= $alunos['total'] ?>,
                    <?= $cursos['total'] ?>,
                    <?= $professores['total'] ?>,
                    <?= $matriculas['total'] ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include 'footer.php'; ?>