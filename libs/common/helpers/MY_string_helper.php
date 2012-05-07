<?php 
//ಠ_ಠ
/**
 *  Returns weather the supplied $needle is the start of the $haystack string
 * @param $haystack
 * @param $needle
 */
function starts_with($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

/**
 * 
 * Returns weather the supplied $needle is the end of the $haystack string
 * @param $haystack
 * @param $needle
 */
function ends_with($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}