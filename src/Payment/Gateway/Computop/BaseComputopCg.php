<?php
namespace Payment\Gateway\Computop;

abstract class BaseComputopCg{

    public $merchantId;                // via e-mail from computop support
    public $blowfishPassword;   // via phone from computop support
    public $hMacPassword;           // via phone from computop support
    public $serverUrl;
	public $mac;
	public $language;
    public $payId;
    public $transId; // = "TransID";
    public $amount; // = 11;
    public $currency; // = "EUR";
    
    // Default values
    const DEFAULT_CURRENCY = 'EUR';
    public function __construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl = null)
    {
        $this->resetFields();
        $this->merchantId = $merchantId;
        $this->blowfishPassword = $blowfishPassword;
        $this->hMacPassword = $hMacPassword;
        $this->serverUrl = $serverUrl;
    }
    protected function resetFields()
    {
        $this->merchantId = null;      
        $this->blowfishPassword = null;
        $this->hMacPassword = null; 
        $this->serverUrl = null;     
        $this->mac = null;  
        $this->payId = null;      
        $this->transId = null;
        $this->currency = null;
        $this->amount = null;
        $this->language = null;
    }

    protected function getParams(){
        $arr = array();
        if($this->language){
            array_push($arr, "language=$this->language");
        }
        return $arr;
    }

    protected function encryptRequestParams(){
        $params = $this->getParams();
        $myPayGate = new ctPaygate;
        $this->mac = $myPayGate->ctHMAC($this->payId, $this->transId, $this->merchantId, $this->amount, $this->currency, $this->hMacPassword);
        array_push($params, "MAC=$this->mac");
        
        $plaintext = join("&", $params);
        $len = strlen($plaintext);  // Length of the plain text string

        // encrypt plaintext
        $data = $myPayGate->ctEncrypt($plaintext, $len, $this->blowfishPassword);

        // format variables for URL
        $pUrlMerchant = "MerchantID=$this->merchantId";
        $pUrlLen = "Len=$len";
        $pUrlData = "Data=$data";

        $rQuery = array($pUrlMerchant,$pUrlLen,$pUrlData);

        return join("&", $rQuery);
    }

    protected function buildRequest(){

        $encParams = $this->encryptRequestParams();
        // create url
        return $this->serverUrl.'?'.$encParams;
    }

    protected function decryptResponse($response){
        if (!$this->blowfishPassword) {
            throw new CmptpMissingParException("Missing blowfishPassword");
        }

        $rsExp = explode('&', $response);
        $myPayGate = new ctPaygate;
        $len       = $myPayGate->ctSplit($rsExp, '=', 'Len');
        $data      = $myPayGate->ctSplit($rsExp, '=', 'Data');

        // decrypt the data string
        $myPayGate = new ctPaygate;
        $plaintext = $myPayGate->ctDecrypt($data, $len, $this->blowfishPassword);

        // prepare information string
        $a = "";
        $a = explode('&', $plaintext);
        
        parse_str($plaintext, $arr);

        return $arr;
    }

    protected function checkFields() {
		if (!$this->hMacPassword) {
            throw new CmptpMissingParException("Missing hMacPassword");
        }
        if (!$this->blowfishPassword) {
            throw new CmptpMissingParException("Missing blowfishPassword");
        }
        if (!$this->merchantId) {
            throw new CmptpMissingParException("Missing merchantId");
        }
        if (!$this->serverUrl) {
            throw new CmptpMissingParException("Missing serverUrl");
        }
        if ($this->payId === null) {
            throw new CmptpMissingParException("Missing payId");
        }
        if (!$this->transId) {
            throw new CmptpMissingParException("Missing transId");
        }
        if (!$this->amount) {
            throw new CmptpMissingParException("Missing amount");
        }
        if(!trim($this->currency)){
            $this->currency = self::DEFAULT_CURRENCY;
        }
    }

    public function mapStatus($status){
        $myPayGate = new ctPaygate;
        // check transmitted decrypted status
        return $myPayGate->ctRealstatus($status);
    }

    public function execute(){

        $data = $this->encryptRequestParams();

        // init curl connection and execute it

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->serverUrl);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        
        // PHP <5.5.0
        defined("CURLE_OPERATION_TIMEDOUT") || define("CURLE_OPERATION_TIMEDOUT", CURLE_OPERATION_TIMEOUTED);
        
        //execute post
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
                throw new ReadWriteException($url, curl_error($ch));
            } else {
                throw new ConnectionException($url, curl_error($ch));
            }
        } else {
            //close connection
            curl_close($ch);
        }

        $this->parseResponseMap($this->decryptResponse($result));
    }

    protected function parseResponseMap($response){
        $this->payId = ComputopUtils::getValue($response, "PayID"); 
        $this->transId = ComputopUtils::getValue($response, "TransID");
        $this->mac = ComputopUtils::getValue($response, "MAC");
    }
}