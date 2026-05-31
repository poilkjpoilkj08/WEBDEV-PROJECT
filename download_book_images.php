<?php

/**
 * Script to download book cover images from Amazon
 * Place this script in the project root and run: php download_book_images.php
 */

// Book ISBNs and their details
$books = [
    [
        'title' => 'Harry Potter and the Philosopher\'s Stone',
        'isbn_front' => '0747532699',
        'isbn_back' => '0747532699',
    ],
    [
        'title' => 'The Shining',
        'isbn_front' => '0385333838',
        'isbn_back' => '0385333838',
    ],
    [
        'title' => 'The Great Gatsby',
        'isbn_front' => '0743273567',
        'isbn_back' => '0743273567',
    ],
    [
        'title' => 'Pride and Prejudice',
        'isbn_front' => '0141439513',
        'isbn_back' => '0141439513',
    ],
    [
        'title' => 'Murder on the Orient Express',
        'isbn_front' => '0062693735',
        'isbn_back' => '0062693735',
    ],
    [
        'title' => 'Kafka on the Shore',
        'isbn_front' => '1400079276',
        'isbn_back' => '1400079276',
    ],
    [
        'title' => 'Dune',
        'isbn_front' => '0441172717',
        'isbn_back' => '0441172717',
    ],
    [
        'title' => 'Foundation',
        'isbn_front' => '0553293354',
        'isbn_back' => '0553293354',
    ],
    [
        'title' => 'Educated',
        'isbn_front' => '0399590504',
        'isbn_back' => '0399590504',
    ],
    [
        'title' => 'Becoming',
        'isbn_front' => '1524763136',
        'isbn_back' => '1524763136',
    ],
    [
        'title' => 'The Handmaid\'s Tale',
        'isbn_front' => '0385490232',
        'isbn_back' => '0385490232',
    ],
    [
        'title' => 'A Game of Thrones',
        'isbn_front' => '0553103547',
        'isbn_back' => '0553103547',
    ],
    [
        'title' => 'Sapiens: A Brief History of Humankind',
        'isbn_front' => '0062316095',
        'isbn_back' => '0062316095',
    ],
    [
        'title' => 'Atomic Habits',
        'isbn_front' => '0735211299',
        'isbn_back' => '0735211299',
    ],
    [
        'title' => 'The Psychology of Money',
        'isbn_front' => '0857197258',
        'isbn_back' => '0857197258',
    ],
    [
        'title' => 'The Midnight Library',
        'isbn_front' => '0020468629',
        'isbn_back' => '0020468629',
    ],
];

// Create directories if they don't exist
$frontDir = __DIR__ . '/public/product_image/front';
$backDir = __DIR__ . '/public/product_image/back';

if (!is_dir($frontDir)) {
    mkdir($frontDir, 0755, true);
    echo "Created front directory\n";
}

if (!is_dir($backDir)) {
    mkdir($backDir, 0755, true);
    echo "Created back directory\n";
}

// Download images
$downloaded = 0;
$failed = 0;

foreach ($books as $book) {
    $title = strtolower(str_replace(['\'', ' '], ['-', '-'], $book['title']));
    $title = preg_replace('/-+/', '-', $title);
    
    // Front cover
    $frontUrl = "https://images-na.ssl-images-amazon.com/images/P/{$book['isbn_front']}.01.L.jpg";
    $frontPath = "$frontDir/$title-front.jpg";
    
    if (!file_exists($frontPath)) {
        echo "Downloading front cover: {$book['title']}... ";
        if (downloadImage($frontUrl, $frontPath)) {
            echo "✓\n";
            $downloaded++;
        } else {
            echo "✗\n";
            $failed++;
        }
    } else {
        echo "Front cover already exists: {$book['title']}\n";
    }
    
    // Back cover
    $backUrl = "https://images-na.ssl-images-amazon.com/images/P/{$book['isbn_back']}.02.L.jpg";
    $backPath = "$backDir/$title-back.jpg";
    
    if (!file_exists($backPath)) {
        echo "Downloading back cover: {$book['title']}... ";
        if (downloadImage($backUrl, $backPath)) {
            echo "✓\n";
            $downloaded++;
        } else {
            echo "✗\n";
            $failed++;
        }
    } else {
        echo "Back cover already exists: {$book['title']}\n";
    }
}

echo "\n=== Download Summary ===\n";
echo "Successfully downloaded: $downloaded\n";
echo "Failed: $failed\n";

/**
 * Download an image from a URL
 */
function downloadImage($url, $savePath) {
    try {
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return false;
        }
        
        if (file_put_contents($savePath, $imageData) === false) {
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}
