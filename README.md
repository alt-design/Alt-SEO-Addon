# Alt SEO

> Easy SEO for your Statamic sites

## Features

- General sitewide SEO tags
- Page specific SEO tags
- Title, Description, Social Title, Social Description and Social Image


## How to Installation

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require alt-design/alt-seo
```

In layout.antlers.html remove your `<title>` and SEO tags and replace with

``` bash
{{ alt_seo:meta }}
```

You can also just use the following to output the title if you prefer

``` bash
{{ alt_seo:title }}
```

## What actually happens

The addon will look for data in the following order:

- Page specific SEO tags
- General sitewide SEO tags
- Defaults back to Site Name

For General/Site Wide SEO Tags Select Alt SEO from the Tools section in CP where you can set a general SEO Title and Description for the site. In the Social tab you can set the Title, Description and Image for Facebook and Twitter.

For Page Specific SEO Tags go to Collections > Pages > select your page and in the Alt SEO Tab you can set Title, Description, Social Title, Social Description and Social Image to override the general settings on that page.

You can use variables such as {title} | {site_name}

App name and url are taken from .env file so ensure this data is correct. Images must be stored in assets container.

## Advanced usage

### Overriding Meta Fields

All fields handled by the addon are wrapped so that that they can be overridden in your templates.

Let's say, for example, you had a product page that you wanted to change the meta title structure from default, this can be achieved in your template using the {{ section }} tag

Simply add something along the line of this in your template and the appropriate meta field will get overridden:
``` bash
{{ section:alt_seo_title }}Awesome SEO title goes here!!!{{ /section:alt_seo_title }}
```

Note : Only the data sections of the meta fields are tagged so don't us full tags as that will cause some funky HTML

The fields that can be overridden as list here :

- alt_seo_title
- alt_seo_canonical
- alt_seo_description
- alt_seo_robots
- alt_seo_og_url
- alt_seo_og_type
- alt_seo_og_title
- alt_seo_og_description
- alt_seo_og_image
- alt_seo_twitter_card
- alt_seo_twitter_domain
- alt_seo_twitter_url
- alt_seo_twitter_title
- alt_seo_twitter_description
- alt_seo_twitter_image

### Custom Schema Markup
If you'd like to enable custom schema markup as an option, just add the ```.env``` var of

```ALT_SEO_ENABLE_SCHEMA=true```

Then you can use the tag

```{{ alt_seo:alt_custom_schema }}```

## Questions etc

Drop us a big shout-out if you have any questions, comments, or concerns. We're always looking to improve our addons, so if you have any feature requests, we'd love to hear them.

### Starter Kits
- [Alt Starter Kit](https://statamic.com/starter-kits/alt-design/alt-starter-kit) 

### Addons
- [Alt Cookies Addon](https://github.com/alt-design/Alt-Cookies-Addon)
- [Alt Redirect Addon](https://github.com/alt-design/Alt-Redirect-Addon)
- [Alt Akismet Addon](https://github.com/alt-design/Alt-Akismet-Addon)
- [Alt Inbound Addon](https://github.com/alt-design/Alt-Inbound-Addon)
- [Alt Sitemap Addon](https://github.com/alt-design/Alt-Sitemap-Addon)
- [Alt Password Protect Addon](https://github.com/alt-design/Alt-Password-Protect-Addon)
- [Alt Google 2FA Addon](https://github.com/alt-design/Alt-Google-2fa-Addon)

## Postcardware

Send us a postcard from your hometown if you like this addon. We love getting mail from other cool peeps!

Alt Design  
St Helens House  
Derby  
DE1 3EE  
UK   
