<?php


// Security check: Only allow access to Plesk administrators
if (!pm_Session::getClient()->isAdmin()) {
    http_response_code(403);
    echo 'Permission denied.';
    exit;
}


if (pm_Session::isImpersonated()) {
    $clientId = pm_Session::getImpersonatedClientId();
    // Log or use $clientId if needed
}

// Retrieve POST data sent from JavaScript
$enableWidget = isset($_POST['enable_widget']) ? $_POST['enable_widget'] : 0;
$domainId = isset($_POST['domain_id']) ? $_POST['domain_id'] : null;

// Check if a domain ID is provided
if (!$domainId) {
    echo "Invalid domain selection.";
    exit;
}

// Define the widget's script to be added or removed
$customScript = '<script id="aioa-adawidget" src="https://www.skynettechnologies.com/accessibility/js/all-in-one-accessibility-js-widget-minify.js?colorcode=#485f8e&token=&position=middle_right" defer></script>';

// Handle file modification (add or remove the script) based on widget state
$fileManager = new \pm_FileManager($domainId);
$domain = \pm_Domain::getByDomainId($domainId); // Correct way to get the domain
$documentRoot = $domain->getDocumentRoot();  

// Scan all .php and .html files
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($documentRoot));
$targetFiles = [];

foreach ($rii as $file) {
    if ($file->isFile()) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['php', 'html'])) {
            $targetFiles[] = $file->getPathname();
        }
    }
}


foreach ($targetFiles as $filePath) {
    if ($fileManager->fileExists($filePath)) {
        $fileContent = $fileManager->fileGetContents($filePath);

        if ($enableWidget == 1) {
            if (strpos($fileContent, '<script id="aioa-adawidget"') === false) {
                if (str_ends_with($filePath, '.php')) {
                    $injected = "\necho '$customScript';";
                    $fileContent = strpos($fileContent, '?>') !== false
                        ? str_replace('?>', "$injected\n?>", $fileContent)
                        : $fileContent . $injected;
                } else {
                    $fileContent .= "\n" . $customScript;
                }

                $fileManager->filePutContents($filePath, $fileContent);
                echo "Script added to $filePath<br>";
            }
        } else {
            $updatedContent = preg_replace(
                '/<script\s+id=[\'"]aioa-adawidget[\'"][^>]*><\/script>/i',
                '',
                $fileContent
            );
            if ($updatedContent !== $fileContent) {
                $fileManager->filePutContents($filePath, $updatedContent);
                echo "Script removed from $filePath<br>";
            }
        }
    }
}
?>
