<?php

class EclaimsEncoder extends CComponent {

	public function formatUTF8($str) {
		$encoding = mb_detect_encoding($str);
        

		if($encoding == 'UTF-8') {
            $str = utf8_decode($str);
            $str = self::fixEnye($str);
            return $str;
		}

		return $str;
	}

	private static function fixEnye($str)
    {
        $output = "";
        
        $letters = str_split($str);
        foreach ($letters as $key => $letter) {
            if (ord($letter) == 241) {
                $output .= (String) "&ntilde";
            } else if (ord($letter) == 209) {
                $output .= (String) "&Ntilde";
            } else if (ord($letter) == 195 || ord($letter) == 177) {
                if (ord($letter) == 177) {
                    $output .= "ñ";
                }
            } else {
                $output .= $letter;
            }
        }

        return $output;
    }

}