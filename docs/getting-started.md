# Basic usage

## Creating an app

Glue is rather simple to bootstrap, simply create an `index.php` file in a directory of your choice (`/public` or `/web` per example).
Then create a new instance of `Glue` and call `run` on it.

**public/index.php**
```php
$app = new Glue();
$app->run();
```

## Configuration

You can configure the application by passing a `ConfigurationInterface` implementation to the constructor.
If none is passed, Glue will use the `DefaultConfiguration` class which provides some smart defaults.

```php
$app = new Glue(new Configuration([
    'namespace'   => 'MyApp',
    'debug'       => getenv('APP_DEBUG'),
    'providers'   => [SomeProvider::class],
    'middlewares' => [SomeMiddleware::class],
    'paths'       => [
        'views' => __DIR__.'/paths/to/views',
    ],
]));
```

You can also override only certain parts of the configuration through the `configure` method:

```php
// This will use the DefaultConfigure and override `namespace` on it
$app = new Glue();
$app->configure([
    'namespace' => 'MyApp',
]);

// Or
$app->configure('namespace', 'MyApp');
```

Any configuration key passed to Glue will be bound on the container as `config.{key}`, per example if you need to share a configuration
value amongst your application, simply pass it to the configuration:

```php
// Will make `$this->container->get('config.my_key')` available in providers and such
$app->configure([
    'my_key' => 'somevalue',
]);
```

## Environment variables

Some configuration, like database credentials and such, are fetched through environment variables.
By default Glue will attempt to load an `.env` file in the root path if found, so you can define things there too.

## Directory structure

While Glue doesn't assume any directory structure, here are the paths configured by default:

```
'assets'     => $rootPath.'/public/builds',
'migrations' => $rootPath.'/resources/migrations',
'views'      => $rootPath.'/resources/views',
'cache'      => $rootPath.'/storage/cache',
'logs'       => $rootPath.'/storage/logs',
```

Those are of course only needed in case you use the related providers, if per example you don't use migrations, you won't need
the `migrations` path, and so on.

## Changing container

The application also implements `ContainerAwareInterface` so you can swap the container at any time:

```php
$container = new Container;
$container->share(Foobar::class, function() {
    return new Foobar;
});

$app = new Glue();
$app->setContainer($container);
```

It has however to be an instance of `League\Container` as Glue relies heavily on its service provider feature.