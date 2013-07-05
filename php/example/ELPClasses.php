<?php

// Optional security feature
// if (!defined("_CONST_")) { die("Unauthorized access."); }

class ELPLanguage {
	public $languageName;
	public $isELPLangClass;
	private $data;
	
	public function __construct () {
		$this->isELPLangClass = true;
		$this->languageName = "";
		$this->data = array();
	}
	public function setLanguage ($l, $d) {
		if (gettype($l)!="string"||gettype($d)!="array") { return null; }
		$this->languageName = $l;
		foreach ($d as $k=>$v) {
			if (gettype($k)=="string"&&gettype($v)=="string") {
				$this->data[$k] = $v;
			}
		}
		return sizeof($this->data);
	}
	public function setLanguageFromFile ($f) {
		if (gettype($f)!="string") { return null; }
		if (!file_exists($f)||!file_exists("./".$f)) { return -1; }
		
		if (file_exists($f)) {
			$fcontents = file_get_contents($f);
		} else {
			$fcontents = file_get_contents("./".$f);
		}
		if (strpos($fcontents, "\n")!==false) {
			$fcontents = str_replace("\r", "", $fcontents);
			$flines = explode("\n", $fcontents);
		} elseif (strpos($fcontents, "\r")!==false) {
			$fcontents = str_replace("\n", "", $fcontents);
			$flines = explode("\r", $fcontents);
		} else { return null; }
		
		$this->languageName = str_replace(" ", "", $flines[0]);		
		foreach ($flines as $a) {
			if (strpos($a, "#")!==0&&strpos($a, "=")!==false) {
				$t = explode("=", $a, 2);
				$this->data[$t[0]] = $t[1];
			}
		}
		return sizeof($this->data);
	}
	public function getValue ($key) {
		if (gettype($key)!="string") { return null; }
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		return $key;
	}
	public function setValue ($key, $value) {
		if (gettype($key)!="string"||gettype($value)!="string") { return null; }
		$this->data[$key] = $value;
		return $this->data[$key];
	}
	public function setName ($l) {
		if (gettype($l)=="string"&&!empty($l)) {
			$this->languageName = $l; return true;
		} return null;
	}
}

class ELPParser {
	private $languages;
	private $mode;
	
	public function __construct () {
		$this->mode = 0;
		$this->languages = array();
	}
	
	public function setMode ($i) {
		if (gettype($i)!="integer"||$i>3||$i<0) { return null; }
		$this->mode = $i;
		// mode 0: default mode:	replaces {$key} with $value
		// mode 1: alternate mode:	replaces [$key] with $value
		// mode 2: auto mode:		replaces {$key} or [$key] with $value
		// mode 3: translate mode:	replaces $key with $value
		return true;
	}
	public function addLanguage ($lang) {
		if (gettype($lang)=="string") { return $this->addLanguageFromFile($lang); }
		if (gettype($lang)!="object"||empty($lang->isELPLangClass)) { return null; }
		$this->languages[$lang->languageName] = $lang;
	}
	public function addLanguageFromFile ($f) {
		if (gettype($f)!="string"||!file_exists($f)||!file_exists("./".$f)) { return null; }
		$language = new ELPLanguage;
		if ($language->setLanguageFromFile($f)==true) {
			return $this->addLanguage($language);
		}
		return -1;
	}
	public function parse ($input, $lang = "") {
		if (gettype($input)!="string") { return null; }
		if (sizeof($this->languages)<1) { return false; }
		if (empty($lang)||gettype($lang)!="string") { $lang = key($this->languages); }
		
		if (array_key_exists($lang, $this->languages)) {
			$lang = $this->languages[$lang];
		} else { return false; }
		
		switch ($this->mode) {
			case 0: return $this->modeZero($input, $lang);
			case 1: return $this->modeOne($input, $lang);
			case 2: return $this->modeTwo($input, $lang);
			case 3: return $this->modeThree($input, $lang);
		}
	}
	
	private function modeZero ($textin, $lang) {
		$textout = "";
		$ar = explode("}", $textin);
		foreach ($ar as $a) {
			if (strpos($a, "\\")!==false&&strpos($a, "\\")<strpos($a, "{")) {
				$textout .= str_replace("\\", "", $a) . "}";
			} else {
				$t = explode("{", $a, 2);
				if (sizeof($t)==2) {
					$textout .= $t[0] . $lang->getValue($t[1]);
				} else {
					$textout .= $a;
				}
			}
		}
		return $textout;
	}
	private function modeOne ($textin, $lang) {
		$textout = "";
		$ar = explode("]", $textin);
		foreach ($ar as $a) {
			if (strpos($a, "\\")!==false&&strpos($a, "\\")<strpos($a, "[")) {
				$textout .= str_replace("\\", "", $a) . "]";
			} else {
				$t = explode("[", $a, 2);
				if (sizeof($t)==2) {
					$textout .= $t[0] . $lang->getValue($t[1]);
				} else {
					$textout .= $a;
				}
			}
		}
		return $textout;
	}
	private function modeTwo ($textin, $lang) {
		$t = $this->modeOne($textin, $lang);
		return $this->modeZero($t, $lang);
	}
	private function modeThree ($textin, $lang) {
		$textout = "";
		$ar = explode(")", $textin);
		foreach ($ar as $a) {
			if (strpos($a, "\\")!==false&&strpos($a, "\\")<strpos($a, "(")) {
				$textout .= str_replace("\\", "", $a) . ")";
			} else {
				$t = explode("(", $a, 2);
				if (sizeof($t)==2) {
					$tr = preg_split("/\s/", $t[0]);
					$t[0] = "";
					foreach ($tr as $rt) {
						$t[0] .= $lang->getValue($rt) . " ";
					}
					$textout .= $t[0] . " " . $lang->getValue($t[1]) . " ";
				} else {
					$textout .= $a . " ";
				}
			}
		}
		return $textout;
	}
	
}
