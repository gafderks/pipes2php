<?php
// /lib/Common.Evaluator.php

/**
 * Evaluates pipe definitions
 */

namespace Common;

class Evaluator {
    
    private $pipeDefinition;
    private $modules = array();
    
    /**
     * Creates a pipe Evaluator as specified by the given definition.
     * 
     * @param object $pipeDefinition definition for the module to be created
     */
    public function __construct($pipeDefinition) {
        $this->pipeDefinition = $pipeDefinition;
    }
    
    /**
     * Runs the pipe.
     */
    public function evaluate() {
        
        // create modules
        foreach($this->pipeDefinition->pipe->modules as $moduleKey => $moduleDefinition) {
            array_push($this->modules, $this->createModule($moduleDefinition));
        }
        
        // sort the modules topologically
        $wiresDefinition = $this->pipeDefinition->pipe->wires;
        $sorter = new \Common\TopologicalSort($wiresDefinition, $this->modules);
        $orderedModules = $sorter->sort();
        
        // final output - will eventually contain the output from the Output module
        $finalOutput;
        
        // run each module in order
        foreach ($orderedModules as $module) {
            $executionResult = $module->out();
            
            // supply result of module to linked target modules
            foreach ($this->mapsToX($module, $this->modules, $wiresDefinition) as $targetModule) {
                $targetModule->in($executionResult);
            }
            
            // overwrite final output
            $finalOutput = $executionResult;
        }
        
        // return output from the Output module
        return $finalOutput;
    }
    
    /**
     * Returns Module objects as specified by the given definition.
     * 
     * @param object $moduleDefinition definition for the module to be created
     */
    private function createModule($moduleDefinition) {
        // retrieve module name
        $moduleName = $moduleDefinition->type;
        // escape name
        $moduleName = preg_replace('/[^A-Za-z0-9]/', '', $moduleName);
        
        // retrieve module id
        $moduleId = (int) $moduleDefinition->id;
        
        // retrieve module configuration
        $moduleConf = $moduleDefinition->conf;
        
        // create module
        $className = "\\Module\\" . $moduleName;
        if(!class_exists($className, true)) {
            throw new \Exception("Module $moduleName does not exist");
            die;
        }
        $module = new $className($moduleId, $moduleConf);
        
        return $module;
    }
    
    /**
     * Returns an array of modules that the specified module maps to.
     * 
     * Almost a duplicate of \Common\TopologicalSort::mapsToX.
     * 
     * @param  \Module\Module   $from            Source module
     * @param  \Module\Module[] $X               Search pool for target modules
     * @param  array            $wiresDefinition Definition of the wires
     * @return \Module\Module[] Array of modules which are target to $from
     */
    private function mapsToX($from, $X, $wiresDefinition) {
        $to = array();
        foreach ($X as $module) {
            if ($this->mapsTo($from, $module, $wiresDefinition)) {
                array_push($to, $module);
            }
        }
        return $to;
    }
    
    /**
     * Returns whether a specified module has a directed wire to another specified module.
     * 
     * Almost a duplicate of \Common\TopologicalSort::mapsTo.
     * 
     * @param  \Module\Module $from            Source module
     * @param  \Module\Module $to              Target module
     * @param  array          $wiresDefinition Definition of the wires
     * @return boolean        whether the wire exists
     */
    private function mapsTo($from, $to, $wiresDefinition) {
        $mapsTo = false;
        $fromId = $from->getId();
        $toId = $to->getId();
        foreach ($wiresDefinition as $wire) {
            if ($wire->src->module == $fromId && $wire->tgt->module == $toId) {
                $mapsTo = true;
                break;
            }
        }
        return $mapsTo;
    }
    
}
