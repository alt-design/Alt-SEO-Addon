<?php namespace AltDesign\AltSeo\Events;

use Statamic\Events;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Entry;
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
    }

    /**
     * Adds the SEO fields to the blueprint
     *
     * @param $event
     * @return void
     */
    public function addSeoData($event)
    {
        // Grab the old directory just in case
        $oldDirectory = Blueprint::directory();

        // Grab the tabs - there may be a better way of doing this?
        $blueprint = Blueprint::setDirectory(__DIR__ . '/../../resources/blueprints')->find('seo');
        $blueprintReady = $event->blueprint->contents();
        $data = new Data('settings');

        //Global override
        if ($data->get('alt_seo_asset_container') !== null)
        {
            $contents = $blueprint->contents();
            $contents['tabs']['alt_seo']['sections'][0]['fields'][6]['field']['container'] = $data->get('alt_seo_asset_container');
            $blueprint->setContents($contents);
        }
        
        //Pre-collection overridex
        if ($data->get('alt_seo_collection_asset_containers'))
        {
            $containerSettings = $data->get('alt_seo_collection_asset_containers');
            $thisEntryHandle = $event->entry->collection->handle;
            $contents = $blueprint->contents();
            foreach($containerSettings as $setting) {
                if ($setting['collection'] === $thisEntryHandle)
                {
                    $contents['tabs']['alt_seo']['sections'][0]['fields'][6]['field']['container'] = $setting['asset_handle'] ?? 'assets';
                }
            }
            $blueprint->setContents($contents);
        }
        $blueprintReady['tabs'] = array_merge($blueprintReady['tabs'], $blueprint->contents()['tabs']);

        // Set the contents
        $event->blueprint->setContents($blueprintReady);

        // Reset the directory to the old one
        Blueprint::setDirectory($oldDirectory);
    }



}
