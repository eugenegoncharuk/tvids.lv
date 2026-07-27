<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* URI Language Identifier
*
* Adds a language identifier prefix to all site_url links
* Loads the users language file
*
* version 0.5 (c) Wiredesignz 2008-04-11
*/
class MY_Language extends CI_Language
{
    // ADDED THIS
    var $lang;

    function MY_Language() {
        parent::CI_Language();
        
        global $RTR;
        
        //get the language from uri segment
        $lang_abbr = $RTR->uri->segment(1);
        $lang_uri_abbr = $RTR->config->item('lang_uri_abbr');        
		
        //or from the config language
        if(!isset($lang_uri_abbr[$lang_abbr]))
        {
            $user_lang = $RTR->config->item('language');
            $lang_abbr = $RTR->config->item('language_abbr');
        } else {
            $user_lang = $lang_uri_abbr[$lang_abbr];

            //reset config language to match the user language
            $RTR->config->set_item('language', $user_lang);
            $RTR->config->set_item('language_abbr', $lang_abbr);
        } 

        // AND THIS
        $this->lang = $user_lang;
		//append the language abbreviation to the index_page config url
		$index_page = $RTR->config->item('index_page');
		$index_page = ($index_page=="")?"":$index_page."/";
        $RTR->config->set_item('index_page', $index_page.$lang_abbr.'/');

    }

    function load($langfile) {
        parent::load($langfile, $this->lang);
    }
	
	function get_current_lang(){
		global $RTR;
        //get the language from uri segment
        $lang_abbr = $RTR->uri->segment(1);
        //or from the config language
		$lang_uri_abbr = $RTR->config->item('lang_uri_abbr');
        if(!isset($lang_uri_abbr[$lang_abbr])) {
            $lang_abbr = $RTR->config->item('language_abbr');
        } 
		return $lang_abbr;
	}
} 
?>