<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class Point2D implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x58;
	
	public function __construct(
		public readonly int $srid,
		public readonly float $x,
		public readonly float $y
	) {}
	public function _stringify_(): string {
		return "point({srid: {$this->srid}, x: {$this->x}, y: {$this->y}})";
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->srid, $this->x, $this->y);
	}
}