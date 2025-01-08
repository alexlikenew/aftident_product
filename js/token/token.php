<?php

/*		Obsluga tokenow 
 *		plik:		js/token.token.php
 *		autor: 		Technetium [Tc]
 *				Marek Kleszyk
 *		data:		01 sierpień 2008
 *		system:		TCMS-4.0-www
 */
 
	$tokens=file('tokens.txt');
	$token=$tokens[$_GET["n"]];
	$text = trim($token);
	$font = './tahoma.ttf';
	$width = 135;
	$height = 24;
	
	$i=imagecreate($width,$height);  // szerokosc, wysokosc wygenerowanego obrazka
	$white=imagecolorallocate($i,255,255,255);
	$black=imagecolorallocate($i,0,0,0);
	$gray=imagecolorallocate($i,150,150,150);
	imagefill($i,1,1,$white);
	
	for($c=0;$c<600;$c++ ) {
		$los1=rand(0,$width);
		$los2=rand(0,$height);
		imageline($i,$los1,$los2,$los1,$los2,$gray);
	}
	
	imagettftext($i, 20, -5, 22, 17, $gray, $font, $text);
	imagettftext($i, 20, 8, 20, 27, $black, $font, $text);
	header("Content-type: image/gif");
	imagegif($i);
?>