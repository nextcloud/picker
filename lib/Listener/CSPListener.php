<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Picker\Listener;

use OCA\Picker\AppInfo\Application;
use OCP\AppFramework\Http\EmptyContentSecurityPolicy;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IConfig;
use OCP\IRequest;
use OCP\Security\CSP\AddContentSecurityPolicyEvent;

class CSPListener implements IEventListener {
	protected IRequest $request;
	protected IConfig $config;

	public function __construct(IRequest $request,
		IConfig $config
	) {
		$this->request = $request;
		$this->config = $config;
	}

	public function handle(Event $event): void {
		if (!$event instanceof AddContentSecurityPolicyEvent) {
			return;
		}

		if (!$this->isPageLoad()) {
			return;
		}

		$rawConfigUrls = $this->config->getAppValue(Application::APP_ID, 'allowed_frame_ancestor_domains', '');
		$urls = explode(',', $rawConfigUrls);

		$urls = array_filter($urls, function ($url) {
			return !empty($url);
		});

		$policy = new EmptyContentSecurityPolicy();
		foreach ($urls as $url) {
			$policy->addAllowedFrameDomain($url);
			$policy->addAllowedFormActionDomain($url);
			$policy->addAllowedFrameAncestorDomain($url);
			$policy->addAllowedImageDomain($url);
		}
		$policy->addAllowedScriptDomain('https://binaries.webex.com');

		$event->addPolicy($policy);
	}

	private function isPageLoad(): bool {
		$scriptNameParts = explode('/', $this->request->getScriptName());
		return end($scriptNameParts) === 'index.php';
	}
}
