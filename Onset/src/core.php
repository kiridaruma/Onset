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

/*
 * isExistRoom
 */
function isExistRoom($roomlist, $name) {
	if(isset($roomlist[$name])) {
		return true;
	}
	return false;
}
