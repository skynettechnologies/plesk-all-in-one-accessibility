<?php


// Security check: Only allow access to Plesk administrators
if (!pm_Session::getClient()->isAdmin()) {
    http_response_code(403);
   
    exit;
}


if (pm_Session::isImpersonated()) {
    $clientId = pm_Session::getImpersonatedClientId();
    // Log or use $clientId if needed
}

// Retrieve widget status and domain id 
$enableWidget = isset($_POST['enable_widget']) ? $_POST['enable_widget'] : 0;
$domainId = isset($_POST['domain_id']) ? $_POST['domain_id'] : null;

// Check if a domain ID is provided
if (!$domainId) {
   
    exit;
}

// Define the widget's script to be added or removed
$customScript = '<script id="aioa-adawidget" src="https://www.skynettechnologies.com/accessibility/js/all-in-one-accessibility-js-widget-minify.js?colorcode=#485f8e&token=&position=middle_right" defer></script>';

// Handle file modification (add or remove the script) based on widget state
$fileManager = new \pm_FileManager($domainId);
$domain = \pm_Domain::getByDomainId($domainId); // Get domain object
$documentRoot = $domain->getDocumentRoot();

$files = $fileManager->scanDir($documentRoot, true); // Recursively scan the directory

// Filter files for .php and .html only
$targetFiles = array_filter($files, function ($file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($ext, ['php', 'html']);
});

foreach ($targetFiles as $filePath) {
    // Resolve absolute path for the file
    $absoluteFilePath = realpath($documentRoot . DIRECTORY_SEPARATOR . $filePath);

    if ($fileManager->fileExists($absoluteFilePath)) {
        $fileContent = $fileManager->fileGetContents($absoluteFilePath);

        if ($enableWidget == 1) {
            // Enable Widget: Add the script if not already present
            if (strpos($fileContent, '<script id="aioa-adawidget"') === false) {
                if (str_ends_with($filePath, '.php')) {
                    $injected = "\necho '$customScript';";
                    $fileContent = strpos($fileContent, '?>') !== false
                        ? str_replace('?>', "$injected\n?>", $fileContent)
                        : $fileContent . $injected;
                } else {
                    $fileContent .= "\n" . $customScript;
                }

                $fileManager->filePutContents($absoluteFilePath, $fileContent);
              
            }
        } else {
            // Disable Widget: Remove the script if it exists
            $updatedContent = preg_replace(
                '/<script\s+id=[\'"]aioa-adawidget[\'"][^>]*><\/script>/i',
                '',
                $fileContent
            );
            if ($updatedContent !== $fileContent) {
                $fileManager->filePutContents($absoluteFilePath, $updatedContent);
               
            }
        }
    } else {
       
    }
}

?>
