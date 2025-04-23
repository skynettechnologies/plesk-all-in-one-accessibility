<?php
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

// Fetch widget settings from the API
$apiUrl = 'https://ada.skynettechnologies.us/api/widget-settings?website_url=concretecmsaccessibility.skynettechnologies.us';
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
));

$response = curl_exec($curl);
curl_close($curl);

$widgetSettings = json_decode($response, true);

// Check if the API response contains the 'platform_widget_status' field
$isCustomPosition = isset($widgetSettings['platform_widget_status']) ? $widgetSettings['platform_widget_status'] : false;

if ($isCustomPosition) {
   
} else {
    
}

// Handle file modification (add or remove the script) based on widget state
$fileManager = new \pm_FileManager($domainId);
$frontPageFiles = ['index.php', 'index.html', 'home.php']; // Add or remove script from these files

foreach ($frontPageFiles as $frontPageFile) {
    $filePath = $fileManager->getFilePath("httpdocs/$frontPageFile");

    if ($fileManager->fileExists($filePath)) {
        $fileContent = $fileManager->fileGetContents($filePath);

        if ($enableWidget == 1) {
            // Add the script if it doesn't already exist
            if (strpos($fileContent, '<script id="aioa-adawidget"') === false) {
                if ($frontPageFile == 'index.php' || $frontPageFile == 'home.php') {
                    $fileContent .= "\necho '$customScript';"; // For PHP files, we use echo
                } elseif ($frontPageFile == 'index.html') {
                    $fileContent .= "\n" . $customScript; // For HTML files, we append the script directly
                }
                $fileManager->filePutContents($filePath, $fileContent);
                echo "Script added successfully!";
            }
             else {
               
            }
        } else {
            // Remove the script if widget is disabled
            $updatedContent = preg_replace(
                '/<script\s+id=[\'"]aioa-adawidget[\'"][^>]*><\/script>/i',
                '',
                $fileContent
            );
            
            if ($updatedContent !== $fileContent) {
                $fileManager->filePutContents($filePath, $updatedContent);
                echo "Script removed successfully!";
            } else {
                echo "No script found to remove.";
            }
        }
    }
}
?>
