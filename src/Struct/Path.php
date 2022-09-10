<?php
namespace Krishna\PackStream\Struct;

use Krishna\PackStream\Helper\JSON;
use Krishna\PackStream\{Structure, I_Structable, PackEx};
use Krishna\PackStream\Helper\{ListType, T_MakeStructable, T_CachedToString};

class Path implements I_Structable, \Stringable {
	use T_MakeStructable, T_CachedToString;
	const SIG = 0x50;
	
	public function __construct(
		public readonly array $nodes,
		public readonly array $rels,
		public readonly array $ids
	) {
		if(ListType::notNodeList($this->nodes)) {
			throw new PackEx('Field nodes must only contain ' . Node::class . ' objects');
		}
		if(ListType::notURelList($this->rels)) {
			throw new PackEx('Field rels must only contain ' . UnboundRelationship::class . ' objects');
		}
		if(ListType::notIntList($this->ids)) {
			throw new PackEx('Field ids must only contain int items');
		}
	}
	public function _stringify_(): string {
		$segments = [];
		for ($i = 0, $max = count($this->nodes) - 1; $i < $max; $i++) {
			$segments[] = [
				'start' => $this->nodes[$i],
				'relationship' => $this->rels[$i],
				'end' => $this->nodes[$i + 1]
			];
		}
		$obj = [
			'start' => $this->nodes[0] ?? null,
			'end' => $this->nodes[count($this->nodes) - 1] ?? null,
			'segments' => $segments,
			'length' => count($this->ids) - 1
		];
		return JSON::encode($obj);
	}
	public function toStructure() : Structure {
		return new Structure(static::SIG, $this->nodes, $this->rels, $this->ids);
	}
}