<?php
namespace Crescendo\Config;

use \SDS\ArraySupport\Arr;

class Config implements \Crescendo\Config
{
    protected $options;
    protected $includePaths;
    protected $loadedGroups;
    protected $environmentContainer;
    
    public function __construct(\Crescendo\EnvironmentContainer $environmentContainer)
    {
        $this->options = new Arr([]);
        $this->includePaths = [];
        $this->loadedGroups = [];
        
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
        $this->ensureGroupExists($option);
        
        return $this->options->offsetDeepGet($option, $default);
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
        $this->ensureGroupExists($option);
        
        return $this->options->offsetDeepExists($option);
    }
    
    public function addIncludePath($path, $priority = self::APPLET_PRIORITY)
    {
        $priority = (int) $priority;
        $path = rtrim($path, "/\\");
        
        if (!isset($this->includePaths[$priority])) {
            $this->includePaths[$priority] = [];
            
            ksort($this->includePaths, SORT_NUMERIC);
        }
        
        $this->includePaths[$priority][] = $path;
        
        return $this;
    }
    
    protected function sanitizeOption($option)
    {
        $option = trim($option, " \t\n\r\0\x0B.");
        
        return $option;
    }
    
    protected function ensureGroupExists($option)
    {
        $group = explode(".", $option)[0];
        
        if (!in_array("application", $this->loadedGroups)) {
            $this->loadGroup("application");
        }
        
        if (!isset($this->options[$group]) && !in_array($group, $this->loadedGroups)) {
            $this->loadGroup($group);
        }
        
        return $this;
    }
    
    protected function loadGroup($group)
    {
        $group = $this->sanitizeOption($group);
        $environmentContainer = $this->getEnvironmentContainer();
        $environments = $environmentContainer->getEnvironmentNames(true);
        
        foreach ($this->includePaths as $priorityPaths) {
            foreach ($priorityPaths as $path) {
                if (is_dir($path)) {
                    foreach ($environments as $environment) {
                        $environmentDirectory = ($environment === "global") ? "" : "/{$environment}";
                        $configPath = "{$path}{$environmentDirectory}/{$group}.php";
                        
                        if (file_exists($configPath)) {
                            $options = require $configPath;
                            
                            if (!is_array($options)) {
                                $options = null;
                            }
                            
                            if (isset($options)) {
                                if ($group !== "application") {
                                    $options = [
                                        $group => $options
                                    ];
                                }
                                
                                $this->options->deepMerge($options);
                            }
                        }
                    }
                }
            }
        }
        
        $this->loadedGroups[] = $group;
        
        return $this;
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