<?php 

use Phalcon\Mvc\Model;

class Subscriptions extends Model
{
	public $id;
	public $email;
	public $name;
	public $publication_id;

	public function initialize() {
		$this->belongsTo("publication_id", "Publications", "id");
	}
}

?>