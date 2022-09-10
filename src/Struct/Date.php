<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class Date implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x44;
	
	public function __construct(public readonly int $days) {}
	public function _stringify_(): string {
		return $this->cache = gmdate('Y-m-d', strtotime(sprintf("%+d days +0000", $this->days), 0));
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->days);
	}
}