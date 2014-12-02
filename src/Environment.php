<?php
namespace Crescendo;

interface Environment
{
    public function __construct(\Application $application, $name = null);
    
    public function getName();
}