<?php

namespace Payment\Gateway\Computop\S2S;
use Payment\Gateway\Computop\ComputopUtils;

abstract class BaseComputopCgS2S extends \Payment\Gateway\Computop\BaseComputopCg{
    
    // Response only variables
	public $mId;
	public $xId;
	public $status;
	public $description;
	public $code;

    protected $request;
    
    protected function resetFields() {
		parent::resetFields();
        $this->mId = null;   
        $this->xId = null;   
        $this->status = null;   
        $this->description = null;   
        $this->code = null;
    }
    
    protected function parseResponseMap($response){
        parent::parseResponseMap($response);
        $this->mId = ComputopUtils::getValue($response, "mid");
        $this->xId = ComputopUtils::getValue($response, "XID"); 
        $this->status = ComputopUtils::getValue($response, "Status");
        $this->description = ComputopUtils::getValue($response, "Description");
        $this->code = ComputopUtils::getValue($response, "Code");
    }
}