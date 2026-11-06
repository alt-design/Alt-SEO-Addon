<?php

namespace AltDesign\AltSeo;

use Illuminate\Support\Str;

// Facades
use Statamic\Facades\CP\Nav;
use AltDesign\AltSeo\Events\Seo;
use Statamic\Facades\Permission;
use Facades\Statamic\Version;

// Providers
use Illuminate\Support\Facades\Event;
use Statamic\Providers\AddonServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package  AltDesign\AltSeo
 * @author   Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class ServiceProvider extends AddonServiceProvider
{
    /**
     * @var string - Sets the namespace of the addon
     */
    protected $viewNamespace = 'alt-seo';

    /**
     * @var string[] - Bring in the tags for use
     */
    protected $tags = [
        \AltDesign\AltSeo\Tags\AltSeo::class,
    ];

    /**
     * @var string[] - Register our routes (mainly for settings tbh).
     */
    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    /**
     * Pull the option into the CP Nav.
     */
    public function addToNav()
    {
        Nav::extend(function ($nav) {
            $nav->content('Alt SEO')
                ->section('Alt Tools')
                ->can('view alt-seo')
                ->route('alt-seo.index')
                ->icon(config('alt-seo.alt_seo_icon'));
        });
    }

    /**
     * Register our permissions, so we can control who can see the settings.
     *
     * @return void
     */
    public function registerPermissions()
    {
        Permission::register('view alt-seo')
                  ->label('View Alt SEO Settings');
    }

    /**
     * Register our events.
     *
     * @return void
     */
    public function registerEvents()
    {
        Event::subscribe(Seo::class);
    }

    /**
     * Load our views.
     *
     * @return void
     */
    protected function loadViews()
    {
        $path = __DIR__.'/resources/views';
        $published = resource_path('views/vendor/alt-seo');

        if (file_exists($published)) {
            $path = $published;
        }

        $this->loadViewsFrom($path, 'alt-seo');
    }

    /**
     * Allow views to be published.
     *
     * @return void
     */
    protected function publishViews(): void
    {
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/alt-seo'),
        ], 'alt-seo');
    }

    /**
     * Allow blueprints to be published.
     *
     * @return void
     */
    protected function publishBlueprints(): void
    {
        $this->publishes([
            __DIR__.'/../resources/blueprints' => resource_path('blueprints/vendor/alt-seo'),
        ], 'alt-seo');
    }

    /**
     * Statamic boot method.
     *
     * @return void
     */
    public function bootAddon()
    {
        $this->publishViews();
        $this->publishBlueprints();
        $this->loadViews();
        $this->addToNav();
        $this->registerPermissions();
        $this->registerEvents();

        // Statamic V6 - unbind the settings blueprint to remove the default settings page and permissions
        // as we are handling this manually instead
        // Statamic >= V6
        if(intval(Str::before(Version::get(), '.')) >= 6) {
            app()->offsetUnset("statamic.addons.alt-seo.settings_blueprint");
        }
    }
}


