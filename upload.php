<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    // Check if file is an SVG
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($fileType != "svg") {
        echo "Sorry, only SVG files are allowed.";
        exit;
    }

    // Upload file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Load SVG content
        $svgContent = file_get_contents($target_file);

        // Change colors
        foreach ($_POST['colors'] as $originalColor => $newColor) {
            $svgContent = str_replace('fill="' . $originalColor . '"', 'fill="' . $newColor . '"', $svgContent);
        }

        // Save new SVG
        $newFileName = $target_dir . 'colored_' . basename($_FILES["file"]["name"]);
        file_put_contents($newFileName, $svgContent);

        // Provide download link
        echo "File uploaded and colors changed successfully. <a href='$newFileName'>Download here</a>";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
