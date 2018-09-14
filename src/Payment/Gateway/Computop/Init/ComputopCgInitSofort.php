<?php
namespace Payment\Gateway\Computop\Init;
use Payment\Gateway\Computop\CmptpMissingParException;

/**
 * Computop class
 * Responsible for all the init() calls
 */
class ComputopCgInitSofort extends ComputopCgInit {

    public $addrCountryCode;
    public $sellingPoint;
    public $accOwner;
    public $bic;
    public $iban;

    public function __construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl)
    {
        parent::__construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl.'/sofort.aspx');
    }

    protected function resetFields(){
        $this->addrCountryCode = null;
        $this->sellingPoint = null;
        $this->accOwner = null;
        $this->bic = null;
        $this->iban = null;
        parent::resetFields();
    }

    /**
     * Return an array of objects containing all the extra parameters that have to be passed in the request 
     *
     * @return array|array|string
     */
    public function getExtraParams(){
        return array
        (
            array("addrCountryCode",'mandatory','dropdown','CountryCode'),
            array("sellingPoint",'optional','text',''),
            array("accOwner",'mandatory','text',''), 
            array("bic",'optional','text',''), 
            array("iban",'optional','text',''), 
        );
    }

    protected function checkFields() {

        if (!$this->addrCountryCode) {
            throw new CmptpMissingParException("Missing addrCountryCode");
        }
        if (!$this->accOwner) {
            throw new CmptpMissingParException("Missing accOwner");
        }

        parent::checkFields();
    }

    protected function getParams(){
        // format data which is to be transmitted - required
        $arr = parent::getParams();
        
        $pAddrCountryCode = "AddrCountryCode=$this->addrCountryCode";
        $pSellingPoint = "SellingPoint=$this->sellingPoint";
        $pAccOwner = "AccOwner=$this->accOwner";
        $pBic = "BIC=$this->bic";
        $pIban = "IBAN=$this->iban";

        array_push($arr,$pAddrCountryCode,$pSellingPoint,$pAccOwner,$pBic,$pIban);
        return $arr;
    }
}