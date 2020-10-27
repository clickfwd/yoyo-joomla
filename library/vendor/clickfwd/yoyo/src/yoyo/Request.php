<?php

namespace Clickfwd\Yoyo;

use Clickfwd\Yoyo\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    public function __construct()
    {
        $this->request = $_REQUEST;

        $this->server = $_SERVER;
    }

    public function mock($request, $server)
    {
        $this->request = $request;

        $this->server = $server;

        return $this;
    }

    public function reset()
    {
        $this->request = [];

        $this->server = [];
    }

    public function all()
    {
        return array_map(function ($value) {
            if ($decoded = YoyoHelpers::test_json($value)) {
                return $decoded;
            }

            return $value;
        }, $this->request);
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

    public function input($key, $default = null)
    {
        $value = $this->request[$key] ?? $default;

        if ($decoded = YoyoHelpers::test_json($value)) {
            return $decoded;
        }

        return $value;
    }

    public function drop($key)
    {
        unset($this->request[$key]);
    }

    public function method()
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function fullUrl()
    {
        if (empty($this->request)) {
            return null;
        }

        if (isset($this->server['HTTP_HX_CURRENT_URL'])) {
            return $this->server['HTTP_HX_CURRENT_URL'];
        }

        $protocol = 'http';

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $protocol = 'https';
        }

        $host = $_SERVER['HTTP_HOST'];

        $path = rtrim($_SERVER['REQUEST_URI'], '?');

        return "{$protocol}://{$host}{$path}";
    }

    public function isYoyoRequest()
    {
        return $this->server['HTTP_HX_REQUEST'] ?? false;
    }

    public function windUp()
    {
        unset($this->server['HTTP_HX_REQUEST']);
    }

    public function triggerId()
    {
        return $this->server['HTTP_HX_TRIGGER'];
    }
}
