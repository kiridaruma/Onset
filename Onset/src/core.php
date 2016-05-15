<?php

/*
 * isIllegalAccess
 *
 */
function isIllegalAccess($rand, $onset_rand) {
	if($rand != $onset_rand) {
		return false;
	}

	return true;
}
