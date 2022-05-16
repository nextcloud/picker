<?php
$appId = OCA\PublicPicker\AppInfo\Application::APP_ID;
\OCP\Util::addScript($appId, $appId . '-main');
\OCP\Util::addStyle($appId, 'main');
?>

<div id="public_picker"></div>
