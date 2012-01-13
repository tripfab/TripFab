<?php

class Model_PlacesLandscapes extends Zend_Db_Table_Abstract
{

    protected $_name = 'places_landscapes';

    protected $_referenceMap = array(
        'Places' => array(
            'columns'       => array('place_id'),
            'refTableClass' => 'Model_Places',
            'refColumns'    => array('id')
        ),
        'Lamdscapes'=> array(
            'columns'       => array('landscape_id'),
            'refTableClass' => 'Model_Landscapes',
            'refColumns'    => array('id')
        )
    );
    
}

