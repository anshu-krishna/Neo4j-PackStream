<?php
require_once "vendor/autoload.php";

use Krishna\PackStream\{Bytes, I_ByteSource, Packer, Unpacker, Struct, Structure};

final class Example implements I_ByteSource {
	// I_ByteSource related
	private array|string $store;

	private ?int $length;
	private int $offset = 0;

	private function __construct() {}
	
	public function read(int $len = 0): string {
		$remaning = $this->length - $this->offset;
		if($len < 1) { // Read all remaining
			$len = $remaning;
		} elseif ($len > $remaning) {
			throw new Exception("Cannot read {$len} bytes; {$remaning} bytes remaining");
		}
		$part = mb_strcut($this->store, $this->offset, $len, '8bit');
		$this->offset += $len;
		return $part;
	}
	public function unreadSize(): int {
		return $this->length - $this->offset;
	}

	// Creation related
	public static int $rowSize = 20, $wordSize = 4;

	private static function hexify(string $data, int $rowSize, int $wordSize): string {
		$rowEnd = $rowSize - 1; $wordEnd = $wordSize - 1;
		$i = -1; $hex = [];
		foreach(str_split(strtoupper(unpack('H*', $data)[1]), 2) as $byte) {
			$i++;
			$hex[] = $byte;
			if($i % $rowSize === $rowEnd) { $hex[] = "\n"; }
			elseif($i % $wordSize === $wordEnd) { $hex[] = '  '; }
			else { $hex[] = ' '; }
		}
		return trim(implode('', $hex));
	}
	private static function typeName(mixed $data) {
		if(array_key_exists('fullname', $_GET)) {
			return '<strong>' . get_debug_type($data) . '</strong>';
		}
		$t = explode('\\', get_debug_type($data));
		return '<strong>' . end($t) . '</strong>';
	}
	private static function encode(mixed $data) {
		$type = static::typeName($data);
		$out = json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT);
		if($out === false) { return "{$type}: Encode Error"; }

		return "{$type}: " . htmlentities(
			$out,
			flags: ENT_HTML5 | ENT_SUBSTITUTE,
			encoding: 'UTF-8'
		);
	}
	
	public static function create(mixed $data, ?int $rowSize = null, ?int $wordSize = null)  {
		echo '<tr><td><pre>', static::encode($data), '</pre></td>';

		$packed = new self;
		$packed->store = [];

		// Load packed data
		foreach(Packer::pack($data) as $bytes) {
			$packed->store[] = $bytes;
		}

		// Make readable
		$packed->store = implode('', $packed->store);
		$packed->length = mb_strlen($packed->store, '8bit');

		echo '<td><pre>', static::hexify($packed->store, $rowSize ?? static::$rowSize, $wordSize ?? static::$wordSize), '</pre></td>';

		echo '<td><pre>', static::encode(Unpacker::unpack($packed)), '</pre></td></tr>';
	}	
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PackStream (v1) Examples</title>
<style>
* { box-sizing: border-box; }
body {
	padding: 0; margin: 0;
	background: #212121; color: white;
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace;
}
table {
	margin: auto;
	border-collapse: collapse;
	border: 1px solid grey;
	max-width: 90%;
	position: relative;
}
th, td {
	padding: 0.5em 0.75em;
	border-bottom: 1px solid grey;
}
:is(th, td):not(:first-child) {
	border-left: 1px solid grey;
}
td {
	vertical-align: top;
}
pre {
	white-space: pre-wrap;
	font-size: 1em;
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace;
}
h2 {
	text-align: center;
}
thead th {
	background: rgba(0, 0, 0, 0.8);
	position: sticky;
	top: 0;
}
tbody tr:nth-child(even) {
	background: #121212;
}
</style>
</head>
<body>
<h2>PackStream (v1)</h2>
<table>
	<thead><tr><th>Data</th><th>Packed</th><th>Unpacked</th></tr></thead>
	<tbody>
<?php
	Example::$rowSize = 20;

	// General data
	Example::create(null);
	Example::create(25);
	Example::create(15.5);
	Example::create(true);
	Example::create(false);
	Example::create('Hello world');
	Example::create([1, 2.3, true, 'abc']);
	Example::create(['a' => 10, 'b' => 20]);
	Example::create(new Bytes(hex2bin('0102030405')));
	
	// Structued data
	Example::create(new Struct\Node(45, ['abc', 'def'], ['xyz' => 55]));
	Example::create(new Struct\Relationship(96, 45, 47, 'example', ['prop' => 'test']));
	Example::create(new Struct\UnboundRelationship(100, 'unbound-example', []));
	Example::create(new Struct\Path([
		new Struct\Node(45, ['abc', 'def'], [])
	], [
		new Struct\UnboundRelationship(100, 'unbound', [])
	], [
		15
	]));
	Example::create(new Struct\Date(15));
	Example::create(new Struct\Time(1e5, 50));
	Example::create(new Struct\LocalTime(1e5));
	Example::create(new Struct\DateTime(50, 100, 100));
	Example::create(new Struct\DateTimeZoneId(45, 1e4 + 5, 'Asia/Kolkata'));
	Example::create(new Struct\LocalDateTime(1e8, 155));
	Example::create(new Struct\Duration(10, 20, 0, 5));
	Example::create(new Struct\Point2D(105, 10.2, 15.3));
	Example::create(new Struct\Point3D(101, 5.2, 10.7, 4.9));
	Example::create(new Structure(0x70, ['a' => 5, 'b' => 10]));
?>
	</tbody>
</table>
</body>
</html>