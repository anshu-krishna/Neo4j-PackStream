<?php
namespace Krishna\PackStream;

class Structure {
	public readonly array $fields;
	public function __construct(public readonly int $sig, mixed ...$fields) {
		$this->fields = $fields;
	}
}