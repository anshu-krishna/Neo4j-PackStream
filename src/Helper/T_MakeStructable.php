<?php
namespace Krishna\PackStream\Helper;

use Krishna\PackStream\PackEx;
use Krishna\PackStream\Structure;

// Bench Results on PHP 8.1
// 'Design1' => float 5.0490999221802
// 'Design2' => float 15.689797401428

// DESIGN 1
trait T_MakeStructable {
	public static function fromStructure(Structure $struct): ?static {
		if($struct->sig !== static::SIG) { return null; } // Intellisense error indicator is irrelevant
		try {
			return new static(...$struct->fields);
		} catch (\Throwable $th) {
			throw new PackEx('Invalid ' . static::class . '; ' . $th->getMessage());
		}
	}
	public function toStructure() : Structure {
		$refProps = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
		$props = [];
		foreach($refProps as $prop) {
			$props[] = $prop->getValue($this);
		}
		return new Structure(static::SIG, ...$props); // Intellisense error indicator is irrelevant
	}
}

/*
// DESIGN 2
trait T_MakeStructable {	
	public function clock() {
		$t1 = microtime(true);
		$a = static::SIG; // Design 1
		$t2 = microtime(true);
		$b = (new \ReflectionClassConstant(static::class, 'SIG'))->getValue();
		$t3 = microtime(true); // Design 2
		return [$t2 - $t1, $t3 - $t2];
	}
	public function bench(int $run = 1000000) {
		$t1 = 0;
		$t2 = 0;
		for ($i = 0; $i < $run; $i++) { 
			[$a, $b] = $this->clock();
			$t1 += $a;
			$t2 += $b;
		}
		var_dump(['Design1' => ($t1 / $run) * 10e6, 'Design2' => ($t2 / $run) * 10e6]);
	}
	public static function fromStruct(Struct $struct): ?static {
		$SIG = (new \ReflectionClassConstant(static::class, 'SIG'))->getValue();
		if($struct->sig !== $SIG) { return null; }
		try {
			return new static(...$struct->fields);
		} catch (\Throwable $th) {
			throw new PackEx('Invalid ' . static::class . '; ' . $px->getMessage());
		}
	}
	public function toStruct() : Struct {
		$refProps = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
		$props = [];
		foreach($refProps as $prop) {
			$props[$prop->getName()] = $prop->getValue($this);
		}
		$SIG = (new \ReflectionClassConstant(static::class, 'SIG'))->getValue();
		return new Struct($SIG, ...$props);
	}
}
*/