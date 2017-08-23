<?php

namespace ResumeNext\AutoLoaderTest;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ResumeNext\AutoLoader\{AutoLoaderInterface, BuilderInterface};
use ResumeNext\AutoLoader\{Container, ManagerInterface};

/**
 * @coversDefaultClass \ResumeNext\AutoLoader\Container
 */
class ContainerTest extends TestCase {

	public static function setupBeforeClass() {
		require_once __DIR__ . "/../src/AutoLoaderInterface.php";
		require_once __DIR__ . "/../src/BuilderInterface.php";
		require_once __DIR__ . "/../src/ManagerInterface.php";
		require_once __DIR__ . "/../src/Container.php";
	}

	/**
	 * @covers ::__construct
	 *
	 * @return void
	 */
	public function testConstruct() {
		$constructor = (new ReflectionClass(Container::class))
			->getConstructor();
		$builder = $this->getMockBuilder(BuilderInterface::class)
			->getMock();
		$manager = $this->getMockBuilder(ManagerInterface::class)
			->getMock();
		$mockSut = $this->getMockBuilder(Container::class)
			->disableOriginalConstructor()
			->setMethods(["setBuilder", "setManager"])
			->getMock();

		$mockSut->expects($this->once())
			->method("setBuilder")
			->with($this->identicalTo($builder))
			->will($this->returnSelf());
		$mockSut->expects($this->once())
			->method("setManager")
			->with($this->identicalTo($manager))
			->will($this->returnSelf());

		$constructor->invoke($mockSut, $builder, $manager);
	}

	/**
	 * @covers ::getBuilder
	 *
	 * @return void
	 */
	public function testGetBuilderClonesBuilder() {
		$builder = new class() extends TestCase implements BuilderInterface {

			public function __clone() {
				$this->that->assertTrue(true);
			}

			public function add(
				string $namespace,
				string $path,
				bool $prepend = false
			): BuilderInterface {
			}

			public function build(): AutoLoaderInterface {
			}

			public function set(
				string $namespace,
				string $path
			): BuilderInterface {
			}

		};

		$class = new ReflectionClass(Container::class);
		$instance = $class->newInstanceWithoutConstructor();
		$property = $class->getProperty("builder");

		$property->setAccessible(true);
		$property->setValue($instance, $builder);
		$builder->that = $this;

		$instance->getBuilder();
	}

	/**
	 * @covers ::getBuilder
	 *
	 * @return array
	 */
	public function testGetBuilderReturnsBuilderInterface() {
		$class = new ReflectionClass(Container::class);
		$instance = $class->newInstanceWithoutConstructor();
		$property = $class->getProperty("builder");
		$builder = $this->getMockBuilder(BuilderInterface::class)
			->getMock();

		$property->setAccessible(true);
		$property->setValue($instance, $builder);

		$result = $instance->getBuilder();

		$this->assertInstanceOf(BuilderInterface::class, $result);

		return [$builder, $result];
	}

	/**
	 * @coversNothing
	 * @depends testGetBuilderReturnsBuilderInterface
	 * @param array $result
	 *
	 * @return void
	 */
	public function testGetBuilderReturnsClone($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertNotSame($hash0, $hash1);
	}

	/**
	 * @covers ::getManager
	 *
	 * @return void
	 */
	public function testGetManager() {
		$class = new ReflectionClass(Container::class);
		$instance = $class->newInstanceWithoutConstructor();
		$property = $class->getProperty("manager");
		$manager = $this->getMockBuilder(ManagerInterface::class)
			->getMock();

		$property->setAccessible(true);
		$property->setValue($instance, $manager);

		$result = $instance->getManager();

		$hash0 = spl_object_hash($manager);
		$hash1 = spl_object_hash($result);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::register
	 *
	 * @return mixed
	 */
	public function testRegister() {
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$manager = $this->getMockBuilder(ManagerInterface::class)
			->setMethods(["has", "register", "unregister"])
			->getMock();
		$mockSut = $this->getMockBuilder(Container::class)
			->disableOriginalConstructor()
			->setMethods(["getManager"])
			->getMock();

		$manager->expects($this->never())
			->method("has");
		$manager->expects($this->never())
			->method("unregister");
		$manager->expects($this->once())
			->method("register")
			->with($this->identicalTo($loader))
			->will($this->returnValue("yolo"));
		$mockSut->expects($this->once())
			->method("getManager")
			->will($this->returnValue($manager));

		return $mockSut->register($loader);
	}

	/**
	 * @coversNothing
	 * @depends testRegister
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testRegisterReturnsIdentifier($result) {
		$this->assertSame("yolo", $result);
	}

	/**
	 * @covers ::setBuilder
	 *
	 * @return array
	 */
	public function testSetBuilder() {
		$class = new ReflectionClass(Container::class);
		$property = $class->getProperty("builder");
		$instance = $class->newInstanceWithoutConstructor();
		$builder = $this->getMockBuilder(BuilderInterface::class)
			->getMock();

		$property->setAccessible(true);
		$property->setValue($instance, $builder);

		$result = $instance->setBuilder($builder);

		$hash0 = spl_object_hash($builder);
		$hash1 = spl_object_hash($property->getValue($instance));

		$this->assertSame($hash0, $hash1);

		return [$instance, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSetBuilder
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetBuilderReturnsSelf($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::setManager
	 *
	 * @return array
	 */
	public function testSetManager() {
		$class = new ReflectionClass(Container::class);
		$property = $class->getProperty("manager");
		$instance = $class->newInstanceWithoutConstructor();
		$manager = $this->getMockBuilder(ManagerInterface::class)
			->getMock();

		$property->setAccessible(true);
		$property->setValue($instance, $manager);

		$result = $instance->setManager($manager);

		$hash0 = spl_object_hash($manager);
		$hash1 = spl_object_hash($property->getValue($instance));

		$this->assertSame($hash0, $hash1);

		return [$instance, $result];
	}

	/**
	 * @coversNothing
	 * @depends testSetManager
	 * @param array $result
	 *
	 * @return void
	 */
	public function testSetManagerReturnsSelf($result) {
		$hash0 = spl_object_hash($result[0]);
		$hash1 = spl_object_hash($result[1]);

		$this->assertSame($hash0, $hash1);
	}

	/**
	 * @covers ::setup
	 *
	 * @return mixed
	 */
	public function testSetup() {
		$loader = $this->getMockBuilder(AutoLoaderInterface::class)
			->getMock();
		$builder = $this->getMockBuilder(BuilderInterface::class)
			->setMethods(["add", "build", "set"])
			->getMock();
		$mockSut = $this->getMockBuilder(Container::class)
			->disableOriginalConstructor()
			->setMethods(["getBuilder", "register"])
			->getMock();

		$builder->expects($this->exactly(2))
			->method("add")
			->withConsecutive(
				[$this->equalTo("On"), $this->equalTo("/error")],
				[$this->equalTo("Resume"), $this->equalTo("/next")]
			)
			->will($this->returnSelf());
		$builder->expects($this->once())
			->method("build")
			->will($this->returnValue($loader));
		$builder->expects($this->never())
			->method("set");

		$mockSut->expects($this->once())
			->method("getBuilder")
			->will($this->returnValue($builder));
		$mockSut->expects($this->once())
			->method("register")
			->with($this->identicalTo($loader))
			->will($this->returnValue("YOLO"));

		$result = $mockSut->setup([
			["On", "/error"],
			["Resume", "/next"],
		]);

		return $result;
	}

	/**
	 * @coversNothing
	 * @depends testSetup
	 * @param mixed $result
	 *
	 * @return void
	 */
	public function testSetupReturnsIdentifier($result) {
		$this->assertSame("YOLO", $result);
	}

}
