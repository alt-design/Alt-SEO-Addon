<?php

use Statamic\Modifiers\CoreModifiers;

/**
 * The meta view drops entry values into element text and into content="..."
 * attributes, and Antlers does not escape output. A title carrying markup used
 * to render as markup, and a double quote closed the attribute early.
 *
 * The values sit inside yield fallbacks, which render empty unless a section
 * supplies them, so these assert the shape of the template rather than its
 * output: every value must be escaped, including any added later.
 */
function metaView(): string
{
    return file_get_contents(__DIR__.'/../../src/resources/views/meta.antlers.html');
}

it('escapes every value it prints', function () {
    $view = metaView();

    preg_match_all('/\{\{ yield:(\w+) \}\}\{\{ (.+?) \}\}\{\{ \/yield:\1 \}\}/', $view, $matches, PREG_SET_ORDER);

    expect($matches)->not->toBeEmpty();

    $unescaped = array_filter($matches, fn ($m) => ! str_contains($m[2], '| sanitize'));

    expect($unescaped)->toBe([], 'these meta values are printed unescaped: '
        .implode(', ', array_map(fn ($m) => $m[1], $unescaped)));
});

it('escapes a value for both element text and an attribute', function () {
    $hostile = 'Boilersuit </script><img src=x onerror=alert(1)> 12" & \'co\'';

    $escaped = (new CoreModifiers)->sanitize($hostile, []);

    // nothing left that can close a tag or an attribute
    expect($escaped)->not->toContain('<')
        ->and($escaped)->not->toContain('>')
        ->and($escaped)->not->toContain('"')
        // and it is still the same string once the browser decodes it
        ->and(html_entity_decode($escaped, ENT_QUOTES))->toBe($hostile);
});

it('leaves a value with no markup alone once decoded', function () {
    $plain = 'Beeswift ARC153 Boilersuit';

    expect(html_entity_decode((new CoreModifiers)->sanitize($plain, []), ENT_QUOTES))->toBe($plain);
});
