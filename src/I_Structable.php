<?php
namespace Krishna\PackStream;

interface I_Structable {
	public static function fromStructure(Structure $struct): ?static;
	public function toStructure() : Structure;
}