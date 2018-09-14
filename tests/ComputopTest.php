<?php
use PHPUnit\Framework\TestCase;

final class ComputopTest extends TestCase
{
    private $orderNumber;

    public function setUp(){
        $this->orderNumber = uniqid(rand(0,100000), true);
    }

    public function testInit()
    {
        $payg = PayGateway::getIstance(PayGateway::CMPT1, true);
        $this->assertInstanceOf(
            \Payment\Gateway\Computop\Gateway::class,
            $payg
        );
        
        $params = [
            //Same fields on both payment methods
            'baseURL' => "https://localhost/ctPaygatePHP",
            'notifyUrl' => '/notify.php',
            'callbackUrl' => '/success.php',
            'errorUrl' => '/failure.php',
            'amount' => 1.1,
            'orderReference' => $this->orderNumber,
            'paymentReference' => $this->orderNumber,
            'transactionType' => 'MANUAL', 
            'description' => 'Casuale',
            'language' => 'it_IT', 
            'paymentMethod' => 'visa', // test with: cc,mybank,alipay,cupay,wechat,giropay,sofort,ideal,p24,multibanco,zimpler
            'terminalId' => null,
            'hashMessage' => null,
            'currency' => 'EUR',
            'addInfo1' => '[BNL Demo] Ordine da nuovo cliente (145) - {order_date}',
            'addInfo2' => '[BNL Demo] Ordine da nuovo cliente (145) - {order_date}',
            'addInfo3' => '[BNL Demo] Ordine da nuovo cliente (145) - {order_date}',
            'addInfo4' => '[BNL Demo] Ordine da nuovo cliente (145) - {order_date}',
            'addInfo5' => '[BNL Demo] Ordine da nuovo cliente (145) - {order_date}',
            'acquirer' => 'bnlpositivity',

            'hMacPassword' => null,
            
            // Extra values
            'addrCountryCode' => '380',
            'sellingPoint' => 'BNL Demo',
            'accOwner' => null,
            'device' => null,
            'email' => null,
            'phone' => null,
            'scheme' => null,
            'bic' => null,
            'expirationTime' => null,
            'iban' => null,
            'mobileNo' => null,

            // Configuration values
            'template' => null,
            //'background' => null,
            //'bgColor' => null,
            //'bgImage' => null,
            //'fColor' => null,
            //'fFace' => null,
            //'fSize' => null,
            //'centro' => null,
            //'tWidth' => null,
            //'tHeight' => null,
            
            'logoUrl' => null,
            'shippingDetails' => null,
            'invoiceDetails' => null,
        ];

        //get response from gateway
//        $initResponse = $payg->init($params);
//        var_dump($initResponse);
    }

    public function testVerify()
    {
        $payg = PayGateway::getIstance(PayGateway::CMPT1, true);
        $this->assertInstanceOf(
            \Payment\Gateway\Computop\Gateway::class,
            $payg
        );

        //ComputopUtils::getPaymentResultParam($_GET);
        $params = [
            'hashMessage' => null,
            'UrlParams' => 'Len=361&Data=6256CF18B10A0FBE4D9028746090F3D1BC22DF6328C51BB6448378724C502F35217D5862879D0BC2170F60583FD9A9FEF0D5A999813D4AB7DAC36C420246EAB4F3F1DB085F9F30015D7ECDEC92DDD412DC198E50C46752542E048BB84916185862168E8CB744D7E62052A95458C8AB965808EDD67F125F6C69C7E9DEA375777A8D8EECE8099CA005AC7CCC0B1514308338BC6E8DAC8193770CE8A2F831F7474A015147C66EAB7FCC5E57CD4E1E8BB6537ADF915BC73F1E83B47BD92955874870D19E63CCE2B7F0744B653522F0AE29E3182119C371622208A7093C00CF0E1C227D424F2461E7CB820BCD581B6D512274F44C3897B791A8007B417BB6F205886E542955D95BEE29F369D41FF7D6817CEB9AF51ED6803A434CC40409489E74AF1640B76CF96F659AAD5A5E76385EAD47E1D4AD70E0FB26D1C9D9297EDD1D6155E454C40E501659F690B35ABCA38BF0B6669ECC7F5F32915A7598E8B921B1D0AD2F241A249CED053783DCEF67BD1688AB2A',
        ];

        //get response from gateway
        $pResult = $payg->verify($params);
        var_dump($pResult);

    }

    public function testConfirm()
    {
        $payg = PayGateway::getIstance(PayGateway::CMPT1, true);
        $this->assertInstanceOf(
            \Payment\Gateway\Computop\Gateway::class,
            $payg
        );

        $params = [
            'terminalId' => null,
            'hashMessage' => null,
            'hMacPassword' => null,
            'paymentID' => 'e4f7a864b72d4fe393ff805e7afbc7bc', // we retrieve it from them
            'paymentReference' => '516245b617fc265da67.16008640',
            'orderReference' => '516245b617fc265da67.16008640',
            'amount' => 1.1,
            'currency' => null,
            'acquirer' => 'bnlpositivity',
        ];

        //get response from gateway
//        $pResult = $payg->confirm($params);
//        var_dump($pResult);
    }

    public function testRefund()
    {
        $payg = PayGateway::getIstance(PayGateway::CMPT1, true);
        $this->assertInstanceOf(
            \Payment\Gateway\Computop\Gateway::class,
            $payg
        );

        $params = [
            'terminalId' => null,
            'hashMessage' => null,
            'hMacPassword' => null,
            'paymentID' => '1faa96e4a64a435ca34dc281ff632cc3', // we retrieve it from them
            'paymentReference' => '972375b617710d47b89.25444934',
            'amount' => 1.1,
            'currency' => null,
            'acquirer' => 'bnlpositivity',
        ];

        //get response from gateway
        //$pResult = $payg->refund($params);
        //var_dump($pResult);
    }

    public function testCancel()
    {
        $payg = PayGateway::getIstance(PayGateway::CMPT1, true);
        $this->assertInstanceOf(
            \Payment\Gateway\Computop\Gateway::class,
            $payg
        );

        $params = [
            'terminalId' => null,
            'hashMessage' => null,
            'hMacPassword' => null,
            'paymentID' => 'a255a6b8dd6a42abaa61fd7a9c861862', // we retrieve it from them
            'paymentReference' => '472605b617e6da848e9.37684960',
            'orderReference' => '472605b617e6da848e9.37684960',
            'amount' => 1.1,
            'currency' => null,
            'acquirer' => 'bnlpositivity',
            'xId' => '',
        ];

        //get response from gateway
        //$pResult = $payg->cancel($params);
        //var_dump($pResult);
    }

    public function testXml(){
        $payg = PayGateway::getIstance(PayGateway::CMPT1, true);
        $this->assertInstanceOf(
            \Payment\Gateway\Computop\Gateway::class,
            $payg
        );
        //var_dump($payg->getSellingLocations());
    }
}

