<?php
namespace Krishna\PackStream\Helper;

use Krishna\PackStream\Struct\{Node, UnboundRelationship};

final class ListType {
	use T_StaticOnly;

	// Generic tester
	protected static function notTester(string $testerFuncName, array $list) : bool {
		try { ([static::class, $testerFuncName])(...$list); return false; }
		catch (\Throwable $th) { return true; }
	}
	protected static function isTester(string $testerFuncName, array $list) : bool {
		try { ([static::class, $testerFuncName])(...$list); return true; }
		catch (\Throwable $th) { return false; }
	}

	// Specific testers

	// String
	protected static function stringList(string ...$items) {}
	public static function notStringList(array $list): bool {
		return static::notTester('stringList', $list);
	}
	public static function isStringList(array $list): bool {
		return static::isTester('stringList', $list);
	}

	// Int
	protected static function intList(int ...$items) {}
	public static function notIntList(array $list): bool {
		return static::notTester('intList', $list);
	}
	public static function isIntList(array $list): bool {
		return static::isTester('intList', $list);
	}
	
	// Node
	protected static function nodeList(Node ...$items) {}
	public static function notNodeList(array $list): bool {
		return static::notTester('nodeList', $list);
	}
	public static function isNodeList(array $list): bool {
		return static::isTester('nodeList', $list);
	}
	
	// UnboundRelationship
	protected static function uRelList(UnboundRelationship ...$items) {}
	public static function notURelList(array $list): bool {
		return static::notTester('uRelList', $list);
	}
	public static function isURelList(array $list): bool {
		return static::isTester('uRelList', $list);
	}
}