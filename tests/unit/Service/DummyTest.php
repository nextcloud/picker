<?php

/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Picker\Tests;

use OCA\Picker\AppInfo\Application;
use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('picker', $app::APP_ID);
	}
}
