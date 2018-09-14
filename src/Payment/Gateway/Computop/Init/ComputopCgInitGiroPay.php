<?php
namespace Payment\Gateway\Computop\Init;
use Payment\Gateway\Computop\CmptpMissingParException;

/**
 * Computop class
 * Responsible for all the init() calls
 */
class ComputopCgInitGiroPay extends ComputopCgInit {

    public $sellingPoint;
    public $accOwner; // <name><space><surname><space>
    public $scheme; // "gir" | "eps"
    public $bic;
    public $expirationTime; // Format: YYYY-MM-ddTHH:mm:ss
    public $iban;

    public function __construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl)
    {
        parent::__construct($merchantId,$blowfishPassword,$hMacPassword,$serverUrl.'/giropay.aspx');
    }

    protected function resetFields(){
        $this->sellingPoint = null;
        $this->accOwner = null;
        $this->scheme = null;
        $this->bic = null;
        $this->expirationTime = null;
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
            array("sellingPoint",'optional','text',''),
            array("accOwner",'optional','text',''), // <nome><spazio><cognome><spazio>
            array("scheme",'optional','text',''), // TODO: "gir" || "eps" how do i choose the correct one? 
            array("bic",'optional','text',''),
            array("expirationTime",'optional','date',''), // Formato: AAAA-MM-ggTHH:mm:ss
            array("iban",'optional','text',''), // TODO: IBAN	ans..34	C	Solo per EVO: Numero Conto Bancario Internazionale (obbligatorio per la funzione credito e verifica conto tramite EVO)
        );
    }

    protected function checkFields() {

    
        parent::checkFields();
    }

    protected function getParams(){
        // format data which is to be transmitted - required
        $arr = parent::getParams();
        
        $pSellingPoint = "SellingPoint=$this->sellingPoint";
        $pAccOwner = "AccOwner=$this->accOwner";
        $pScheme = "Scheme=$this->scheme";
        $pBic = "BIC=$this->bic";
        $pExpirationTime = "expirationTime=$this->expirationTime";
        $pIban = "IBAN=$this->iban";

        array_push($arr,$pSellingPoint,$pAccOwner,$pScheme,$pBic,$pExpirationTime,$pIban);
        return $arr;
    }
}