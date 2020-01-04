<?php

require "vendor/autoload.php";

use Popcorn\Beans\Models\QueueTask;


function boomiSyncMyOrders($data)
{
	//Initialize a mongo connection
	$queueTask = new QueueTask(['host' => '34.210.227.78', 'database' => 'rally-local']);

	//If the request contains a batch of records, loop through them
	if (!isset($data['task'])) {
		$tasks = [];
		foreach ($data as $task) {
			//Make a mongo task record for each object in request
			$tasks[] = QueueTask::make(
				$task['task'],
				$task['parameters'],
				[
					'batch' => $task['batch'],
					'sales_channel' => $task['sales_channel']
				]
			);
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

	print_r($tasks);

	//insert transformed task(s)
	$result = $queueTask->insertOne($tasks);

	$response = [
		'status' => 'ok',
		'inserted_count' => $result->getInsertedCount(),
	];

	return json_encode($response);
}
