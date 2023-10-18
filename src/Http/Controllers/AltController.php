<?php namespace AltDesign\AltSeo\Http\Controllers;

use AltDesign\AltSeo\Helpers\Data;
use Illuminate\Http\Request;
use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;

/**
 * Class AltController
 *
 * @category AltController_Category
 * @package  AltDesign\AltSeo\Http\Controllers
 * @author   Ben Harvey <ben@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class AltController {

    public function index()
    {
        $data = new Data('settings');
        $values = $data->all();

        $blueprint = Blueprint::setDirectory(__DIR__ . '/../../../resources/blueprints')->find('settings'); // TODO - move this boi

        $fields = $blueprint->fields();
        $fields = $fields->addValues($values);
        $fields = $fields->preProcess();

        return view('alt-seo::index', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    public function update(Request $request) {

        $blueprint = Blueprint::setDirectory(__DIR__ . '/../../../resources/blueprints')->find('settings'); // TODO - move this boi
        $fields = $blueprint->fields()->addValues($request->all());

        $fields->validate();

        $data = new Data('settings');
        $data->setAll($fields->process()->values()->toArray());
    }

}
