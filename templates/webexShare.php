<?php
use OCP\Util;

$appId = OCA\Picker\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-webexShare');
?>

<div id="webex_share"></div>
