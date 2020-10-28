# Yoyo Joomla

Yoyo Joomla is an implementation of the [Yoyo Reactive PHP Framework](https://github.com/clickfwd/yoyo) so you can easily incorporate reactive Yoyo components on Joomla sites.

The Yoyo System Plugin automatically loads the necessary Javascript and CSS files as needed, and it works in the site's front-end and administration.

Below you'll find references to `Yoyo components` and `Joomla components`. Keep in mind that with Yoyo reactive components, have nothing to do with Joomla components. You can implement one or more Yoyo components within a Joomla component.

The Yoyo Joomla package includes:

- Yoyo System Plugin
- Yoyo Library
- Yoyo Site Module (demo)
- Yoyo Administrator Module (demo)

## Instalation

Download the zip file and install using the Joomla installer. The Yoyo system plugin will be automatically enabled. 

To see the included module demos, publish the site and administrator modules and assign them to visible positions on your site and administrator templates.

## Developing with Yoyo

To use Yoyo in Joomla modules and Joomla components, create a `yoyo` directory in the module or component root folder. 

The directory structure for a `mod_shopping_cart` module looks like this:

```files
\mod_shopping_cart
`-- \yoyo
    |-- \components
    `-- \views
```
Place the Yoyo component classes inside the `components` directory and the templates inside the `views` directory.

For reference, take a look at the code for the included demo Yoyo modules.

## Component Classes and Templates

When creating dynamic components, the naming of PHP classes needs to follow a specific naming convention:

`Yoyo\{ExtensionTypePlural}\{ExtensionName}`

For example, when creating a Joomla module named `mod_shopping_cart`, the Yoyo component class namespace should be:

```php
<?php

namespace Yoyo\Modules\ShoppingCart;
```

For a `com_products` Joomla component, the Yoyo component class namespace should be:

```php
<?php

namespace Yoyo\Components\Products;
```

As you can see from the Yoyo modules included in the package, you can have more than one Yoyo component within a single Joomla module or Joomla component. And you can name your Yoyo components classes whatever you want within the Joomla module/component namespace. 

For example, for a `Cart` Yoyo component within `mod_shopping_cart`, the Yoyo component class would look like this:

```php
<?php

namespace Yoyo\Modules\ShoppingCart;

use Clickfwd\Yoyo\Component;

class Cart extends Component {
    // ...
}
```

The corresponding template `cart.php` should be placed inside the `views` directory. 

The structure for the above example is:

```files
yoyo
|-- \components
    `-- Cart.php
`-- \views
    `-- cart.php
```

## Rendering Yoyo components

To render the above `Cart` component within the module template you would use the following code:

```php
<?php echo Yoyo\yoyo_render('cart',[
    'yoyo:source' => 'module.shopping_cart'
]); ?>
```

As you can see, we are passing a second argument to `yoyo_render` which is a variables array and include the `yoyo:source`. This allows the Joomla Yoyo Component Resolver to figure out where to find the component's files.

If you are developing a `com_products` component, you would use the following render code:

```php
<?php echo Yoyo\yoyo_render('cart',[
    'yoyo:source' => 'component.products'
]); ?>
```

And then include the component files within the `yoyo` directory inside your component root folder.

## Updating Yoyo components

The system plugin automatically sets the Yoyo update route for you, so whenever a component makes an update request, it automatically executes the update function.

## Creating Custom Resolvers

The Yoyo System Plugin triggers an `onYoyoAfterInitialize` event, allowing you to extend some of the plugin functionality, like adding your own Yoyo component resolvers.

For example, you could use this to allow Yoyo to load Yoyo component class and template files from any directory on your site.

To tell Yoyo to use a different resolver when rendering a Yoyo component, use the `yoyo:resolver` variable. The example below uses a `custom` resolver instead of the default `joomla` resolver.

```php
<?php 
echo Yoyo\yoyo_render($component['name'],[
    'yoyo:resolver' => 'custom',
    'yoyo:source' => 'module.yoyo'
]); 
?>
```

To create a custom resolver, create a new Joomla plugin in the `yoyo` group with the `onYoyoAfterInitialize` method. This method receives a `$yoyo` instance that can be used to register a new resolver.

Below you can see some sample code for a custom resolver that loads the files from a `yoyo` directory in the root of the site.

    /plugins/yoyo/custom_resolver/custom_resolver.php

```php
<?php
defined('_JEXEC') || die;

use Clickfwd\Yoyo\Yoyo;
use Clickfwd\Yoyo\Joomla\CustomResolver;
use Joomla\CMS\Plugin\CMSPlugin;

class PlgYoyoCustom_Resolver extends CMSPlugin
{
	public function onYoyoAfterInitialize(Yoyo $yoyo)
	{
		require_once __DIR__.'/src/CustomResolver.php';

		$yoyo->registerComponentResolver('custom', CustomResolver::class);
	}
}
````

    /plugins/yoyo/custom_resolver/src/CustomResolver.php

```php
<?php

namespace Clickfwd\Yoyo\Joomla;

use Clickfwd\Yoyo\ComponentResolver;
use Clickfwd\Yoyo\YoyoHelpers;

class CustomResolver extends JoomlaComponentResolver
{
    protected function getViewPath()
    {
        $name = $this->name;

        [$extensionType,$extensionName] = explode('.',$this->source());

        switch($extensionType)
        {
            case 'component':
                $path = JPATH_BASE."/yoyo/components/{$name}.php";
                break;  

            case 'module':
                $path = JPATH_BASE."/yoyo/views";
                break;  
        }

        if (! is_dir($path)) {
            throw new \Exception("View path not found for Yoyo component [$name] at [{$path}].");
        }

        return $path;
    }

    protected function autoloadComponentClass($source, $name)
    {
        [$extensionType,$extensionName] = explode('.',$source);

        switch($extensionType)
        {
            case 'component':
                $path = JPATH_BASE."/yoyo/components/{$name}.php";
                break;  

            case 'module':
                $path = JPATH_BASE."/yoyo/components/{$name}.php";
                break;  
        }

        if (file_exists($path)) {
            require_once($path);

            return 'Yoyo\Modules\\'.YoyoHelpers::studly($extensionName).'\\'.YoyoHelpers::studly($name);
        }
    }
}
```