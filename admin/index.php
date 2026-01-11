<?php
require_once '../includes/config.php';

// Verificar autenticação
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$route = $_GET['route'] ?? 'dashboard';
$parts = explode('/', $route);
$page = $parts[0];
$action = $parts[1] ?? 'list';
$id = $parts[2] ?? null;

// Header do Admin
include 'header.php';

// Roteamento simples
switch ($page) {
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    case 'resume':
        include 'pages/resume.php';
        break;
    case 'messages':
        include 'pages/messages.php';
        break;
    case 'analytics':
        include 'pages/analytics.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: login.php');
        exit;
    default:
        include 'pages/dashboard.php';
        break;
}

// Footer do Admin
include 'footer.php';
?>
