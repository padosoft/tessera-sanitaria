<?php
namespace Padosoft\TesseraSanitaria\Test;

use Padosoft\TesseraSanitaria\ValidateHelper;

class ErrorableTest extends TestBase
{
    /**
     * @var
     */
    protected $objValidateHelper;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->objValidateHelper = new ValidateHelper(new \fdisotto\PartitaIVA(), new \CodiceFiscale\Checker());
    }

    /**
     * @test
     */
    public function testAddArrErrors()
    {
        $this->objValidateHelper->AddArrErrors('hello');
        $this->assertFalse($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->AddArrErrors(array());
        $this->assertFalse($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->AddArrErrors(array('ciao', 'hello'));
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->assertTrue(is_array($this->objValidateHelper->getArrErrors()));
        $this->assertTrue(count($this->objValidateHelper->getArrErrors()) == 2);
        $this->assertTrue($this->objValidateHelper->getArrErrors()[0] == 'ciao');
        $this->assertTrue($this->objValidateHelper->getArrErrors()[1] == 'hello');
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->AddArrErrors(array('ciao', ''));
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->assertTrue(is_array($this->objValidateHelper->getArrErrors()));
        $this->assertTrue(count($this->objValidateHelper->getArrErrors()) == 2);
        $this->assertTrue($this->objValidateHelper->getArrErrors()[0] == 'ciao');
        $this->assertTrue($this->objValidateHelper->getArrErrors()[1] == '');
        $this->objValidateHelper->resetErrors();
    }


    /**
     * @test
     */
    public function testAddError()
    {
        $this->objValidateHelper->addError('ciao');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->assertTrue(is_array($this->objValidateHelper->getArrErrors()));
        $this->assertTrue(count($this->objValidateHelper->getArrErrors()) == 1);
        $this->assertTrue($this->objValidateHelper->getArrErrors()[0] == 'ciao');
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->addError(array('ciao', 'hello'));
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->addError('');
        $this->assertFalse($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();
    }
}
