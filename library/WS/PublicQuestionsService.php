<?php

class WS_PublicQuestionsService {
 
    protected $questions;
    protected $answers;
    
    public function __construct() 
    {
        $this->questions = new Model_PublicQuestions();
        $this->answers   = new Model_PublicAnswers();
    }
    
    public function countNew($user)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $count = $db->fetchRow('select count(*) as total from public_answers where (question_user_id = '.$user.' and viewed is null)');
        return $count['total'];
    }
    
    public function getPostedBy($user, $pending = false)
    {
        $user = 1;
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('questions'=>'public_questions'));
        $select->join('listings', 'questions.listing_id = listings.id', array('listing_name' => 'title'));
        $select->join('listing_types', 'listings.main_type = listing_types.id', array('listing_type' => 'name'));
        $select->join('places', 'listings.city_id = places.id', array('city' => 'title'));
        $select->join(array('places2' => 'places'), 'listings.country_id = places2.id', array('country' => 'title'));
        $select->where('questions.user_id = ?', $user);
        if($pending)
            $select->where('questions.replied = ?', 0);
        else
            $select->where('questions.replied = ?', 1);
        $select->order('updated DESC');
        
        $questions = $db->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
        if($pending)
            return $questions;
        
        $_questions = array();
        foreach($questions as $q){
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select();
            $select->from(array('answers'=>'public_answers'));
            $select->join('users','answers.user_id = users.id', array(
                'user_image'=>'image',
                'username'  =>'name'
            ));
            $select->where('answers.question_id = ?', $q->id);
            $select->order('answers.created DESC');
            $answers = $db->fetchAll($select, array(), Zend_Db::FETCH_OBJ);
            $_questions[] = array(
                'question' => $q,
                'answers'  => $answers
            );
        }
        //var_dump($_questions); die; 
        return $_questions; 
    }
    
    public function getQuestionsOn($listing)
    {
        //$listing = 11;
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array('questions'=>'public_questions'));
        $select->join('users', 'questions.user_id = users.id', array(
            'user_image'=>'image',
        ));
        $select->where('questions.listing_id = ?', $listing);
        $select->orWhere('questions.listing_id = ?', 11);
        $select->order('questions.updated DESC');
        $questions = $db->fetchAll($select);
        if(!count($questions) > 0)
            return array();
        
        foreach($questions as $i => $question){
            $select2 = $db->select();
            $select2->from(array('answers'=>'public_answers'));
            $select2->join('users', 'answers.user_id = users.id', array(
                'user_role' =>'role_id',
                'user_name' =>'name',
                'username'  => 'username',
                'user_image'=> 'image'
            ));
            $select2->where('answers.question_id = ?', $question['id']);
            $answers = $db->fetchAll($select2, array(), Zend_Db::FETCH_OBJ);
            if(count($answers) > 0)
                $questions[$i]['answers'] = $answers;
        }
        
        return $questions;
    }
}
