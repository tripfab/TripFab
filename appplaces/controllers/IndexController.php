<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $places = new Zend_Db_Table('places');
        $select = $places->select();
        $select->where('parent_id = 18');
        $select->order('title ASC');
        
        $regions = $places->fetchAll($select);
        
        $this->view->cities = $regions;
    }


}

