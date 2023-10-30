<?php namespace AltDesign\AltSeo\Helpers;

use Statamic\Facades\YAML;
use Statamic\Filesystem\Manager;

/**
 * Class Data
 *
 * @package  AltDesign\AltSeo
 * @author   Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class Data
{
    /**
     * @var - The manager class for Statamic
     */
    public $type;

    /**
     * @var Manager
     */
    public $manager;
    /**
     * @var mixed|null
     */
    public $currentFile;
    /**
     * @var array
     */
    public $data;

    /**
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;

        // New up Statamic File Manager
        $this->manager = new Manager();

        // Grab the current file we're working with
        $this->currentFile = $this->manager->disk()->get('content/alt-seo/' . $this->type . '.yaml');

        $this->data = Yaml::parse($this->currentFile);
    }

    /**
     * Gets data from the Yaml file by key.
     *
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        Yaml::dump($this->data, $this->currentFile);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return void
     */
    public function setAll($data)
    {
        $this->data = $data;

        $this->manager->disk()->put('content/alt-seo/' . $this->type . '.yaml', Yaml::dump($this->data));
    }

}
