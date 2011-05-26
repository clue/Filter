<?php

/**
 * check whether the field value is an element of the given array
 * 
 * @author mE
 */
class Filter_Array extends Filter implements Filter_Interface_Negate, Filter_Interface_Simplify{
    /**
     * field to search in
     * 
     * @var string
     */
    protected $name;
    
    /**
     * array of values to search for
     * 
     * @var string
     */
    protected $array;
    
    /**
     * whether to negate the filter
     * 
     * @var boolean
     */
    protected $negate;
    
    /**
     * instanciate new array filter
     * 
     * @param string $name  field to search in
     * @param array  $array values to search for
     */
    public function __construct($name,$array){
        $this->name   = $name;
        $this->array  = $array;
        $this->negate = false;
    }
    
    public function toNegate(){
        $this->negate = !$this->negate;
        return $this;
    }
    
    public function toSimplify(){
        if(!$this->array){ // empty array to compare to
            return $this->negate; // normal = always fail, negated = always succeed
        }
        return $this;
    }
    
    public function toSql($db){
        $ret = $this->escapeDbName($this->name,$db);
        if($this->negate){
            $ret .= ' NOT';
        }
        $ret .= ' IN (';
        $first = true;
        foreach($this->array as $value){
            if($first){
                $first = false;
            }else{
                $ret .= ',';
            }
            $ret .= $this->escapeDbValue($value,$db);
        }
        if($first){ // empty array given, no need to actually filter
            return $this->negate ? '1' : '0'; // searching in empty array will always fail
        }
        $ret .= ')';
        return $ret;
    }
}