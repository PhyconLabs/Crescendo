<?php
namespace Crescendo;

class Application
{
    protected static $instance;
    
    protected $container;
    
    public static function init()
    {
        if (isset(static::$instance)) {
            $class = static::class;
            
            throw new Exceptions\ApplicationAlreadyInitializedException(
                "Application `{$class}` is already initialized. You can call `{$class}::getInstance()` to retrieve it."
            );
        }
        
        static::$instance = new static();
        
        return static::getInstance();
    }
    
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            $class = static::class;
            
            throw new Exceptions\ApplicationNotInitializedException(
                "Application `{$class}` is not initialized. Call `{$class}::init()` to initialize it."
            );
        }
        
        return static::$instance;
    }
    
    public function __construct()
    {
        $this->setContainer($this->dispatchContainer());
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    public function setContainer(IoC\Container $container)
    {
        $this->container = $container;
        
        return $this;
    }
    
    protected function dispatchContainer()
    {
        return new IoC\Container();
    }
}