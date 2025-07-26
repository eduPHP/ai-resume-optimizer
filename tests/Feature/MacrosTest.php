<?php

test('a collection can be deep merged', function () {
    $original = collect([
        'foo' => 'bar',
        'bar' => [
            1 => 'baz',
            2 => 'foo'
        ]
    ]);

    $merged = $original->deepMerge([
        'foo' => 'test',
        'bar' => [
            3 => 'boohoo',
        ],
    ]);

    expect($merged['bar'])->toContain('baz', 'foo', 'boohoo');
});
