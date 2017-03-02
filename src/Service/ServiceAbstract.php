<?php
namespace Humphries\Service;

use Humphries\Application;
use Humphries\ApplicationTrait;

abstract class ServiceAbstract
{

    use ApplicationTrait;

    /**
     * @var ServiceLoader
     */
    protected $serviceLoader;

    /**
     * ServiceAbstract constructor.
     *
     * @param Application   $application
     * @param ServiceLoader $serviceLoader
     */
    public function __construct(Application $application, ServiceLoader $serviceLoader)
    {
        $this->application = $application;
        $this->serviceLoader = $serviceLoader;
        $this->initialize();
    }

    /**
     * Initializes service
     *
     * @return self
     */
    protected function initialize() { return $this; }

    /**
     * Gets a service
     *
     * @param        $name
     * @param string $module
     *
     * @return ServiceAbstract
     */
    protected function getService($name, $module = '')
    {
        return $this->serviceLoader->getService($name, $module);
    }

    /**
     * Gets Database Connection
     *
     * @return \Illuminate\Database\Connection
     */
    protected function getDb()
    {
        return $this->getApp()->getDB();
    }

    /**
     * Gets Cache
     *
     * @return \Contracts\Cache
     */
    protected function getCache()
    {
        return $this->getApp()->getCache();
    }

}
