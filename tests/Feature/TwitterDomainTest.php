<?php

use AltDesign\AltSeo\Tags\AltSeo;
use Illuminate\Http\Request;
use Statamic\Facades\Site;

function twitterDomainFor(string $url, ?\Statamic\Sites\Site $site = null): string
{
    app()->instance('request', Request::create($url));

    return (new AltSeo)
        ->setContext(['site' => $site ?? Site::default()])
        ->getTwitterDomain();
}

it('uses the request host on a site without a sites.yaml', function () {
    expect(Site::default()->url())->toBe('/');

    expect(twitterDomainFor('https://www.example.org/training'))->toBe('www.example.org');
});

it('uses the host of a site configured with an absolute url', function () {
    Site::setSites([
        'default' => ['name' => 'Example', 'url' => 'https://www.example.org/', 'locale' => 'en_GB'],
    ]);

    expect(twitterDomainFor('https://www.example.org/training'))->toBe('www.example.org');
});

it('gives each site in a multisite install its own host', function () {
    Site::setSites([
        'english' => ['name' => 'Example', 'url' => 'https://www.example.org/', 'locale' => 'en_GB'],
        'french' => ['name' => 'Exemple', 'url' => 'https://www.example.fr/', 'locale' => 'fr_FR'],
    ]);

    expect(twitterDomainFor('https://www.example.org/training', Site::get('english')))->toBe('www.example.org')
        ->and(twitterDomainFor('https://www.example.fr/formation', Site::get('french')))->toBe('www.example.fr');
});

it('uses the request host for multisite installs on one domain with relative urls', function () {
    Site::setSites([
        'english' => ['name' => 'Example', 'url' => '/', 'locale' => 'en_GB'],
        'french' => ['name' => 'Exemple', 'url' => '/fr/', 'locale' => 'fr_FR'],
    ]);

    expect(twitterDomainFor('https://www.example.org/fr/formation', Site::get('french')))->toBe('www.example.org');
});
