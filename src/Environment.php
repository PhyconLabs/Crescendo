<?php
namespace Crescendo;

interface Environment
{
    public function __construct($name = null);
    
    public function getName();
}