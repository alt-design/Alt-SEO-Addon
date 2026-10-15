<?php

use AltDesign\AltSeo\Tags\AltSeo;
use Illuminate\Http\Request;
use Statamic\Facades\Site;

function canonicalFor(string $url, array $context = []): string
{
    app()->instance('request', Request::create($url));

    return (string) (new AltSeo)
        ->setContext(array_merge(['site' => Site::default()], $context))
        ->getCanonical();
}

it('drops tracking parameters from the fallback canonical', function () {
    expect(canonicalFor('http://localhost/training?utm_source=newsletter&utm_campaign=autumn'))
        ->toBe('http://localhost/training');
});

it('keeps the page number by default so a paginated listing points at itself', function () {
    expect(canonicalFor('http://localhost/news?utm_source=newsletter&page=3'))
        ->toBe('http://localhost/news?page=3');
});

it('keeps only the query strings allowed in the config, in a stable order', function () {
    config()->set('alt-seo.alt_seo_canonical_allowed_query_strings', ['page', 'category']);

    expect(canonicalFor('http://localhost/news?page=2&utm_source=newsletter&category=fire+safety'))
        ->toBe('http://localhost/news?category=fire%20safety&page=2');
});

it('drops every query string when the config allows none', function () {
    config()->set('alt-seo.alt_seo_canonical_allowed_query_strings', []);

    expect(canonicalFor('http://localhost/news?page=2'))->toBe('http://localhost/news');
});

it('uses the canonical set on the entry', function () {
    expect(canonicalFor('http://localhost/training?utm_source=newsletter', [
        'alt_seo_canonical_url' => 'https://example.com/elsewhere',
    ]))->toBe('https://example.com/elsewhere');
});
