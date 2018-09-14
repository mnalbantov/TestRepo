<?php

namespace Payment\Gateway\Computop\S2S;
use Payment\Gateway\Computop\ComputopUtils;


class ComputopCgCapture extends BaseComputopCgS2S{

    public $refNr;
	public $finishAuth;
	public $textField1;
    public $textField2;
    
    protected function resetFields() {
        parent::resetFields();
        $this->refNr = null;      
        $this->finishAuth = null;
        $this->textField1 = null;
        $this->textField2 = null;
    }

    protected function checkFields() {
        parent::checkFields();
    }
    
    protected function parseResponseMap($response){
        parent::parseResponseMap($response);
        $this->refNr = ComputopUtils::getValue($response, "refnr");
    }

    protected function getParams(){
        $this->checkFields();

        // formatting data to transmit - required
        $pMerchantId = "&MerchantID=".$this->merchantId;
        $pPayId = "&PayID=".$this->payId;
        $pTransId = "&TransID=".$this->transId;
        $pAmount = "&Amount=".$this->amount;
        $pCurrency = "&Currency=".$this->currency; 

        $params = array($pMerchantId, $pPayId, $pTransId, $pAmount, $pCurrency);

        // adding optional params
        if($this->refNr !== null){
            array_push($params, "&RefNr=".$this->refNr);
        }
        if($this->finishAuth !== null){
            array_push($params, "&FinishAuth=".$this->finishAuth);
        }
        if($this->textField1 !== null){
            array_push($params, "&Textfeld1=".$this->textField1);
        }
        if($this->textField2 !== null){
            array_push($params, "&Textfeld2=".$this->textField2);
        }

        return $params;
    }
}