<?php
namespace Payment\Gateway\Computop\Init;
use Payment\Gateway\Computop\CmptpMissingParException;

/**
 * Computop class
 * Responsible for all the init() calls
 */
class ComputopCgInitIdeal extends ComputopCgInit {

    public $bic;

    public function __construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl)
    {
        parent::__construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl.'/ideal.aspx');
    }

    protected function resetFields(){
        $this->bic = null;
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
            array("bic",'optional','text','')
        );
    }

    protected function checkFields() {

        parent::checkFields();
    }

    protected function getParams(){
        // format data which is to be transmitted - required
        $arr = parent::getParams();
        
        $pBic = "BIC=$this->bic";

        array_push($arr,$pBic);
        return $arr;
    }
}