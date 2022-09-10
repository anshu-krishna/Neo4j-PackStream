<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class LocalTime implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x74;

	public function __construct(public readonly int $nanoseconds) {}
	public function _stringify_(): string {
		return \DateTime::createFromFormat(
			'U.u',
			sprintf("%0.6f", round($this->nanoseconds / 1e9, 6))
		)->format('H:i:s.u');
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->nanoseconds);
	}
}