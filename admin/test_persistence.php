<?php
/**
 * Teste Ultra-Persistente de Gravação
 * Testa INSERT com verificações múltiplas e forçamento de commit
 */

require_once '../includes/config.php';

echo "<h1>🔧 Teste Ultra-Persistente de Gravação</h1>";
echo "<hr>";

if (!isset($_SESSION['admin_id'])) {
    echo '<div class="alert alert-danger">❌ Você precisa estar logado!</div>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_persistence'])) {
    echo '<h2>Iniciando teste de persistência...</h2>';
    
    $title = 'Teste Persistência ' . date('Y-m-d H:i:s');
    $full_name = 'Rodrigo Marchi Gonella';
    $professional_title = 'Teste ' . rand(1000, 9999);
    
    try {
        echo '<p>1️⃣ <strong>Desabilitando chaves estrangeiras...</strong></p>';
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        echo '<p style="color: green;">✅ Chaves estrangeiras desabilitadas</p>';
        
        echo '<p>2️⃣ <strong>Executando INSERT...</strong></p>';
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
            echo '<p style="color: green;">✅ INSERT executado. ID: ' . $insert_id . '</p>';
            
            echo '<p>3️⃣ <strong>Forçando COMMIT...</strong></p>';
            $pdo->commit();
            $pdo->exec("COMMIT");
            echo '<p style="color: green;">✅ COMMIT forçado</p>';
            
            echo '<p>4️⃣ <strong>Verificação 1: Leitura imediata...</strong></p>';
            $check1 = $pdo->prepare("SELECT * FROM resumes WHERE id = ?")->fetch(PDO::FETCH_ASSOC);
            $pdo->prepare("SELECT * FROM resumes WHERE id = ?")->execute([$insert_id]);
            $check1 = $pdo->prepare("SELECT * FROM resumes WHERE id = ?")->fetch();
            
            if ($check1) {
                echo '<p style="color: green;">✅ Verificação 1: Dados encontrados!</p>';
                echo '<pre>' . print_r($check1, true) . '</pre>';
            } else {
                echo '<p style="color: red;">❌ Verificação 1: Dados NÃO encontrados!</p>';
            }
            
            echo '<p>5️⃣ <strong>Aguardando 2 segundos...</strong></p>';
            sleep(2);
            
            echo '<p>6️⃣ <strong>Verificação 2: Após espera...</strong></p>';
            $check2 = $pdo->prepare("SELECT * FROM resumes WHERE id = ?")->fetch();
            if ($check2) {
                echo '<p style="color: green;">✅ Verificação 2: Dados encontrados!</p>';
            } else {
                echo '<p style="color: red;">❌ Verificação 2: Dados NÃO encontrados!</p>';
            }
            
            echo '<p>7️⃣ <strong>Reabilitando chaves estrangeiras...</strong></p>';
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
            echo '<p style="color: green;">✅ Chaves estrangeiras reabilitadas</p>';
            
            echo '<div class="alert alert-info mt-3">';
            echo '<strong>Resultado Final:</strong><br>';
            if ($check1 && $check2) {
                echo '✅ <strong style="color: green;">PERSISTÊNCIA CONFIRMADA!</strong> Os dados foram gravados com sucesso.';
            } else {
                echo '❌ <strong style="color: red;">FALHA DE PERSISTÊNCIA!</strong> Os dados não estão sendo salvos.';
            }
            echo '</div>';
            
        } else {
            echo '<div class="alert alert-danger">❌ Erro ao executar INSERT!</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">❌ Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }
} else {
    echo '<form method="POST">';
    echo '<p>Este teste tentará gravar dados e verificar a persistência múltiplas vezes.</p>';
    echo '<button type="submit" name="test_persistence" value="1" class="btn btn-primary btn-lg">Executar Teste de Persistência</button>';
    echo '</form>';
}

echo '<hr>';
echo '<a href="index.php?route=resume">Voltar aos Currículos</a>';
?>
