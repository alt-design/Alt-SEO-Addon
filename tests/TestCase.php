<?php

namespace AltDesign\AltSeo\Tests;

use AltDesign\AltSeo\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // The addon checks the Statamic version as it boots, which happens before
        // AddonTestCase gets a chance to mock it out.
        \Facades\Statamic\Version::shouldReceive('get')->andReturn(
            ltrim(\Composer\InstalledVersions::getPrettyVersion('statamic/cms'), 'v')
        );

        // A local disk serving from the site itself, and one standing in for S3 or a CDN,
        // which differ only in whether the disk url is absolute.
        $app['config']->set('filesystems.disks.assets', [
            'driver' => 'local',
            'root' => __DIR__.'/__fixtures__/assets',
            'url' => '/assets',
        ]);

        $app['config']->set('filesystems.disks.cdn', [
            'driver' => 'local',
            'root' => __DIR__.'/__fixtures__/assets',
            'url' => 'https://cdn.example.com',
        ]);

        // The settings helper reads content/alt-seo/settings.yaml from the standard disk.
        $app['config']->set('filesystems.disks.local.root', __DIR__.'/__fixtures__/storage');
        $app->bind('filesystems.paths.standard', fn () => __DIR__.'/__fixtures__/storage');
    }

    protected function writeSettings(array $settings): void
    {
        $path = __DIR__.'/__fixtures__/storage/content/alt-seo';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($path.'/settings.yaml', \Statamic\Facades\YAML::dump($settings));
    }

    protected function tearDown(): void
    {
        $settings = __DIR__.'/__fixtures__/storage/content/alt-seo/settings.yaml';

        if (file_exists($settings)) {
            unlink($settings);
        }

        parent::tearDown();
    }
}
