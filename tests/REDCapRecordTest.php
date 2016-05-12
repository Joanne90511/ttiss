<?php

/**
 * Created by PhpStorm.
 * User: mat
 * Date: 12/05/16
 * Time: 1:01 PM
 */
class REDCapRecordTest extends PHPUnit_Framework_TestCase {

    //Can initialize with data

    public function testCRUD()
    {
        $record = new App\REDCap\Record();

        $test = $record->get('test');

        $this->assertEquals(false, $test);

        $record->test = 42;

        $test = $record->get('test');

        $this->assertNotEquals(false, $test);

        $this->assertEquals(42, $test);

        $this->assertEquals('{"test":42}', $record->toJSON());

        $this->assertEquals('[{"test":42}]', $record->toJSON(true));

        $record->test = 24;

        $test = $record->test;

        $this->assertEquals(24, $test);

        $this->assertEquals('{"test":24}', $record->toJSON());

        $this->assertEquals('[{"test":24}]', $record->toJSON(true));
    }

    public function testInitWithData()
    {
        $startingData = [
            'answer' => 42,
            'five' => 5,
            'string' => 'stringtest'
        ];

        $record = new App\REDCap\Record($startingData);

        $this->assertEquals('{"answer":42,"five":5,"string":"stringtest"}', $record->toJSON());
    }
}