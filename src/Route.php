<?php

/**
 * Route
 * @since 2014
 */
class Route
{
    public static $pre = '';
    // 基础URL
    public $base_url;
    protected $method;
    public static $router;
    public $match = '/<(\w+):([^>]+)?>/';
    public static $app = [];
    // 相对URL
    public static $index;
    /**
     * 默认路由模块namespace为module
     */
    public static $r = ['core', 'app', 'modules'];
    // 当前正则的URL 如 aa
    protected $_url;
    // 当前URL的function 如 function(){}
    protected $_value;
    public $host;
    public $class = [];
    public static $obj;
    // 当前使用的CLASS
    public static $current_class;
    // 当前域名
    public static $current_domain;
    public static $err;
    public static $status;
    /**
     * 控制器名称
     * strtolower
     * ucfirst
     */
    public static $controller_name = 'strtolower';
    /**
     * 支持的语言代码
     */
    public static $supported_languages = [
        'ab',
        'aa',
        'af',
        'ak',
        'sq',
        'am',
        'ar',
        'an',
        'hy',
        'as',
        'av',
        'ae',
        'ay',
        'az',
        'bm',
        'ba',
        'eu',
        'be',
        'bn',
        'bh',
        'bi',
        'bs',
        'br',
        'bg',
        'my',
        'ca',
        'ch',
        'ce',
        'ny',
        'zh',
        'zh-cn',
        'zh-tw',
        'zh-hk',
        'zh-sg',
        'cv',
        'kw',
        'co',
        'cr',
        'hr',
        'cs',
        'da',
        'dv',
        'nl',
        'dz',
        'en',
        'en-us',
        'en-gb',
        'en-au',
        'en-ca',
        'en-in',
        'eo',
        'et',
        'ee',
        'fo',
        'fj',
        'fi',
        'fr',
        'fr-fr',
        'fr-ca',
        'fr-be',
        'fr-ch',
        'ff',
        'gl',
        'ka',
        'de',
        'de-de',
        'de-at',
        'de-ch',
        'el',
        'gn',
        'gu',
        'ht',
        'ha',
        'he',
        'hz',
        'hi',
        'ho',
        'hu',
        'is',
        'io',
        'ig',
        'id',
        'ia',
        'ie',
        'iu',
        'ik',
        'ga',
        'it',
        'ja',
        'jv',
        'kl',
        'kn',
        'kr',
        'ks',
        'kk',
        'km',
        'ki',
        'rw',
        'ky',
        'kv',
        'kg',
        'ko',
        'ku',
        'kj',
        'la',
        'lb',
        'lg',
        'li',
        'ln',
        'lo',
        'lt',
        'lu',
        'lv',
        'gv',
        'mk',
        'mg',
        'ms',
        'ml',
        'mt',
        'mi',
        'mr',
        'mh',
        'mn',
        'na',
        'nv',
        'nd',
        'ne',
        'ng',
        'nb',
        'nn',
        'no',
        'nr',
        'oc',
        'oj',
        'cu',
        'om',
        'or',
        'os',
        'pa',
        'pi',
        'fa',
        'pl',
        'ps',
        'pt',
        'pt-br',
        'pt-pt',
        'qu',
        'rm',
        'rn',
        'ro',
        'ru',
        'sa',
        'sc',
        'sd',
        'se',
        'sm',
        'sg',
        'sr',
        'gd',
        'sn',
        'si',
        'sk',
        'sl',
        'so',
        'st',
        'es',
        'es-es',
        'es-mx',
        'es-ar',
        'su',
        'sw',
        'ss',
        'sv',
        'ta',
        'te',
        'tg',
        'th',
        'ti',
        'bo',
        'tk',
        'tl',
        'tn',
        'to',
        'tr',
        'ts',
        'tt',
        'tw',
        'ty',
        'ug',
        'uk',
        'ur',
        'uz',
        've',
        'vi',
        'vo',
        'wa',
        'cy',
        'wo',
        'fy',
        'xh',
        'yi',
        'yo',
        'za',
        'zu',
    ];
    /**
     * 初始化
     */
    public static function init()
    {
        if (!isset(static::$obj)) {
            static::$obj = new static();
        }
        return static::$obj;
    }
    /**
     * 执行路由
     */
    public static function do($ok = null, $not_find = null)
    {
        $IRoute = Route::run();
        $err = Route::$err;
        if (self::$status == 'ok') {
            echo $IRoute;
            $ok();
        } else {
            if ($err) {
                // 未找到路由
                $not_find();
            }
        }
    }
    /**
     * 处理对象
     */
    public static function do_object($res) {}
    /**
     * uri
     */
    public static function uri()
    {
        $uri = static::_uri();
        if ($uri != '/') {
            $uri = substr($uri, 1);
        }
        return $uri;
    }
    /**
     * 内部函数
     */
    public static function _uri()
    {
        // 解析URL $uri 返回 /app/public/ 或 /
        $uri = $_SERVER['REQUEST_URI'];
        $uri = str_replace("//", '/', $uri);
        if (self::$pre) {
            $uri = substr($uri, strlen(self::$pre) + 1);
        }
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        return $uri;
    }
    /**
     * 构造函数
     */
    public function __construct()
    {
        // 请求方式 GET POST
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->host = static::host();
    }
    /**
     * server_name
     * @return
     */
    public static function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }
    /**
     * host自动加http://或https://
     * @return string
     */
    public static function host()
    {
        $top = 'http';
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
            $top = 'https';
        } elseif (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 1 || $_SERVER['HTTPS'] == 'on')) {
            $top = 'https';
        }
        return $top . "://" . static::getServerName();
    }
    /**
     * domain路由
     *
     * @param string $domain
     * @param string $fun
     * @return call_user_func
     */
    public static function domain($domain, $fun)
    {
        if ($domain != static::getServerName()) {
            return;
        }
        call_user_func($fun);
    }
    /**
     * 取得控制器的 model id action
     * [action] => login
     * [module] => admin
     * [package] => core
     * [controller] => site
     * [lang] => zh-cn (if present)
     */
    public static function getActions()
    {
        $ar = static::init()->class;
        $id = str_replace('\\', '/', $ar[0]);
        $arr = explode("/", $id);
        $action = $ar[1];
        $action = strtolower($action);
        if (strpos($action, '-') !== false) {
            $action = static::toCamelCase($action);
        }
        $output['action'] = $action;
        $output['package'] = $arr[0];
        $output['module'] = $arr[1];
        $controller_name = strtolower($arr[3]);
        $controller_name = str_replace("controller", "", $controller_name);
        $output['controller'] = $controller_name;
        $uri = static::_uri();
        $uri_parts = explode('/', trim($uri, '/'));
        if (!empty($uri_parts) && in_array($uri_parts[0], static::$supported_languages)) {
            $output['lang'] = $uri_parts[0];
        }
        return $output;
    }
    /**
     * 对GET POST all 设置router
     */
    protected function setRoute($url, $do, $method = 'GET', $name = null)
    {
        if (is_string($do)) {
            $do = str_replace("/", "\\", $do);
            if (!$name && strpos($do, '@') !== false) {
                $name = str_replace("\\controller", "", $do);
                $name = str_replace("\\", "/", $name);
                $name = substr($name, strpos($name, '/') + 1);
                $name = str_replace("@", "/", $name);
            }
        }
        if (strpos($url, '|') !== false) {
            $arr = explode('|', $url);
            if (strpos($name, '|') !== false) {
                $names = explode('|', $name);
            } else {
                $names[0] = $name;
            }
            $i = 0;
            foreach ($arr as $v) {
                $this->setRoute($v, $do, $method, $names[$i] ?? '');
                $i++;
            }
            return;
        }
        if (strpos($url, '<') !== false) {
            $url = "#^\/{$url}\$#";
        } elseif (substr($url, 0, 1) != '/') {
            $url = '/' . $url;
        }
        static::$router[$method][$url] = $do;
        if ($name) {
            static::$router['__#named#__'][$name] = $url;
        }
    }
    /** * 生成URL */
    public static function url($url, $par = [])
    {
        return static::init()->createUrl($url, $par);
    }
    /**
     * 生成URL
     */
    protected function createUrl($url, $par = [])
    {
        $url = str_replace('.', '/', $url);
        $id = 'route_url' . $url . json_encode($par);
        if (isset(static::$app[$id]) && static::$app[$id]) {
            return static::$app[$id];
        }
        if (isset(static::$router['__#named#__'][$url])) {
            $r = static::$router['__#named#__'][$url];
            preg_match_all($this->match, $r, $out);
            $a = $out[0];
            $b = $out[1];
        } else {
            $a = array();
            $b = array();
        }
        if ($b) {
            $i = 0;
            foreach ($b as $v) {
                if (isset($a[$i]) && isset($par[$v])) {
                    $r = str_replace($a[$i], $par[$v], $r);
                    unset($par[$v]);
                }
                $i++;
            }
        }
        if (isset($r) && $r == '/') {
            goto GT;
        }
        if (isset($r) && substr($r, 0, 2) == '#^') {
            $r = substr($r, 4, -2);
        }
        if (isset($r) && substr($r, -1) == '/') {
            $r = substr($r, 0, -1);
        }
        if (!isset($r) || !$r) {
            $r = $url;
        }
        GT:
        if ($par) {
            $r = $r . "?" . http_build_query($par);
        }
        $url = $this->base_url . $r;
        $url = str_replace("//", '/', $url);
        $lang = self::getActions()['lang'] ?? '';
        if ($lang) {
            $url = '/' . $lang . $url;
        } else if (function_exists('cookie')) {
            $lang = cookie('lang');
            if ($lang) {
                $url = '/' . $lang . $url;
            }
        }
        static::$app[$id] = $url;
        return $url;
    }
    /**
     * get request
     */
    public static function get($url, $do, $name = null)
    {
        static::init()->setRoute($url, $do, 'GET', $name);
    }
    /**
     * post request
     */
    public static function post($url, $do, $name = null)
    {
        static::init()->setRoute($url, $do, 'POST', $name);
    }
    /**
     * put request
     */
    public static function put($url, $do, $name = null)
    {
        static::init()->setRoute($url, $do, 'PUT', $name);
    }
    /**
     * delete request
     */
    public static function delete($url, $do, $name = null)
    {
        static::init()->setRoute($url, $do, 'DELETE', $name);
    }
    /**
     * get/post request
     */
    public static function all($url, $do, $name = null)
    {
        static::init()->setRoute($url, $do, 'POST', $name);
        static::init()->setRoute($url, $do, 'GET', $name);
    }
    /**
     * 执行路由
     */
    public static function run()
    {
        return static::init()->exec();
    }
    /**
     * 内部函数, 执行解析URL 到对应namespace 或 closure
     */
    protected function exec()
    {
        // 解析URL $uri 返回 /app/public/ 或 /
        $uri = static::_uri();
        // 取得入口路径
        $index = static::getServerName();
        $index = substr($index, 0, strrpos($index, '/'));
        $action = substr($uri, strlen($index));
        $this->base_url = $index ? $index . '/' : '/';

        $uri_parts = explode('/', trim($action, '/'));
        $lang = null;
        if (!empty($uri_parts) && in_array($uri_parts[0], static::$supported_languages)) {
            $lang = array_shift($uri_parts);
            $action = '/' . implode('/', $uri_parts);
        }
        /**
         * 对于未使用正则的路由匹配到直接goto
         */
        if (isset(static::$router[$this->method][$action])) {
            $this->_value = static::$router[$this->method][$action];
        } else {
            $this->_value = false;
        }
        $data = [];
        if ($this->_value) {
            goto TODO;
        }
        if (!isset(static::$router[$this->method])) {
            goto NEXT;
        }
        foreach (static::$router[$this->method] as $pre => $class) {
            if (preg_match_all($this->match, $pre, $out)) {
                // 转成正则
                foreach ($out[0] as $k => $v) {
                    $pre = str_replace($v, "(" . $out[2][$k] . ")", $pre);
                }
                $pregs[$pre] = ['class' => $class, 'par' => $out[1]];
            }
        }
        NEXT:
        /**
         * 匹配当前URL是否存在路由
         */
        if (isset($pregs) && $pregs) {
            foreach ($pregs as $p => $par) {
                $class = $par['class'];
                if (preg_match($p, $action, $new)) {
                    unset($new[0]);
                    // 根据请求设置值 $_POST $_GET
                    $data = $this->setRequestData($this->arrayCombine($par['par'], $new));
                    $this->_url = $pre;
                    $this->_value = $class;
                    goto TODO;
                }
            }
        }
        if ($this->_value) {
            TODO:
            // 如果是 closure
            if (is_object($this->_value) || ($this->_value instanceof Closure)) {
                $res = call_user_func_array($this->_value, $data);
                return $this->output($res);
            }
            // 对 namespace 进行路由
            $this->_value = str_replace('/', '\\', $this->_value);
            $cls = explode('@', $this->_value);
            $class = $cls[0];
            if ($data) {
                foreach ($data as $k => $v) {
                    $class = str_replace("$" . $k, $v, $class);
                }
            }
            $ac = $cls[1];
            return $this->loadRoute($class, $ac, $data);
        }
        // 加载app\admin\login.php 这类的自动router
        $action = trim(str_replace('/', ' ', $action));
        $a = explode(' ', $action);
        $classes = [];
        if (isset($a[0])) {
            foreach (static::$r as $r) {
                $class = $r . "\\" . $a[0];
                if (isset($a[1])) {
                    $class = $class . "\\controller\\" . $a[1];
                }
                $classes[] = $class;
            }
        }
        if (isset($a[2]) && $a[2]) {
            $ac = static::toCamelCase($a[2]);
        } else {
            $ac = 'index';
        }
        foreach ($classes as $class) {
            $res = $this->loadRoute($class, $ac, $data);
            if ($res !== false) {
                self::$status = 'ok';
                return $res;
            }
        }
    }
    /**
     * 内部函数，支持框架内部框架
     */
    protected function loadRoute($class, $ac, $data)
    {
        $a = substr($class, 0, strrpos($class, '\\'));
        $b = substr($class, strrpos($class, '\\') + 1);
        $fun = Route::$controller_name;
        $b = $fun($b);
        $b = ucfirst($b);
        $class = $a . "\\" . $b . 'Controller';
        $ac = ucfirst($ac);
        $this->class = [$class, $ac];
        static::$current_class = $class;
        if (!class_exists($class)) {
            self::$err[] = "class 【" . $class . "】 not exists ";
            return false;
        }
        $obj = new $class();
        if (method_exists($class, 'before')) {
            $obj->before();
        }
        $res = '';
        if (method_exists($class, "action" . $ac)) {
            $action = "action" . $ac;
            $res = $obj->$action();
            self::$err = [];
            if ($res) {
                return $this->output($res);
            }
        } else {
            self::$err[] = "action 【action" . $ac . "】 not exists ";
            return false;
        }
        if (method_exists($class, 'after')) {
            $obj->after();
        }
    }
    /**
     * 输出
     */
    protected function output($res)
    {
        if (is_array($res)) {
            header('content-type:application/json');
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
            exit;
        } elseif (is_string($res)) {
            echo $res;
        } elseif (is_object($res)) {
            return static::do_object($res);
        }
    }
    /**
     * 内部函数 ，对arrayCombine优化
     */
    protected function arrayCombine($a = [], $b = [])
    {
        $i = 0;
        foreach ($b as $v) {
            $out[$a[$i]] = $v;
            $i++;
        }
        return $out;
    }
    /**
     * 内部函数 ,根据请求设置值
     */
    protected function setRequestData($data)
    {
        switch ($this->method) {
            case 'GET':
                $_GET = array_merge($data, $_GET);
                break;
            case 'POST':
                $_POST = array_merge($data, $_POST);
                break;
        }
        return $data;
    }
    /**
     * 驼峰字符串转换为URL友好格式
     */
    public static function toUrlFriendly($string)
    {
        $url = preg_replace('/([A-Z])/', '-$1', $string);
        $url = strtolower($url);
        $url = ltrim($url, '-');
        return $url;
    }
    /**
     * 转换为驼峰格式
     */
    public static function toCamelCase($string)
    {
        $string = str_replace('-', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        return $string;
    }
}
