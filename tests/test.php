<?php

require './FizzBuzz.php';

// FizzBuzz
class FizzBuzzTest extends PHPUnit_Framework_TestCase
{
    public function inputSets()
    {
        return array(
            array(
                array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
                array(1, 2, 'Fizz', 4, 'Buzz', 'Fizz', 7, 8, 'Fizz', 'Buzz')),
            array(
                array(),
                array()),
            array(
                array('Foo', 'Bar', 'Buzz'),
                array()
            )
        );
    }

    /**
     * @dataProvider inputSets
     */
    public function testRun($inputSet, $expectedResult)
    {

        $myFizzBuzz = new FizzBuzz($inputSet);
        $testResult = $myFizzBuzz->run();
        $this->assertEquals($expectedResult, $testResult);
    }

}
