# Neo4j PackStream (v1)

## Installation:
```
composer require anshu-krishna/neo4j-pack-stream
```
## Requirements:
- PHP >= 8.1

## Example:

```php
// For packing use
Krishna\PackStream\Packer::pack(mixed $value): iterable;
// Note: pack() is a generator. It yields binary strings.

// For unpacking use
Krishna\PackStream\Unpacker::unpack(I_ByteSource $source): mixed;
// Source must implement I_ByteSource interface.
```


<table>
<colgroup>
<col style="width: 30%" />
<col style="width: 40%" />
<col style="width: 30%" />
</colgroup>
<thead>
<tr class="header">
<th>Data</th>
<th>Packed (Hex Representation)</th>
<th>Unpacked</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td><pre><code>null: null</code></pre></td>
<td><pre><code>C0</code></pre></td>
<td><pre><code>null: null</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>int: 25</code></pre></td>
<td><pre><code>19</code></pre></td>
<td><pre><code>int: 25</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>float: 15.5</code></pre></td>
<td><pre><code>C1 40 2F 00  00 00 00 00  00</code></pre></td>
<td><pre><code>float: 15.5</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>bool: true</code></pre></td>
<td><pre><code>C3</code></pre></td>
<td><pre><code>bool: true</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>bool: false</code></pre></td>
<td><pre><code>C2</code></pre></td>
<td><pre><code>bool: false</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>string: &quot;Hello world&quot;</code></pre></td>
<td><pre><code>8B 48 65 6C  6C 6F 20 77  6F 72 6C 64</code></pre></td>
<td><pre><code>string: &quot;Hello world&quot;</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>array: [
    1,
    2.3,
    true,
    &quot;abc&quot;
]</code></pre></td>
<td><pre><code>94 01 C1 40  02 66 66 66  66 66 66 C3  83 61 62 63</code></pre></td>
<td><pre><code>array: [
    1,
    2.3,
    true,
    &quot;abc&quot;
]</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>array: {
    &quot;a&quot;: 10,
    &quot;b&quot;: 20
}</code></pre></td>
<td><pre><code>A2 81 61 0A  81 62 14</code></pre></td>
<td><pre><code>array: {
    &quot;a&quot;: 10,
    &quot;b&quot;: 20
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>Bytes: {
    &quot;length&quot;: 5,
    &quot;hex&quot;: &quot;0102030405&quot;
}</code></pre></td>
<td><pre><code>CC 05 01 02  03 04 05</code></pre></td>
<td><pre><code>Bytes: {
    &quot;length&quot;: 5,
    &quot;hex&quot;: &quot;0102030405&quot;
}</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>Node: {
    &quot;id&quot;: 45,
    &quot;labels&quot;: [
        &quot;abc&quot;,
        &quot;def&quot;
    ],
    &quot;properties&quot;: {
        &quot;xyz&quot;: 55
    }
}</code></pre></td>
<td><pre><code>B3 4E 2D 92  83 61 62 63  83 64 65 66  A1 83 78 79  7A 37</code></pre></td>
<td><pre><code>Node: {
    &quot;id&quot;: 45,
    &quot;labels&quot;: [
        &quot;abc&quot;,
        &quot;def&quot;
    ],
    &quot;properties&quot;: {
        &quot;xyz&quot;: 55
    }
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>Relationship: {
    &quot;id&quot;: 96,
    &quot;startNodeId&quot;: 45,
    &quot;endNodeId&quot;: 47,
    &quot;type&quot;: &quot;example&quot;,
    &quot;properties&quot;: {
        &quot;prop&quot;: &quot;test&quot;
    }
}</code></pre></td>
<td><pre><code>B5 52 60 2D  2F 87 65 78  61 6D 70 6C  65 A1 84 70  72 6F 70 84
74 65 73 74</code></pre></td>
<td><pre><code>Relationship: {
    &quot;id&quot;: 96,
    &quot;startNodeId&quot;: 45,
    &quot;endNodeId&quot;: 47,
    &quot;type&quot;: &quot;example&quot;,
    &quot;properties&quot;: {
        &quot;prop&quot;: &quot;test&quot;
    }
}</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>UnboundRelationship: {
    &quot;id&quot;: 100,
    &quot;type&quot;: &quot;unbound-example&quot;,
    &quot;properties&quot;: []
}</code></pre></td>
<td><pre><code>B3 72 64 8F  75 6E 62 6F  75 6E 64 2D  65 78 61 6D  70 6C 65 A0</code></pre></td>
<td><pre><code>UnboundRelationship: {
    &quot;id&quot;: 100,
    &quot;type&quot;: &quot;unbound-example&quot;,
    &quot;properties&quot;: []
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>Path: {
    &quot;nodes&quot;: [
        {
            &quot;id&quot;: 45,
            &quot;labels&quot;: [
                &quot;abc&quot;,
                &quot;def&quot;
            ],
            &quot;properties&quot;: []
        }
    ],
    &quot;rels&quot;: [
        {
            &quot;id&quot;: 100,
            &quot;type&quot;: &quot;unbound&quot;,
            &quot;properties&quot;: []
        }
    ],
    &quot;ids&quot;: [
        15
    ]
}</code></pre></td>
<td><pre><code>B3 50 91 B3  4E 2D 92 83  61 62 63 83  64 65 66 A0  91 B3 72 64
87 75 6E 62  6F 75 6E 64  A0 91 0F</code></pre></td>
<td><pre><code>Path: {
    &quot;nodes&quot;: [
        {
            &quot;id&quot;: 45,
            &quot;labels&quot;: [
                &quot;abc&quot;,
                &quot;def&quot;
            ],
            &quot;properties&quot;: []
        }
    ],
    &quot;rels&quot;: [
        {
            &quot;id&quot;: 100,
            &quot;type&quot;: &quot;unbound&quot;,
            &quot;properties&quot;: []
        }
    ],
    &quot;ids&quot;: [
        15
    ]
}</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>Date: {
    &quot;days&quot;: 15
}</code></pre></td>
<td><pre><code>B1 44 0F</code></pre></td>
<td><pre><code>Date: {
    &quot;days&quot;: 15
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>Time: {
    &quot;nanoseconds&quot;: 100000,
    &quot;tz_offset_seconds&quot;: 50
}</code></pre></td>
<td><pre><code>B2 54 CA 00  01 86 A0 32</code></pre></td>
<td><pre><code>Time: {
    &quot;nanoseconds&quot;: 100000,
    &quot;tz_offset_seconds&quot;: 50
}</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>LocalTime: {
    &quot;nanoseconds&quot;: 100000
}</code></pre></td>
<td><pre><code>B1 74 CA 00  01 86 A0</code></pre></td>
<td><pre><code>LocalTime: {
    &quot;nanoseconds&quot;: 100000
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>DateTime: {
    &quot;seconds&quot;: 50,
    &quot;nanoseconds&quot;: 100,
    &quot;tz_offset_seconds&quot;: 100
}</code></pre></td>
<td><pre><code>B3 46 32 64  64</code></pre></td>
<td><pre><code>DateTime: {
    &quot;seconds&quot;: 50,
    &quot;nanoseconds&quot;: 100,
    &quot;tz_offset_seconds&quot;: 100
}</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>DateTimeZoneId: {
    &quot;seconds&quot;: 45,
    &quot;nanoseconds&quot;: 10005,
    &quot;tz_id&quot;: &quot;Asia\/Kolkata&quot;
}</code></pre></td>
<td><pre><code>B3 66 2D C9  27 15 8C 41  73 69 61 2F  4B 6F 6C 6B  61 74 61</code></pre></td>
<td><pre><code>DateTimeZoneId: {
    &quot;seconds&quot;: 45,
    &quot;nanoseconds&quot;: 10005,
    &quot;tz_id&quot;: &quot;Asia\/Kolkata&quot;
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>LocalDateTime: {
    &quot;seconds&quot;: 100000000,
    &quot;nanoseconds&quot;: 155
}</code></pre></td>
<td><pre><code>B2 64 CA 05  F5 E1 00 C9  00 9B</code></pre></td>
<td><pre><code>LocalDateTime: {
    &quot;seconds&quot;: 100000000,
    &quot;nanoseconds&quot;: 155
}</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>Duration: {
    &quot;months&quot;: 10,
    &quot;days&quot;: 20,
    &quot;seconds&quot;: 0,
    &quot;nanoseconds&quot;: 5
}</code></pre></td>
<td><pre><code>B4 45 0A 14  00 05</code></pre></td>
<td><pre><code>Duration: {
    &quot;months&quot;: 10,
    &quot;days&quot;: 20,
    &quot;seconds&quot;: 0,
    &quot;nanoseconds&quot;: 5
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>Point2D: {
    &quot;srid&quot;: 105,
    &quot;x&quot;: 10.2,
    &quot;y&quot;: 15.3
}</code></pre></td>
<td><pre><code>B3 58 69 C1  40 24 66 66  66 66 66 66  C1 40 2E 99  99 99 99 99
9A</code></pre></td>
<td><pre><code>Point2D: {
    &quot;srid&quot;: 105,
    &quot;x&quot;: 10.2,
    &quot;y&quot;: 15.3
}</code></pre></td>
</tr>
<tr class="even">
<td><pre><code>Point3D: {
    &quot;srid&quot;: 101,
    &quot;x&quot;: 5.2,
    &quot;y&quot;: 10.7,
    &quot;z&quot;: 4.9
}</code></pre></td>
<td><pre><code>B4 59 65 C1  40 14 CC CC  CC CC CC CD  C1 40 25 66  66 66 66 66
66 C1 40 13  99 99 99 99  99 9A</code></pre></td>
<td><pre><code>Point3D: {
    &quot;srid&quot;: 101,
    &quot;x&quot;: 5.2,
    &quot;y&quot;: 10.7,
    &quot;z&quot;: 4.9
}</code></pre></td>
</tr>
<tr class="odd">
<td><pre><code>Structure: {
    &quot;fields&quot;: [
        {
            &quot;a&quot;: 5,
            &quot;b&quot;: 10
        }
    ],
    &quot;sig&quot;: 112
}</code></pre></td>
<td><pre><code>B1 70 A2 81  61 05 81 62  0A</code></pre></td>
<td><pre><code>Structure: {
    &quot;fields&quot;: [
        {
            &quot;a&quot;: 5,
            &quot;b&quot;: 10
        }
    ],
    &quot;sig&quot;: 112
}</code></pre></td>
</tr>
</tbody>
</table>
