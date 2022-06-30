<?php
/**
 * Nextcloud - Picker
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2022
 */

namespace OCA\Picker\Controller;

use Exception;
use OCP\App\IAppManager;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Services\IInitialState;
use OCP\Constants;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IServerContainer;
use OCP\IURLGenerator;
use Psr\Log\LoggerInterface;
use Throwable;
use OCA\Picker\Service\ImageService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

use OCA\Picker\AppInfo\Application;

class PageController extends Controller {

	/**
	 * @var string|null
	 */
	private $userId;
	/**
	 * @var ImageService
	 */
	private $imageService;
	/**
	 * @var LoggerInterface
	 */
	private $logger;
	private IAppManager $appManager;
	private IServerContainer $serverContainer;
	private IL10N $l10n;
	private IInitialState $initialStateService;
	private IRootFolder $root;
	private IConfig $config;
	private IURLGenerator $urlGenerator;

	public function __construct(string   $appName,
								IRequest $request,
								ImageService $imageService,
								LoggerInterface $logger,
								IAppManager $appManager,
								IServerContainer $serverContainer,
								IRootFolder $root,
								IInitialState $initialStateService,
								IL10N $l10n,
								IConfig $config,
								IURLGenerator $urlGenerator,
								?string  $userId) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->imageService = $imageService;
		$this->logger = $logger;
		$this->appManager = $appManager;
		$this->serverContainer = $serverContainer;
		$this->l10n = $l10n;
		$this->initialStateService = $initialStateService;
		$this->root = $root;
		$this->config = $config;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function webexSingleLinkPage(): TemplateResponse {
		$webexSdkUrl = 'https://binaries.webex.com/static-content-pipeline/webex-embedded-app/v1/webex-embedded-app-sdk.js';
		return $this->singleLinkPage($webexSdkUrl);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function singleLinkPage(?string $additionalScriptUrl = null): TemplateResponse {
		$supportEnabled = $this->appManager->isEnabledForUser('support', $this->userId);
		if ($supportEnabled) {
			/** @var \OCA\Support\Service\SubscriptionService $subscriptionService */
			$subscriptionService = $this->serverContainer->get(\OCA\Support\Service\SubscriptionService::class);
			$activeUserCount = $subscriptionService->getActiveUserCount();
			[
				$instanceSize,
				$hasSubscription,
				$isInvalidSubscription,
				$isOverLimit,
				$subscriptionInfo
			] = $subscriptionService->getSubscriptionInfo();
			if ($activeUserCount > 500 && (!$hasSubscription || $isInvalidSubscription)) {
				// forbidden if support app is active AND nbUsers > 500 AND no subscription
				$limitInitialState = [
					'message' => $this->l10n->t('This feature requires Nextcloud Enterprise for instances with more than 500 users. Please contact Nextcloud support.'),
					'supportLink' => 'https://nextcloud.com/pricing',
					'supportLinkMessage' => $this->l10n->t('Nextcloud support'),
				];
				$this->initialStateService->provideInitialState('limit', $limitInitialState);
				return new TemplateResponse(
					Application::APP_ID,
					'limit',
					[],
					TemplateResponse::RENDER_AS_GUEST
				);
			}
		}
		$response = new TemplateResponse(Application::APP_ID, 'main', ['additionalScriptUrl' => $additionalScriptUrl]);
		$csp = new ContentSecurityPolicy();
		if ($additionalScriptUrl) {
			$allowedScriptDomains = $this->config->getAppValue(Application::APP_ID, 'allowed_script_domains');
			$allowedScriptDomains = explode(',', $allowedScriptDomains);
			// add the one for webex
			$allowedScriptDomains[] = 'binaries.webex.com';

			$parsedUrl = parse_url($additionalScriptUrl);
			if (in_array($parsedUrl['host'], $allowedScriptDomains)) {
				$csp->addAllowedScriptDomain($parsedUrl['host']);
			}
		}
		$response->setContentSecurityPolicy($csp);
		return $response;
	}

	/**
	 * @NoCSRFRequired
	 * @PublicPage
	 *
	 * @return TemplateResponse
	 */
	public function webexSharePage(string $token, string $fileId): TemplateResponse {
		$link = $this->urlGenerator->getAbsoluteURL(
			$this->urlGenerator->linkToRoute('files_sharing.Share.showShare', ['token' => $token])
		);
		$state = [
			'token' => $token,
			'publicLink' => $link,
			'fileId' => $fileId,
		];
		$this->initialStateService->provideInitialState('webex-share', $state);
		return new TemplateResponse(
			Application::APP_ID,
			'webexShare',
			[],
			TemplateResponse::RENDER_AS_GUEST
		);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $path
	 * @param int $x
	 * @param int $y
	 * @return DataDownloadResponse|DataResponse|RedirectResponse
	 */
	public function getFileImage(string $path, int $x = 100, int $y = 100): Response {
		try {
			$preview = $this->imageService->getFilePreviewFile($path, $this->userId, $x, $y);
			if ($preview === null) {
				$this->logger->error('No preview for user "' . $this->userId . '"');
				return new DataResponse('', Http::STATUS_NOT_FOUND);
			}
			if ($preview['type'] === 'file') {
				return new DataDownloadResponse(
					$preview['file']->getContent(),
					(string)Http::STATUS_OK,
					$preview['file']->getMimeType()
				);
			} elseif ($preview['type'] === 'icon') {
				return new RedirectResponse($preview['icon']);
			}
		} catch (Exception | Throwable $e) {
			$this->logger->error('getImage error', ['exception' => $e]);
			return new DataResponse('', Http::STATUS_NOT_FOUND);
		}
		return new DataResponse('', Http::STATUS_NOT_FOUND);
	}

	/**
	 * @param string $path
	 * @return DataResponse
	 */
	public function canShareFile(string $path): DataResponse {
		$userFolder = $this->root->getUserFolder($this->userId);
		try {
			$file = $userFolder->get($path);
			$perms = $file->getPermissions();
			return new DataResponse(['allowed' => ($perms & Constants::PERMISSION_SHARE) === Constants::PERMISSION_SHARE]);
		} catch (NotFoundException $e) {
			return new DataResponse(['allowed' => false]);
		}
	}
}
