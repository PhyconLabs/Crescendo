<?php
namespace Crescendo;

use \ArrayAccess;

interface Config extends ArrayAccess
{
    const CRESCENDO_PRIORITY = 0;
    const APPLET_PRIORITY = 100;
    const APPLICATION_PRIORITY = 1000;
    
    public function __construct(EnvironmentContainer $environmentContainer);
    
    public function get($option, $default = null);
    
    public function set($option, $value);
    
    public function add($option, $value);
    
    public function replace($option, $value);
    
    public function remove($option);
    
    public function has($option);
    
    public function addIncludePath($path, $priority = self::APPLET_PRIORITY);
}