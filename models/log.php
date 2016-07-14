<?php

namespace __zf__;

class Log extends ZModel{
	public $table = "access_logs";
	static function activity($array){
		self::orm()->table("access_logs")->add($array);
	}

	function orm(){
		return new ZORM;
	}
}