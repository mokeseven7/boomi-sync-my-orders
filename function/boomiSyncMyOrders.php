<?php

require "vendor/autoload.php";

use Popcorn\Beans\Models\QueueTask;


function boomiSyncMyOrders($data)
{
	//Initialize a mongo connection
	$queueTask = new QueueTask(
		[
			'host' => $_ENV['MONGOHOST'],
			'database' => $_ENV['MONGODATABASE'],
			'username' => $_ENV['MONGOUSER'],
			'password' => $_ENV['MONGOPASSWORD'],
		]
	);

	//If the request contains a batch of records, loop through them
	if (!isset($data['task'])) {
		$tasks = [];
		foreach ($data as $task) {
			$tasks[] = makeTask($task);
		}
	} else {
		//Only one record sent, make a mongo task 
		$tasks = QueueTask::make(
			$data['task'],
			$data['parameters'],
			[
				'batch' => $data['batch'],
				'sales_channel' => $data['sales_channel']
			]
		);
	}

	//insert transformed task(s)
	$result = $queueTask->insertOne($tasks);

	//Build a response object to send from lambda
	$response = [
		'status' => 'ok',
		'inserted_count' => $result->getInsertedCount(),
	];

	//Lambda responses must always be returned as serialized json strings
	return json_encode($response);
}


function makeTask($data)
{
	return QueueTask::make(
		$data['task'],
		$data['parameters'],
		[
			'batch' => $data['batch'],
			'sales_channel' => $data['sales_channel']
		]
	);
}

function configureMongo()
{
	return [
		'host' => $_ENV['MONGOHOST'],
		'database' => $_ENV['MONGODATABASE'],
		'username' => $_ENV['MONGOUSER'],
		'password' => $_ENV['MONGOPASSWORD'],
	];
}
