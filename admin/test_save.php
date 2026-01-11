<?php
/**
 * Script de Teste de Salvamento Direto
 * Use este script para testar se o banco de dados está gravando dados corretamente
 */

require_once '../includes/config.php';

echo "<h1>🧪 Teste de Salvamento Direto no Banco</h1>";
echo "<hr>";

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    echo '<div class="alert alert-danger">❌ Você precisa estar logado para testar o salvamento!</div>';
    echo '<a href="login.php">Voltar ao Login</a>';
    exit;
}

echo '<p>Admin ID: <strong>' . $_SESSION['admin_id'] . '</strong></p>';

// Testar INSERT simples
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_save'])) {
    echo '<h2>Testando INSERT...</h2>';
    
    $title = 'Teste ' . date('Y-m-d H:i:s');
    $full_name = 'Rodrigo Marchi Gonella';
    $professional_title = 'Designer Gráfico';
    
    try {
        // Verificar estrutura da tabela
        $columns = $pdo->query("DESCRIBE resumes")->fetchAll();
        echo '<p><strong>Colunas da tabela resumes:</strong></p>';
        echo '<ul>';
        foreach ($columns as $col) {
            echo '<li>' . $col['Field'] . ' (' . $col['Type'] . ')</li>';
        }
        echo '</ul>';
        
        // Tentar INSERT
        $stmt = $pdo->prepare("
            INSERT INTO resumes (user_id, title, full_name, professional_title, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $_SESSION['admin_id'],
            $title,
            $full_name,
            $professional_title
        ]);
        
        if ($result) {
            $insert_id = $pdo->lastInsertId();
            echo '<div class="alert alert-success">✅ INSERT realizado com sucesso!</div>';
            echo '<p>ID do novo currículo: <strong>' . $insert_id . '</strong></p>';
            
            // Verificar se foi realmente gravado
            $check = $pdo->prepare("SELECT * FROM resumes WHERE id = ?")->execute([$insert_id]);
            $data = $pdo->prepare("SELECT * FROM resumes WHERE id = ?")->fetch();
            if ($data) {
                echo '<p style="color: green;">✅ Dados confirmados no banco!</p>';
                echo '<pre>';
                print_r($data);
                echo '</pre>';
            } else {
                echo '<p style="color: red;">❌ Dados não encontrados após INSERT!</p>';
            }
        } else {
            echo '<div class="alert alert-danger">❌ Erro ao executar INSERT!</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">❌ Erro: ' . $e->getMessage() . '</div>';
    }
} else {
    echo '<form method="POST">';
    echo '<button type="submit" name="test_save" value="1" class="btn btn-primary">Testar INSERT</button>';
    echo '</form>';
}

echo '<hr>';
echo '<a href="index.php?route=resume">Voltar aos Currículos</a>';
?>
