<?php

namespace Payment\Gateway\Computop\S2S;
use Payment\Gateway\Computop\ComputopUtils;

class ComputopCgReverse extends BaseComputopCgS2S{

    public $refNr;
    
    protected function resetFields() {
        parent::resetFields();
        $this->refNr = null;
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
        $pXId = "&XID=".$this->xId;
        $pTransId = "&TransID=".$this->transId;
        $pAmount = "&Amount=".$this->amount;
        $pCurrency = "&Currency=".$this->currency; 

        $params = array($pMerchantId, $pPayId, $pXId, $pTransId, $pAmount, $pCurrency);

        // adding optional params
        if($this->refNr !== null){
            array_push($params, "&RefNr=".$this->refNr);
        }

        return $params;
    }
}