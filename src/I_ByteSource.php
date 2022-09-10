<?php
namespace Krishna\PackStream;

interface I_ByteSource {
	public function read(int $length): string;
	public function unreadSize(): int;
}