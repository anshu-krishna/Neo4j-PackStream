<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\Helper\JSON;
use Krishna\PackStream\{Structure, I_Structable, PackEx};
use Krishna\PackStream\Helper\{ListType, T_MakeStructable, T_CachedToString};

class Node implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x4E;

	public function __construct(
		public readonly int $id,
		public readonly array $labels,
		public readonly array $properties
	) {
		if(ListType::notStringList($this->labels)) {
			throw new PackEx('Field labels must only contain string items');
		}
	}
	public function _stringify_(): string {
		return JSON::encode([
			'id' => $this->id,
			'labels' => $this->labels,
			'properties' => $this->properties
		]);
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->id, $this->labels, (object)$this->properties);
	}
}