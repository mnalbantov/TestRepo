<?php
namespace Payment;

/**
 *
 * @author Sendabox
 */
interface GatewayInterface
{
    /**
     * 
     * Transaction initializer. Create the Redirect URL.
     * 
     * @param array $params
     * @return array|object
     */
    public function init(array $params);
    /**
     * 
     * Verify transaction. Receive only the status of the specific transaction.
     * 
     * @param array $params
     * @return array|object
     */
    public function verify(array $params);
    /**
     * 
     * Transaction confirmation. 
     * Transfer a specific amount from an authorized transaction
     * 
     * @param array $params
     * @return array|object
     */
    public function confirm(array $params);
    /**
     * 
     * Refund transaction. Return a specific amount back to buyer.
     * 
     * @param array $params
     * @return array|object
     */
    public function refund(array $params);
    /**
     * 
     * Cancel pending transaction. Return a specific amount back to buyer.
     * 
     * @param array $params
     * @return array|object
     */
    public function cancel(array $params);
    /**
     * 
     * Return all the possible payment instruments
     * 
     * @param 
     * @return array|object
     */
    public function getPaymentInstruments();
    /**
     * 
     * Return all the possible payment instruments for credit cards
     * 
     * @param 
     * @return array|object
     */
    public function getCcPaymentInstruments();
    /**
     * 
     * Return all the possible transaction types
     * 
     * @param 
     * @return array|object
     */
    public function getTransactionTypes();
    /**
     * 
     * Return all the possible cheout types
     * 
     * @param 
     * @return array|object
     */
    public function getCheckoutTypes();
    /**
     * Get Allowed Currencies
     *
     * @return array|object
     */
    public function getCurrenciesAllowed();
    /**
     * Get Allowed Languages
     *
     * @return array|object
     */
    public function getLanguagesAllowed();
    /**
     * Get Default Terminal Id
     *
     * @return string
     */
    public function getTestTerminalId();
    /**
     * Get Default Hased Password
     *
     * @return string
     */
    public function getTestHashMessage();
    /**
     * Get Default Extra Hased Password
     *
     * @return string
     */
    public function getTesthMacPassword();
    /**
     * Return the possible acquirers
     *
     * @return array|object
     */
    public function getAcquirer();
    /**
     * Return a list with all available countries
     *
     * @return array|object
     */
    public function getSellingLocations();
    
}
