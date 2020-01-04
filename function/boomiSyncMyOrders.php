<?php

require "vendor/autoload.php";

use Popcorn\Beans\Models\QueueTask;


function boomiSyncMyOrders($data)
{
	$task = new QueueTask(['host' => '34.210.227.78', 'database' => 'rally-local']);

	$record = QueueTask::make(
		$data['task'],
		$data['parameters'],
		[
			'batch' => $data['batch'],
			'sales_channel' => $data['sales_channel']
		]
	);

	$result = $task->insertOne($record);

	$response = [
		'status' => 'ok',
		'inserted_count' => $result->getInsertedCount(),
	];

	return json_encode($response);
}
