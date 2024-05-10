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
        $returnArray = [];
        $returnArray[] = '<title>' . $this->getTitle() . '</title>';
        $returnArray[] = '<link rel="canonical" href="' . $this->getCanonical() . '" />';
        $returnArray[] = '<meta name="description" content="' . strip_tags($this->getDescription()) . '" />';
        $returnArray[] = '<!-- Facebook Meta Tags -->';
        $returnArray[] = '<meta property="og:url" content="' . $this->getUrl() . '">';
        $returnArray[] = '<meta property="og:type" content="website">';
        $returnArray[] = '<meta property="og:title" content="' . $this->getSocialTitle() . '">';
        $returnArray[] = '<meta property="og:description" content="' . strip_tags($this->getSocialDescription()) . '">';
        $returnArray[] = '<meta property="og:image" content="' . $this->getSocialImage() . '">';
        $returnArray[] = '<!-- Twitter Meta Tags -->';
        $returnArray[] = '<meta name="twitter:card" content="summary_large_image">';
        $returnArray[] = '<meta property="twitter:domain" content="' . ENV('APP_URL') . '">';
        $returnArray[] = '<meta property="twitter:url" content="' . ENV('APP_URL') . '">';
        $returnArray[] = '<meta name="twitter:title" content="' . $this->getSocialTitle() . '">';
        $returnArray[] = '<meta name="twitter:description" content="' . strip_tags($this->getSocialDescription()) . '">';
        $returnArray[] = '<meta property="twitter:image" content="' . $this->getSocialImage() .'">';

        return implode(PHP_EOL, $returnArray);
    }

    /**
     * Replace the variables in the string.
     *
     * @param $string
     * @return array|string|string[]
     */
    public function replaceVars($string){
        $blueprintPageTitle = $this->context->value('title'); // Page Title
        $appName = $this->context->value('config.app.name'); // App Name
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

        return $this->context->value('title') . ' | ' . $this->context->value('config.app.name');
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
     * Bring the url in and return the correct instance.
     *
     * @return mixed|string
     */
    public function getUrl()
    {
        if(!empty($this->context->value('alt_seo_meta_url'))) {
            return Antlers::parse($this->replaceVars($this->context->value('alt_seo_meta_url')));
        }

        return ENV('APP_URL');
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
        return ENV('APP_URL') . $this->context->value('url');
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

        return $this->context->value('title') . ' | ' . $this->context->value('config.app.name');
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
            $imageURL =  str_replace('/assets/', '', Antlers::parse($this->context->value('alt_seo_social_image')));
        } else {
            $data = new Data('settings');
            if($data->get('alt_seo_social_image_default')) {
                $image = $data->get('alt_seo_social_image_default');
                $imageURL = str_replace('/assets/', '', $image);
            }
        }

        if(!empty($imageURL)) {
            $imageURL = ENV('APP_URL') . '/assets/' . $imageURL;
        }
        return $imageURL;
    }
}
