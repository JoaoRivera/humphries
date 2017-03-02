<?php
namespace Humphries;

use Humphries\Contracts\CacheInterface;
use Humphries\Contracts\RequestInterface;
use Humphries\Contracts\ResponseInterface;
use Humphries\Security\Encryption;
use Humphries\Utility\ExceptionFactory;
use Humphries\Service\ServiceLoader;
use Humphries\Http\Request;
use Humphries\Http\Response;
use Humphries\Utility\Config;
use Phalcon\Crypt;
use Phalcon\Di;
use League\Flysystem\Filesystem;
use Phalcon\Security;
use \Whoops\Run as Whoops;

abstract class Application extends Di
{

    const APP = 'app';
    const CONFIG = 'config';
    const DB = 'db';
    const CACHE = 'cache';
    const FILE_SYSTEM = 'filesystem.private';
    const PUBLIC_FILE_SYSTEM = 'filesystem.public';
    const REQUEST = 'request';
    const RESPONSE = 'response';
    const SECURITY = 'security';
    const SERVICE_LOADER = 'service.loader';

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var null|self
     */
    protected static $instance = null;

    /**
     * Application constructor.
     *
     * @param string $rootPath
     */
    public function __construct($rootPath = __DIR__ . '/..')
    {
        parent::__construct();

        $this->setInstance($this);
        $this->setRootPath($rootPath);
    }

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Register a list of commands.
     *
     * @return self
     */
    public function registerCommand()
    {
        foreach (func_get_args() as $param) $this->getConsole()->add(new $param($this));

        return $this;
    }

    /**
     * Registers the application core services.
     *
     * @throws \Exception
     * @return self
     */
    public function loadCore()
    {
        if (!file_exists($this->configPath("{$this->getEnvironment()}.php"))) {
            ExceptionFactory::InvalidArgumentException("Missing configuration file '{$this->getEnvironment()}'");
        }

        // Config
        $this->setShared(self::CONFIG, function() {
            return (new Config("default.php"))
                    ->merge(new Config($this->configPath("{$this->getEnvironment()}.php")));
        });

        // Database Connections
        $this->loadDatabaseConnections();

        // Encryption
        $this->setShared(self::CRYPT, function() {
            $crypt = new Encryption();
            $crypt->setKey($this->getConfig()->key);

            return $crypt;
        });

        // HTTP Request
        $this->setShared(self::REQUEST, function() {
            return new Request($this);
        });

        // HTTP Response
        $this->setShared(self::RESPONSE, function() {
            return new Response($this);
        });

        // Cache
        $this->setShared(self::CACHE, function() {
            $cache = $this->getConfig()->cache;

            $cache = new $cache->driver($this, ...$cache->options->toArray());

            return $cache;
        });

        // File System
        $this->setShared(self::FILE_SYSTEM, function() {
            $fsConfig = $this->getConfig()->filesystem;
            $fileSystem = new Filesystem(new $fsConfig->driver(...$fsConfig->options->toArray()));
            $fileSystem->addPlugin(new ListFiles());

            return $fileSystem;
        });

        // Public File System
        $this->setShared(self::PUBLIC_FILE_SYSTEM, function() {
            $fsConfig = $this->getConfig()->public_filesystem;
            $fileSystem = new Filesystem(new $fsConfig->driver(...$fsConfig->options->toArray()));
            $fileSystem->addPlugin(new ListFiles());

            return $fileSystem;
        });

        // Security
        $this->setShared(self::SECURITY, function() {
            return new Security();
        });

        // Service Loader
        $this->setShared(self::SERVICE_LOADER, function() {
            return new ServiceLoader($this);
        });

        // Error Handler
        $errorHandlerDriver =  ?
            $this->getConfig()->error->cli : $this->getConfig()->error->http;

        $whoops = new Whoops();
        $whoops->pushHandler($this->getErrorHandler());
        $whoops->register();


        return $this;
    }

    /**
     * Handles the request
     */
    public function handle()
    {
        $this->loadCore();

        php_sapi_name() === 'cli' ? $this->getConsole()->run() : $this->getRouter()->run();
    }

    /**
     * Gets Root Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function rootPath($path = null)
    {
        return $this->rootPath . ($path ? '/'.$path : $path);
    }

    /**
     * Gets App Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function appPath($path = null)
    {
        return $this->rootPath('App' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Public Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function publicPath($path = null)
    {
        return $this->rootPath('public' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Storage Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function storagePath($path = null)
    {
        return $this->rootPath('Storage' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Tests Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function testPath($path = null)
    {
        return $this->rootPath('Test' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Resource Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function resourcePath($path = null)
    {
        return $this->rootPath('Resource' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Config Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function configPath($path = null)
    {
        return $this->rootPath('Config' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Controller Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function controllerPath($path = null)
    {
        return $this->appPath('Controller' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Model Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function modelPath($path = null)
    {
        return $this->appPath('Model' . ($path ? '/'.$path : $path));
    }

    /**
     * Gets Service Path
     *
     * @param string|null $path
     *
     * @return string
     */
    public function servicePath($path = null)
    {
        return $this->appPath('Service' . ($path ? '/'.$path : $path));
    }

    /**
     * Get Domain Url
     *
     * @param string $key
     * @param string $path
     *
     * @return string
     */
    public function domainPath($key, $path)
    {
        return $this->getConfig()->domain->get($key, '') . ($path ? '/'.$path : $path);
    }

    /**
     * Gets a Database Connection
     *
     * @param string $name
     *
     * @return Connection
     */
    public function getDatabase($name = null)
    {
        return $this->get(self::DB);
    }

    /**
     * Gets Config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->get(self::CONFIG);
    }

    /**
     * Get Cache
     *
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->get(self::CACHE);
    }

    /**
     * Get File system
     *
     * @param boolean $public
     *
     * @return Filesystem
     */
    public function getFileSystem($public = false)
    {
        return $public ? $this->get(self::PUBLIC_FILE_SYSTEM) : $this->get(self::FILE_SYSTEM);
    }

    /**
     * Get Crypt
     *
     * @return Crypt
     */
    public function getCrypt()
    {
        return $this->get(self::CRYPT);
    }

    /**
     * Get Request
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->get(self::REQUEST);
    }

    /**
     * Get Response
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->get(self::RESPONSE);
    }

    /**
     * Get Security
     *
     * @return Security
     */
    public function getSecurity()
    {
        return $this->get(self::SECURITY);
    }

    /**
     * Get Service Loader
     *
     * @return ServiceLoader
     */
    public function getServiceLoader()
    {
        return $this->get(self::SERVICE_LOADER);
    }

    /**
     * Gets environment value
     *
     * @return string
     */
    public function getEnvironment()
    {
        return env('APP_ENV', 'development');
    }

    /**
     * Loads possible database connections.
     */
    protected function loadDatabaseConnections()
    {
        foreach ($this->getConfig()->database as $name => $options) {
            $name = is_string($name) ? $name : 'default';
            $this->set(self::DB . '.' . $name, function() use ($options) {
                return new $options['driver']($options['params']);
            });
        }
    }

    /**
     * Sets the application root path
     *
     * @param string $path
     */
    protected function setRootPath($path)
    {
        $this->rootPath = $path;
        if (!defined('ROOT_PATH')) define('ROOT_PATH', $path);
    }

    /**
     * Set the shared instance of the container.
     *
     * @param  Application $container
     * @return static
     */
    protected static function setInstance(Application $container = null)
    {
        return static::$instance = $container;
    }

    /**
     * Checks if the request is of type HTTP.
     *
     * @return boolean
     */
    protected function isHttpRequest()
    {
        return !$this->isCliRequest();
    }

    /**
     * Checks if the request is of type CLI.
     *
     * @return boolean
     */
    protected function isCliRequest()
    {
        return php_sapi_name() === 'cli';
    }
}
