<?php

// FizzBuzz implementation 
// from PHP Application Testing Bootcamp class
class FizzBuzz
{
    protected $_inputSet;

    public function __construct($inputSet)
    {
        $this->_inputSet = $inputSet;;
    }

    public function run()
    {
        /**
         * Iterate from start to end
         * if % 3, Fizz
         * if % 5, Buzz
         * if both, FizzBuzz
         * else output the number
         */
        if (!is_array($this->_inputSet)) {
            return array();
        }

        if (count($this->_inputSet) == 0) {
            return array();
        }

        $output = array(); 

        foreach ($this->_inputSet as $x) {
            if ((int)$x !== $x) {
                continue;
            }

            $val = '';
            if ($x % 3 == 0 && $x % 5 == 0) {
                $val = 'FizzBuzz';
            } elseif ($x % 3 == 0) {
                $val = 'Fizz';
            } elseif ($x % 5 == 0) {
                $val = 'Buzz';
            } else {
                $val = $x;
            }

            $output[] = $val;
        }

        return $output; 
    }
}
