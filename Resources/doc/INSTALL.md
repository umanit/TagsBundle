Netgen Tags Bundle installation instructions
============================================

Requirements
------------

* eZ Publish 5.3+ / eZ Publish Community Project 2014.05+
* eZ Publish Legacy Stack with legacy eZ Tags 1.4 installed and configured

### Note

Netgen Tags Bundle 1.0 can only be used with eZ Publish Enterprise 5.2 or eZ Publish Community Project 2013.07-2014.03 due to [changes in field type API](https://github.com/ezsystems/ezpublish-kernel/pull/429). If you have previous versions of eZ Publish, please use 0.9 version of Tags Bundle.

Netgen Tags Bundle 1.1 can only be used with eZ Publish Enterprise 5.3 or later or eZ Publish Community Project 2014.05 or later.

Netgen Tags Bundle 2.1 can only be used with eZ Platform. Last version available for eZ Publish Community and eZ Publish Enterprise is 2.0.x

Installation steps
------------------

### Use Composer

Add the following to your composer.json and run `php composer.phar update netgen/tagsbundle` to refresh dependencies:

```json
"require": {
    "netgen/tagsbundle": "~2.0.0"
}
```

### Activate the bundle

Activate the bundle in `ezpublish/EzPublishKernel.php` file.

```php
use Netgen\TagsBundle\NetgenTagsBundle;

...

public function registerBundles()
{
   $bundles = array(
       new FrameworkBundle(),
       ...
       new NetgenTagsBundle()
   );

   ...
}
```

### Edit configuration

Put the following in your `ezpublish/config/routing.yml` file to be able to display tag view pages:

```yml
_eztagsRoutes:
    resource: "@NetgenTagsBundle/Resources/config/routing.yml"
```

### Clear the caches

Clear eZ Publish 5 caches.

```bash
php ezpublish/console cache:clear
```

### Edit Varnish configuration (requires eZ Publish Enterprise 5.4+ or eZ Publish Community 2014.11+)

#### Varnish 3

Add the following block to the end of `if (req.request == "BAN")` block in `ez_purge` method in your Varnish configuration file to be able to clear Varnish cache for tag view pages:

```varnish
if ( req.http.X-Tag-Id == "*" ) {
    # Ban all tags
    ban( "obj.http.X-Tag-Id ~ ^[0-9]+$" );

    if (client.ip ~ debuggers) {
        set req.http.X-Debug = "Ban done for all tags";
    }

    error 200 "Banned";
} elseif ( req.http.X-Tag-Id ) {
    # Ban tag by its ID
    ban( "obj.http.X-Tag-Id == " + req.http.X-Tag-Id );

    if (client.ip ~ debuggers) {
        set req.http.X-Debug = "Ban done for tag with ID " + req.http.X-Tag-Id;
    }

    error 200 "Banned";
}
```

#### Varnish 4

Add the following block to the end of `if (req.method == "BAN")` block in `ez_purge` method in your Varnish configuration file to be able to clear Varnish cache for tag view pages:

```varnish
if ( req.http.X-Tag-Id == "*" ) {
    # Ban all tags
    ban( "obj.http.X-Tag-Id ~ ^[0-9]+$" );

    if (client.ip ~ debuggers) {
        set req.http.X-Debug = "Ban done for all tags";
    }

    return (synth(200, "Banned"));
} elseif ( req.http.X-Tag-Id ) {
    # Ban tag by its ID
    ban( "obj.http.X-Tag-Id == " + req.http.X-Tag-Id );

    if (client.ip ~ debuggers) {
        set req.http.X-Debug = "Ban done for tag with ID " + req.http.X-Tag-Id;
    }

    return (synth(200, "Banned"));
}
```

#### Clearing tag view pages caches

After you restart Varnish, you will be able to clear the caches for a specific tag with the following example shell command:

```bash
$ curl -v -X BAN -H "X-Tag-Id: 1" http://varnish.local:81/
```

This will clear Varnish cache for tag view pages for tag with ID of 1.

### Use the bundle

1) You can now load and create content with `eztags` field type

2) Use `TagsService` in your controllers to work with tags. The service is accessible through Symfony2 DIC, with ID `ezpublish.api.service.tags`
