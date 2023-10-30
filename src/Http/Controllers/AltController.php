<?php namespace AltDesign\AltSeo\Http\Controllers;

use Illuminate\Http\Request;
use AltDesign\AltSeo\Helpers\Data;

/**
 * Class AltController
 *
 * @package  AltDesign\AltSeo
 * @author   Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class AltController {

    /**
     *  Render the default options page.
     */
    public function index()
    {
        $data = new Data('settings');

        $blueprint = $data->getBlueprint(true);
        $fields = $blueprint->fields()->addValues($data->all())->preProcess();

        return view('alt-seo::index', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    /**
     * Update the settings.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $data = new Data('settings');

        // Set the fields etc
        $blueprint = $data->getBlueprint(true);
        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        // Save the data
        $data->setAll($fields->process()->values()->toArray());

        return true;
    }

}
