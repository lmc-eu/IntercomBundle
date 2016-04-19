<?php

namespace IntercomBundle\Tests;

use Mockery as m;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
}
