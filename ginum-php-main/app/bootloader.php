<?php
// This is a bootloader program to initialize things in the application

// Define the upload folder path
$prefix_img_folders = "./assets/imgs/uploads";
$prefix_docs_folders = "./assets/docs/uploads";

// Define the subfolders
$img_folders = [
    'bill_img',
    'business_img',
    'customer_img',
    'employee_img',
    'supplier_img',
];

$docs_folders = [
    'business_report',
    'supplier_report',
];

// Check if the image uploads folder exists
if (!file_exists($prefix_img_folders)) {
    mkdir($prefix_img_folders, 0777, true); // Use true for recursive creation
}

// Create each subfolder if it doesn't exist
foreach ($img_folders as $folder) {
    $folderPath = "$prefix_img_folders/$folder";
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }
}

// Check if the docs uploads folder exists
if (!file_exists($prefix_docs_folders)) {
    mkdir($prefix_docs_folders, 0777, true); // Use true for recursive creation
}

// Create each subfolder if it doesn't exist
foreach ($docs_folders as $folder) {
    $folderPath = "$prefix_docs_folders/$folder";
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }
}
?>