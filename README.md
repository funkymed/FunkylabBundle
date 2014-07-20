##Author

Cyril Pereira <cyril.pereira@gmail.com>

## Installation

Edit `symfony/composer.json` file to add this bundle package:

```yml
"require": {
    ...
    "cyril-pereira/funkylab-bundle": "dev-master"
},
```

Run `php composer.phar update cyril-pereira/funkylab-bundle`

Then, add the bundle into `symfony/app/AppKernel.php`:

```php
<?php
    public function registerBundles()
    {
        $bundles = array(
            ...
            new CyrilPereira\FunkylabBundle\CyrilPereiraFunkylabBundle(),
        );

        ...

        return $bundles;
    }
```

Add the FunkylabBundle routing file in your symfony/app/config/routing.yml

```
...
funkylab:
    resource: "@CyrilPereiraFunkylabBundle/Controller/"
    prefix:   /funkylab/
```


##Console

```
$ php app/console assets:install web --symlink
$ php app/console doctrine:database:create
$ php app/console doctrine:schema:create
$ php app/console doctrine:fixtures:load

#Start

http://domain/funkylab

login : admin@admin.com
pass  : admin


