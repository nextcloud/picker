<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Picker\Listener;

use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent;
use OCA\Picker\AppInfo\Application;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\FileInfo;
use OCP\Util;

/**
 * Helper class to extend the "publicshare" template from the server.
 *
 * The additional scripts modify the page in the browser to inject the Talk UI as needed.
 */
class PublicShareTemplateLoader implements IEventListener {
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
			Util::addStyle(Application::APP_ID, 'pickerShare');
			Util::addScript(Application::APP_ID, Application::APP_ID . '-pickerShare');
		}
	}
}
