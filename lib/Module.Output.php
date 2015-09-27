<?php
// /lib/Module.Output.php

/**
 * 
 */

namespace Module;

class Output implements \Module\Module {
    
    private $id;
    private $input;
    private $conf;
    
    /**
     * 
     */
    public function __construct($id, $conf) {
        $this->id = $id;
        $this->conf = $conf;
    }
    
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     */
    public function in(\SimpleXMLElement $in) {
        $this->input = $in;
    }
    
    /**
     * 
     */
    public function out() {
        return $this->input;
    }
}
