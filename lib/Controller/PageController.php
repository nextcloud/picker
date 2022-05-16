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

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

use OCA\PublicPicker\AppInfo\Application;

class PageController extends Controller {

	/**
	 * @var string|null
	 */
	private $userId;

	public function __construct(string   $appName,
								IRequest $request,
								?string  $userId) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
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
		return $response;
	}
}
