<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class LocalDateTime implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x64;

	public readonly int $seconds, $nanoseconds;
	
	public function __construct(
		int $seconds,
		int $nanoseconds
	) {
		$this->seconds = $seconds + intdiv($nanoseconds, 1e9);
		$this->nanoseconds = $nanoseconds % 1e9;
	}
	public function _stringify_(): string {
		return \DateTime::createFromFormat(
			'U.u',
			\Krishna\PackStream\Helper\SecNanoSec::stringify($this->seconds, $this->nanoseconds)
		)->format('Y-m-d\TH:i:s.u');
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->seconds, $this->nanoseconds);
	}
}