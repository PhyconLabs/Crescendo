<?php
namespace Crescendo;

use \SDS\ClassSupport\Klass;
use \SDS\IoC\Exceptions\IoCException;
use \SDS\ClassSupport\Exceptions\ClassSupportException;

class Application
{
    const ENVIRONMENT_VARIABLE = "CRESCENDO_ENV";
    
    protected static $instance;
    
    protected $container;
    protected $applets;
    
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
    {var_dump("CREATE!");
        $this->applets = [];
        
        $this->setContainer($this->dispatchContainer());
    }
    
    public function registerApplet($applet)
    {
        if (is_object($applet)) {
            return $this->registerAppletByObject($applet);
        } else {
            if (preg_match("#^[a-zA-Z0-9-_]+\\/[a-zA-Z0-9-_]+$#", $applet)) {
                return $this->registerAppletByName($applet);
            } else {
                return $this->registerAppletFromContainer($applet);
            }
        }
    }
    
    public function registerAppletByObject($applet)
    {
        if (!is_object($applet)) {
            $class = get_class($class);
            $type = gettype($applet);
            
            throw new Exceptions\InvalidAppletException(
                "Applet must be an object if given to `{$class}::registerAppletByObject()` method. `{$type}` given."
            );
        }
        
        if (!$applet instanceof Applet) {
            $class = get_class($applet);
            
            throw new Exceptions\InvalidAppletException(
                "Applet must be instance of `\\Crescendo\\Applet` - instance of `\\{$class}` given."
            );
        }
        
        $appletName = $applet->getName();
        
        if (isset($this->applets[$appletName])) {
            $applet = $this->applets[$appletName];
        } else {
            $this->applets[$appletName] = $applet;
            
            $applet->onRegister();
        }
        
        return $applet;
    }
    
    public function registerAppletFromContainer($applet)
    {
        $container = $this->getContainer();
        $applet = $container->make($applet, [
            "application" => $this
        ]);
        
        return $this->registerAppletByObject($applet);
    }
    
    public function registerAppletByName($appletName)
    {
        $appletPath = COMPOSER_ROOT_PATH . "/{$appletName}/src/Applet.php";
        
        try {
            $klass = Klass::createFromPath($appletPath);
        } catch (ClassSupportException $e) {
            throw new Exceptions\InvalidAppletException(
                "Couldn't retrieve applet details for `{$appletName}` with path `{$appletPath}`.",
                0,
                $e
            );
        }
        
        return $this->registerAppletFromContainer($klass->getClass());
    }
    
    public function initEnvironment()
    {
        $container = $this->getContainer();
        
        $this->bindEnvironmentImplementation();
        
        $environments = $this->findEnvironments();
        $environments = $this->resolveEnvironments($environments);
        
        $this->bindEnvironmentContainerImplementation($environments);
        
        $environmentContainer = $container->make("Crescendo\\EnvironmentContainer");
        
        $this->ensureGlobalEnvironment($environmentContainer);
        
        return $this;
    }
    
    public function initConfig()
    {
        $this->bindConfigImplementation();
        $this->setDefaultConfigIncludePaths();
        
        return $this;
    }
    
    public function initApplets()
    {
        $container = $this->getContainer();
        $config = $container->make("Crescendo\\Config");
        
        $applets = $config->get("applets", []);
        
        if (is_array($applets)) {
            foreach ($applets as $applet) {
                $this->registerApplet($applet);
            }
        }
        
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
            "global" => "Crescendo\\Config\\Environments\\GlobalEnvironment",
            "development" => "Crescendo\\Config\\Environments\\DevelopmentEnvironment",
            "testing" => "Crescendo\\Config\\Environments\\TestingEnvironment",
            "staging" => "Crescendo\\Config\\Environments\\StagingEnvironment",
            "production" => "Crescendo\\Config\\Environments\\ProductionEnvironment"
        ];
    }
    
    protected function bindEnvironmentImplementation()
    {
        $container = $this->getContainer();
        
        $container->bind("Crescendo\\Environment", "Crescendo\\Config\\Environment");
        
        return $this;
    }
    
    protected function bindEnvironmentContainerImplementation(array $environments)
    {
        $container = $this->getContainer();
        
        $container->bindSingleton("Crescendo\\EnvironmentContainer", "Crescendo\\Config\\EnvironmentContainer");
        $container->bindSingletonArgument("Crescendo\\EnvironmentContainer", "environments", $environments);
        
        return $this;
    }
    
    protected function bindConfigImplementation()
    {
        $container = $this->getContainer();
        
        $container->bindSingleton("Crescendo\\Config", "Crescendo\\Config\\Config");
        
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
            $environments = [ "production" ];
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
                $environment = $container->make("Crescendo\\Environment", [
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
    
    protected function setDefaultConfigIncludePaths()
    {
        $container = $this->getContainer();
        $config = $container->make("Crescendo\\Config");
        
        $config->addIncludePath(ROOT_PATH . "/config", $config::CRESCENDO_PRIORITY);
        $config->addIncludePath(APPLICATION_ROOT_PATH . "/config", $config::APPLICATION_PRIORITY);
        
        return $this;
    }
}