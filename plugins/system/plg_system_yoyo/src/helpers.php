<?php

namespace Yoyo;

use Clickfwd\Yoyo\Yoyo;
use Joomla\CMS\Factory;

if (! function_exists('Yoyo\yoyo_render')) 
{
    function yoyo_render($name, $variables = [], $attributes = []): string
    {
        $dispatcher = Factory::getApplication();

        $dispatcher->triggerEvent('onYoyoRender');

        $yoyo = new Yoyo();

        $variables = array_merge(['yoyo:resolver' => 'joomla'], $variables);
    
        return $yoyo->mount($name, $variables, $attributes)->render();
    }
}