<?php
namespace Crescendo;

use \SDS\IoC\Exceptions\IoCException;

class Application
{
    const ENVIRONMENT_VARIABLE = "CRESCENDO_ENV";
    
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
    
    public function initEnvironment()
    {
        $container = $this->getContainer();
        
        $this->bindEnvironmentImplementation();
        
        $environments = $this->findEnvironments();
        $environments = $this->resolveEnvironments($environments);
        
        $this->bindEnvironmentContainerImplementation($environments);
        
        $environmentContainer = $container->make("\\Crescendo\\EnvironmentContainer");
        
        $this->ensureGlobalEnvironment($environmentContainer);
        
        return $this;
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
    
    protected function getEnvironmentMap()
    {
        return [
            "global" => "\\Crescendo\\Config\\Environments\\GlobalEnvironment",
            "development" => "\\Crescendo\\Config\\Environments\\DevelopmentEnvironment",
            "testing" => "\\Crescendo\\Config\\Environments\\TestingEnvironment",
            "staging" => "\\Crescendo\\Config\\Environments\\StagingEnvironment",
            "production" => "\\Crescendo\\Config\\Environments\\ProductionEnvironment"
        ];
    }
    
    protected function bindEnvironmentImplementation()
    {
        $container = $this->getContainer();
        
        $container->bind("\\Crescendo\\Environment", "\\Crescendo\\Config\\Environment");
        
        return $this;
    }
    
    protected function bindEnvironmentContainerImplementation(array $environments)
    {
        $container = $this->getContainer();
        
        $container->bindSingleton("\\Crescendo\\EnvironmentContainer", "\\Crescendo\\Config\\EnvironmentContainer");
        $container->bindSingletonArgument("\\Crescendo\\EnvironmentContainer", "environments", $environments);
        
        return $this;
    }
    
    protected function findEnvironments()
    {
        $environmentFile = APPLICATION_ROOT_PATH . "/environment.php";
        
        if (file_exists($environmentFile)) {
            $environments = require $environmentFile;
            
            if (!is_array($environments)) {
                $environments = [ $environments ];
            }
        } elseif (($environmentVariableValue = getenv(static::ENVIRONMENT_VARIABLE)) !== false) {
            $environments = explode(",", $environmentVariableValue);
        } else {
            $environments = [];
        }
        
        return $environments;
    }
    
    protected function resolveEnvironments(array $environments)
    {
        foreach ($environments as &$environment) {
            $environment = $this->resolveEnvironment($environment);
        }
        unset($environment);
        
        return $environments;
    }
    
    protected function resolveEnvironment($environment)
    {
        if (!is_object($environment)) {
            $map = $this->getEnvironmentMap();
            $container = $this->getContainer();
            
            if (isset($map[$environment])) {
                $environment = $map[$environment];
            }
            
            try {
                $environment = $container->make($environment);
            } catch (IoCException $e) {
                $environment = $container->make("\\Crescendo\\Environment", [
                    "name" => $environment
                ]);
            }
        }
        
        return $environment;
    }
    
    protected function ensureGlobalEnvironment(EnvironmentContainer $environmentContainer)
    {
        if (!$environmentContainer->hasEnvironmentWithName("global")) {
            $globalEnvironment = $this->resolveEnvironment("global");
            
            $environmentContainer->appendEnvironment($globalEnvironment);
        }
        
        return $this;
    }
}