<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class DateTimeZoneId implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x66;

	public readonly int $seconds, $nanoseconds;
	
	public function __construct(
		int $seconds,
		int $nanoseconds,
		public readonly string $tz_id
	) {
		$this->seconds = $seconds + intdiv($nanoseconds, 1e9);
		$this->nanoseconds = $nanoseconds % 1e9;
	}
	public function _stringify_(): string {
		return sprintf(
			"%s[%s]",
			\DateTime::createFromFormat(
				'U.u',
				\Krishna\PackStream\Helper\SecNanoSec::stringify(
					$this->seconds, $this->nanoseconds
				),
				new \DateTimeZone($this->tz_id)
			)->format('Y-m-d\TH:i:s.u'),
			$this->tz_id
		);
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->seconds, $this->nanoseconds, $this->tz_id);
	}
}