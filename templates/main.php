<?php
use OCP\Util;

$appId = OCA\Picker\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-main');
?>

<script src="https://binaries.webex.com/static-content-pipeline/webex-embedded-app/v1/webex-embedded-app-sdk.js"></script>
<div id="picker"></div>
