<?php
namespace Krishna\PackStream;

final class Bytes implements \ArrayAccess, \Countable, \Stringable, \IteratorAggregate, \JsonSerializable {
	public readonly int $length;
	protected int $curr = 0;
	public function __construct(public readonly string $bytes) {
		$this->length = mb_strlen($this->bytes, '8bit');
	}
	public function offsetExists(mixed $offset): bool {
		$offset = intval($offset);
		return $offset >= 0 && $offset < $this->length;
	}
	public function offsetGet(mixed $offset): ?string {
		$offset = intval($offset);
		if($offset >= 0 && $offset < $this->length) {
			return mb_strcut($this->bytes, $offset, 1, '8bit');
		}
		return null;
	}
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new PackEx('Bytes is readonly');
	}
	public function offsetUnset(mixed $offset): void {
		throw new PackEx('Bytes is readonly');
	}
	public function count(): int {
		return $this->length;
	}
	public function __toString(): string {
		return $this->bytes;
	}
	public function getIterator(): \Traversable {
		for($i = 0; $i < $this->length; $i++) {
			yield mb_strcut($this->bytes, $i, 1, '8bit');
		}
	}
	public function jsonSerialize(): mixed {
		return [
			'length' => $this->length,
			'hex' => unpack('H*', $this->bytes)[1]
		];
	}
}