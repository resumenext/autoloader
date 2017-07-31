<?php

namespace ResumeNext\AutoLoader;

if (!interface_exists(__NAMESPACE__ . "\\Exception\\ExceptionInterface", false)) {
	require __DIR__ . "/src/Exception/ExceptionInterface.php";
}
if (!class_exists(__NAMESPACE__ . "\\Exception\\AlreadyRegisteredException", false)) {
	require __DIR__ . "/src/Exception/AlreadyRegisteredException.php";
}
if (!class_exists(__NAMESPACE__ . "\\Exception\\InvalidAutoLoaderException", false)) {
	require __DIR__ . "/src/Exception/InvalidAutoLoaderException.php";
}
if (!class_exists(__NAMESPACE__ . "\\Exception\\InvalidIdentifierException", false)) {
	require __DIR__ . "/src/Exception/InvalidIdentifierException.php";
}

if (!interface_exists(__NAMESPACE__ . "\\AutoLoaderInterface", false)) {
	require __DIR__ . "/src/AutoLoaderInterface.php";
}
if (!interface_exists(__NAMESPACE__ . "\\BuilderInterface", false)) {
	require __DIR__ . "/src/BuilderInterface.php";
}
if (!interface_exists(__NAMESPACE__ . "\\ManagerInterface", false)) {
	require __DIR__ . "/src/ManagerInterface.php";
}

if (!class_exists(__NAMESPACE__ . "\\Builder", false)) {
	require __DIR__ . "/src/Builder.php";
}
if (!class_exists(__NAMESPACE__ . "\\Container", false)) {
	require __DIR__ . "/src/Container.php";
}
if (!class_exists(__NAMESPACE__ . "\\IncludeHelper", false)) {
	require __DIR__ . "/src/IncludeHelper.php";
}
if (!class_exists(__NAMESPACE__ . "\\Manager", false)) {
	require __DIR__ . "/src/Manager.php";
}
if (!class_exists(__NAMESPACE__ . "\\Psr4AutoLoader", false)) {
	require __DIR__ . "/src/Psr4AutoLoader.php";
}

return new Container(new Builder(), new Manager());
