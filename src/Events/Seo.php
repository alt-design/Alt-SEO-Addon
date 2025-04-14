<?php namespace AltDesign\AltSeo\Events;

use Illuminate\Support\Str;
use Statamic\Events;
use Statamic\Facades\Blink;
use Statamic\Fields\BlueprintRepository;
use AltDesign\AltSeo\Helpers\Data;

/**
 * Class Seo
 *
 * @package  AltDesign\AltSeo
 * @author   Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class Seo
{
    /**
     * Sets the events to listen for
     *
     * @var string[]
     */
    protected $events = [
        Events\EntryBlueprintFound::class => 'addSeoData',
        Events\TermBlueprintFound::class => 'addSeoData',
    ];

    /**
     * Subscribe to events
     *
     * @param $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(Events\EntryBlueprintFound::class, self::class.'@'.'addSeoData');
        $events->listen(Events\TermBlueprintFound::class, self::class.'@'.'addSeoData');
    }

    /**
     * Adds the SEO fields to the blueprint
     *
     * @param $event
     * @return void
     */
    public function addSeoData($event)
    {
        $data = new Data('settings');
        //check explicit include and do nothing if not included
        $seoInclude = $data->get('alt_seo_asset_container_include');
        if ($seoInclude != null && !in_array(Str::afterLast($event->blueprint->namespace(), '.'), $seoInclude)) {
            return;
        }

        // Grab the old directory just in case
        if ($event->blueprint->initialPath()) {
            $oldDirectory = with(new BlueprintRepository)->directory();
        }

        // Grab the tabs - there may be a better way of doing this?
        $blueprint = with(new BlueprintRepository)->setDirectory(__DIR__ . '/../../resources/blueprints')->find(config('alt-seo.alt_seo_enable_schema') ? 'seo-with-schema' : 'seo');
        $blueprintReady = $event->blueprint->contents();

        //Global override
        if ($data->get('alt_seo_asset_container') !== null) {
            $contents = $blueprint->contents();
            $index = array_search('alt_seo_social_image', array_column($contents['tabs']['alt_seo']['sections'][0]['fields'], 'handle'));
            $contents['tabs']['alt_seo']['sections'][0]['fields'][$index]['field']['container'] = $data->get('alt_seo_asset_container');
            $blueprint->setContents($contents);
        }

        //Pre-collection override
        if ($data->get('alt_seo_collection_asset_containers')) {
            $containerSettings = $data->get('alt_seo_collection_asset_containers');
            $thisEntryHandle = $event->blueprint->parent()->handle ?? $event->entry->collection->handle ?? '';
            $contents = $blueprint->contents();

            foreach ($containerSettings as $setting) {
                if ($setting['collection'] === $thisEntryHandle) {
                    $contents['tabs']['alt_seo']['sections'][0]['fields'][6]['field']['container'] = $setting['asset_handle'] ?? 'assets';
                }
            }
            $blueprint->setContents($contents);
        }
        $blueprintReady['tabs'] = array_merge($blueprintReady['tabs'], $blueprint->contents()['tabs']);

        // Set the contents
        Blink::forget("blueprint-contents-{$event->blueprint->namespace()}-{$event->blueprint->handle()}");
        $event->blueprint->setContents($blueprintReady);

        // Reset the directory to the old one
        if (isset($oldDirectory)) {
            with(new BlueprintRepository)->directory($oldDirectory);
        }
    }
}
