<?php
/**
 * Nextcloud - Picker
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2022
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
