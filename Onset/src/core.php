<?php

/*
 * isIllegalAccess
 */
function isIllegalAccess($rand, $onset_rand) {
	if($rand != $onset_rand) {
		return false;
	}
	return true;
}

/*
 * unserial
 */
function unserial($dir) {
	return unserialize(file_get_contents($dir.'roomlist'));
}
