<?php
//User.class.php

require_once 'DB.class.php';


class Report {

	public $task_id;
	public $user_id;
	public $task;
	public $time;
	public $comment;
	public $day;
	public $rep_date;
	public $on_vacation;

	//Constructor is called whenever a new object is created.
	//Takes an associative array with the DB row as an argument.
	function __construct($data) {
		$this->user_id = (isset($data['user_id'])) ? $data['user_id'] : "";
		$this->task_id = (isset($data['task_id'])) ? $data['task_id'] : "";
		$this->task = (isset($data['task'])) ? $data['task'] : "";
		$this->time = (isset($data['time'])) ? $data['time'] : "";
		$this->comment = (isset($data['comment'])) ? $data['comment'] : "";
		$this->day = (isset($data['day'])) ? $data['day'] : "";
		$this->rep_date = (isset($data['rep_date'])) ? $data['rep_date'] : "";
		$this->on_vacation = (isset($data['on_vacation'])) ? $data['on_vacation'] : "";
	}

	public function save($isNewReport = false) {
		$db = new DB();
		
		if(!$isNewReport) {
			$data = array(
				"task" => "'$this->task'",
				"time" => "'$this->time'",
				"comment" => "'$this->comment'",
				"on_vacation" => "'$this->on_vacation'");

			$db->update($data, 'reports', 'user_id = '.$this->user_id.'AND report_id = '.$this->report_id);
		}else {
			$tempDate = "'".date("Y-m-d",time())."'"; // ???

			$data = array(
				"user_id" => "'$this->user_id'",
				"task" => "'$this->task'",
				"time" => "'$this->time'",
				"comment" => "'$this->comment'",
				"on_vacation" => "'$this->on_vacation'",
				"rep_date" => "'".date("Y-m-d",time())."'",
				"day" => "'".date('D', strtotime( $tempDate))."'"
				);
			
			$this->id = $db->insert($data, 'reports');
			

			
		}
		return true;
	}
	
}

?>