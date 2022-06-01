<?php
use OCP\Util;

$appId = OCA\Picker\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-main');
Util::addStyle($appId, 'main');
?>

<div id="picker"></div>
