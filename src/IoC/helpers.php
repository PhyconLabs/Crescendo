<?php
namespace Crescendo\IoC {
    function application()
    {
        return \Application::getInstance();
    }
    
    function bind($abstract, $concrete, array $options = [])
    {
        return application()->getContainer()->bind($abstract, $concrete, $options);
    }
    
    function bindSingleton($abstract, $concrete, array $options = [])
    {
        return application()->getContainer()->bindSingleton($abstract, $concrete, $options);
    }
    
    function bindArgument($abstractClass, $abstractArgument, $concrete, array $options = [])
    {
        return application()->getContainer()->bindArgument($abstractClass, $abstractArgument, $concrete, $options);
    }
    
    function bindSingletonArgument($abstractClass, $abstractArgument, $concrete, array $options = [])
    {
        return application()->getContainer()->bindSingletonArgument($abstractClass, $abstractArgument, $concrete, $options);
    }
    
    function unbind($abstract)
    {
        return application()->getContainer()->unbind($abstract);
    }
    
    function make($abstract, array $arguments = [])
    {
        return application()->getContainer()->make($abstract, $arguments);
    }
    
    function isBound($abstract)
    {
        return application()->getContainer()->isBound($abstract);
    }
    
    function isSingleton($abstract)
    {
        return application()->getContainer()->isSingleton($abstract);
    }
    
    function isArgument($abstract)
    {
        return application()->getContainer()->isArgument($abstract);
    }
}

namespace {
    if (isset($globalizeHelperFunctions) && $globalizeHelperFunctions) {
        if (!function_exists("application")) {
            function application()
            {
                return \Crescendo\IoC\application();
            }
        }
        
        if (!function_exists("bind")) {
            function bind($abstract, $concrete, array $options = [])
            {
                return \Crescendo\IoC\bind($abstract, $concrete, $options);
            }
        }
        
        if (!function_exists("bindSingleton")) {
            function bindSingleton($abstract, $concrete, array $options = [])
            {
                return \Crescendo\IoC\bindSingleton($abstract, $concrete, $options);
            }
        }
        
        if (!function_exists("bindArgument")) {
            function bindArgument($abstractClass, $abstractArgument, $concrete, array $options = [])
            {
                return \Crescendo\IoC\bindArgument($abstractClass, $abstractArgument, $concrete, $options);
            }
        }
        
        if (!function_exists("bindSingletonArgument")) {
            function bindSingletonArgument($abstractClass, $abstractArgument, $concrete, array $options = [])
            {
                return \Crescendo\IoC\bindSingletonArgument($abstractClass, $abstractArgument, $concrete, $options);
            }
        }
        
        if (!function_exists("unbind")) {
            function unbind($abstract)
            {
                return \Crescendo\IoC\unbind($abstract);
            }
        }
        
        if (!function_exists("make")) {
            function make($abstract, array $arguments = [])
            {
                return \Crescendo\IoC\make($abstract, $arguments);
            }
        }
        
        if (!function_exists("isBound")) {
            function isBound($abstract)
            {
                return \Crescendo\IoC\isBound($abstract);
            }
        }
        
        if (!function_exists("isSingleton")) {
            function isSingleton($abstract)
            {
                return \Crescendo\IoC\isSingleton($abstract);
            }
        }
        
        if (!function_exists("isArgument")) {
            function isArgument($abstract)
            {
                return \Crescendo\IoC\isArgument($abstract);
            }
        }
    }
}