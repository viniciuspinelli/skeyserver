<?php
// Test database connection
$host = "bb6vh9uihq3g0epupmsy-mysql.services.clever-cloud.com";
$db = "bb6vh9uihq3g0epupmsy";
$user = "u8pwc4hfatq4rmvw";
$pass = "xANu3T1VvcFg3tDUzw56";
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "=== CONEXAO OK ===\n\n";
    
    // Show tables
    echo "=== TABELAS ===\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Check wp_player_skins table
    echo "\n=== ESTRUTURA wp_player_skins ===\n";
    try {
        $cols = $pdo->query("DESCRIBE wp_player_skins")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    } catch (Exception $e) {
        echo "Tabela nao existe: " . $e->getMessage() . "\n";
    }
    
    // Check for any skin data
    echo "\n=== DADOS EM wp_player_skins ===\n";
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM wp_player_skins")->fetchColumn();
        echo "Total de registros: $count\n";
        
        if ($count > 0) {
            $rows = $pdo->query("SELECT * FROM wp_player_skins LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            print_r($rows);
        }
    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
    
    // Check wp_player_knife
    echo "\n=== DADOS EM wp_player_knife ===\n";
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM wp_player_knife")->fetchColumn();
        echo "Total de registros: $count\n";
    } catch (Exception $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo "ERRO DE CONEXAO: " . $e->getMessage() . "\n";
}
