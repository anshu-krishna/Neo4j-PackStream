<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\Helper\JSON;
use Krishna\PackStream\{Structure, I_Structable};
use Krishna\PackStream\Helper\{T_MakeStructable, T_CachedToString};

class UnboundRelationship implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x72;

	public function __construct(
		public readonly int $id,
		public readonly string $type,
		public readonly array $properties
	) {}
	public function _stringify_(): string {
		return JSON::encode([
			'id' => $this->id,
			'type' => $this->type,
			'properties' => $this->properties
		]);
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->id, $this->type, (object)$this->properties);
	}
}