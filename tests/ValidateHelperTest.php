<?php

namespace Padosoft\TesseraSanitaria\Test;

/**
 * User: Lore
 * Date: 05/12/2015
 * Time: 12:38
 */
class ValidateHelperTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        echo "ciao";
    }

    protected function tearDown()
    {
        echo "clean";
    }

    public function is_CodiceRegione_valid()
    {
        $test=5;
        $this->assertEquals(5,$test);
    }


}
