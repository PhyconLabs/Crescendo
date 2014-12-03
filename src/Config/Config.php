<?php
namespace Crescendo\Config;

class Config implements \Crescendo\Config
{
    protected $options;
    protected $includePaths;
    protected $environmentContainer;
    
    public function __construct(\Crescendo\EnvironmentContainer $environmentContainer)
    {
        $this->options = [];
        $this->includePaths = [];
        
        $this->setEnvironmentContainer($environmentContainer);
    }
    
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    
    public function get($option, $default = null)
    {
        //
    }
    
    public function set($option, $value)
    {
        //
    }
    
    public function add($option, $value)
    {
        //
    }
    
    public function replace($option, $value)
    {
        //
    }
    
    public function remove($option)
    {
        //
    }
    
    public function has($option)
    {
        //
    }
    
    public function addIncludePath($path, $priority = self::APPLET_PRIORITY)
    {
        //
    }
    
    protected function getEnvironmentContainer()
    {
        return $this->environmentContainer;
    }
    
    protected function setEnvironmentContainer(\Crescendo\EnvironmentContainer $environmentContainer)
    {
        $this->environmentContainer = $environmentContainer;
        
        return $this;
    }
}