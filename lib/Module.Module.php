<?php
// /lib/Module.Module.php

namespace Module;

interface Module {
    
    public function in(SimpleXMLElement $in);
    
    public function out();
    
}
