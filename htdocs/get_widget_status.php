<?php
$domain = isset($_POST['domain']) ? $_POST['domain'] : '';

if (!$domain) {
    echo "0"; // Default to disabled if no domain is selected
    exit;
}

$filePath = "domain_status/{$domain}_widget_status.txt";

if (file_exists($filePath)) {
    echo trim(file_get_contents($filePath));
} else {
    echo "0"; // Default to disabled if file does not exist
}
?>
