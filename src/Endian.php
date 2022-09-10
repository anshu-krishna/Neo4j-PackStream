<?php
namespace Krishna\PackStream;

final class Endian {
	use Helper\T_StaticOnly;

	protected static ?bool $little = null;

	public static function isLittle(): bool {
		static::$little ??= unpack('S', "\x01\x00")[1] === 1;
		return static::$little;
	}

	public static function isBig(): bool {
		static::$little ??= unpack('S', "\x01\x00")[1] === 1;
		return !static::$little;
	}
}