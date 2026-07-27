<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/* SVN FILE: $Id$ */
/**
 * view element to display a language selection.
 *
 * This element shows the flags of all supported languages in a row
 * and allows the user to select one of them.
 * The flag images are expected in the img/lang subdirectory of the
 * site's webroot. The image names must correspond to the keys of
 * the array in the configuration entry $config['lang_avail'].
 * Per language there shold be two images for the selected and the
 * unselected state, i.e. 'en.gif' and 'en_sel.gif'
 */
if (!function_exists('anchor'))
{
	$this->load->helper('url');
}
?>
<table>
	<tr>
<?php
	// prepare session flashdata to find back to the current page
	$this->session->set_flashdata('uri', $this->uri->uri_string());
    // get array of available languages
    $_lang_avail = $this->config->item('lang_avail');
    // get user's current language code
    $_sel_lang = $RTR->uri->segment(1);
	
    // load the respective language file
    $this->lang->load('lang');
    $_Output = Array();
    $v = 0;
    foreach ($_lang_avail as $_lang => $_language) 
    {
        // get language name in currently selected language
        $_lng = $this->lang->line('lng_'.$_lang);
        if ($_sel_lang == $_lang) 
        {
            // show selected language button
            $fstr = $_lang;
        } 
        else
        {
            // show unselected language button
            $fstr = $_lang;
            $fstr = anchor(site_url("/languages/change/$_lang"), $fstr, 
                           array('title'=>'',
                                 'tabindex'=>($v+20)));
            $v++;
      }
      //$this->log("language: $_lang: $fstr", LOG_DEBUG);
      $_Output[] = $fstr;
    }
    echo "    <td>" . implode("</td>\n    <td>", $_Output) . "</td>\n";
?>
	</tr>
</table>
