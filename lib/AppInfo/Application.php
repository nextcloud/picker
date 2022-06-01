<?php
/**
 * Nextcloud - Picker
 *
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2022
 */

namespace OCA\Picker\AppInfo;

use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent;
use OCA\Picker\Listener\CSPListener;
use OCA\Picker\Listener\ShareLinkAccessedListener;
//use OCA\Files_Sharing\Event\ShareLinkAccessedEvent;
use OCA\Picker\Listener\PublicShareTemplateLoader;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\Security\CSP\AddContentSecurityPolicyEvent;

/**
 * Class Application
 *
 * @package OCA\Picker\AppInfo
 */
class Application extends App implements IBootstrap {
	public const APP_ID = 'picker';

	/**
	 * Constructor
	 *
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(AddContentSecurityPolicyEvent::class, CSPListener::class);
		// both ShareLinkAccessedEvent and BeforeTemplateRenderedEvent events can be listened to load our CSS
		// $context->registerEventListener(ShareLinkAccessedEvent::class, ShareLinkAccessedListener::class);
		$context->registerEventListener(BeforeTemplateRenderedEvent::class, PublicShareTemplateLoader::class);
	}

	public function boot(IBootContext $context): void {
	}
}

