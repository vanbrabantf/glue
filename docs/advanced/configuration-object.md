# The Configuration object

## From the AbstractConfiguration class

For advanced usage, it is recommended to create a `Configuration` class extending `AbstractConfiguration`.
You can there tweak the providers, middlewares, etc. in a more advanced fashion:

```php
class MyConfiguration extends \Madewithlove\Glue\AbstractConfiguration
{
    public function __construct()
    {
        $this->providers        = [SomeProvider::class];
        $this->someCustomConfig = 'foobar';
    }
}
```

If you need access to the container or environment variables, you can do so through the `configure` method which is called
on the configuration once these two are booted:

```php
class MyConfiguration extends \Madewithlove\Glue\AbstractConfiguration
{
    public function __construct()
    {
        $this->providers        = [SomeProvider::class];
        $this->someCustomConfig = 'foobar';
    }

    public function configure()
    {
        $this->debug = $this->container->get('something') ?: getenv('FOOBAR');
    }
}
```

If arrays are more your thing, you can just call the parent constructor with the array:

```php
class MyConfiguration extends \Madewithlove\Glue\AbstractConfiguration
{
    public function __construct()
    {
        parent::__construct([
            'providers'        => [SomeProvider::class],
            'someCustomConfig' => 'foobar',
        ]);
    }
}
```

Once you have your class, simply pass it to the application constructor:

```php
$app = new Glue(new MyConfiguration());
$app->run();
```

You can change the configuration at any time through the getter and setter:

```php
$configuration = $app->getConfiguration();
$configuration->setPaths($paths);

$app->setConfiguration($configuration);
```

## Going commando

If you don't like the base configuration class you can just create a custom class and make it implement `ConfigurationInterface`:

```php
class MyConfiguration implements ConfigurationInterface
{
    public function isDebug()
    {
        return true;
    }

    public function getRootPath()
    {
        return 'some/path';
    }

    public function getPaths()
    {
        return [
            'cache' => 'storage/cache',
        ];
    }

    // etc.
}
```