<?php namespace AltDesign\AltSeo\Helpers;

use Statamic\Facades\YAML;
use Statamic\Filesystem\Manager;

class Data
{
    public $type;
    public $manager;
    public $currentFile;
    public $data;

    public function __construct($type)
    {
        $this->type = $type;

        // New up Stat File Manager
        $this->manager = new Manager();

        // Grab the current file we're working with
        $this->currentFile = $this->manager->disk()->get('content/alt-seo/' . $this->type . '.yaml');

        $this->data = Yaml::parse($this->currentFile);
    }

    public function get($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        return $this->data[$key];
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;

        Yaml::dump($this->data, $this->currentFile);
    }

    public function all()
    {
        return $this->data;
    }

    public function setAll($data)
    {
        $this->data = $data;

        $this->manager->disk()->put('content/alt-seo/' . $this->type . '.yaml', Yaml::dump($this->data));
    }

}
