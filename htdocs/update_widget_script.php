<?php
include 'setting_comman.php';

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
addUpdateScript($domainId,'1',$enableWidget);
?>
