<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class Time implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x54;

	public function __construct(
		public readonly int $nanoseconds,
		public readonly int $tz_offset_seconds
	) {}
	public function _stringify_(): string {
		return \DateTime::createFromFormat(
			'U.u',
			\Krishna\PackStream\Helper\SecNanoSec::stringify(
				intdiv($this->nanoseconds, 1e9) - $this->tz_offset_seconds,
				$this->nanoseconds % 1e9
			),
			new \DateTimeZone('UTC')
		)->setTimezone(
			new \DateTimeZone(sprintf("%+05d", intdiv($this->tz_offset_seconds, 3600) * 100))
		)->format('H:i:s.uP');
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->nanoseconds, $this->tz_offset_seconds);
	}
}