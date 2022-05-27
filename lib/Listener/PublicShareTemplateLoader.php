<?php

declare(strict_types=1);

/**
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2022
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\PublicPicker\Listener;

use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent;
use OCA\PublicPicker\AppInfo\Application;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\FileInfo;
use OCP\IConfig;
use OCP\Util;

/**
 * Helper class to extend the "publicshare" template from the server.
 *
 * The additional scripts modify the page in the browser to inject the Talk UI as needed.
 */
class PublicShareTemplateLoader implements IEventListener {

	public function __construct(IConfig $serverConfig) {
		$this->serverConfig = $serverConfig;
	}

	/**
	 * @param Event $event
	 */
	public function handle(Event $event): void {
		if (!$event instanceof BeforeTemplateRenderedEvent) {
			return;
		}

		if ($event->getScope() !== null) {
			// If the event has a scope, it's not the default share page, but e.g. authentication
			return;
		}

		$share = $event->getShare();
		if ($share->getNodeType() !== FileInfo::TYPE_FILE) {
			return;
		}

		$label = $share->getLabel();

		if (preg_match('/^\[P\] /', $label) === 1) {
			Util::addStyle(Application::APP_ID, 'publicPickerShare');
		}
	}
}
