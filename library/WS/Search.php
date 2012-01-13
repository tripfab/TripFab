<?php

class WS_Search {
    
    protected $index;
    
    public function __construct() {
        $this->index = new Model_Index();
    }
    
    public function find($q)
    {
        return $this->index->search($q);
    }
}

?>
