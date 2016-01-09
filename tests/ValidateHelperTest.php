<?php

namespace Padosoft\TesseraSanitaria\Test;

use Padosoft\TesseraSanitaria\CodiceAsl;
use Padosoft\TesseraSanitaria\TipoSpesa;
use Padosoft\TesseraSanitaria\ValidateHelper;
use Padosoft\TesseraSanitaria\CodiceRegione;
use Padosoft\TesseraSanitaria\CodiceSSA;
use Padosoft\TesseraSanitaria\FlagOperazione;

/**
 * User: Lore
 * Date: 05/12/2015
 * Time: 12:38
 */
class ValidateHelperTest extends TestBase
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
    public function testCodiceRegione()
    {
        $this->objValidateHelper->checkCodiceRegione('');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->checkCodiceRegione('dsfsdfs');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        foreach (CodiceRegione::getCostants() as $codice) {

            $this->objValidateHelper->checkCodiceRegione($codice);
            $this->assertFalse($this->objValidateHelper->hasErrors());
            $this->objValidateHelper->resetErrors();
        }
    }

    /**
     * @test
     */
    public function testCodiceSSA()
    {
        $this->objValidateHelper->checkCodiceSSA('');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->checkCodiceSSA('dsfsdfs');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        foreach (CodiceSSA::getCostants() as $codice) {

            $this->objValidateHelper->checkCodiceSSA($codice);
            $this->assertFalse($this->objValidateHelper->hasErrors());
            $this->objValidateHelper->resetErrors();
        }
    }

    /**
     * @test
     */
    public function testCheckCodiceAsl()
    {
        $this->objValidateHelper->checkCodiceAsl('');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->checkCodiceAsl('dsfsdfs');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        foreach (CodiceAsl::getCostants() as $codice) {

            $this->objValidateHelper->checkCodiceAsl($codice);
            $this->assertFalse($this->objValidateHelper->hasErrors());
            $this->objValidateHelper->resetErrors();
        }
    }

    /**
     * @test
     */
    public function testCheckTipoSpesa()
    {
        $this->objValidateHelper->checkTipoSpesa('');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->checkTipoSpesa('dsfsdfs');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        foreach (TipoSpesa::getCostants() as $codice) {

            $this->objValidateHelper->checkTipoSpesa($codice);
            $this->assertFalse($this->objValidateHelper->hasErrors());
            $this->objValidateHelper->resetErrors();
        }
    }

    /**
     * @test
     */
    public function testCheckFlagOperazione()
    {
        $this->objValidateHelper->checkFlagOperazione('');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        $this->objValidateHelper->checkFlagOperazione('dsfsdfs');
        $this->assertTrue($this->objValidateHelper->hasErrors());
        $this->objValidateHelper->resetErrors();

        foreach (FlagOperazione::getCostants() as $codice) {

            $this->objValidateHelper->checkFlagOperazione($codice);
            $this->assertFalse($this->objValidateHelper->hasErrors());
            $this->objValidateHelper->resetErrors();
        }
    }

    /**
     * @test
     */
    public function testIsoDateValidate()
    {
        // Correct Date:
        $data = '2015-01-05';
        $this->assertTrue($this->objValidateHelper->isoDateValidate($data));

        // Wrong Date:
        $data = '2015-15-05';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data = '2015-11-40';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data = '15-11-01';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data = '2015-1-01';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));

        $data = '2015-11-1';
        $this->assertFalse($this->objValidateHelper->isoDateValidate($data));
    }

    /**
     * @test
     */
    public function testCheckDataValida()
    {
        // Correct Date:
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '2015-01-05'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '2020-12-31'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        // ok date, but Wrong Date range
        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '2014-12-01'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        // Wrong Date:
        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '2015-15-05'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '2015-11-40'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '15-11-01'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '2015-1-01'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '2015-11-1'));
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }

    /**
     * @test
     */
    public function testCheckDataEmissione()
    {
        // Correct Date:
        $this->invokeMethod($this->objValidateHelper, 'checkDataEmissione', array('test', '2015-01-05'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataEmissione', array('test', '2020-12-31'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        // ok date, but Wrong Date range
        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataEmissione', array('test', '2014-12-01'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        // Wrong Date:
        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataEmissione', array('test', '2015-15-05'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataEmissione', array('test', '2015-11-40'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataValida', array('test', '15-11-01'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataEmissione', array('test', '2015-1-01'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkDataEmissione', array('test', '2015-11-1'));
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }

    /**
     * @test
     */
    public function testCheckNumericField()
    {
        $this->assertTrue($this->objValidateHelper->checkNumericField(11555262, 0));
        $this->assertTrue($this->objValidateHelper->checkNumericField(1, 3));
        $this->assertTrue($this->objValidateHelper->checkNumericField(11, 3));
        $this->assertTrue($this->objValidateHelper->checkNumericField(115, 3));

        $this->assertFalse($this->objValidateHelper->checkNumericField(11555262, 3));
        $this->assertFalse($this->objValidateHelper->checkNumericField('abc', 0));
        $this->assertFalse($this->objValidateHelper->checkNumericField('abc', 3));

        $this->assertTrue($this->objValidateHelper->checkNumericField(1232, 'abc'));
        $this->assertFalse($this->objValidateHelper->checkNumericField('sdsdsa', 'abc'));
    }

    /**
     * @test
     */
    public function testCheckNumDocumento()
    {
        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkNumDocumento(11555262);
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkNumDocumento(12345678901234567890);
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkNumDocumento(123456789012345678901);
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkNumDocumento('000123');
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkNumDocumento('dsafa');
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }

    /**
     * @test
     */
    public function testCheckDispositivo()
    {
        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkDispositivo(115);
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkDispositivo('000123');
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkDispositivo(123456789012345678901);
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkDispositivo('asdsa');
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }

    /**
     * @test
     */
    public function testCheckPIva()
    {
        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkPIva('05172580481');
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkPIva('IT05172580481');
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkPIva('it05172580481');
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkPIva('a05172580481');
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkPIva('05');
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkPIva('051735454335430481');
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->objValidateHelper->checkPIva('');
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }


    /**
     * @test
     */
    public function testCheckCfProprietario()
    {
        $this->invokeMethod($this->objValidateHelper, 'checkCfProprietario', array('PDVLNZ75C19D612P'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfProprietario', array('PDVLNZ75C19D612F'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfProprietario', array('ADVLNZ75C19D612P'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfProprietario', array('D612F'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfProprietario', array('PDVLNZwq3432rfdse19D612P'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfProprietario', array(''));
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }

    /**
     * @test
     */
    public function testCheckCfCittadino()
    {
        $this->invokeMethod($this->objValidateHelper, 'checkCfCittadino', array('PDVLNZ75C19D612P'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfCittadino', array('PDVLNZ75C19D612F'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfCittadino', array('ADVLNZ75C19D612P'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfCittadino', array('D612F'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfCittadino', array('PDVLNZwq3432rfdse19D612P'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkCfCittadino', array(''));
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }

    /**
     * @test
     */
    public function testCheckImporto()
    {
        $this->invokeMethod($this->objValidateHelper, 'checkImporto', array('0'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkImporto', array('12'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkImporto', array('12.3'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkImporto', array('15.25'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkImporto', array('PDV'));
        $this->assertTrue($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkImporto', array(''));
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }

    /**
     * @test
     */
    public function testCheckRequiredField()
    {
        $this->invokeMethod($this->objValidateHelper, 'checkRequiredField', array('sdfsdfs', 'test'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkRequiredField', array('a', 'test'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkRequiredField', array('0', 'test'));
        $this->assertFalse($this->objValidateHelper->hasErrors());

        $this->objValidateHelper->resetErrors();
        $this->invokeMethod($this->objValidateHelper, 'checkRequiredField', array('', 'test'));
        $this->assertTrue($this->objValidateHelper->hasErrors());
    }
}
