<?php

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Picker\AppInfo;

use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent;
use OCA\Picker\Listener\CSPListener;
use OCA\Picker\Listener\PublicShareTemplateLoader;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Security\CSP\AddContentSecurityPolicyEvent;

/**
 * Class Application
 *
 * @package OCA\Picker\AppInfo
 */
class Application extends App implements IBootstrap {
	public const APP_ID = 'picker';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(AddContentSecurityPolicyEvent::class, CSPListener::class);
		$context->registerEventListener(BeforeTemplateRenderedEvent::class, PublicShareTemplateLoader::class);
	}

	public function boot(IBootContext $context): void {
	}
}
