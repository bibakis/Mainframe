<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Functions related to 'debugging' your application.
 */

// ------------------------------------------------------------------------
/**
 * A print_r with style
 * @param object $object
 * @return null
 */
function damn($object, $die = true){
	echo("<pre style='padding:5px;background-color:#fbfbfb;border:1px solid #eeeeee;color:#111111;'>".
		print_r($object,true).
		"</pre>");
	if ($die)
	{
		die();
	}
}


function var_damn($var){
	echo("<div style='padding:5px;background-color:#fbfbfb;border:1px solid #eeeeee;color:#111111;'>".
		var_dump($var).
		"</div>");
	die();
}


/**
 * Echoes a javascript alert with some text you specify 
 * @param string $text
 * @return string
 */
function alert($text = 'Alert'){
	echo '<script type="text/javascript">alert ("'.$text.'");</script>';
}


/*
 * TODO: Make this one log and output an error
 */
function throw_error($message){
	die($message);
}


/**
 * TODO: fix this
 * it must return the session information
 * @return unknown_type
 */
function site_info(){
	$ci =& get_instance();
	if (config('debug')){
		echo '<div style="border: 1px solid #ddd; background-color: #f6f6f6; padding: 10px; margin: 50px auto 10px auto; width: 400px; text-align: left; border-radius: 5px;">';
		//echo 'Active language: '.wide::active_lang();
		echo '<a href="'.site_url('user/logout').'">Kill session</a><span class="light"> - Executed in: '.$ci->benchmark->elapsed_time().'</span><hr />';
		//echo 'HTTPS status: '. $_SERVER['HTTPS'];
		//echo '<b>session_id</b>: '.$ci->session->userdata('session_id').'<br />';
		echo '<b>user_id</b>: '.$ci->session->userdata('user_id').'<br />';
		echo '<b>username</b>: '.$ci->session->userdata('username').'<br />';
		echo '<b>type</b>: '.$ci->session->userdata('type').'<br />';
		echo '<hr />';
		echo '<b>open_links_in_new_tabs</b>: '.$ci->session->userdata('open_links_in_new_tabs').'<br />';
		echo '<b>bookmarks_private</b>: '.$ci->session->userdata('bookmarks_private').'<br />';
		echo '<b>bookmarks_open</b>: '.$ci->session->userdata('bookmarks_open').'<br />';
		echo '<b>theme</b>: '.$ci->session->userdata('theme').'<br />';
		//echo '<b>user_type</b>: '.$ci->session->userdata('user_type').'<br />';
		echo '</div>';
	}
}


function dom_dump($obj) {
    if ($classname = get_class($obj)) {
        $retval = "Instance of $classname, node list: \n";
        switch (true) {
            case ($obj instanceof DOMDocument):
                $retval .= "XPath: {$obj->getNodePath()}\n".$obj->saveXML($obj);
                break;
            case ($obj instanceof DOMElement):
                $retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
                break;
            case ($obj instanceof DOMAttr):
                $retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
                //$retval .= $obj->ownerDocument->saveXML($obj);
                break;
            case ($obj instanceof DOMNodeList):
                for ($i = 0; $i < $obj->length; $i++) {
                    $retval .= "Item #$i, XPath: {$obj->item($i)->getNodePath()}\n"."{$obj->item($i)->ownerDocument->saveXML($obj->item($i))}\n";
                }
                break;
            default:
                return "Instance of unknown class";
        }
    } else {
        return 'no elements...';
    }
    return htmlspecialchars($retval);
}
