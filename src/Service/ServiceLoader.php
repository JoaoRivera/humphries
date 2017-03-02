<?php
namespace Humphries\Service;

class ServiceLoader
{

    const SERVICE_NAMESPACE = "\\App\\Service\\";

    /**
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * @var ServiceAbstract[]
     */
    protected $_services;

    /**
     * @var ModelAbstract[]
     */
    protected $_models;

    /**
     * ServiceLoader constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->db = $this->app->getDB();
        $this->_services = [];
        $this->_models = [];
    }

    /**
     * Gets a service
     *
     * @param string $name
     * @param string $module
     * @return ServiceAbstract
     */
    public function getService($name, $module = '')
    {
        $namespace = $this->discoverNamespace(self::SERVICE_NAMESPACE, $name, $module);
        if (!isset($this->_services[$namespace])) {
            $this->_services[$namespace] = new $namespace( $this->app, $this );
        }

        return $this->_services[$namespace];
    }

    /**
     * Discovers namespace
     *
     * @param string $namespace
     * @param string $name
     * @param string $module
     * @return string
     */
    protected function discoverNamespace($namespace, $name, $module = '')
    {
        return $namespace . (empty($module) ? $name : $module . "\\" . $name);
    }

}