<?php

use AltDesign\AltSeo\Tags\AltSeo;
use Statamic\Facades\Asset;
use Statamic\Facades\Site;

function socialImageFor(array $context = []): string
{
    return (new AltSeo)
        ->setContext(array_merge(['site' => Site::default()], $context))
        ->getSocialImage();
}

it('returns an absolute url for an asset on a local disk', function () {
    $url = socialImageFor(['alt_seo_social_image' => Asset::find('assets::img.jpg')]);

    expect($url)->toBe('http://localhost/assets/img.jpg');
});

it('leaves the container url alone for an asset on an external disk', function () {
    $url = socialImageFor(['alt_seo_social_image' => Asset::find('cdn::img.jpg')]);

    expect($url)->toBe('https://cdn.example.com/img.jpg');
});

it('prefers the image set on the entry over the default', function () {
    $this->writeSettings(['alt_seo_social_image_default' => 'img.jpg']);

    $url = socialImageFor(['alt_seo_social_image' => Asset::find('assets::entry.jpg')]);

    expect($url)->toBe('http://localhost/assets/entry.jpg');
});

it('falls back to the default image when the entry has none', function () {
    $this->writeSettings(['alt_seo_social_image_default' => 'img.jpg']);

    expect(socialImageFor(['alt_seo_social_image' => null]))->toBe('http://localhost/assets/img.jpg');
});

it('falls back to the default image from the settings', function () {
    $this->writeSettings(['alt_seo_social_image_default' => 'img.jpg']);

    expect(socialImageFor())->toBe('http://localhost/assets/img.jpg');
});

it('uses the configured asset container for the default image', function () {
    $this->writeSettings([
        'alt_seo_asset_container' => 'cdn',
        'alt_seo_social_image_default' => 'img.jpg',
    ]);

    expect(socialImageFor())->toBe('https://cdn.example.com/img.jpg');
});

it('returns an empty string when no image is set', function () {
    $this->writeSettings([]);

    expect(socialImageFor())->toBe('');
});
