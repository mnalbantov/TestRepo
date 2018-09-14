<?php
namespace Payment\Gateway\Computop\Init;
use Payment\Gateway\Computop\CmptpMissingParException;

/**
 * Computop class
 * Responsible for all the init() calls
 */
class ComputopCgInitAlipay extends ComputopCgInit {

    public $addrCountryCode;
    public $sellingPoint;
    public $accOwner;
    public $device; // if device = "Mobile" it show the mobile version

    public function __construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl)
    {
        parent::__construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl.'/alipay.aspx');
    }

    protected function resetFields(){
        $this->addrCountryCode = null;
        $this->sellingPoint = null;
        $this->accOwner = null;
        $this->device = null;
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
            array("addrCountryCode",'optional','dropdown','CountryCode'),
            array("sellingPoint",'optional','text',''),
            array("accOwner",'mandatory','text',''),
            array("device",'optional','checkbox',''),
        );
    }

    protected function checkFields() {

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
        $pDevice = "Device=$this->device";

        array_push($arr,$pAddrCountryCode,$pSellingPoint,$pAccOwner,$pDevice);
        return $arr;
    }
}