<?php
/**
 * Nextcloud - GitLab
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2020
 */

return [
	'routes' => [
		['name' => 'page#singleLinkPage', 'url' => '/single-link', 'verb' => 'GET'],
		['name' => 'page#getFileImage', 'url' => '/preview', 'verb' => 'GET'],
	]
];
