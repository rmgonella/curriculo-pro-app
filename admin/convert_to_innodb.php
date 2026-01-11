<?php
/**
 * Script de Conversão para InnoDB
 * Converte todas as tabelas do banco para ENGINE=InnoDB
 * Isso resolve problemas de persistência de dados
 */

require_once '../includes/config.php';

echo "<h1>🔧 Conversão de Tabelas para InnoDB</h1>";
echo "<hr>";

if (!isset($_SESSION['admin_id'])) {
    echo '<div class="alert alert-danger">❌ Você precisa estar logado!</div>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['convert'])) {
    echo '<h2>Iniciando conversão...</h2>';
    
    $tables = ['users', 'resumes', 'experiences', 'education', 'skills', 'projects', 'certifications', 'languages', 'messages', 'views'];
    
    try {
        foreach ($tables as $table) {
            echo '<p>Convertendo tabela: <strong>' . $table . '</strong>...</p>';
            
            // Primeiro, verificar o motor atual
            $check = $pdo->query("SELECT ENGINE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '" . $table . "' AND TABLE_SCHEMA = '" . DB_NAME . "'")->fetch();
            $current_engine = $check['ENGINE'] ?? 'Desconhecido';
            echo '<p style="margin-left: 20px;">Motor atual: <strong>' . $current_engine . '</strong></p>';
            
            // Converter para InnoDB
            $sql = "ALTER TABLE " . $table . " ENGINE=InnoDB";
            $pdo->exec($sql);
            
            echo '<p style="margin-left: 20px; color: green;">✅ Convertida para InnoDB</p>';
        }
        
        echo '<div class="alert alert-success mt-3">';
        echo '<strong>✅ Conversão Concluída com Sucesso!</strong><br>';
        echo 'Todas as tabelas foram convertidas para ENGINE=InnoDB.<br>';
        echo 'Agora o banco de dados suporta transações e a persistência de dados será garantida.';
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">❌ Erro durante conversão: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-warning">';
    echo '<strong>⚠️ Aviso Importante:</strong><br>';
    echo 'Este script converterá todas as tabelas do banco de dados para ENGINE=InnoDB.<br>';
    echo 'Isso é necessário para resolver problemas de persistência de dados.<br><br>';
    echo '<strong>O que vai acontecer:</strong>';
    echo '<ul>';
    echo '<li>Todas as tabelas serão convertidas para InnoDB</li>';
    echo '<li>Transações serão habilitadas</li>';
    echo '<li>Os dados existentes serão preservados</li>';
    echo '<li>O processo pode levar alguns segundos</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<form method="POST">';
    echo '<button type="submit" name="convert" value="1" class="btn btn-danger btn-lg">';
    echo '<i class="fas fa-cog me-2"></i>Converter para InnoDB Agora';
    echo '</button>';
    echo '</form>';
}

echo '<hr>';
echo '<a href="index.php?route=resume">Voltar aos Currículos</a>';
?>
