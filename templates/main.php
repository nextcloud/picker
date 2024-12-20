<?php

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

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
