<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

//auto load the models
$loader = new Loader();

$loader->registerDirs(array(
	__DIR__ . '/models/' 
))->register();

$di = new FactoryDefault();

//setup the database
$di->set('db', function(){
	return new PdoMysql(array(
		"host" => "waldorf.kkanderic.com",
		"username" => "applicationUser",
		"password" => "supersecretapplicationpassword",
		"dbname" => "subs"
	));
});

$app = new Micro($di);

// Define all of our restful application service routes

//Get all available publications
$app->get('/api/publications', function() use ($app) {
	$phql = "SELECT * FROM Publications";
	$pubs = $app->modelsManager->executeQuery($phql);
	$data = array();
	foreach ($pubs as $pub) {
		$data[] = array(
			'id' => $pub->id,
			'name' => $pub->name,
		);
	}
	echo json_encode($data);
});

//Get all subscriptions
$app->get('/api/subscriptions', function() use ($app) {
	$phql = "SELECT * FROM Subscriptions";
	$subs = $app->modelsManager->executeQuery($phql);
	$data = array();
	foreach ($subs as $sub) {
		$data[] = array(
			'id' => $sub->id,
			'name' => $sub->name,
			'email' => $sub->email,
			'publication_id' => $sub->publication_id,
		);
	}
	echo json_encode($data);
});


//Insert a subsription
$app->post('/api/subscriptions', function() use ($app) {
	$sub = $app->request->getJsonRawBody();

	$phql = "INSERT INTO Subscriptions (name, email, publication_id) VALUES (:name:, :email:, :publication_id:)";
	$status = $app->modelsManager->executeQuery($phql, array(
		'name' => $sub->name,
		'email' => $sub->email,
		'publication_id' => $sub->publication_id
	));

	echo "Success";
});

//Update a subscription
$app->put('/api/subscriptions/{id:[0-9]+}', function($id) use($app) {
	$sub = $app->request->getJsonRawBody();

    $phql = "UPDATE Subscriptions SET name = :name:, email = :email:, publication_id = :publication_id: WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
        'name' => $sub->name,
        'email' => $sub->email,
        'publication_id' => $sub->publication_id
    ));

    //Create a response
    $response = new Response();

    //Check if the insertion was successful
    if ($status->success() == true) {
        $response->setJsonContent(array('status' => 'OK'));
    } else {

        //Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }

    return $response;
});

//Find subscriptions by email
$app->get('/api/subscriptions/search/{email}', function($email) use ($app) {
	$phql = "SELECT * FROM Subscriptions WHERE email = :email:";
    $subs = $app->modelsManager->executeQuery($phql, array(
        'email' => $email
    ));

    //Create a response
    $response = new Response();

    if ($sub == false) {
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
    	$data = array();
		foreach ($subs as $sub) {
			$data[] = array(
				'id' => $sub->id,
				'name' => $sub->name,
				'email' => $sub->email,
				'publication_id' => $sub->publication_id,
			);
		}
        $response->setJsonContent($data);
    }

    return $response;
});

//Delete a subscription
$app->delete('/api/subscriptions/{id:[0-9]+}', function($id) use($app) {
	$phql = "DELETE FROM Subscriptions WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ));

    //Create a response
    $response = new Response();

    if ($status->success() == true) {
        $response->setJsonContent(array('status' => 'OK'));
    } else {

        //Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));

    }

    return $response;
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'You Broke Something!';
});

$app->handle();

?>