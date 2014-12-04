<?php
namespace Crescendo;

interface EnvironmentContainer
{
    public function __construct(array $environments);
    
    public function getEnvironments($reverse = false);
    
    public function getEnvironmentNames($reverse = false);
    
    public function getEnvironmentByName($name);
    
    public function getEnvironmentByClass($class);
    
    public function appendEnvironment(Environment $environment);
    
    public function prependEnvironment(Environment $environment);
    
    public function insertEnvironment(Environment $environment, $position);
    
    public function removeEnvironment(Environment $environment);
    
    public function removeEnvironmentByName($name);
    
    public function removeEnvironmentByClass($class);
    
    public function hasEnvironment(Environment $environment);
    
    public function hasEnvironmentWithName($name);
    
    public function hasEnvironmentWithClass($class);
}