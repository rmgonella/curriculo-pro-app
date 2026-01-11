<?php
// Buscar todos os currículos do usuário
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['admin_id']]);
$resumes = $stmt->fetchAll();

// Estatísticas gerais
$total_views = 0;
$total_messages = 0;
$views_by_resume = [];

foreach ($resumes as $resume) {
    $views = $pdo->query("SELECT COUNT(*) as total FROM views WHERE resume_id = " . $resume['id'])->fetch()['total'];
    $messages = $pdo->query("SELECT COUNT(*) as total FROM messages WHERE resume_id = " . $resume['id'])->fetch()['total'];
    
    $total_views += $views;
    $total_messages += $messages;
    
    $views_by_resume[] = [
        'title' => $resume['title'],
        'views' => $views,
        'messages' => $messages
    ];
}

// Últimas visualizações
$stmt = $pdo->prepare("
    SELECT v.*, r.title as resume_title 
    FROM views v 
    LEFT JOIN resumes r ON v.resume_id = r.id 
    WHERE r.user_id = ? 
    ORDER BY v.viewed_at DESC 
    LIMIT 20
");
$stmt->execute([$_SESSION['admin_id']]);
$recent_views = $stmt->fetchAll();

// Visualizações por dia (últimos 7 dias)
$stmt = $pdo->prepare("
    SELECT DATE(v.viewed_at) as date, COUNT(*) as count
    FROM views v
    LEFT JOIN resumes r ON v.resume_id = r.id
    WHERE r.user_id = ? AND v.viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(v.viewed_at)
    ORDER BY DATE(v.viewed_at)
");
$stmt->execute([$_SESSION['admin_id']]);
$views_by_date = $stmt->fetchAll();
?>

<script>updatePageTitle('Analytics');</script>

<!-- Estatísticas Principais -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-eye" style="font-size: 2.5rem; color: #667eea;"></i>
                <h2 class="mt-3 mb-0"><?php echo $total_views; ?></h2>
                <small class="text-muted">Visualizações Totais</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-envelope" style="font-size: 2.5rem; color: #764ba2;"></i>
                <h2 class="mt-3 mb-0"><?php echo $total_messages; ?></h2>
                <small class="text-muted">Mensagens Recebidas</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-file-alt" style="font-size: 2.5rem; color: #27ae60;"></i>
                <h2 class="mt-3 mb-0"><?php echo count($resumes); ?></h2>
                <small class="text-muted">Currículos Criados</small>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Visualizações -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Visualizações nos Últimos 7 Dias</h5>
            </div>
            <div class="card-body">
                <canvas id="viewsChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Visualizações por Currículo -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Visualizações por Currículo</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Currículo</th>
                            <th>Visualizações</th>
                            <th>Mensagens</th>
                            <th>Taxa de Engajamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($views_by_resume as $item): ?>
                        <tr>
                            <td><?php echo $item['title']; ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo $item['views']; ?></span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $item['messages']; ?></span>
                            </td>
                            <td>
                                <?php 
                                $rate = $item['views'] > 0 ? round(($item['messages'] / $item['views']) * 100, 2) : 0;
                                ?>
                                <span class="badge bg-<?php echo $rate > 10 ? 'success' : ($rate > 5 ? 'warning' : 'secondary'); ?>">
                                    <?php echo $rate; ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Últimas Visualizações -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Últimas Visualizações</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Currículo</th>
                            <th>IP</th>
                            <th>User Agent</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_views)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Nenhuma visualização ainda</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recent_views as $view): ?>
                            <tr>
                                <td><?php echo $view['resume_title']; ?></td>
                                <td><code><?php echo $view['ip_address'] ?: 'N/A'; ?></code></td>
                                <td><small><?php echo substr($view['user_agent'], 0, 50); ?>...</small></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($view['viewed_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico de visualizações
const ctx = document.getElementById('viewsChart').getContext('2d');
const viewsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            $dates = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('d/m', strtotime("-$i days"));
                $dates[] = "'" . $date . "'";
            }
            echo implode(',', $dates);
            ?>
        ],
        datasets: [{
            label: 'Visualizações',
            data: [
                <?php 
                $data = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $count = 0;
                    foreach ($views_by_date as $view) {
                        if ($view['date'] == $date) {
                            $count = $view['count'];
                            break;
                        }
                    }
                    $data[] = $count;
                }
                echo implode(',', $data);
                ?>
            ],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
