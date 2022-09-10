<?php
namespace Krishna\PackStream\Helper;

trait T_StaticOnly {
	final protected function __construct() {}
	final public static function __getStaticProperties__() {
		$class = new \ReflectionClass(static::class);
		return $class->getStaticProperties();
	}
}