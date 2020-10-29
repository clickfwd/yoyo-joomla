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

## Developing with Yoyo in Joomla

Refer to the [Yoyo documentation](https://github.com/clickfwd/yoyo). You can add Yoyo reactive components in Joomla components, modules, plugins, and templates in both front-end and administration.

### Joomla Components

Create a `yoyo` directory in the Joomla component root.

```files
com_foo_bar
`-- \yoyo
    |-- \components
    `-- \views
```

Use the following namespace convention for Yoyo classes in Joomla components:

```php
<?php

namespace Yoyo\Components\FooBar;

use Clickfwd\Yoyo\Component;

class Counter extends Component {
    // ...
}
```

To render the Yoyo component in templates use:

```php
<?php echo Yoyo\yoyo_render('counter',[
    'yoyo:source' => 'component.foo_bar'
]); ?>
```

### Modules

Create a `yoyo` directory in the Joomla module root.

```files
mod_foo_bar
`-- \yoyo
    |-- \components
    `-- \views
```

Use the following namespace convention for Yoyo classes in Joomla components:

```php
<?php

namespace Yoyo\Modules\FooBar;

use Clickfwd\Yoyo\Component;

class Counter extends Component {
    // ...
}
```

To render the Yoyo component in templates use:

```php
<?php echo Yoyo\yoyo_render('counter',[
    'yoyo:source' => 'module.foo_bar'
]); ?>
```

### Plugins

Create a `yoyo` directory in the Joomla plugin root.

```files
foo_bar
`-- \yoyo
    |-- \components
    `-- \views
```

Use the following namespace convention for Yoyo classes in Joomla plugins:

```php
<?php

namespace Yoyo\Plugins\FooBar;

use Clickfwd\Yoyo\Component;

class Counter extends Component {
    // ...
}
```

If the plugin is within a specific group (i.e. content, system, etc.), then include the group name in the namespace. For example, for a `content` plugin:

```php
<?php

namespace Yoyo\Plugins\Content\FooBar;

use Clickfwd\Yoyo\Component;

class Counter extends Component {
    // ...
}
```

To render the Yoyo component in templates use:

```php
<?php echo Yoyo\yoyo_render('counter',[
    'yoyo:source' => 'plugins.foo_bar'
]); ?>
```

And for plugins within specific groups (i.e. content):

```php
<?php echo Yoyo\yoyo_render('counter',[
    'yoyo:source' => 'plugins.content.foo_bar'
]); ?>
```

### Templates

Create a `yoyo` directory in the Joomla template root

```files
your-template
`-- \yoyo
    |-- \components
    `-- \views
```

Use the following namespace convention for Yoyo classes in Joomla templates:

```php
<?php

namespace Yoyo\Templates;

use Clickfwd\Yoyo\Component;

class Counter extends Component {
    // ...
}
```

To render the Yoyo component in templates use:

```php
<?php echo Yoyo\yoyo_render('counter',[
    'yoyo:source' => 'template'
]); ?>
```

There's no need to specify the template name because Yoyo automatically resolves the component for the current template.

## Front-end vs. Administration

Yoyo will automatically resolve Yoyo components to the right directories. 

If a front-end request is made, Yoyo will resolve its components only from front-end extensions and templates.

If an administration request is made, Yoyo will resolve its components only from administration extensions and templates. 

## Rendering Yoyo components

You can render any Yoyo component from anywhere on the site as long as the functionality is self contained and loads all the necessary classes. So if you have a Yoyo component in  Joomla plugin, you can call it from within Joomla components, modules and templates, just by referencing the right `yoyo:source`:

```php
<?php echo Yoyo\yoyo_render('cart',[
    'yoyo:source' => 'plugin.foo_bar'
]); ?>
```

## Creating Custom Resolvers

The Yoyo System Plugin triggers an `onYoyoAfterInitialize` event, allowing you to extend some of the plugin functionality, like adding your own Yoyo component resolvers.

For example, you could use this to allow Yoyo to load Yoyo component class and template files from any directory on your site.

To tell Yoyo to use a different resolver when rendering a Yoyo component, use the `yoyo:resolver` variable. The example below uses a `custom` resolver instead of the default `joomla` resolver, to load Yoyo components from the `yoyo` directory in the root of the site. The class namespace used for all Yoyo components is `Yoyo\Custom`.

```php
<?php 
echo Yoyo\yoyo_render($component['name'],[
    'yoyo:resolver' => 'custom',
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
```

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
        $yoyoComponentName = $this->name;

        $path = JPATH_BASE."/yoyo/views";

        if (! is_dir($path)) {
            throw new \Exception("View path not found for Yoyo component [$yoyoComponentName] at [{$path}].");
        }

        return $path;
    }

    protected function autoloadComponentClass()
    {
        $yoyoComponentName = $this->name;
        
        $path = JPATH_BASE."/yoyo/components/{$yoyoComponentName}.php";

        $className = YoyoHelpers::studly($yoyoComponentName);

        if (file_exists($path)) {
            require_once($path);

            return 'Yoyo\Custom\\'.YoyoHelpers::studly($className);
        }
    }
}
```