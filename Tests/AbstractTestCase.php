<?php

namespace IntercomBundle\Tests;

use Mockery as m;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @param int $time
     * @return \DateTime
     */
    protected function dateTime($time)
    {
        return \DateTime::createFromFormat('U', $time);
    }
}
