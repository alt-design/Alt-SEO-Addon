<?php

namespace AltDesign\AltSeo\Tags;

use AltDesign\AltSeo\Helpers\Data;
use Statamic\Facades\Antlers;
use Statamic\Tags\Tags;
use Statamic\Facades\Taxonomy;

class AltSeo extends Tags
{
    /**
     * The {{ alt_seo }} tag.
     *
     * @return string|array
     */
    public function index()
    {
        //
    }

    /**
     * The {{ alt_seo:example }} tag.
     *
     * @return string|array
     */
    public function meta()
    {
        $returnString = '';
        $returnString .= '<title>' . $this->getTitle() . '</title>';

        $returnString .= '<meta name="description" content="' . strip_tags($this->getDescription()) . '" />';
        $returnString .= '<!-- Facebook Meta Tags -->';
        $returnString .= '<meta property="og:url" content="' . ENV('APP_URL') . '">';
        $returnString .= '<meta property="og:type" content="website">';
        $returnString .= '<meta property="og:title" content="' . $this->getSocialTitle() . '">';
        $returnString .= '<meta property="og:description" content="' . strip_tags($this->getSocialDescription()) . '">';
        $returnString .= '<meta property="og:image" content="' . $this->getSocialImage() . '">';
        $returnString .= '<!-- Twitter Meta Tags -->';
        $returnString .= '<meta name="twitter:card" content="summary_large_image">';
        $returnString .= '<meta property="twitter:domain" content="' . ENV('APP_URL') . '">';
        $returnString .= '<meta property="twitter:url" content="' . ENV('APP_URL') . '">';
        $returnString .= '<meta name="twitter:title" content="' . $this->getSocialTitle() . '">';
        $returnString .= '<meta name="twitter:description" content="' . strip_tags($this->getSocialDescription()) . '">';
        $returnString .= '<meta property="twitter:image" content="' . $this->getSocialImage() .'">';

        return $returnString;
    }

    public function replaceVars($string){
        $blueprintPageTitle = $this->context->value('title'); // Page Title
        $appName = $this->context->value('config.app.name'); // App Name
        $string = str_replace('{title}', $blueprintPageTitle, $string);
        $string = str_replace('{site_name}', $appName, $string);
        return $string;
    }

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

        $defaultTitle = $this->context->value('title') . ' | ' . $this->context->value('config.app.name');
        return $defaultTitle;
    }

    public function getDescription()
    {
        if(!empty($this->context->value('alt_seo_meta_description'))) {
            return Antlers::parse($this->replaceVars($this->context->value('alt_seo_meta_description')));
        }

        $data = new Data('settings');
        if($data->get('alt_seo_meta_description_default')) {
            $description = $data->get('alt_seo_meta_description_default');
            $description = $this->replaceVars($description);
            $description = Antlers::parse($description);

            return $description;
        }

        return '';
    }

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

        $defaultTitle = $this->context->value('title') . ' | ' . $this->context->value('config.app.name');
        return $defaultTitle;
    }

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
