## Features

This addon does:

- Add's general sitewide SEO tags
- Page specific SEO tags
- Title, Description, Social Title, Social Description and Social Image


## Installation

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

## License 
Licensed under the [MIT license](https://github.com/alt-design/Alt-SEO-Addon/blob/main/LICENSE).
