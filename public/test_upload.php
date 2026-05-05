<?php

// Simple PHP file upload test script
// Run this to test if file uploads work to storage/app/public/applications/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {
    $uploadDir = __DIR__ . '/../storage/app/public/applications/';

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['cv'];
    $fileName = basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo "File uploaded successfully: " . $fileName;
        echo "<br>Path: " . $targetPath;
    } else {
        echo "Upload failed!";
    }
} else {
    echo '<form method="POST" enctype="multipart/form-data">
        <input type="file" name="cv" accept=".pdf,.doc,.docx">
        <button type="submit">Upload CV</button>
    </form>';
}
