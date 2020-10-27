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

class JoomlaComponentResolver extends ComponentResolver
{
    public function resolveDynamic($registered): ?Component
    {
        if ($source = $this->source()) {

            $class = $this->autoloadComponentClass($source, $this->name);

            if ($class && is_subclass_of($class, Component::class)) {
                return new $class($this->id, $this->name, $this);
            }
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
        $name = $this->name;

        [$extensionType,$extensionName] = explode('.',$this->source());

        switch($extensionType)
        {
            case 'component':
                $path = JPATH_BASE."/component/com_{$extensionName}/yoyo/components/{$name}.php";
                break;  

            case 'module':
                $path = JPATH_BASE."/modules/mod_{$extensionName}/yoyo/views";
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
                $path = JPATH_BASE."/component/com_{$extensionName}/yoyo/components/{$name}.php";
                break;  

            case 'module':
                $path = JPATH_BASE."/modules/mod_{$extensionName}/yoyo/components/{$name}.php";
                break;  
        }

        if (file_exists($path)) {
            require_once($path);

            return 'Yoyo\Modules\\'.YoyoHelpers::studly($extensionName).'\\'.YoyoHelpers::studly($name);
        }
    }
}