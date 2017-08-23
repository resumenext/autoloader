<?php

namespace ResumeNext\AutoLoaderTest;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ResumeNext\AutoLoader\{AutoLoaderInterface, Psr4AutoLoader};

/**
 * @coversDefaultClass \ResumeNext\AutoLoader\Psr4AutoLoader
 */
class Psr4AutoLoaderTest extends TestCase {

	public static function setupBeforeClass() {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/Psr4AutoLoader.php";
	}

	/**
	 * @covers ::load
	 *
	 * @return void
	 */
	public function testLoadUnknownClass() {
		$mockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"splitNamespace",
				"searchClass",
				"requireFile",
			])
			->getMock();

		$mockSut->expects($this->never())
			->method("requireFile");
		$mockSut->expects($this->once())
			->method("splitNamespace")
			->with($this->equalTo("ResumeNext\\Widget\\Nuke"))
			->will($this->returnValue(["ResumeNext\\Widget", "Nuke"]));
		$mockSut->expects($this->once())
			->method("searchClass")
			->with(
				$this->equalTo("ResumeNext\\Widget"),
				$this->equalTo("Nuke")
			)
			->will($this->returnValue(false));

		$mockSut->load("ResumeNext\\Widget\\Nuke");
	}

	/**
	 * @covers ::load
	 *
	 * @return void
	 */
	public function testLoadKnownClass() {
		$mockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"splitNamespace",
				"searchClass",
				"requireFile",
			])
			->getMock();

		$mockSut->expects($this->once())
			->method("splitNamespace")
			->with($this->equalTo("ResumeNext\\Widget\\Nuke"))
			->will($this->returnValue(["ResumeNext\\Widget", "Nuke"]));
		$mockSut->expects($this->once())
			->method("searchClass")
			->with(
				$this->equalTo("ResumeNext\\Widget"),
				$this->equalTo("Nuke")
			)
			->will($this->returnValue("/resume-next/widget/nuke.php"));
		$mockSut->expects($this->once())
			->method("requireFile")
			->with($this->equalTo("/resume-next/widget/nuke.php"));

		$mockSut->load("ResumeNext\\Widget\\Nuke");
	}

	/**
	 * @covers ::requireFile
	 *
	 * @return void
	 */
	public function testRequireFile() {
		$sut = new Psr4AutoLoader();
		$method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("requireFile");
		$helper = new class() extends TestCase {

			public static $that;

			public static function requireFile(string $file) {
				static::$that->assertSame("/nuke.php", $file);
			}

		};

		$helper::$that = $this;

		$method->setAccessible(true);
		$method->invoke($sut, "/nuke.php", get_class($helper));
	}

	/**
	 * @covers ::resolveInclude
	 * @runInSeparateProcess
	 *
	 * @return void
	 */
	public function testResolveInclude() {
		$sut = new Psr4AutoLoader();
		$method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("resolveInclude");

		$method->setAccessible(true);

		set_include_path(__DIR__);

		$result = $method->invoke($sut, "_files", "dummy");
		$expect = __DIR__ . DIRECTORY_SEPARATOR . "_files" .
			DIRECTORY_SEPARATOR . "dummy.php";

		$this->assertSame($expect, $result);
	}

	/**
	 * @covers ::searchClass
	 *
	 * @return mixed
	 */
	public function testSearchClassRecursive() {
		$method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("searchClass");
		$mockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"resolveInclude",
				"splitNamespace",
				"searchClass",
			])
			->getMock();

		$mockSut->expects($this->never())
			->method("resolveInclude");
		$mockSut->expects($this->once())
			->method("splitNamespace")
			->with($this->equalTo("ResumeNext\\Widget"))
			->will($this->returnValue(["ResumeNext", "Widget"]));
		$mockSut->expects($this->once())
			->method("searchClass")
			->with(
				$this->equalTo("ResumeNext"),
				$this->equalTo("Widget" . DIRECTORY_SEPARATOR . "Nuke")
			)
			->will($this->returnValue("/nuke.php"));

		$method->setAccessible(true);

		$result = $method->invoke($mockSut, "ResumeNext\\Widget", "Nuke");

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSearchClassRecursive
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSearchClassRecursiveReturnValue($result) {
		$this->assertSame("/nuke.php", $result);
	}

	/**
	 * @covers ::searchClass
	 *
	 * @return mixed
	 */
	public function testSearchClassEmptyNamespace() {
		$method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("searchClass");
		$mockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"resolveInclude",
				"splitNamespace",
				"searchClass",
			])
			->getMock();

		$mockSut->expects($this->never())
			->method("resolveInclude");
		$mockSut->expects($this->never())
			->method("splitNamespace");
		$mockSut->expects($this->never())
			->method("searchClass");

		$method->setAccessible(true);

		$result = $method->invoke($mockSut, "", "Nuke");

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSearchClassEmptyNamespace
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSearchClassEmptyNamespaceReturnsFalse($result) {
		$this->assertFalse($result);
	}

	/**
	 * @covers ::searchClass
	 *
	 * @return mixed
	 */
	public function testSearchClass() {
		$namespace = "ResumeNext\\Widget";
		$method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("searchClass");
		$mockSut = $this->getMockBuilder(Psr4AutoLoader::class)
			->setMethods([
				"resolveInclude",
				"splitNamespace",
				"searchClass",
			])
			->getMock();

		$mockSut->$namespace = ["/on/error", "/resume/next"];
		$mockSut->expects($this->never())
			->method("splitNamespace");
		$mockSut->expects($this->never())
			->method("searchClass");
		$mockSut->expects($this->exactly(2))
			->method("resolveInclude")
			->withConsecutive(
				[
					$this->equalTo("/on/error"),
					$this->equalTo("Nuke"),
				],
				[
					$this->equalTo("/resume/next"),
					$this->equalTo("Nuke"),
				]
			)
			->will($this->onConsecutiveCalls(false, "/nuke.php"));

		$method->setAccessible(true);

		$result = $method->invoke($mockSut, $namespace, "Nuke");

		$method->setAccessible(true);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSearchClass
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSearchClassReturnsFile($result) {
		$this->assertSame("/nuke.php", $result);
	}

	/**
	 * @covers ::splitNamespace
	 *
	 * @return array
	 */
	public function testSplitNamespace() {
		$sut = new Psr4AutoLoader();
		$method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("splitNamespace");

		$method->setAccessible(true);

		$result = $method->invoke($sut, "ResumeNext\\Widget\\Nuke");

		$this->assertInternalType("array", $result);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceArrayCount($result) {
		$this->assertCount(2, $result);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceArrayFirstMember($result) {
		$this->assertSame("ResumeNext\\Widget", $result[0]);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceArraySecondMember($result) {
		$this->assertSame("Nuke", $result[1]);
	}

	/**
	 * @covers ::splitNamespace
	 *
	 * @return array
	 */
	public function testSplitNamespaceWithoutNamespace() {
		$sut = new Psr4AutoLoader();
		$method = (new ReflectionClass(Psr4AutoLoader::class))
			->getMethod("splitNamespace");

		$method->setAccessible(true);

		$result = $method->invoke($sut, "Nuke");

		$this->assertInternalType("array", $result);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespaceWithoutNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceWithoutNamespaceArrayCount($result) {
		$this->assertCount(2, $result);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespaceWithoutNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceWithoutNamespaceArrayFirstMember($result) {
		$this->assertSame("", $result[0]);
	}

	/**
	 * @coversNothing
	 * @depends testSplitNamespaceWithoutNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSplitNamespaceWithoutNamespaceArraySecondMember($result) {
		$this->assertSame("Nuke", $result[1]);
	}

}
