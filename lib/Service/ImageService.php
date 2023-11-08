<?php

/**
 * Nextcloud - Picker
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier
 * @copyright Julien Veyssier 2022
 */

namespace OCA\Picker\Service;

use OC\Files\Node\File;
use OCP\Files\IMimeTypeDetector;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IPreview;
use Psr\Log\LoggerInterface;

class ImageService {

	/**
	 * @var IRootFolder
	 */
	private $root;
	/**
	 * @var LoggerInterface
	 */
	private $logger;
	/**
	 * @var IPreview
	 */
	private $previewManager;
	/**
	 * @var IMimeTypeDetector
	 */
	private $mimeTypeDetector;

	public function __construct(IRootFolder $root,
		LoggerInterface $logger,
		IPreview $previewManager,
		IMimeTypeDetector $mimeTypeDetector) {
		$this->root = $root;
		$this->logger = $logger;
		$this->previewManager = $previewManager;
		$this->mimeTypeDetector = $mimeTypeDetector;
	}

	/**
	 * @param string $filePath
	 * @param string $userId
	 * @param int $x
	 * @param int $y
	 * @return array|null
	 * @throws NotFoundException
	 * @throws \OCP\Files\NotPermittedException
	 * @throws \OC\User\NoUserException
	 */
	public function getFilePreviewFile(string $filePath, string $userId, int $x = 100, int $y = 100): ?array {
		$userFolder = $this->root->getUserFolder($userId);
		$file = $userFolder->get($filePath);
		if ($file instanceof File) {
			if ($this->previewManager->isMimeSupported($file->getMimeType())) {
				try {
					return [
						'type' => 'file',
						'file' => $this->previewManager->getPreview($file, $x, $y),
					];
				} catch (NotFoundException $e) {
					$this->logger->error('Mimetype is supported but no preview available', ['exception' => $e]);
				}
			}
			// fallback: mimetype icon
			return [
				'type' => 'icon',
				'icon' => $this->mimeTypeDetector->mimeTypeIcon($file->getMimeType()),
			];
		}
		return null;
	}
}
