<?php


class KlarnaPrestaEncoding
{
	public static $prestaEncoding = 'UTF-8';

	public static $klarnaEncoding = 'ISO-8859-1';

	public static function encode($str, $from = null, $to = null)
	{
		if ($from === null)
		{
			$from = self::$prestaEncoding;
		}

		if ($to === null) {
			$to = self::$klarnaEncoding;
		}

		return iconv($from, $to, $str);
	}

	public static function decode($str, $from = null, $to = null)
	{
		if ($from === null)
		{
			$from = self::$prestaEncoding;
		}

		if ($to === null) {
			$to = self::$klarnaEncoding;
		}	
	}
}