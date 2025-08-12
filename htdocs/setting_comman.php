<?php
function addUpdateScript($domainId,$type=0,$enableWidget=0){
    // Define the widget's script to be added or removed
    $customScript = '<script id="aioa-adawidget" src="https://www.skynettechnologies.com/accessibility/js/all-in-one-accessibility-js-widget-minify.js?colorcode=#485f8e&token=&position=middle_right" defer></script>';

    $fileManager = new \pm_FileManager($domainId);
    $domain = \pm_Domain::getByDomainId($domainId); // Get domain object
    $documentRoot = $domain->getDocumentRoot();

    $files = $fileManager->scanDir($documentRoot, true); // Recursively scan the directory

    // Filter files for .php and .html only
    /*$targetFiles = array_filter($files, function ($file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($ext, ['php', 'html']);
    });*/
    // Filter files for .php and .html only
    $targetFiles = array_filter($files, function ($file) use ($documentRoot) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        // Ensure the file path is absolute
        $absolutePath = realpath($documentRoot . DIRECTORY_SEPARATOR . $file);
        return in_array($ext, ['php', 'html']) && $absolutePath !== false;
    });
    $scriptAdded = false;
    foreach ($targetFiles as $filePath) {
        // Resolve absolute path for the file
        $absoluteFilePath = realpath($documentRoot . DIRECTORY_SEPARATOR . $filePath);

        if ($fileManager->fileExists($absoluteFilePath)) {
            $fileContent = $fileManager->fileGetContents($absoluteFilePath);

            if($type==0) {
                // Inject only if script is not already present
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

                    $updatedContent = $fileManager->fileGetContents($absoluteFilePath);
                    if (strpos($updatedContent, '<script id="aioa-adawidget"') !== false) {

                        $scriptAdded = true;
                    } else {

                    }
                } else {

                }
            }
            else {
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
            }
        } else {

        }
    }

    if($type==0) {
        // If no file was modified and index.php does not exist, create it
        $defaultFilePath = realpath($documentRoot . "/index.php"); // Ensure absolute path for default index.php
        if (!$scriptAdded && !$fileManager->fileExists($defaultFilePath) && $fileManager->isDir($documentRoot)) {
            try {
                $defaultContent = "<?php echo ''; ?>\n" . $customScript;
                $fileManager->filePutContents($defaultFilePath, $defaultContent);
            } catch (\pm_Exception $e) {
                //echo "Error: Could not retrieve client information. " . $e->getMessage();
            }


        }
    }

}
?>
