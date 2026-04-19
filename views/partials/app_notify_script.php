<?php
/**
 * Loads app-notify.js once per request (toast UI + window.alert shim).
 * Include immediately after <head> on pages that do not use views/tourist/header.php
 * (that header already loads this script).
 */
if (!empty($GLOBALS['ceylon_app_notify_script'])) {
    return;
}
$GLOBALS['ceylon_app_notify_script'] = true;
$ceylon_notify_base = defined('BASE_URL') ? rtrim((string) BASE_URL, '/') : '/CeylonGo/public';
?>
<script src="<?php echo htmlspecialchars($ceylon_notify_base, ENT_QUOTES, 'UTF-8'); ?>/js/app-notify.js"></script>
