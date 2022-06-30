<?php
use OCP\Util;
$appId = OCA\Picker\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-limit');
?>

<div id="limit"></div>
