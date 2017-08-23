<?php

namespace ResumeNext\AutoLoaderTest;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ResumeNext\AutoLoader\{AutoLoaderInterface, Builder};
use Throwable;

/**
 * @coversDefaultClass \ResumeNext\AutoLoader\Builder
 */
class BuilderTest extends TestCase {

	public static function setupBeforeClass() {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/Exception/ExceptionInterface.php";
		require_once __DIR__ . "/../src/Exception/InvalidAutoLoaderException.php";
		require_once __DIR__ . "/../src/BuilderInterface.php";
		require_once __DIR__ . "/../src/Builder.php";
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstructWithArgument() {
		$constructor = (new ReflectionClass(Builder::class))
			->getConstructor();
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["setProduct"])
			->getMock();

		$mockSut->expects($this->once())
			->method("setProduct")
			->with($this->equalTo("Beer"));

		$constructor->invoke($mockSut, "Beer");
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstructWithoutArgument() {
		$constructor = (new ReflectionClass(Builder::class))
			->getConstructor();
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["setProduct"])
			->getMock();

		$mockSut->expects($this->once())
			->method("setProduct")
			->with($this->equalTo(Builder::DEFAULT_PRODUCT));

		$constructor->invoke($mockSut);
	}

	/**
	 * @covers ::add
	 *
	 * @return array
	 */
	public function testAddWithExistingNamespaceAppend() {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$mockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\We\\")
			->will($this->returnValue("We"));
		$mockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/have/")
			->will($this->returnValue("/signal"));

		$property->setAccessible(true);
		$property->setValue($mockSut, ["We" => ["/have"]]);

		$result = $mockSut->add("\\We\\", "/have/", false);

		return [$mockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespaceAppend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespaceAppendReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespaceAppend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespaceAppendProperty($result) {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$property->setAccessible(true);

		$value = $property->getValue($result[0]);

		$this->assertSame(["We" => ["/have", "/signal"]], $value);
	}

	/**
	 * @covers ::add
	 *
	 * @return array
	 */
	public function testAddWithExistingNamespacePrepend() {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$mockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\We\\")
			->will($this->returnValue("We"));
		$mockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/signal/")
			->will($this->returnValue("/have"));

		$property->setAccessible(true);
		$property->setValue($mockSut, ["We" => ["/signal"]]);

		$result = $mockSut->add("\\We\\", "/signal/", true);

		return [$mockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespacePrepend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespacePrependReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testAddWithExistingNamespacePrepend
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithExistingNamespacePrependProperty($result) {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$property->setAccessible(true);

		$value = $property->getValue($result[0]);

		$this->assertSame(["We" => ["/have", "/signal"]], $value);
	}

	/**
	 * @covers ::add
	 *
	 * @return array
	 */
	public function testAddWithNewNamespace() {
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$mockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\Resume\\")
			->will($this->returnValue("Resume"));
		$mockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/next/")
			->will($this->returnValue("/next"));

		$result = $mockSut->add("\\Resume\\", "/next/");

		return [$mockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testAddWithNewNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithNewNamespaceReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testAddWithNewNamespace
	 * @param array $result
	 *
	 * @return void
	 */
	public function testAddWithNewNamespaceProperty($result) {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$property->setAccessible(true);

		$value = $property->getValue($result[0]);

		$this->assertSame(["Resume" => ["/next"]], $value);
	}

	/**
	 * @covers ::build
	 *
	 * @return array
	 */
	public function testBuild() {
		$stub0 = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$stub1 = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["createLoader", "configureLoader"])
			->getMock();

		$mockSut->expects($this->once())
			->method("createLoader")
			->will($this->returnValue($stub0));
		$mockSut->expects($this->once())
			->method("configureLoader")
			->with($this->identicalTo($stub0))
			->will($this->returnValue($stub1));

		$result = $mockSut->build();

		return [$stub1, $result];
	}

	/**
	 * @coversNothing
	 * @depends testBuild
	 * @param array $result
	 *
	 * @return void
	 */
	public function testBuildReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::getProduct
	 *
	 * @return void
	 */
	public function testGetProduct() {
		$class = new ReflectionClass(Builder::class);
		$property = $class->getProperty("product");
		$instance = $class->newInstanceWithoutConstructor();

		$property->setAccessible(true);
		$property->setValue($instance, "Yolo");

		$result = $instance->getProduct();

		$this->assertSame("Yolo", $result);
	}

	/**
	 * @covers ::set
	 *
	 * @return array
	 */
	public function testSet() {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods([
				"canonicalizeNamespace",
				"canonicalizePath",
			])
			->getMock();

		$mockSut->expects($this->once())
			->method("canonicalizeNamespace")
			->with("\\ResumeNext\\")
			->will($this->returnValue("ResumeNext"));
		$mockSut->expects($this->once())
			->method("canonicalizePath")
			->with("/resume-next/src/")
			->will($this->returnValue("/resume-next/src"));

		$property->setAccessible(true);
		$property->setValue($mockSut, ["We" => ["/have", "/signal"]]);

		$result = $mockSut->set("\\ResumeNext\\", "/resume-next/src/");

		return [$mockSut, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSet
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testSet
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetPropertyHasExistingNamespace($result) {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$property->setAccessible(true);

		$namespaces = $property->getValue($result[0]);

		$this->assertArrayHasKey("We", $namespaces);
	}

	/**
	 * @coversNothing
	 * @depends testSet
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetPropertyHasNewNamespace($result) {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("namespaces");

		$property->setAccessible(true);

		$namespaces = $property->getValue($result[0]);

		$this->assertArrayHasKey("ResumeNext", $namespaces);

		return $namespaces;
	}

	/**
	 * @coversNothing
	 * @depends testSetPropertyHasNewNamespace
	 * @param array $namespaces
	 *
	 * @return void
	 */
	public function testSetPropertyHasNewPath($namespaces) {
		$this->assertSame(["/resume-next/src"], $namespaces["ResumeNext"]);
	}

	/**
	 * @covers ::setProduct
	 *
	 * @return array
	 */
	public function testSetProduct() {
		$class = get_class(
			$this->getMockBuilder(AutoLoaderInterface::class)
				->getMock()
		);
		$instance = (new ReflectionClass(Builder::class))
			->newInstanceWithoutConstructor();
		$result = $instance->setProduct($class);

		$this->assertTrue(true);

		return [$class, $instance, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSetProduct
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetProductReturn($result) {
		$hash0 = spl_object_hash($result[1]);
		$hash1 = spl_object_hash($result[2]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testSetProduct
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetProductProperty($result) {
		$property = (new ReflectionClass(Builder::class))
			->getProperty("product");

		$property->setAccessible(true);

		$this->assertSame($result[0], $property->getValue($result[1]));
	}

	/**
	 * @covers ::setProduct
	 * @expectedException \ResumeNext\AutoLoader\Exception\InvalidAutoLoaderException
	 *
	 * @return void
	 */
	public function testSetProductThrowsException() {
		$instance = (new ReflectionClass(Builder::class))
			->newInstanceWithoutConstructor();

		$instance->setProduct("Yolo");
	}

	/**
	 * @covers ::setProduct
	 *
	 * @return void
	 */
	public function testSetProductExceptionPropertyNotChanged() {
		$class = new ReflectionClass(Builder::class);
		$instance = $class->newInstanceWithoutConstructor();
		$property = $class->getProperty("product");

		$property->setAccessible(true);
		$property->setValue($instance, "Resume");

		try {
			$instance->setProduct("Next");
		}
		catch (Throwable $ex) {
		}

		$this->assertSame("Resume", $property->getValue($instance));
	}

	/**
	 * @covers ::configureLoader
	 *
	 * @return array
	 */
	public function testConfigureLoader() {
		$namespaces = [
			"ResumeNext" => ["/resume-next/src"],
			"Series" => ["/of", "/tubes"],
		];
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["getAll"])
			->getMock();
		$loader = new class() implements AutoLoaderInterface {
			public function load(string $qcn) {
			}
		};
		$method = (new ReflectionClass(Builder::class))
			->getMethod("configureLoader");

		$mockSut->expects($this->once())
			->method("getAll")
			->will($this->returnValue($namespaces));

		$method->setAccessible(true);

		$result = $method->invoke($mockSut, $loader);

		return [$loader, $result];
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testConfigureLoaderReturn($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyZero($result) {
		$this->assertObjectHasAttribute("ResumeNext", $result[0]);

		return $result[0];
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoaderSetsPropertyZero
	 * @param object $loader
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyZeroValue($loader) {
		$this->assertSame(["/resume-next/src"], $loader->ResumeNext);
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyOne($result) {
		$this->assertObjectHasAttribute("Series", $result[0]);

		return $result[0];
	}

	/**
	 * @coversNothing
	 * @depends testConfigureLoaderSetsPropertyZero
	 * @param object $loader
	 *
	 * @return void
	 */
	public function testConfigureLoaderSetsPropertyOneValue($loader) {
		$this->assertSame(["/of", "/tubes"], $loader->Series);
	}

	/**
	 * @covers ::createLoader
	 *
	 * @return array
	 */
	public function testCreateLoader() {
		$class = get_class(new class() implements AutoLoaderInterface {
			public function load(string $qcn) {
			}
		});
		$mockSut = $this->getMockBuilder(Builder::class)
			->disableOriginalConstructor()
			->setMethods(["getProduct"])
			->getMock();
		$method = (new ReflectionClass(Builder::class))
			->getMethod("createLoader");

		$mockSut->expects($this->once())
			->method("getProduct")
			->will($this->returnValue($class));

		$method->setAccessible(true);

		$result = $method->invoke($mockSut);

		return [$class, $result];
	}

	/**
	 * @coversNothing
	 * @depends testCreateLoader
	 * @param array $result
	 *
	 * @return void
	 */
	public function testCreateLoaderReturnsObject($result) {
		$this->assertInternalType("object", $result[1]);
	}

	/**
	 * @coversNothing
	 * @depends testCreateLoader
	 * @depends testCreateLoaderReturnsObject
	 * @param array $result
	 *
	 * @return void
	 */
	public function testCreateLoaderReturnsInstanceOfProduct($result) {
		$this->assertInstanceOf($result[0], $result[1]);
	}

	/**
	 * @covers ::getAll
	 *
	 * @return void
	 */
	public function testGetAll() {
		$class = new ReflectionClass(Builder::class);
		$method = $class->getMethod("getAll");
		$property = $class->getProperty("namespaces");
		$instance = $class->newInstanceWithoutConstructor();

		$method->setAccessible(true);
		$property->setAccessible(true);
		$property->setValue($instance, ["Resume" => ["/next"]]);

		$result = $method->invoke($instance);

		$this->assertSame(["Resume" => ["/next"]], $result);
	}

	/**
	 * @covers ::canonicalizeNamespace
	 *
	 * @return void
	 */
	public function testCanonicalizeNamespace() {
		$class = new ReflectionClass(Builder::class);
		$method = $class->getMethod("canonicalizeNamespace");
		$instance = $class->newInstanceWithoutConstructor();

		$method->setAccessible(true);

		$result = $method->invoke($instance, "\\ResumeNext\\AutoLoader\\");

		$this->assertSame("ResumeNext\\AutoLoader", $result);
	}

	/**
	 * @covers ::canonicalizePath
	 *
	 * @return void
	 */
	public function testCanonicalizePathUnix() {
		$class = new ReflectionClass(Builder::class);
		$method = $class->getMethod("canonicalizePath");
		$instance = $class->newInstanceWithoutConstructor();

		$method->setAccessible(true);

		$result = $method->invoke($instance, "/resume/next/");

		$this->assertSame("/resume/next", $result);
	}

	/**
	 * @covers ::canonicalizePath
	 *
	 * @return void
	 */
	public function testCanonicalizePathDos() {
		$class = new ReflectionClass(Builder::class);
		$method = $class->getMethod("canonicalizePath");
		$instance = $class->newInstanceWithoutConstructor();

		$method->setAccessible(true);

		$result = $method->invoke($instance, "\\Resume\\Next\\");

		$this->assertSame("\\Resume\\Next", $result);
	}

}
