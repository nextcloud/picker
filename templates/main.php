<?php
use OCP\Util;
$appId = OCA\Picker\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-main');
?>

<?php
if ($_['additionalScriptUrl']) {
	echo '<script src="' . $_['additionalScriptUrl'] . '"></script>';
}
?>
<div id="picker"></div>
