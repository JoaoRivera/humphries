<?php
namespace Humphries;

trait ApplicationTrait
{

    /**
     * @var Application
     */
    protected $application = null;

    /**
     * Gets the application
     *
     * @return Application
     */
    protected function getApplication()
    {
        if (is_null($this->application)) $this->application = Application::getInstance();

        return $this->application;
    }

    /**
     * Gets a service
     *
     * @param string $name
     * @param string $module
     *
     * @return
     */
    protected function getService($name, $module = '')
    {
        return $this->getApplication()->getServiceLoader()->getService($name, $module);
    }

}