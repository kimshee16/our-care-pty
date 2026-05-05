<?php

// Setup storage directories for file uploads
$appDir = __DIR__ . '/../storage/app/public/applications';

if (!is_dir($appDir)) {
    if (mkdir($appDir, 0755, true)) {
        echo "✓ Created directory: $appDir\n";
    } else {
        echo "✗ Failed to create directory: $appDir\n";
    }
} else {
    echo "✓ Directory already exists: $appDir\n";
}

// Verify directory exists
if (is_dir($appDir)) {
    echo "✓ Directory verified\n";
    $files = glob($appDir . '/*');
    echo "  Files in directory: " . count($files) . "\n";
    if (count($files) > 0) {
        foreach ($files as $file) {
            echo "    - " . basename($file) . "\n";
        }
    }
} else {
    echo "✗ Directory does not exist\n";
}

// Check storage/app/public contents
$publicDir = __DIR__ . '/../storage/app/public';
echo "\nContents of storage/app/public:\n";
if (is_dir($publicDir)) {
    $items = scandir($publicDir);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..') {
            echo "  - $item\n";
        }
    }
} else {
    echo "✗ storage/app/public does not exist\n";
}
