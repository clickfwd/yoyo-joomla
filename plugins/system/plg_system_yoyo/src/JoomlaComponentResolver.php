<?php

namespace Clickfwd\Yoyo\Joomla;

use Clickfwd\Yoyo\Component;
use Clickfwd\Yoyo\AnonymousComponent;
use Clickfwd\Yoyo\ComponentResolver;
use Clickfwd\Yoyo\Interfaces\ViewProviderInterface;
use Clickfwd\Yoyo\Services\Configuration;
use Clickfwd\Yoyo\ViewProviders\YoyoViewProvider;
use Clickfwd\Yoyo\YoyoHelpers;
use Clickfwd\Yoyo\View;
use Joomla\CMS\Factory;

class JoomlaComponentResolver extends ComponentResolver
{
    public function resolveDynamic($registered): ?Component
    {
        $class = $this->autoloadComponentClass();

        if ($class && is_subclass_of($class, Component::class)) {
            return new $class($this->id, $this->name, $this);
        }

        return parent::resolveDynamic($registered);
    }

    public function resolveAnonymous($registered): ?Component
    {
        $view = $this->resolveViewProvider();

        if ($view->exists($this->name)) {
            return new AnonymousComponent($this->id, $this->name, $this);
        }

        return null;
    }    

    public function resolveViewProvider(): ViewProviderInterface
    {
        return new YoyoViewProvider(new View($this->getViewPath()));
    }

    protected function getViewPath()
    {
        $yoyoComponentName = $this->name;

        [$sourceType,$sourceName,$sourceGroup] = explode('.',$this->source().'..');

        switch($sourceType)
        {
            case 'component':
                $path = JPATH_BASE."/components/com_{$sourceName}/yoyo/views";
                break;  

            case 'module':
                $path = JPATH_BASE."/modules/mod_{$sourceName}/yoyo/views";
                break;  

            case 'plugin':
                if ($sourceGroup) {
                    $path = JPATH_BASE."/plugins/{$sourceGroup}/{$sourceName}/yoyo/views";
                } else {
                    $path = JPATH_BASE."/plugins/{$sourceName}/yoyo/views";   
                }
                break;  

            case 'template':
                    $path = JPATH_THEMES.'/'.Factory::getApplication()->getTemplate().'/yoyo/views';
                break;                

        }

        if (! is_dir($path)) {
            throw new \Exception("View path not found for Yoyo component [$yoyoComponentName] at [{$path}].");
        }

        return $path;
    }

    protected function autoloadComponentClass()
    {
        $source = $this->source();

        $yoyoComponentName = $this->name;
        
        [$sourceType,$sourceName,$sourceGroup] = explode('.',$source.'..');

        $className = YoyoHelpers::studly($yoyoComponentName);

        switch($sourceType)
        {
            case 'component':
                $path = JPATH_BASE."/components/com_{$sourceName}/yoyo/components/{$className}.php";
                
                $className = 'Yoyo\Components\\'.YoyoHelpers::studly($sourceName).'\\'.$className;
                break;

            case 'module':
                $path = JPATH_BASE."/modules/mod_{$sourceName}/yoyo/components/{$className}.php";
            
                $className = 'Yoyo\Modules\\'.YoyoHelpers::studly($sourceName).'\\'.$className;
                break;  

            case 'plugin':
                if ($sourceGroup) {
                    $path = JPATH_BASE."/plugins/{$sourceGroup}/{$sourceName}/yoyo/components/{$className}.php";
            
                    $className = 'Yoyo\Plugins\\'.YoyoHelpers::studly($sourceGroup).'\\'.YoyoHelpers::studly($sourceName).'\\'.$className;
                } else {
                    $path = JPATH_BASE."/plugins/{$sourceName}/yoyo/components/{$className}.php";
            
                    $className = 'Yoyo\Plugins\\'.YoyoHelpers::studly($sourceName).'\\'.$className;
                }
                break;  

            case 'template':
                    $path = JPATH_THEMES.'/'.Factory::getApplication()->getTemplate().'/yoyo/components/{$className}.php';

                    $className = 'Yoyo\Templates\\'.$className;
                break;                
        }

        if (file_exists($path)) {
            require_once($path);

            return $className;
        }
    }
}