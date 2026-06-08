<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1:6001;dbname=bookstoredatabase', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== New Italian and German Books ===\n\n";
    $result = $pdo->query('SELECT b.id, b.title, b.language, a.name as author, bc.name as category, p.name as publisher FROM books b LEFT JOIN authors a ON b.author_id = a.id LEFT JOIN book_categories bc ON b.category_id = bc.id LEFT JOIN publishers p ON b.publisher_id = p.id WHERE b.language IN ("German", "Italian") AND b.title NOT LIKE "%Ring%" ORDER BY b.language, b.title');
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . "\n";
        echo "Title: " . $row['title'] . "\n";
        echo "Language: " . $row['language'] . "\n";
        echo "Author: " . ($row['author'] ?? 'N/A') . "\n";
        echo "Category: " . ($row['category'] ?? 'N/A') . "\n";
        echo "Publisher: " . ($row['publisher'] ?? 'N/A') . "\n";
        echo "---\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
