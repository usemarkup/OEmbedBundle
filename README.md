# MarkupOEmbedBundle

[![Build Status](https://api.travis-ci.org/usemarkup/OEmbedBundle.png?branch=master)](http://travis-ci.org/usemarkup/OEmbedBundle)

## Installation

You can install this bundle using composer or add the package to your composer.json file directly.

```bash
   composer require markup/oembed-bundle
```

After you have installed the package, you just need to add the bundle to your AppKernel.php file:

```php
   // in AppKernel::registerBundles()
   $bundles = array(
       // ...
       new Markup\OEmbedBundle\MarkupOEmbedBundle(),
       // ...
   );
```

## About

This Symfony2 bundle offers the ability to easily define integrations with [OEmbed](http://oembed.com/) providers on an ad-hoc basis without needing to make use of a service like [Embed.ly](http://embed.ly/).

## Usage

Say you want to include Youtube videos using OEmbed - you can set up a provider as semantic configuration in your config.yml file:

```yml
    markup_o_embed:
        providers:
            youtube:
                endpoint: http://www.youtube.com/oembed
                scheme: "http://www.youtube.com/watch?v=$ID$"
                code_property: html
```

You can then render oEmbed blocks in a template either by referencing the media ID and provider inline:

```twig
    {{ markup_oembed('dQw4w9WgXcQ', 'youtube', {}) }}
```

or if you have passed an object of class `Markup\OEmbedBundle\OEmbed\Reference` into your template:

```php
    use Markup\OEmbedBundle\OEmbed\Reference;

    $oEmbed = new Reference('dQw4w9WgXcQ', 'youtube');
    $twig->render('my_template.html.twig', array('oembed' => $oEmbed));
```

you can then reference it slightly differently:

```twig
    {{ markup_oembed_render(oembed, {}) }}
```

## License

Released under the MIT License. See LICENSE.
