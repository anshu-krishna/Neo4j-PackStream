<?php
namespace Krishna\PackStream\Helper;

trait T_CachedToString {
	protected ?string $_toStringCache = null;
	abstract protected function _stringify_(): string;
	public function __toString(): string {
		$this->_toStringCache ??= $this->_stringify_();
		return $this->_toStringCache;
	}
}