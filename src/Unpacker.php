<?php
namespace Krishna\PackStream;

use Krishna\PackStream\Struct;

final class Unpacker {
	use Helper\T_StaticOnly;
	
	public static function fetch(I_ByteSource $source, string $format, int $len) {
		return unpack($format, $source->read($len))[1];
	}
	public static function fetchEndian(I_ByteSource $source, string $format, int $len) {
		return unpack(
			$format,
			Endian::isLittle()? strrev($source->read($len)) : $source->read($len)
		)[1];
	}
	public static function fetchSize(
		I_ByteSource $source,
		int $tag, int $high,
		int|bool $tag_tiny,	// Size_Tiny
		int $tag_8,			// Size_8
		int $tag_16,		// Size_16
		int|bool $tag_32	// Size_32
	): ?int {
		if($high === $tag_tiny) {
			return $tag_tiny ^ $tag; // Size_Tiny
		}
		return match($tag) {
			$tag_8 => (int)static::fetch($source, 'C', 1), // Size_8
			$tag_16 => (int)static::fetch($source, 'n', 2), // Size_16
			$tag_32 => (int)static::fetch($source, 'N', 4), // Size_32
			default => null
		};
	}
	protected static function checkList(I_ByteSource $source, int $tag): iterable {
		$high = $tag & 0xF0;
		// yield static::unpackBool($source, $tag); // Short-circuited
		yield static::unpackInteger($source, $tag, $high);
		// yield static::unpackFloat($source, $tag); // Short-circuited
		yield static::unpackString($source, $tag, $high);
		yield static::unpackList($source, $tag, $high);
		yield static::unpackMap($source, $tag, $high);
		yield static::unpackBytes($source, $tag, $high);
		yield static::unpackStruct($source, $tag, $high);
	}
	public static function unpack(I_ByteSource $source) {
		if($source->unreadSize() === 0) {
			return null;
		}
		$tag = ord($source->read(1));
		switch($tag) {
			case 0xC0: return null;
			case 0xC2: return false; // Short-circuit for False
			case 0xC3: return true; // Short-circuit for True
			case 0xC1: return (float)static::fetchEndian($source, 'd', 8); // Short-circuit for Float
		}

		foreach(static::checkList($source, $tag) as $v) {
			if($v !== null) { return $v; }
		}
		throw new PackEx('Invalid binary');
	}
	public static function unpackInteger(I_ByteSource $source, int $tag, int $high): ?int {
		if($high <= 0x70 || 0xF0 <= $high) {
			return (int)unpack('c', chr($tag))[1];
		}
		return match($tag) {
			0xC8 => (int)static::fetchEndian($source, 'c', 1), // INT_8
			0xC9 => (int)static::fetchEndian($source, 's', 2), // INT_16
			0xCA => (int)static::fetchEndian($source, 'l', 4), // INT_32
			0xCB => (int)static::fetchEndian($source, 'q', 8), // INT_64
			default => null
		};
	}
	public static function unpackFloat(I_ByteSource $source, int $tag): ?float {
		return match($tag) {
			0xC1 => (float)static::fetchEndian($source, 'd', 8),
			default => null
		};
	}
	public static function unpackBool(I_ByteSource $source, int $tag): ?bool {
		return match($tag) {
			0xC2 => false,
			0xC3 => true,
			default => null
		};
	}
	public static function unpackString(I_ByteSource $source, int $tag, int $high): ?string {
		$size = static::fetchSize($source, $tag, $high, 0x80, 0xD0, 0xD1, 0xD2);
		if($size === null) { return null; }
		return $source->read($size);
	}
	public static function unpackMap(I_ByteSource $source, int $tag, int $high): ?array {
		$size = static::fetchSize($source, $tag, $high, 0xA0, 0xD8, 0xD9, 0xDA);
		if($size === null) { return null; }
		$ret = [];
		for($i = 0; $i < $size; $i++) {
			$nxt = ord($source->read(1));
			$key = static::unpackString($source, $nxt, $nxt & 0xF0);
			if($key === null) {
				throw new PackEx('Invalid map key');
			}
			$ret[$key] = static::unpack($source);
		}
		return $ret;
	}
	public static function unpackList(I_ByteSource $source, int $tag, int $high): ?array {
		$size = static::fetchSize($source, $tag, $high, 0x90, 0xD4, 0xD5, 0xD6);
		if($size === null) { return null; }
		$ret = [];
		for($i = 0; $i < $size; $i++) {
			$ret[] = static::unpack($source);
		}
		return $ret;
	}
	public static function unpackBytes(I_ByteSource $source, int $tag, int $high): ?Bytes {
		$size = static::fetchSize($source, $tag, $high, false, 0xCC, 0xCD, 0xCE);
		if($size === null) { return null; }
		return new Bytes($source->read($size));
	}
	public static function unpackStruct(I_ByteSource $source, int $tag, int $high): Structure|I_Structable|null {
		$size = static::fetchSize($source, $tag, $high, 0xB0, 0xDC, 0xDD, false);
		if($size === null) { return null; }
		$sig = ord($source->read(1));
		$fields = [];
		for($i = 0; $i < $size; $i++) {
			$fields[] = static::unpack($source);
		}
		$gstruct = new Structure($sig, ...$fields);
		// Convert to I_Structable
		$pstruct = match($gstruct->sig) {
			0x70 => null, // Short-circuit for Bolt Success Message,
			0x7E => null, // Short-circuit for Bolt Ignored Message,
			0x7F => null, // Short-circuit for Bolt Failure Message,
			0x71 => null, // Short-circuit for Bolt Record Message,
			0x4E => Struct\Node::fromStructure($gstruct),
			0x52 => Struct\Relationship::fromStructure($gstruct),
			0x72 => Struct\UnboundRelationship::fromStructure($gstruct),
			0x50 => Struct\Path::fromStructure($gstruct),
			0x44 => Struct\Date::fromStructure($gstruct),
			0x54 => Struct\Time::fromStructure($gstruct),
			0x74 => Struct\LocalTime::fromStructure($gstruct),
			0x46 => Struct\DateTime::fromStructure($gstruct),
			0x66 => Struct\DateTimeZoneId::fromStructure($gstruct),
			0x64 => Struct\LocalDateTime::fromStructure($gstruct),
			0x45 => Struct\Duration::fromStructure($gstruct),
			0x58 => Struct\Point2D::fromStructure($gstruct),
			0x59 => Struct\Point3D::fromStructure($gstruct),
			default => null
		};
		return ($pstruct === null) ? $gstruct : $pstruct;
	}
}