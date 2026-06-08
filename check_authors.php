<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1:6001;dbname=bookstoredatabase', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Authors List ===\n\n";
    $result = $pdo->query('SELECT id, name FROM authors ORDER BY id');
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo 'ID ' . $row['id'] . ': ' . $row['name'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
