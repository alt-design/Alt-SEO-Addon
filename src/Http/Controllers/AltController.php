<?php namespace AltDesign\AltSeo\Http\Controllers;

use Illuminate\Http\Request;
use AltDesign\AltSeo\Helpers\Data;
use Statamic\Facades\AssetContainer;

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

        // Check if the asset container exists
        $contents = $blueprint->contents();
        $containerName = $fields->get('alt_seo_asset_container')->value() ?? 'assets';
        $socialDefaultKey = 'alt_seo_social_image_default';
        $theFields = $contents['tabs']['social']['sections'][0]['fields'];

        if(!AssetContainer::find($containerName)) {
            $fields = $fields->except($socialDefaultKey); // Remove the field if we can't find the asset container.
            $index = array_search($socialDefaultKey, array_column($theFields, 'handle'));
            unset($theFields[$index]);
        } else {
            $index = array_search($socialDefaultKey, array_column($theFields, 'handle')); // Attempt to change asset container
            $theFields[$index]['field']['container'] = $containerName;

            $newFields = $fields->all();
            $newFields[$socialDefaultKey]->setConfig(
                array_merge($newFields[$socialDefaultKey]->config(), ['container' => $containerName])
            );
            $fields->setFields(collect($newFields));
        }

        $blueprint->setContents($contents);

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
