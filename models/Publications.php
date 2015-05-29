<?php

use Phalcon\Mvc\Model;

class Publications extends Model 
{
	public $id;
	public $name;

	public function initialize() {
		$this->hasMany("id", "Subscriptions", "publication_id");
	}
}

?>