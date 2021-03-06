# Writing definitions

Writing definitions can be daunting but it's a lot easier than you might think. First of all you need to create a class implementing the `DefinitionProviderInterface`. It only needs to have one method called `getDefinitions` that returns an array of definitions:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        return [
            // Your definitions go here
        ];
    }
}
```

This array is an associative array where the value is what will be bound on the container, and the key what it will be bound as.

Per example using the most basic definition, the `ParameterDefinition` which is just a wrapper for "any value", this provider:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        return [
            'foo' => new ParameterDefinition('bar'),
        ];
    }
}
```

Would bind the string `bar` under the key `foo` in the container.

## Objects

The most common type of things you'll want to define are objects: services, instances, etc.
There are two classes you'll want to use for this: the `ObjectDefinition` and the `FactoryCallDefinition`.

The former just defines how to create an object, you say what class you want to create, its arguments, and possible methods to call on it:

```php
$object = new ObjectDefinition(SomeClass::class);
$object->setConstructorArguments('foo', 'bar');
$object->addMethodCall('setLogger', new Logger());

// You can also use the second argument of ObjectDefinition to pass constructor arguments
$object = new ObjectDefinition(SomeClass::class, ['foo', 'bar']);
```

If any of these arguments need to be something already in the container, you can use the `Reference` class for this, which will retrieve the object from the container before using it.
Per example if we wanted to call the `setLogger` method of our object with whatever is bound to `LoggerInterface` on the container, we'd do this:

```php
$object = new ObjectDefinition(SomeClass::class);
$object->setConstructorArguments('foo', 'bar');
$object->addMethodCall('setLogger', new Reference(LoggerInterface::class));
```

The `FactoryCallDefinition` works differently in the sense that you call a callable that will return your instance, already prepared:

```php
$object = new FactoryCallDefinition(MyFactory::class, 'theMethodToCall', ['argument1', 'argument2']);

// Or use setArguments
$object = new FactoryCallDefinition(MyFactory::class, 'theMethodToCall');
$object->setArguments('argument1', 'argument2');
```

Per example with `zend/diactoros` to create a request this is what is done in Glue:

```php
$request = new FactoryCallDefinition(ServerRequestFactory::class, 'fromGlobals');
```

Once you have your object, you simply return it in the array of your provider, its key being what it'll be bound as:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        $object = new ObjectDefinition(SomeClass::class);
        $object->setConstructorArguments('foo', 'bar');
        $object->addMethodCall('setLogger', new Reference(LoggerInterface::class));

        return [
            SomeClass::class => $object,
        ];
    }
}
```

## Aliases

You might also want to bind aliases on the container, for this you simply use `Reference` again and return it directly as a definition:

```php
class MyProvider implements DefinitionProviderInterface
{
    public function getDefinitions()
    {
        $object = new ObjectDefinition(SomeClass::class);
        $object->setConstructorArguments('foo', 'bar');
        $object->addMethodCall('setLogger', new Reference(LoggerInterface::class));

        return [
            SomeClass::class => $object,
            'alias-to-some-class' => new Reference(SomeClass::class),
        ];
    }
}
```

## Options and dependencies

If you need to make your definition configurable, as it is at its core a plain class, you can add any constructor arguments you might want need to it:

```php
class MyProvider implements DefinitionProviderInterface
{
    protected $someOption;

    public function __construct($someOption)
    {
        $this->someOption = $someOption;
    }

    public function getDefinitions()
    {
        $object = new ObjectDefinition(SomeClass::class);
        $object->setConstructorArguments($this->someOption, 'bar');
        $object->addMethodCall('setLogger', new Reference(LoggerInterface::class));

        return [
            SomeClass::class => $object,
        ];
    }
}
```

If you need to retrieve something from the container and you _absolutely can't_ use references for it, you can also make your provider implement the `ImmutableContainerAwareInterface` and the relevant trait.
The container will then automatically be available in your provider within Glue as `$this->container`:

 ```php
 use League\Container\ImmutableContainerAwareInterface;
 use League\Container\ImmutableContainerAwareTrait;

 class MyProvider implements DefinitionProviderInterface, ImmutableContainerAwareInterface
 {
     use ImmutableContainerAwareTrait;

     protected $someOption;

     public function __construct($someOption)
     {
         $this->someOption = $someOption;
     }

     public function getDefinitions()
     {
         $something = $this->container->get('something');
         $thing = $something->getOtherThing('foobar');

         $object = new ObjectDefinition(SomeClass::class);
         $object->setConstructorArguments($this->someOption, $thing);
         $object->addMethodCall('setLogger', new Reference(LoggerInterface::class));

         return [
             SomeClass::class => $object,
         ];
     }
 }
 ```

Keep in mind however this can be a *very dangerous practice* if you don't know what you're doing because your provider then needs to be registered _after_ whatever provider defines `something`. Only use it in last resort.

