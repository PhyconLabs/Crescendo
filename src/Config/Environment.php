<?php
namespace Crescendo\Config;

use \Application;
use \SDS\ClassSupport\Klass;

class Environment implements \Crescendo\Environment
{
    protected $application;
    protected $name;
    
    public function __construct(Application $application, $name = null)
    {
        if (!isset($name)) {
            $name = $this->resolveNameFromClassName();
        }
        
        $this->setApplication($application);
        $this->setName($name);
    }
    
    public function getName()
    {
        return $this->name;
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
    
    protected function setName($name)
    {
        $this->name = (string) $name;
        
        return $this;
    }
    
    protected function resolveNameFromClassName()
    {
        $klass = new Klass($this);
        $name = $klass->getName();
        
        $name = basename($name, "Environment");
        $name = strtolower($name);
        
        return $name;
    }
}