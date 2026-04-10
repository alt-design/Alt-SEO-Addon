<?php

namespace AltDesign\AltSeo\Tags;

use Statamic\Facades\Antlers;
use Statamic\Tags\Tags;

use AltDesign\AltSeo\Helpers\Data;
use Statamic\View\Antlers\AntlersString;

/**
 * Class AltSeo
 *
 * @package  AltDesign\AltSeo
 * @author   Ben Harvey <ben@alt-design.net>, Natalie Higgins <natalie@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class AltSeo extends Tags
{
    /**
     * The {{ alt_seo }} tag.
     *
     * @return string|array
     */
    public function index()
    {
        // Default doesn't actually do anything here
        return;
    }

    /**
     * The {{ alt_seo:title }} tag.
     *
     * @return string
     */
    public function title()
    {
        return '<title>' . $this->getTitle() . '</title>';
    }

    /**
     * The {{ alt_seo:meta }} tag.
     *
     * @return string|array
     */
    public function meta()
    {
        return view('alt-seo::meta', $this->meta_array())->render();
    }

    // Supporting function for the meta() function view
    private function meta_array()
    {
        $metaRobots = [];
        if ($this->context->value('alt_seo_noindex')) {
            $metaRobots[] = 'noindex';
        } else {
            $metaRobots[] = 'index';
        }
        if ($this->context->value('alt_seo_nofollow')) {
            $metaRobots[] = 'nofollow';
        } else {
            $metaRobots[] = 'follow';
        }

        return [
            'title' => $this->getTitle(),
            'canonical' => $this->getCanonical(),
            'description' => strip_tags($this->getDescription()),
            'robots' => implode(', ', $metaRobots),

            'og_url' => $this->getCanonical(),
            'og_type' => 'website',
            'og_title' => $this->getSocialTitle(),
            'og_description' => strip_tags($this->getSocialDescription()),
            'og_image' => $this->getSocialImage(),

            'twitter_card' => 'summary_large_image',
            'twitter_domain' => $this->context->value('site')->url,
            'twitter_url' => $this->getCanonical(),
            'twitter_title' => $this->getSocialTitle(),
            'twitter_description' => strip_tags($this->getSocialDescription()),
            'twitter_image' => $this->getSocialImage(),
        ];
    }

    /**
     * Replace the variables in the string.
     *
     * @param $string
     * @return array|string|string[]
     */
    public function replaceVars($string){
        $blueprintPageTitle = $this->context->value('title'); // Page Title
        $appName = $this->context->value('site')->name; // App Name
        $string = str_replace('{title}', $blueprintPageTitle, $string);
        $string = str_replace('{site_name}', $appName, $string);
        return $string;
    }

    /**
     * Bring the title in and return the correct instance.
     *
     * @return array|string|string[]
     */
    public function getTitle()
    {
        if(!empty($this->context->value('alt_seo_meta_title'))) {
            return $this->replaceVars($this->context->value('alt_seo_meta_title'));
        }

        $data = new Data('settings');
        if($data->get('alt_seo_meta_title_default')) {
            $title = $data->get('alt_seo_meta_title_default');
            return $this->replaceVars($title);
        }

        return $this->context->value('title') . ' | ' . $this->context->value('site')->name;
    }

    /**
     * Bring the description in and return the correct instance.
     *
     * @return mixed|string
     */
    public function getDescription()
    {
        if(!empty($this->context->value('alt_seo_meta_description'))) {
            return Antlers::parse($this->replaceVars($this->context->value('alt_seo_meta_description')));
        }

        $data = new Data('settings');
        if($data->get('alt_seo_meta_description_default')) {
            $description = $data->get('alt_seo_meta_description_default');
            $description = $this->replaceVars($description);
            return Antlers::parse($description);
        }

        return '';
    }

    /**
     * Bring the canonical in and return the correct instance.
     *
     * @return mixed|AntlersString|string
     */
    public function getCanonical()
    {
        if(!empty($this->context->value('alt_seo_canonical_url'))) {
            return Antlers::parse($this->replaceVars($this->context->value('alt_seo_canonical_url')));
        }

        // Return current url
        return request()->fullUrl();
    }

    /**
     * Bring the social title in and return the correct instance.
     *
     * @return array|string|string[]
     */
    public function getSocialTitle()
    {
        if(!empty($this->context->value('alt_seo_social_title'))) {
            return $this->replaceVars($this->context->value('alt_seo_social_title'));
        }

        $data = new Data('settings');
        if($data->get('alt_seo_social_title_default')) {
            $title = $data->get('alt_seo_social_title_default');
            return $this->replaceVars($title);
        }

        return $this->context->value('title') . ' | ' . $this->context->value('site')->name;
    }

    /**
     * Bring the social description in and return the correct instance.
     *
     * @return array|mixed|string|string[]
     */
    public function getSocialDescription()
    {

        $socialDescription = '';

        if(!empty($this->context->value('alt_seo_social_description'))) {
            $socialDescription = Antlers::parse($this->replaceVars($this->context->value('alt_seo_social_description')));
        } else {
            $data = new Data('settings');
            if($data->get('alt_seo_social_description_default')) {
                $description = $data->get('alt_seo_social_description_default');
                $description = $this->replaceVars($description);
                $description = Antlers::parse($description);

                $socialDescription = $description;
            }
        }

        if (str_contains($socialDescription, '{description}')) {
            $description = $this->getDescription();
            $socialDescription = str_replace('{description}', $description, $socialDescription);
        }

        return $socialDescription;
    }

    /**
     * Bring the social image in and return the correct instance.
     *
     * @return array|mixed|string|string[]|null
     */
    public function getSocialImage()
    {
        $imageURL = '';
        if(!empty($this->context->value('alt_seo_social_image'))) {
            $imageURL =  Antlers::parse($this->context->value('alt_seo_social_image'));
        } else {
            $data = new Data('settings');
            if($data->get('alt_seo_social_image_default')) {
                $image = $data->get('alt_seo_social_image_default');
                $imageURL = $image;
            }
        }

        // If the image is an absolute URL (e.g., S3), use it as is
        if (preg_match('/^https?:\/\//', $imageURL)) {
            return $imageURL;
        } else {
            // Check if Statamic is configured to use S3 or local assets
            $assetContainer = \Statamic\Facades\AssetContainer::findByHandle('assets');
            $disk = $assetContainer ? $assetContainer->disk() : null;
            $assetBaseUrl = $assetContainer ? $assetContainer->url() : null;

            if ($disk && $assetBaseUrl && !empty($imageURL)) {
                // Remove leading slash if present
                $imageURL = ltrim($imageURL, '/');
                $imageURL = rtrim($assetBaseUrl, '/') . '/' . $imageURL;
            } else {
                $imageURL = str_replace('/assets/', '', $imageURL);
            }
        }
        
        return $imageURL;
    }

    public function schema()
    {
        if (!config('alt-seo.alt_seo_enable_schema')) {
            return '<script>console.error("The schema tag is not enabled.")</script>';
        }

        $raw = $this->context->get('alt_seo_schema');

        $decoded = json_decode(Antlers::parse($this->replaceVars($raw)), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '<script>console.error("Invalid schema JSON.")</script>';
        }

        $sanitisedSchema = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
        return "<script type=\"application/ld+json\">$sanitisedSchema</script>";
    }
}
