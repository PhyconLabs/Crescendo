<?php
namespace Crescendo\Config;

class EnvironmentContainer implements \Crescendo\EnvironmentContainer
{
    protected $environments;
    
    public function __construct(array $environments)
    {
        $this->environments = [];
        
        foreach ($environments as $environment) {
            $this->appendEnvironment($environment);
        }
    }
    
    public function getEnvironments()
    {
        return $this->environments;
    }
    
    public function getEnvironmentNames()
    {
        $names = [];
        
        foreach ($this->environments as $environment) {
            $names[] = $environment->getName();
        }
        
        return $names;
    }
    
    public function getEnvironmentByName($name)
    {
        foreach ($this->environments as $environment) {
            if ($environment->getName() === $name) {
                return $environment;
            }
        }
        
        return null;
    }
    
    public function getEnvironmentByClass($class)
    {
        $class = trim($class, "\\");
        
        foreach ($this->environments as $environment) {
            if (get_class($environment) === $class) {
                return $environment;
            }
        }
        
        return null;
    }
    
    public function appendEnvironment(\Crescendo\Environment $environment)
    {
        $this->environments[] = $environment;
        
        return $this;
    }
    
    public function prependEnvironment(\Crescendo\Environment $environment)
    {
        array_unshift($this->environments, $environment);
        
        return $this;
    }
    
    public function insertEnvironment(\Crescendo\Environment $environment, $position)
    {
        if ($position > count($this->environments)) {
            $this->environments[] = $environments;
        } elseif ($position <= 1) {
            array_unshift($this->environments, $environment);
        } else {
            array_splice($this->environments, $position - 1, [ $environment ]);
        }
        
        return $this;
    }
    
    public function removeEnvironment(\Crescendo\Environment $environment)
    {
        $this->environments = array_filter($this->environments, function($existingEnvironment) use ($environment) {
            return ($existingEnvironment !== $environment);
        });
        
        return $this;
    }
    
    public function removeEnvironmentByName($name)
    {
        $this->environments = array_filter($this->environments, function($environment) use ($name) {
            return ($environment->getName() !== $name);
        });
        
        return $this;
    }
    
    public function removeEnvironmentByClass($class)
    {
        $class = trim($class, "\\");
        
        $this->environments = array_filter($this->environments, function($environment) use ($class) {
            $environmentClass = get_class($environment);
            
            return ($environmentClass !== $class);
        });
        
        return $this;
    }
    
    public function hasEnvironment(\Crescendo\Environment $environment)
    {
        foreach ($this->environments as $existingEnvironment) {
            if ($existingEnvironment === $environment) {
                return true;
            }
        }
        
        return false;
    }
    
    public function hasEnvironmentWithName($name)
    {
        return !is_null($this->getEnvironmentByName($name));
    }
    
    public function hasEnvironmentWithClass($class)
    {
        return !is_null($this->getEnvironmentByClass($class));
    }
}