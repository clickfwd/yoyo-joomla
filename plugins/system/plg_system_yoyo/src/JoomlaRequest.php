<?php

namespace Clickfwd\Yoyo\Joomla;

use Clickfwd\Yoyo\YoyoHelpers;
use Clickfwd\Yoyo\Interfaces\RequestInterface;
use Joomla\CMS\Factory;

class JoomlaRequest implements RequestInterface
{
    private $yoyoRequest;

    private $dropped = [];

    public function __construct()
    {
        $this->request = Factory::getApplication()->input;

        $this->yoyoRequest = $this->request->server->get('HTTP_HX_REQUEST', '', 'bool');
    }

    public function all()
    {
        return array_map(function ($value) {
            if ($decoded = YoyoHelpers::test_json($value)) {
                return $decoded;
            }

            return $value;
        }, $this->request->getArray());
    }

    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : [$keys];

        $all = $this->all();

        $output = [];

        foreach ($all as $key => $value) {
            if (in_array($key, $keys)) {
                continue;
            }

            $output[$key] = $value;
        }

        return $output;
    }

    public function get($key, $default = null)
    {
        if (in_array($key,$this->dropped)) {
            return $default;
        }

        $value = $this->request->get($key, $default);

        // get automatically cleans input and removes slashes
        if (is_string($value)) {
            $value = $this->request->get($key, $default, 'string');
        }

        if ($decoded = YoyoHelpers::test_json($value)) {
            return $decoded;
        }

        return $value;
    }

    public function drop($key)
    {
        $this->dropped[] = $key;
    }

    public function method()
    {
        return $this->request->server->get('REQUEST_METHOD', 'GET', 'string');
    }

    public function fullUrl()
    {
        if (empty($this->request)) {
            return null;
        }

        if ($currentUrl = $this->request->server->get('HTTP_HX_CURRENT_URL', '', 'string')) {
            return $currentUrl;
        }

        $protocol = 'http';

        if ($this->request->server->get('HTTPS', '', 'string') === 'on') {
            $protocol = 'https';
        }

        $host = $this->request->server->get('HTTP_HOST', '', 'string');

        $path = rtrim($this->request->server->get('REQUEST_URI', '', 'string'), '?');

        return "{$protocol}://{$host}{$path}";
    }

    public function isYoyoRequest()
    {
        return $this->yoyoRequest;
    }

    public function windUp()
    {
        $this->yoyoRequest = false;
    }

    public function triggerId()
    {
        return $this->request->server->get('HTTP_HX_TRIGGER', '', 'string');
    }
}
