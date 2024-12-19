<?php

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

return [
	'routes' => [
		['name' => 'page#singleLinkPage', 'url' => '/single-link', 'verb' => 'GET'],
		['name' => 'page#webexSingleLinkPage', 'url' => '/webex-single-link', 'verb' => 'GET'],
		['name' => 'page#webexSharePage', 'url' => '/webex-share/{token}', 'verb' => 'GET'],
		['name' => 'page#getFileImage', 'url' => '/preview', 'verb' => 'GET'],
		['name' => 'page#canShareFile', 'url' => '/can-share', 'verb' => 'GET'],
	]
];
