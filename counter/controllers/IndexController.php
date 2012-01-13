<?php

class IndexController extends Zend_Controller_Action
{
	public function init()
	{
		
	}
	
	public function indexAction()
	{
        $db = Zend_Db_Table::getDefaultAdapter();
		
		$day  = date('N');
		$time = strtotime('today');
		$time = $time - (86400 * ($day - 1));
		$sql1 = 'SELECT count(*) as total FROM emails';
        $sql2 = 'SELECT count(*) as total FROM emails WHERE date LIKE "'.date('Y-m-d').'%"';
		$sql3 = 'SELECT count(*) as total FROM emails WHERE date > "'.date('Y-m-d', $time).'"';
		$sql4 = 'SELECT count(*) as total FROM emails WHERE date > "'.date('Y-m-').'01"';
		
		$sql5 = 'SELECT * FROM emails ORDER BY date DESC LIMIT 100';
		
		$total = $db->fetchRow($sql1);
		$day   = $db->fetchRow($sql2);
		$week  = $db->fetchRow($sql3);
		$month = $db->fetchRow($sql4);
		
		$emails = $db->fetchAll($sql5, 5);
		
		$this->view->total = $total[total];
		$this->view->day   = $day[total];
		$this->view->week  = $week[total];
		$this->view->month = $month[total];
		
		$this->view->emails = $emails;
		
		//var_dump($this->view); die;
	}
}