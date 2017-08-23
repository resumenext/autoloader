<?php

namespace ResumeNext\AutoLoaderTest;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ResumeNext\AutoLoader\Manager;
use ResumeNext\AutoLoader\{AutoLoaderInterface, ManagerInterface};

/**
 * @coversDefaultClass \ResumeNext\AutoLoader\Manager
 */
class ManagerTest extends TestCase {

	public static function setupBeforeClass() {
		require_once __DIR__ . "/../src/Exception/ExceptionInterface.php";
		require_once __DIR__ . "/../src/Exception/AlreadyRegisteredException.php";
		require_once __DIR__ . "/../src/Exception/InvalidIdentifierException.php";
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/ManagerInterface.php";
		require_once __DIR__ . "/../src/Manager.php";
	}

	/**
	 * @covers ::has
	 *
	 * @return void
	 */
	public function testHasWithUnknownIdentifier() {
		$sut = new Manager();

		$result = $sut->has("Yolo");

		$this->assertFalse($result);
	}

	/**
	 * @covers ::has
	 *
	 * @return void
	 */
	public function testHasWithKnownIdentifier() {
		$property = (new ReflectionClass(Manager::class))
			->getProperty("loaders");
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$sut = new Manager();

		$property->setAccessible(true);
		$property->setValue($sut, ["Yolo" => $loader]);

		$result = $sut->has("Yolo");

		$this->assertTrue($result);
	}

	/**
	 * @covers ::register
	 * @expectedException \ResumeNext\AutoLoader\Exception\AlreadyRegisteredException
	 * @expectedExceptionCode 17
	 *
	 * @return void
	 */
	public function testRegisterThrowsException() {
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$mockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getIdentifier",
				"has",
				"setLoader",
				"wrapSplAutoloadRegister",
			])
			->getMock();

		$mockSut->expects($this->never())
			->method("getAutoloadFunction");
		$mockSut->expects($this->never())
			->method("setLoader");
		$mockSut->expects($this->never())
			->method("wrapSplAutoloadRegister");
		$mockSut->expects($this->once())
			->method("getIdentifier")
			->with($this->identicalTo($loader))
			->will($this->returnValue("YOLO"));
		$mockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("YOLO"))
			->will($this->returnValue(true));

		$mockSut->register($loader);
	}

	/**
	 * @covers ::register
	 *
	 * @return mixed
	 */
	public function testRegister() {
		$callable = function() {};
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$mockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getIdentifier",
				"has",
				"setLoader",
				"wrapSplAutoloadRegister",
			])
			->getMock();

		$mockSut->expects($this->once())
			->method("getIdentifier")
			->with($this->identicalTo($loader))
			->will($this->returnValue("YOLO"));
		$mockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("YOLO"))
			->will($this->returnValue(false));
		$mockSut->expects($this->once())
			->method("setLoader")
			->with(
				$this->equalTo("YOLO"),
				$this->identicalTo($loader)
			);
		$mockSut->expects($this->once())
			->method("getAutoloadFunction")
			->with($this->identicalTo($loader))
			->will($this->returnValue($callable));
		$mockSut->expects($this->once())
			->method("wrapSplAutoloadRegister")
			->with(
				$this->identicalTo($callable),
				$this->equalTo(true)
			);

		$result = $mockSut->register($loader, true);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testRegister
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testRegisterReturnsIdentifier($result) {
		$this->assertSame("YOLO", $result);
	}

	/**
	 * @covers ::unregister
	 * @expectedException \ResumeNext\AutoLoader\Exception\InvalidIdentifierException
	 * @expectedExceptionCode 2
	 *
	 * @return void
	 */
	public function testUnregisterThrowsException() {
		$mockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getLoader",
				"has",
				"removeLoader",
				"wrapSplAutoloadUnregister",
			])
			->getMock();

		$mockSut->expects($this->never())
			->method("getAutoloadFunction");
		$mockSut->expects($this->never())
			->method("getLoader");
		$mockSut->expects($this->never())
			->method("removeLoader");
		$mockSut->expects($this->never())
			->method("wrapSplAutoloadUnregister");
		$mockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("Yolo"))
			->will($this->returnValue(false));

		$mockSut->unregister("Yolo");
	}

	/**
	 * @covers ::unregister
	 *
	 * @return array
	 */
	public function testUnregister() {
		$callable = function() {};
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$mockSut = $this->getMockBuilder(Manager::class)
			->setMethods([
				"getAutoloadFunction",
				"getLoader",
				"has",
				"removeLoader",
				"wrapSplAutoloadUnregister",
			])
			->getMock();

		$mockSut->expects($this->once())
			->method("has")
			->with($this->equalTo("Yolo"))
			->will($this->returnValue(true));
		$mockSut->expects($this->once())
			->method("getLoader")
			->with($this->equalTo("Yolo"))
			->will($this->returnValue($loader));
		$mockSut->expects($this->once())
			->method("removeLoader")
			->with($this->equalTo("Yolo"));
		$mockSut->expects($this->once())
			->method("getAutoloadFunction")
			->with($this->identicalTo($loader))
			->will($this->returnValue($callable));
		$mockSut->expects($this->once())
			->method("wrapSplAutoloadUnregister")
			->with($this->identicalTo($callable));

		$result = $mockSut->unregister("Yolo");

		return [$mockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testUnregister
	 * @param array $result
	 *
	 * @return void
	 */
	public function testUnregisterReturnsSelf($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::getAutoloadFunction
	 *
	 * @return void
	 */
	public function testGetAutoloadFunction() {
		$sut = new Manager();
		$method = (new ReflectionClass(Manager::class))
			->getMethod("getAutoloadFunction");
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();

		$method->setAccessible(true);

		$result = $method->invoke($sut, $loader);

		$this->assertInternalType("callable", $result);
	}

	/**
	 * @covers ::getLoader
	 *
	 * @return void
	 */
	public function testGetLoader() {
		$sut = new Manager();
		$class = new ReflectionClass(Manager::class);
		$method = $class->getMethod("getLoader");
		$property = $class->getProperty("loaders");
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();

		$method->setAccessible(true);
		$property->setAccessible(true);
		$property->setValue($sut, ["Yolo" => $loader]);

		$result = $method->invoke($sut, "Yolo");

		$hash0 = spl_object_hash($loader);
		$hash1 = spl_object_hash($result);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::getIdentifier
	 *
	 * @return void
	 */
	public function testGetIdentifierReturnsString() {
		$sut = new Manager();
		$method = (new ReflectionClass(Manager::class))
			->getMethod("getIdentifier");

		$method->setAccessible(true);

		$result = $method->invoke($sut, (object)[]);

		$this->assertInternalType("string", $result);
	}

	/**
	 * @covers ::getIdentifier
	 *
	 * @return void
	 */
	public function testGetIdentifier() {
		$object0 = (object)[];
		$object1 = (object)[];
		$sut = new Manager();

		$method = (new ReflectionClass(Manager::class))
			->getMethod("getIdentifier");

		$method->setAccessible(true);

		$result0 = $method->invoke($sut, $object0);
		$result1 = $method->invoke($sut, $object1);

		$this->assertNotSame($result0, $result1);
	}

	/**
	 * @covers ::removeLoader
	 *
	 * @return void
	 */
	public function testRemoveLoader() {
		$sut = new Manager();
		$class = new ReflectionClass(Manager::class);
		$method = $class->getMethod("removeLoader");
		$property = $class->getProperty("loaders");
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();

		$property->setAccessible(true);
		$property->setValue($sut, ["YOLO" => $loader]);
		$method->setAccessible(true);
		$method->invoke($sut, "YOLO");

		$this->assertArrayNotHasKey("YOLO", $property->getValue($sut));
	}

	public function testSetLoaderSetsProperty() {
		$sut = new Manager();
		$class = new ReflectionClass(Manager::class);
		$method = $class->getMethod("setLoader");
		$property = $class->getProperty("loaders");
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();

		$method->setAccessible(true);
		$method->invoke($sut, "Yolo", $loader);

		$property->setAccessible(true);

		$loaders = $property->getValue($sut);

		$this->assertArrayHasKey("Yolo", $loaders);

		return [$loader, $loaders];
	}

	/**
	 * @coversNothing
	 * @depends testSetLoaderSetsProperty
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetLoaderSetsPropertyAssignsLoader($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]["Yolo"]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::wrapSplAutoloadRegister
	 *
	 * @return void
	 */
	public function testWrapSplAutoloadRegister() {
		$autoloaders = spl_autoload_functions();
		$sut = new Manager();
		$method = (new ReflectionClass(Manager::class))
			->getMethod("wrapSplAutoloadRegister");

		array_unshift($autoloaders, "print_r");

		$method->setAccessible(true);
		$method->invoke($sut, "print_r", true);

		$result = spl_autoload_functions();

		spl_autoload_unregister("print_r");

		$this->assertSame($autoloaders, $result);
	}

	/**
	 * @covers ::wrapSplAutoloadUnregister
	 *
	 * @return void
	 */
	public function testWrapSplAutoloadUnregister() {
		$autoloaders = spl_autoload_functions();
		$sut = new Manager();
		$method = (new ReflectionClass(Manager::class))
			->getMethod("wrapSplAutoloadUnregister");

		spl_autoload_register("print_r", true);

		$method->setAccessible(true);
		$method->invoke($sut, "print_r");

		$result = spl_autoload_functions();

		$this->assertSame($autoloaders, $result);
	}

}
