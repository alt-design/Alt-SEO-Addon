<?php namespace AltDesign\AltSeo\Events;

use Statamic\Events;
use Statamic\Facades\Blueprint;

class Seo
{

    protected $events = [
        Events\EntryBlueprintFound::class => 'addSeoData',
    ];

    public function __invoke()
    {

    }

    public function subscribe($events)
    {
        $events->listen(Events\EntryBlueprintFound::class, self::class.'@'.'addSeoData');
    }

    public function addSeoData($event)
    {
        $blueprint = Blueprint::setDirectory(__DIR__ . '/../../resources/blueprints')->find('seo'); // TODO - move this boi
        $blueprintReady = $event->blueprint->contents();
        $blueprintReady['tabs'] = array_merge($blueprintReady['tabs'], $blueprint->contents()['tabs']);

        $event->blueprint->setContents($blueprintReady);

        Blueprint::setDirectory(resource_path() . '/blueprints');
    }



}
