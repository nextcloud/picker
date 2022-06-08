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
use OCP\IServerContainer;
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

	public function __construct(string   $appName,
								IRequest $request,
								ImageService $imageService,
								LoggerInterface $logger,
								IAppManager $appManager,
								IServerContainer $serverContainer,
								?string  $userId) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->imageService = $imageService;
		$this->logger = $logger;
		$this->appManager = $appManager;
		$this->serverContainer = $serverContainer;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function singleLinkPage(): TemplateResponse {
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
				$message = 'This feature requires Nextcloud enterprise for instances with more than 500 users. Please';
				return new TemplateResponse('core', '403', ['message' => $message], TemplateResponse::RENDER_AS_GUEST);
			}
		}
		/*
		return new TemplateResponse('core', 'error', [
			'errors' => [
				[
					'error' => 'yeye',
					'hint' => 'yeyehint',
				],
			],
		], 'guest');
		*/
		$response = new TemplateResponse(Application::APP_ID, 'main', []);
		return $response;
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
}
