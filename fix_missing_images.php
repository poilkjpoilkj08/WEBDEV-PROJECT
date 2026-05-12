<?php

/**
 * Script to fix missing book cover images
 */

// Books that need front images
$books = [
    [
        'title' => 'Sapiens: A Brief History of Humankind',
        'isbn_front' => '0062316095',
        'filename' => 'sapiens-a-brief-history-of-humankind-front.jpg'
    ],
];

$frontDir = __DIR__ . '/public/product_image/front';

// Create directory if it doesn't exist
if (!is_dir($frontDir)) {
    mkdir($frontDir, 0755, true);
    echo "Created front directory\n";
}

// Download images
$downloaded = 0;
$failed = 0;

foreach ($books as $book) {
    $frontPath = "$frontDir/{$book['filename']}";
    
    // Check if file exists, if not download
    if (!file_exists($frontPath)) {
        echo "Downloading front cover: {$book['title']}... ";
        $frontUrl = "https://images-na.ssl-images-amazon.com/images/P/{$book['isbn_front']}.01.L.jpg";
        
        if (downloadImage($frontUrl, $frontPath)) {
            echo "✓\n";
            $downloaded++;
        } else {
            echo "✗ (trying alternate source)\n";
            // Try alternative URL format
            $altUrl = "https://images-na.ssl-images-amazon.com/images/P/{$book['isbn_front']}.01.jpg";
            if (downloadImage($altUrl, $frontPath)) {
                echo "  Downloaded from alternate source ✓\n";
                $downloaded++;
            } else {
                echo "  Failed\n";
                $failed++;
            }
        }
    } else {
        echo "Front cover already exists: {$book['title']}\n";
    }
}

echo "\n=== Download Summary ===\n";
echo "Successfully downloaded: $downloaded\n";
echo "Failed: $failed\n";

function downloadImage($url, $filepath)
{
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $imageData = @file_get_contents($url, false, $context);
    
    if ($imageData === false) {
        return false;
    }
    
    // Check if it's a valid image
    if (strlen($imageData) < 1000) {
        return false;
    }
    
    $bytes = file_put_contents($filepath, $imageData);
    return $bytes > 0;
}

echo "Done!\n";
