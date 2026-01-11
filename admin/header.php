<?php
// Verificar autenticação
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT u.* FROM users u WHERE u.id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin_user = $stmt->fetch();

// Contar mensagens não lidas
$unread_messages = $pdo->query("SELECT COUNT(*) as total FROM messages WHERE is_read = 0")->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CurrículosPro - Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #10b981;
            --secondary-color: #8b5cf6;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }

        .sidebar {
            background: var(--sidebar-bg);
            color: white;
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--primary-color);
            padding-left: 17px;
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 15px;
            text-align: center;
        }

        .badge-notification {
            margin-left: auto;
            background: #e74c3c;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .topbar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0;
            border: none;
            padding: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: #f5f7fa;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                min-height: auto;
                padding: 10px 0;
            }

            .sidebar-header {
                padding: 10px;
                margin-bottom: 10px;
            }

            .sidebar-header h3 {
                font-size: 1rem;
            }

            .sidebar-menu a {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            .topbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 15px;
            }

            .topbar-title {
                font-size: 1.2rem;
            }

            .card {
                margin-bottom: 15px;
            }

            .table {
                font-size: 0.85rem;
            }

            .btn {
                padding: 0.4rem 0.6rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-briefcase me-2"></i>CurrículosPro</h3>
            <small style="color: rgba(255,255,255,0.6);">Admin</small>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="index.php?route=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="index.php?route=resume" class="<?php echo $page == 'resume' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Meu Currículo</span>
                </a>
            </li>
            <li>
                <a href="index.php?route=messages" class="<?php echo $page == 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Mensagens</span>
                    <?php if ($unread_messages > 0): ?>
                    <span class="badge-notification"><?php echo $unread_messages; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="index.php?route=analytics" class="<?php echo $page == 'analytics' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>
            </li>
            <li style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                <a href="index.php?route=logout" style="color: #e74c3c;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title" id="page-title">Dashboard</div>
            <div class="topbar-user">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['admin_user'], 0, 1)); ?></div>
                <div>
                    <small style="color: #999;">Olá,</small><br>
                    <strong><?php echo $_SESSION['admin_user']; ?></strong>
                </div>
            </div>
        </div>
