<?php
namespace Payment\Gateway\Computop\Init;
use Payment\Gateway\Computop\CmptpMissingParException;

/**
 * Computop class
 * Responsible for all the init() calls
 */
class ComputopCgInitCup extends ComputopCgInit {

    public $sellingPoint;
    public $accOwner;
    public $email;
    public $phone;

    public function __construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl)
    {
        parent::__construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl.'/Chinaunionpay.aspx');
    }

    protected function resetFields(){
        $this->sellingPoint = null;
        $this->accOwner = null;
        $this->email = null;
        $this->phone = null;
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
            array("sellingPoint",'optional','text',''),
            array("accOwner",'mandatory','text',''),
            array("email",'mandatory','text',''),
            array("phone",'mandatory','text',''),
        );
    }

    protected function checkFields() {

        if (!$this->accOwner) {
            throw new CmptpMissingParException("Missing accOwner");
        }
        if (!$this->email) {
            throw new CmptpMissingParException("Missing email");
        }
        if (!$this->phone) {
            throw new CmptpMissingParException("Missing phone");
        }

        parent::checkFields();
    }

    protected function getParams(){
        // format data which is to be transmitted - required
        $arr = parent::getParams();
        
        $pSellingPoint = "SellingPoint=$this->sellingPoint";
        $pAccOwner = "AccOwner=$this->accOwner";
        $pEmail = "Email=$this->email";
        $pPhone = "Phone=$this->phone";

        array_push($arr,$pSellingPoint,$pAccOwner,$pEmail,$pPhone);
        return $arr;
    }
}