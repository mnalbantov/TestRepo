<?php
final class PayGateway
{
    const IGFC = 'igfs';
    const CMPT1 = 'computop';
    
    /**
     * Return the possible payment types
     *
     * @param 
     * @return array|object
     */
    public static function getPaymentTypes(){
        return array(self::IGFC=> 'IGFS',self::CMPT1  => 'Computop');
    }

    /**
     * Undocumented function
     *
     * @param string $paymenttype
     * @return \Payment\Gateway\Igfs\Gateway|\Payment\Gateway\Computop\Gateway|boolean
     */
    public static function getIstance($paymenttype, $test)
    {
        $return =null;
        switch ($paymenttype) {
        case self::IGFC:
            $return = new \Payment\Gateway\Igfs\Gateway($test);
            break;
        case self::CMPT1:
            $return = new \Payment\Gateway\Computop\Gateway($test);
            break;
        default:
            break;
        }
        return $return;
    }
}