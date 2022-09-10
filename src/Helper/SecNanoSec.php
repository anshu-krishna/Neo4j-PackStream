<?php
namespace Krishna\PackStream\Helper;

final class SecNanoSec {
	use T_StaticOnly;
	public static function stringify(int $sec, int $nanosec) : string {
		return sprintf("%.06f", round($sec + ($nanosec / 1e9), 6));
	}
}