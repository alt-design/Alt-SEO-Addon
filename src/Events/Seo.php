<?php namespace AltDesign\AltSeo\Events;

use Statamic\Events;
use Statamic\Facades\Blueprint;

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
        $blueprintReady['tabs'] = array_merge($blueprintReady['tabs'], $blueprint->contents()['tabs']);

        // Set the contents
        $event->blueprint->setContents($blueprintReady);

        // Reset the directory to the old one
        Blueprint::setDirectory($oldDirectory);
    }



}
