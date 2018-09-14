<?php
namespace Payment\Gateway\Igfs\Init;

class InitTerminalInfo {
	
	public $tid;
	public $payInstrToken;

    function __construct() {
	}

	public function toXml($tname) {
		$sb = "";
		$sb .= "<" . $tname . ">";
		if ($this->tid != NULL) {
			$sb .= "<tid><![CDATA[";
			$sb .= $this->tid;
			$sb .= "]]></tid>";
		}
		if ($this->payInstrToken != NULL) {
			$sb .= "<payInstrToken><![CDATA[";
			$sb .= $this->payInstrToken;
			$sb .= "]]></payInstrToken>";
		}
		$sb .= "</" . $tname . ">";
		return $sb;
	}

}
?>
