<?php

namespace OCA\OpenAi\Tests;

use OCA\Picker\AppInfo\Application;
use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('picker', $app::APP_ID);
	}
}
