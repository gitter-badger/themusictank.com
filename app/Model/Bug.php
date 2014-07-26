<?php

class Bug extends AppModel
{
	public $belongsTo    = array("User");

	public function createReport($type, $where, $userid)
	{
		$this->save(array(
			"user_id" 	=> 1,
			"is_fixed" 	=> false,
			"type" 		=> $type,
			"location" 	=> $where,
			"reporter_id" => $userid
		));

		return $this->getLastInsertID();
	}

	public function updateReport($id, $details)
	{
		return $this->save(array(
			"id" 	=> $id,
			"details" 	=> $details
		));
	}
}
