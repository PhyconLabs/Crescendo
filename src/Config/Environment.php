<?php
namespace Crescendo\Config;

use \SDS\ClassSupport\Klass;

class Environment implements \Crescendo\Environment
{
    protected $name;
    
    public function __construct($name = null)
    {
        if (!isset($name)) {
            $name = $this->resolveNameFromClassName();
        }
        
        $this->setName($name);
    }
    
    public function getName()
    {
        return $this->name;
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