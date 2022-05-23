<?php
/**
 * Nextcloud - Public picker
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2022
 */

namespace OCA\PublicPicker\Controller;

use Exception;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Response;
use Psr\Log\LoggerInterface;
use Throwable;
use OCA\PublicPicker\Service\ImageService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

use OCA\PublicPicker\AppInfo\Application;

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

	public function __construct(string   $appName,
								IRequest $request,
								ImageService $imageService,
								LoggerInterface $logger,
								?string  $userId) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->imageService = $imageService;
		$this->logger = $logger;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function singleLinkPage(): TemplateResponse {
		$response = new TemplateResponse(Application::APP_ID, 'main', []);
		// $response->renderAs(TemplateResponse::RENDER_AS_BASE);
//		$csp = new ContentSecurityPolicy();
//		$csp
//			//->addAllowedFrameDomain('*')
//			->addAllowedFrameAncestorDomain('meme.org');
////			->addAllowedImageDomain('*')
////			->addAllowedMediaDomain('*')
////			->addAllowedFrameDomain('*')
////			->addAllowedWorkerSrcDomain('*')
//			//->allowInlineScript(true)
//			// to make eval work in frontend
////			->allowEvalScript(true)
////			->addAllowedObjectDomain('*')
////			->addAllowedScriptDomain('*')
////			->addAllowedConnectDomain('*');
//		$response->setContentSecurityPolicy($csp);
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
