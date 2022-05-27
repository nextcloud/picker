<?php

declare(strict_types=1);

/**
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\PublicPicker\Listener;

use OCA\Files_Sharing\Controller\ShareController;
use OCA\PublicPicker\AppInfo\Application;
use OCA\Files_Sharing\Event\ShareLinkAccessedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IConfig;
use OCP\Share\IManager;
use OCP\Util;
use Psr\Log\LoggerInterface;

class ShareLinkAccessedListener implements IEventListener {

	/** @var IConfig */
	private $config;

	/** @var IManager */
	private $manager;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(IConfig $config,
								IManager $manager,
								LoggerInterface $logger) {
		$this->config = $config;
		$this->manager = $manager;
		$this->logger = $logger;
	}

	public function handle(Event $event): void {
		if (!($event instanceof ShareLinkAccessedEvent)) {
			return;
		}

		// Let's only handle the access events
		if ($event->getStep() !== ShareController::SHARE_ACCESS) {
			return;
		}

		$share = $event->getShare();
		$label = $share->getLabel();

		if (preg_match('/^\[P\] /', $label) === 1) {
			Util::addStyle(Application::APP_ID, 'publicPickerShare');
		}
	}
}
