<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class Duration implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x45;
	const SECONDS_IN_A_DAY = 86400; // = 24 * 60 * 60
	
	public readonly int $months, $days, $seconds, $nanoseconds;

	public function __construct(int $months, int $days, int $seconds, int $nanoseconds) {
		$seconds += intdiv($nanoseconds, 1e9);
		$this->nanoseconds = $nanoseconds % 1e9;
		$this->days = $days + intdiv($seconds, static::SECONDS_IN_A_DAY);
		$this->seconds = $seconds % static::SECONDS_IN_A_DAY;
		$this->months = $months;
	}
	public function _stringify_(): string {
		$d1 = 'P';

		$years = intdiv($this->months, 12);
		if($years > 0) { $d1 .= "{$years}Y"; }
		
		$months = $this->months % 12;
		if($months > 0) { $d1 .= "{$months}M"; }
		
		if($this->days > 0) { $d1 .= "{$this->days}D"; }

		$d2 = '';
		$hours = intdiv($this->seconds, 3600);
		if($hours > 0) { $d2 .= "{$hours}H"; }

		$minutes = intdiv($this->seconds % 3600, 60);
		if($minutes > 0) { $d2 .= "{$minutes}M"; }

		$seconds = rtrim(
			\Krishna\PackStream\Helper\SecNanoSec::stringify(
				$this->seconds % 3600 % 60,
				$this->nanoseconds
			),
			'0.'
		);
		if(!empty($seconds)) { $d2 .= "{$seconds}S"; }
		return $d1 . (strlen($d2) > 0 ? "T${d2}" : '');
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->months, $this->days, $this->seconds, $this->nanoseconds);
	}
}