<?php
// /lib/Module.Module.php

namespace Module;

interface Module {
    
    public function __construct($id, $configuration);
    
    public function in(\SimpleXMLElement $in);
    
    public function out();
    
    public function getId();
    
}
