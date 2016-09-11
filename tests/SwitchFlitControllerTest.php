<?php

use SwitchFlit\SwitchFlitable;

class SwitchFlitControllerTest extends FunctionalTest
{
	protected $usesDatabase = true;

	protected static $fixture_file = 'SwitchFlitControllerTest.yml';

	public function setUp()
	{
		parent::setUp(); // TODO: Change the autogenerated stub
	}

	public function testGetAllowedRecordsForSwitchFlitableDataObject()
	{
		$expected = [
			(object)[
				'title' => 'First',
				'link' => '/sfobject/1'
			],
			(object)[
				'title' => 'Third',
				'link' => '/sfobject/3'
			],
		];

		$result = $this->get('/switchflit/SwitchFlitDemoDataObject/records');

		$this->assertEquals($expected, json_decode($result->getBody()));
	}

	/**
	 * @expectedException        Exception
	 * @expectedExceptionMessage The class Member is not SwitchFlitable.
	 */
	public function testDoNotGetRecordsForNonSwitchFlitableDataObject()
	{
			$this->get('/switchflit/Member/records');
	}

	/**
	 * @expectedException        Exception
	 * @expectedExceptionMessage The class NotReal does not exist.
	 */
	public function testDoNotGetRecordsForNonExistentDataObject()
	{
		$this->get('/switchflit/NotReal/records');
	}

	/**
	 * @expectedException        Exception
	 * @expectedExceptionMessage The class stdClass is not a DataObject.
	 */
	public function testDoNotGetRecordsForNonDataObject()
	{
		$this->get('/switchflit/stdClass/records');
	}
}

class SwitchFlitDemoDataObject extends DataObject implements SwitchFlitable
{
	private static $db = [
		'Name' => 'Varchar(150)',
	];

	public function SwitchFlitTitle()
	{
		return $this->Name;
	}

	public function SwitchFlitLink()
	{
		return '/sfobject/' . $this->ID;
	}

	public function canView($member = null)
	{
		return $this->ID != 2;
	}
}
