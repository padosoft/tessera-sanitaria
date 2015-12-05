<?php

namespace Padosoft\TesseraSanitaria\Test;

use Padosoft\TesseraSanitaria\ValidateHelper;
use Padosoft\TesseraSanitaria\CodiceRegione;

/**
 * User: Lore
 * Date: 05/12/2015
 * Time: 12:38
 */
class ValidateHelperTest extends \PHPUnit_Framework_TestCase
{
    protected $objValidateHelper = null;

    protected function setUp()
    {
        $this->objValidateHelper = new ValidateHelper(new \fdisotto\PartitaIVA(), new \CodiceFiscale\Checker());
    }

    protected function tearDown()
    {

    }

    /**
     * @test
     */
    public function test_CodiceRegione()
    {
        $this->objValidateHelper->checkCodiceRegione('');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->checkCodiceRegione('dsfsdfs');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        foreach (CodiceRegione::getCostants() as $codiceRegione) {

            $this->objValidateHelper->checkCodiceRegione($codiceRegione);
            $this->assertFalse($this->objValidateHelper->hasErrors());
            $this->objValidateHelper->resetErrors();
        }
    }

    /**
     * @test
     */
    public function test_isoDateValidate()
    {
        $data='2015-01-05';
        $this->assertTrue($this->objValidateHelper->isoDateValidate($data));

        $data='2015-15-05';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data='2015-11-40';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data='15-11-01';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data='2015-1-01';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data='2015-11-1';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));
    }



}
