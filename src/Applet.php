<?php
namespace Crescendo;

abstract class Applet
{
    protected $application;
    
    abstract public function getName();
    
    public function __construct(Application $application)
    {
        $this->setApplication($application);
    }
    
    public function onRegister()
    {
        return $this;
    }
    
    public function onUnregister()
    {
        return $this;
    }
    
    protected function getApplication()
    {
        return $this->application;
    }
    
    protected function setApplication(Application $application)
    {
        $this->application = $application;
        
        return $this;
    }
}