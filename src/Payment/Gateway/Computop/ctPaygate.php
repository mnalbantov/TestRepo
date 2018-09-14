<?php

namespace Payment\Gateway\Computop;
/**
 * File function.inc.php
 *
 * Provides basic class with functions for interacting with Paygate, such as Blowfish encryption and decryption, HMAC,
 * as well as texts, config, etc.
 *
 */

/**
 * Class ctPaygate
 *
 * Basic class for interaction with Paygate in PHP environment.
 */
class ctPaygate extends Blowfish
{

    /*
    * Text values for status
    */
    const NO_DATA_FOUND = "No data found!";
    const PAYMENT_FAILED = "Payment failed!";
    const PAYMENT_SUCCESSFUL = "Payment successful!";
    const UNKNOWN_STATUS = "Unknown status!";

    /**
     * ctPaygate constructor.
     */
    function ctPaygate()
    {
    }

    /**
     * Status as text.
     *
     * Returns the text to the passed status.
     *
     * @param string $sStatus
     * @return string
     */
    function ctRealstatus($sStatus)
    {

        switch ($sStatus) {

            case "OK":
                $rs = self::PAYMENT_SUCCESSFUL;   // Payment succeeded. Correct response. (notify.php, success.php)
                break;

            case "AUTHORIZED":
                $rs = self::PAYMENT_SUCCESSFUL;   // Payment succeeded. Correct response. (notify.php, success.php)
                break;

            case "FAILED":
                $rs = self::PAYMENT_FAILED;       // Payment failed. Correct response. (notify.php, failure.php)
                break;

            case "":
                $rs = self::NO_DATA_FOUND;              // No data. Tried to open directly (e.g. with a browser)? (*.php)
                break;

            default:
                $rs = self::UNKNOWN_STATUS;       // Unknown status (notify.php)
        }

        return $rs;
    }

    /**
     * Create HTML with parameters in a NVP array
     *
     * Split the elements in the passed array $arText by the split-string $sSplit. Return the result as html table rows.
     * If $sArg is passed, return only the matching row.
     *
     * @param string[] $arText
     * @param string $sSplit
     * @param string $sArg
     * @return string
     */
    function ctSplit($arText, $sSplit, $sArg = "")
    {

        $b = "";
        $i = 0;
        $info = '';

        while ($i < count($arText)) {
            $b = explode($sSplit, $arText [$i++]);

            if ($b[0] == $sArg) {                // check for $sArg
                $info = $b[1];
                $b = 0;
                break;

            } else {
                $info .= '<tr><td align=right>' . $b[0] . '</td><td>"' . $b[1] . '"</td></tr>';
            }
        }

        if ((strlen($sArg) > 0) & ($b != 0)) {   // $sArg not found
            $info = "";
        }

        return $info;
    }

    /**
     * Calculate the MAC value.
     *
     * @param string $PayId
     * @param string $TransID
     * @param string $MerchantID
     * @param integer $Amount
     * @param string $Currency
     * @param string $HmacPassword
     * @return string
     */
    function ctHMAC($PayId = "", $TransID = "", $MerchantID, $Amount, $Currency, $HmacPassword)
    {
        return hash_hmac("sha256", "$PayId*$TransID*$MerchantID*$Amount*$Currency", $HmacPassword);
    }

    /**
     * Encrypt the passed text (any encoding) with Blowfish.
     *
     * @param string $plaintext
     * @param integer $len
     * @param string $password
     * @return bool|string
     */
    function ctEncrypt($plaintext, $len, $password)
    {
        if (mb_strlen($password) <= 0) $password = ' ';
        if (mb_strlen($plaintext) != $len) {
            echo 'Length mismatch. The parameter len differs from actual length.';
            return false;
        }
        $plaintext = $this->expand($plaintext);
        $this->bf_set_key($password);
        return bin2hex($this->encrypt($plaintext));
    }

    /**
     * Decrypt the passed HEX string with Blowfish.
     *
     * @param string $cipher
     * @param integer $len
     * @param string $password
     * @return bool|string
     */
    function ctDecrypt($cipher, $len, $password)
    {
        if (mb_strlen($password) <= 0) $password = ' ';
        # converts hex to bin
        $cipher = pack('H' . strlen($cipher), $cipher);
        if ($len > strlen($cipher)) {
            echo 'Length mismatch. The parameter len is too large.';
            return false;
        }
        $this->bf_set_key($password);
        return mb_substr($this->decrypt($cipher), 0, intval($len));
    }
}
