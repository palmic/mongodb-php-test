<?php

class MongoTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider findDataProvider
	 *
	 * @param \MongoCollection $collection
	 * @param array $inputData
	 * @param array $q
	 * @param array $fields
	 * @param array $expectedData
	 */
	public function testFind(
		\MongoCollection $collection,
		array $inputData,
		array $q,
		array $fields,
		array $expectedData
	)
	{
		// reset collection at first
		$collection->drop();
		// fill it with fixtures data
		$collection->insert($inputData);

		//test:
		$res = $collection->find($q, $fields);
		$data = [];
		foreach ($res as $doc) {
			unset($doc['_id']);
			$data[] = $doc;
		}
		$this->assertEquals($expectedData, $data);
	}

	public function findDataProvider()
	{
		$client = new MongoClient();
		$collection = $client->selectCollection('fields_test', 'fields');

		$tms1 = strtotime('2016-12-01 10:00:00');
		$tms2 = strtotime('2016-12-01 11:00:00');

		$inputData = [$tms1 => 1, $tms2 => 2];

		return [
			'without fields' => [
				$collection,
				$inputData,
				$q = [],
				$fields = [],
				$expectedData = [$inputData],
			],
			'fields to 1' => [
				$collection,
				$inputData,
				$q = [],
				$fields = [$tms2 => 1],
				$expectedData = [[$tms2 => 2]],
			],
			'fields to true' => [
				$collection,
				$inputData,
				$q = [],
				$fields = [$tms2 => TRUE],
				$expectedData = [[$tms2 => 2]],
			],
			'fields to 1 with _id' => [
				$collection,
				$inputData,
				$q = [],
				$fields = ['_id' => TRUE, $tms2 => 1],
				$expectedData = [[$tms2 => 2]],
			],
		];
	}

}