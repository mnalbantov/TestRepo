<?php

namespace Payment\Gateway\Computop;

class Gateway implements \Payment\GatewayInterface
{
    private $test;
    private $dMerchantId = '';
    private $dBlowfishPassword = '';
    private $dHsMacPassword = '';
    private $sUrl;

    const URL_POSITIVI = 'https://ecpay.bnlpositivity.it/paymentpage';
    const URL_PARIBAS = 'https://ecpay.bnlpositivity.it/paymentpage';
    //const URL_POSITIVI ='https://ecpay-test.bnlpositivity.it/paymentpage';
    //const URL_PARIBAS ='https://ecpay-test.bnlpositivity.it/paymentpage';
    
    // Default credentials 
    const DEFAULT_MERCHANT_ID = 'bnl_test';
    const DEFAULT_BLOWFISH_PASSWORD = 'Fw3[7(bAP8=or*D2';
    const DEFAULT_HS_MAC_PASSWORD = '3Hn)[7Pe2Qf(!j8TK=t9*6DsJk5?m4_C';
    
    // Action methods 
    const ACTION_CAPTURE = '/capture.aspx';
    const ACTION_CREDIT = '/credit.aspx';
    const ACTION_REVERSE = '/reverse.aspx';

    // Extra informations
    const DEFAULT_INFO1 = '';
    const DEFAULT_INFO2 = '';
    const DEFAULT_INFO3 = '';
    const DEFAULT_INFO4 = '';
    const DEFAULT_INFO5 = '';

    // Languages
    const DEFAULT_LANGUAGE = 'EN';

    // Transaction types
    const TRASACTION_AUTO = 'AUTO';
    const TRASACTION_MANUAL = 'MANUAL';
    
    // Acquirer types
    const ACQUIRER_POSITIVI = 'bnlpositivity';
    const ACQUIRER_PARIBAS = 'bnlparibas';

    /**
     * 
     * @return object
     *
     * @throws \Exception
     */
    public function __construct ($test){
       $this->test = $test;

       if($test){
            $this->dMerchantId = self::DEFAULT_MERCHANT_ID;
            $this->dBlowfishPassword = self::DEFAULT_BLOWFISH_PASSWORD;
            $this->dHsMacPassword = self::DEFAULT_HS_MAC_PASSWORD;
       }
    }
    
    /**
     * 
     * Transaction initializer. Create the Redirect URL.
     * 
     * @param array $params
     * @return array|object
     * @throws ConnectionException
     * @throws IgfsException
     */
    public function init(array $params = [])
    {
        $mId = ComputopUtils::getValue($params,'terminalId',$this->dMerchantId);
        $bPs = ComputopUtils::getValue($params,'hashMessage',$this->dBlowfishPassword);
        $hMcPd = ComputopUtils::getValue($params,'hMacPassword',$this->dHsMacPassword);
        $url= ComputopUtils::getValue($params,'baseURL','');
        $acq= ComputopUtils::getValue($params,'acquirer');
        $pm = ComputopUtils::getValue($params,'paymentMethod');
        $amount = ComputopUtils::getValue($params, 'amount', '0');
        
        $this->loadBaseUrl($acq);
        $initObj = $this->getInstrumentEndpoint($mId,$bPs,$hMcPd,$pm);
        $initObj->capture = ComputopUtils::getValue($params,'transactionType',self::TRASACTION_AUTO);
        $initObj->transId = ComputopUtils::getValue($params, 'paymentReference');
        $initObj->refNr = ComputopUtils::getValue($params, 'orderReference');
        $initObj->amount = str_replace('.', '', number_format($amount, 2, '.', ''));
        //$initObj->currency = ComputopUtils::getValue($params,'currency'); // There is only one the default = EN
        $initObj->description = ComputopUtils::getValue($params,'description');
        $initObj->language =ComputopUtils::normalizeLanguage(ComputopUtils::getValue($params, 'language')); // TODO: Don't exist yet and there isn't in the documentation
        
        $successUrl=$url.ComputopUtils::getValue($params,'callbackUrl','');
        $failureUrl=$url.ComputopUtils::getValue($params,'errorUrl','');
        $notifyUrl=$url.ComputopUtils::getValue($params,'notifyUrl','');

        $initObj->UrlSuccess = ComputopUtils::clearUrl($successUrl);
        $initObj->UrlFailure = ComputopUtils::clearUrl($failureUrl);
        $initObj->UrlNotify = ComputopUtils::clearUrl($notifyUrl);

//        $this->UrlSuccess = $successUrl;
//        $this->UrlFailure = $failureUrl;
//        $this->UrlNotify = $notifyUrl;


        $initObj->payId = ComputopUtils::getValue($params,'payId','');
        $initObj->addInfo1 = substr(ComputopUtils::getValue($params,'addInfo1',self::DEFAULT_INFO1),0,204);
        $initObj->addInfo2 = substr(ComputopUtils::getValue($params,'addInfo2',self::DEFAULT_INFO2),0,204);
        $initObj->addInfo3 = substr(ComputopUtils::getValue($params,'addInfo3',self::DEFAULT_INFO3),0,204);
        $initObj->addInfo4 = substr(ComputopUtils::getValue($params,'addInfo4',self::DEFAULT_INFO4),0,204);
        //$initObj->addInfo5 = substr(ComputopUtils::getValue($params,'addInfo5',self::DEFAULT_INFO5),0,204);
        $initObj->addInfo5 = ComputopUtils::combineQueryParams(array($successUrl,$failureUrl,$notifyUrl));

        // Extra values
        $initObj->addrCountryCode = ComputopUtils::getValue($params,'addrCountryCode','');     
        $initObj->sellingPoint = ComputopUtils::getValue($params,'sellingPoint','');     
        $initObj->accOwner = ComputopUtils::getValue($params,'accOwner','');     
        $initObj->device = ComputopUtils::getValue($params,'device','');  // if device = "Mobile" it show the mobile version    
        $initObj->email = ComputopUtils::getValue($params,'email','');    
        $initObj->phone = ComputopUtils::getValue($params,'phone','');     
        $initObj->scheme = ComputopUtils::getValue($params,'scheme','');     
        $initObj->bic = ComputopUtils::getValue($params,'bic','');     
        $initObj->expirationTime = ComputopUtils::getValue($params,'expirationTime','');     
        $initObj->iban = ComputopUtils::getValue($params,'iban','');   
        $initObj->mobileNo = ComputopUtils::getValue($params,'mobileNo','');

        //CustomFields
        $initObj->customField1 = $amount.' '.ComputopUtils::getValue($params,'customField1','EURO');
        $initObj->customField2 = ComputopUtils::getValue($params,ComputopUtils::getValue($params, 'orderReference'));
        $initObj->customField3 = ComputopUtils::getValue($params,'logoUrl');
        $initObj->customField6 = substr(ComputopUtils::getValue($params,'shippingDetails'),0,204);
        $initObj->customField7 = substr(ComputopUtils::getValue($params,'invoiceDetails'),0,204);

        // Graphic customization
        $initObj->template = ComputopUtils::getValue($params,'template');
        // Removed by BNL
        //$initObj->background = ComputopUtils::getValue($params,'Background');
        //$initObj->bgColor = ComputopUtils::getValue($params,'BGColor');
        //$initObj->bgImage = ComputopUtils::getValue($params,'BGImage');
        //$initObj->fColor = ComputopUtils::getValue($params,'FColor');
        //$initObj->fFace = ComputopUtils::getValue($params,'FFace');
        //$initObj->fSize = ComputopUtils::getValue($params,'FSize');
        //$initObj->centro = ComputopUtils::getValue($params,'Centro');
        //$initObj->tWidth = ComputopUtils::getValue($params,'tWidth');
        //$initObj->tHeight = ComputopUtils::getValue($params,'tHeight');
        $resp = $initObj->execute();
        return array(
            'returnCode' => ComputopUtils::getValue($resp,'returnCode'),
            'message' => ComputopUtils::getValue($resp,'error'),
            'error' => ComputopUtils::getValue($resp,'error') !== '',
            'paymentID' => ComputopUtils::getValue($resp,'paymentID'),
            'orderReference' => ComputopUtils::getValue($resp,'orderReference'),
            'notifyURL' => ComputopUtils::getValue($resp,'notifyURL'),
            'redirectURL' => ComputopUtils::getValue($resp,'redirectURL'),
        );
    }
    /**
     * 
     * Verify transaction. Receive only the status of the specific transaction.
     * 
     * @param array $params
     * @return array|object
     */
    public function verify(array $params = [])
    {
        $obj = new Init\ComputopCgVerify(ComputopUtils::getValue($params,'hashMessage',$this->dBlowfishPassword),ComputopUtils::getValue($params, 'UrlParams')); 
        $verifyObj = $obj->execute();
        return array(
            'terminalId' =>ComputopUtils::getValue($verifyObj,'mid',''),
            'returnCode' => ComputopUtils::getValue($verifyObj,'Code',''),
            'message' => ComputopUtils::getValue($verifyObj,'Description',''),
            'error' => ComputopUtils::getValue($verifyObj,'Description','') !== 'success',
            'orderReference' => ComputopUtils::getValue($verifyObj,'refnr',''),
            'paymentID' => ComputopUtils::getValue($verifyObj,'PayID',''),
            'tranID' => ComputopUtils::getValue($verifyObj,'TransID',''),
            'XID' => ComputopUtils::getValue($verifyObj,'XID',''), // check if we need it or not
            'PCNr' => ComputopUtils::getValue($verifyObj,'PCNr',''), // check if we need it or not

        );
    }

    /**
     * 
     * Transaction confirmation. 
     * Transfer a specific amount from an authorized transaction
     * 
     * @param array $params
     * @return array|object
     */
    public function confirm(array $params = []){
        $mId = ComputopUtils::getValue($params,'terminalId',$this->dMerchantId);    
        $bPs = ComputopUtils::getValue($params,'hashMessage',$this->dBlowfishPassword);
        $hMcPd = ComputopUtils::getValue($params,'hMacPassword',$this->dHsMacPassword);
        $acq= ComputopUtils::getValue($params,'acquirer');
        $amount = str_replace('.', '', number_format(ComputopUtils::getValue($params, 'amount', '0'), 2, '.', ''));

        $obj = new S2S\ComputopCgCapture($mId,$bPs,$hMcPd); 
        $this->loadBaseUrl($acq);
        $obj->serverUrl = $this->sUrl.self::ACTION_CAPTURE;
        
        $obj->payId = ComputopUtils::getValue($params,'paymentID','');     
        $obj->transId = ComputopUtils::getValue($params, 'paymentReference');
        $obj->amount = $amount;
        $obj->currency = ComputopUtils::getValue($params,'currency',BaseComputopCg::DEFAULT_CURRENCY);
        $obj->refNr = ComputopUtils::getValue($params, 'orderReference');

        $obj->execute();
        return array(
            'terminalId' => $obj->mId,
            'returnCode' => $obj->code,
            'message' => $obj->description,
            'error' => $obj->description !== 'success',
            'refTranID' => '',
            'tranID' => $obj->transId,

            'paymentID' => $obj->payId, // check if we need it or not
            'XID' => $obj->xId, // check if we need it or not
            'Status' => $obj->status, // check if we need it or not
            'MAC' => $obj->mac, // check if we need it or not
            'orderReference' => $obj->refNr, // check if we need it or not
        ); 
    }

    /**
     * 
     * Refund transaction. Return a specific amount back to buyer.
     * 
     * @param array $params
     * @return array|object
     */
    public function refund(array $params = []){

        $mId = ComputopUtils::getValue($params,'terminalId',$this->dMerchantId);    
        $bPs = ComputopUtils::getValue($params,'hashMessage',$this->dBlowfishPassword);
        $hMcPd = ComputopUtils::getValue($params,'hMacPassword',$this->dHsMacPassword);
        $acq= ComputopUtils::getValue($params,'acquirer');

        $obj = new S2S\ComputopCgCredit($mId,$bPs,$hMcPd);  
        $this->loadBaseUrl($acq);
        $obj->serverUrl = $this->sUrl.self::ACTION_CREDIT;
        
        $obj->payId = ComputopUtils::getValue($params,'paymentID','');     
        $obj->transId = ComputopUtils::getValue($params, 'paymentReference');
        $obj->amount = str_replace('.', '', number_format(ComputopUtils::getValue($params, 'amount', '0'), 2, '.', ''));
        $obj->currency = ComputopUtils::getValue($params,'currency',BaseComputopCg::DEFAULT_CURRENCY);

        $obj->execute();
        return array(
            'terminalId' => $obj->mId,
            'returnCode' => $obj->code,
            'message' => $obj->description,
            'error' => $obj->description !== 'success',
            'orderReference' => '',
            'tranID' => $obj->transId,

            'paymentID' => $obj->payId, // check if we need it or not
            'XID' => $obj->xId, // check if we need it or not
            'Status' => $obj->status, // check if we need it or not
            'MAC' => $obj->mac, // check if we need it or not
        ); 
    }
    /**
     * 
     * Cancel pending transaction. Return a specific amount back to buyer.
     * 
     * @param array $params
     * @return array|object
     */
    public function cancel(array $params){
        
        $mId = ComputopUtils::getValue($params,'terminalId',$this->dMerchantId);    
        $bPs = ComputopUtils::getValue($params,'hashMessage',$this->dBlowfishPassword);
        $hMcPd = ComputopUtils::getValue($params,'hMacPassword',$this->dHsMacPassword);
        $acq= ComputopUtils::getValue($params,'acquirer');

        $obj = new S2S\ComputopCgReverse($mId,$bPs,$hMcPd); 
        $this->loadBaseUrl($acq);
        $obj->serverUrl = $this->sUrl.self::ACTION_REVERSE;
        
        $obj->payId = ComputopUtils::getValue($params,'paymentID','');   
        $obj->xId = ComputopUtils::getValue($params,'xId','');
        $obj->transId = ComputopUtils::getValue($params, 'paymentReference');
        $obj->refNr = ComputopUtils::getValue($params, 'orderReference');
        $obj->amount = str_replace('.', '', number_format(ComputopUtils::getValue($params, 'amount', '0'), 2, '.', ''));
        $obj->currency = ComputopUtils::getValue($params,'currency',BaseComputopCg::DEFAULT_CURRENCY);

        $obj->execute();
        return array(
            'terminalId' => $obj->mId,
            'orderReference' => $obj->refNr,
            'tranID' => $obj->transId,
            'refTranID' => '',
            'returnCode' => $obj->code,
            'message' => $obj->description,
            'error' => $obj->description !== 'success',

            'paymentID' => $obj->payId, // check if we need it or not
            'XID' => $obj->xId, // check if we need it or not
            'Status' => $obj->status, // check if we need it or not
            'MAC' => $obj->mac, // check if we need it or not
        ); 
    }
    /**
     * 
     * Return all the possible payment instruments
     * 
     * @return array|object
     */
    public function getPaymentInstruments(){
        return array(
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
            'maestro' => 'Maestro',
            'americanexpress' => 'American Express',
            'diners' => 'Diners',
            'findomestic' => 'Findomestic',
            'mybank' => 'MyBank',
            'alipay' => 'Alipay',
            'cupay' => 'Chinaunionpay',
            'wechat' => 'WeChat',
            'giropay' => 'Giropay',
            'sofort' => 'Sofort',
            'ideal' => 'Ideal',
            'p24' => 'P24',
            'multibanco' => 'Multibanco',
            'zimpler' => 'Zimpler'
          );
    }
    /**
     * 
     * Return all the possible payment instruments for credit cards
     * 
     * @param 
     * @return array|object
     */
    public function getCcPaymentInstruments(){
        return array(
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
            'maestro' => 'Maestro',
            'americanexpress' => 'American Express',
            'diners' => 'Diners',
            'findomestic' => 'Findomestic',
          );
    }

    private function loadBaseUrl($acq){
        if(self::ACQUIRER_POSITIVI == $acq){
            $this->sUrl = self::URL_POSITIVI;
        }else if(self::ACQUIRER_PARIBAS == $acq){
            $this->sUrl = self::URL_PARIBAS;
        }
        else{
            throw new CmptpMissingParException("Missing Acquirer");
        }
    }

    /**
     * 
     * Return the endpoint action
     * 
     * @param string $inst
     * @return object
     */
    public function getInstrumentEndpoint($mId,$bPs,$hMcPd,$inst){
        $obj;

        if (!$inst) {
            return new Init\ComputopCgInit($mId,$bPs,$hMcPd,$this->sUrl); 
            //throw new CmptpMissingParException("Missing Payment Method");
        }

        switch ($inst) {
            case 'cc':
            case 'visa':
            case 'mastercard':
            case 'maestro':
            case 'americanexpress':
            case 'diners':
            case 'findomestic': // To be verified
                $obj = new Init\ComputopCgInit($mId,$bPs,$hMcPd,$this->sUrl); 
                break;
            case 'mybank':
                $obj = new Init\ComputopCgInitMyBank($mId,$bPs,$hMcPd,$this->sUrl); 
                break;
            case 'alipay' : 
                $obj = new Init\ComputopCgInitAlipay($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'cupay' : 
                $obj = new Init\ComputopCgInitCup($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'wechat' : 
                $obj = new Init\ComputopCgInitWeChat($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'giropay' : 
                $obj = new Init\ComputopCgInitGiroPay($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'sofort' : 
                $obj = new Init\ComputopCgInitSofort($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'ideal' : 
                $obj = new Init\ComputopCgInitIdeal($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'p24' : 
                $obj = new Init\ComputopCgInitP24($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'multibanco' : 
                $obj = new Init\ComputopCgInitMultibanco($mId,$bPs,$hMcPd,$this->sUrl);
                break;
            case 'zimpler' : 
                $obj = new Init\ComputopCgInitZimpler($mId,$bPs,$hMcPd,$this->sUrl);
                break; 
        }
        return $obj;
    }
    /**
     * 
     * Return all the possible transaction types
     * 
     * @return array|object
     */
    public function getTransactionTypes(){
        return array(
            'AUTO' => 'Acquisto',
            'MANUAL' => 'Preautorizzazione',
          );
    }
    /**
     * 
     * Return all the possible cheout types
     * 
     * @return array|object
     */
    public function getCheckoutTypes(){
        return array(
            '1'  => 'Checkout BNLP',
            '2'  => 'Checkout BNLP con sintesi in web store',
            '3'  => 'Checkout BNLP con selezione strumento di pagamento su web store',
          );
      }
    
    /**
     * Get Allowed Currencies
     *
     * @return array|object
     */
    public function getCurrenciesAllowed(){
        return array(
            array(
                'title' => __('Euro', 'bnppay'),
                'code' => 'EUR',
                'symbol' => '&euro;',
            )
        );
    }
    /**
     * Get Allowed Languages
     *
     * @return array|object
     */
    public function getLanguagesAllowed(){
        return array(
            array( 'code' => 'IT', 'name' => 'Italiano'),
            array( 'code' => 'DE', 'name' => 'Tedesco'),
            array( 'code' => 'AL', 'name' => 'Albanese'),
            array( 'code' => 'AT', 'name' => 'Austriaco'),
            array( 'code' => 'CZ', 'name' => 'Ceco'),
            array( 'code' => 'CS', 'name' => 'Ceco'),
            array( 'code' => 'DK', 'name' => 'Danese'),
            array( 'code' => 'EN', 'name' => 'Inglese'),
            array( 'code' => 'FI', 'name' => 'Finlandese'),
            array( 'code' => 'FR', 'name' => 'Francese'),
            array( 'code' => 'GR', 'name' => 'Greco'),
            array( 'code' => 'HU', 'name' => 'Ungherese'),
            array( 'code' => 'JP', 'name' => 'Giapponese'),
            array( 'code' => 'NL', 'name' => 'Olandese'),
            array( 'code' => 'NO', 'name' => 'Norvegese'),
            array( 'code' => 'PL', 'name' => 'Polacco'),
            array( 'code' => 'PT', 'name' => 'Portoghese'),
            array( 'code' => 'RO', 'name' => 'Rumeno'),
            array( 'code' => 'RU', 'name' => 'Russo'),
            array( 'code' => 'ES', 'name' => 'Spagnolo'),
            array( 'code' => 'SE', 'name' => 'Svedese'),
            array( 'code' => 'SK', 'name' => 'Slovacco'),
            array( 'code' => 'SL', 'name' => 'Sloveno'),
            array( 'code' => 'TR', 'name' => 'Turco'),
            array( 'code' => 'ZH', 'name' => 'Cinese semplificato'),
        );
    }
    
    /**
     * Return the possible acquirers
     *
     * @return array|object
     */
    public function getAcquirer(){
        return array(self::ACQUIRER_POSITIVI=> 'BNLPositivity',self::ACQUIRER_PARIBAS  => 'BNLParibas');
    }
    
    /**
     * Get Default Terminal Id
     *
     * @return string
     */
    public function getTestTerminalId(){
        return self::DEFAULT_MERCHANT_ID;
    }
    /**
     * Get Default Hased Password
     *
     * @return string
     */
    public function getTestHashMessage(){
        return self::DEFAULT_BLOWFISH_PASSWORD;
    }
    /**
     * Get Default Extra Hased Password
     *
     * @return string
     */
    public function getTesthMacPassword(){
        return self::DEFAULT_HS_MAC_PASSWORD;
    }
    /**
     * Return a list with all available countries
     *
     * @return array|object
     */
    public function getSellingLocations(){
        $arr = array();
        $filePath = __DIR__ . "/../../Data/countries_it.xml";

        if (file_exists($filePath)) {
            $xmlElements = simplexml_load_file($filePath);
            foreach($xmlElements as $country){
                $arr[(string)$country->code] = (string)$country->name;
            }
        }

        return $arr;
    }
}