<?php

namespace Funcoes\Lib;

class Request
{
    private array $get, $post, $server, $files, $headers, $cookies;

    public function __construct()
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->server = $_SERVER ?? [];
        $this->cookies = $_COOKIE ?? [];
        $this->headers = [];
        if (!function_exists('apache_request_headers')) {
            function apache_request_headers()
            {
                $arh = array();
                $rx_http = '/\AHTTP_/';
                foreach ($_SERVER as $key => $val) {
                    if (preg_match($rx_http, $key)) {
                        $arh_key = preg_replace($rx_http, '', $key);
                        $rx_matches = array();
                        // do some nasty string manipulations to restore the original letter case
                        // this should work in most cases
                        $rx_matches = explode('_', $arh_key);
                        if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                            foreach ($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                            $arh_key = implode('-', $rx_matches);
                        }
                        $arh[$arh_key] = $val;
                    }
                }
                return ($arh);
            }
        } else {
            if ($headers = apache_request_headers()) {
                $this->headers = $headers;
            }
        }

        unset($_POST, $_GET, $_FILES, $_SERVER);
    }

    public function getArray(int $filter = FILTER_DEFAULT): array
    {
        return $this->get;
    }

    public function get($key, $default = "", int $filter = FILTER_DEFAULT)
    {
        return htmlspecialchars(filter_var($this->get[$key] ?? $default, $filter));
    }

    public function postArray(int $filter = FILTER_DEFAULT): array
    {
        /*
        return array_map(function ($var) use ($filter) {
            return htmlspecialchars(filter_var($var, $filter));
        }, $this->post);
        */

        return $this->post;
    }

    public function post($key, $default = "", int $filter = FILTER_DEFAULT)
    {
        if (is_array($this->post[$key] ?? '')) {
            return filter_var_array($this->post[$key] ?? $default, $filter);
        }

        return htmlspecialchars(filter_var($this->post[$key] ?? $default, $filter));
    }

    public function getCookie(string $nome = '')
    {
        return $this->cookies[$nome] ?? '';
    }

    public function header($key, $default = "")
    {
        return $this->headers[$key] ?? $default;
    }

    public function server($key, $default = "")
    {
        return $this->server[$key] ?? $default;
    }

    public function file($key)
    {
        return $this->files[$key] ?? [];
    }

    public function filesArray()
    {
        return $this->files;
    }
}
