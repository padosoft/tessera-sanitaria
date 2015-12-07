<?php

namespace Padosoft\TesseraSanitaria\Test;

/**
 * User: Lore
 * Date: 05/12/2015
 * Time: 12:38
 */
class TestBase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

    }

    protected function tearDown()
    {

    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
