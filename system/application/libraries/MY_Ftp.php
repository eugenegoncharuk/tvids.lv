<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package	CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Ftp Class
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Ftp
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class MY_Ftp extends CI_Ftp {

	/**
	* FTP List files recursively
	*
	* @access	public
	* @return	array
	*/
	function list_all_files($path = '.', $deep=0, $f_path=""){
	
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}
		
		$buff = ftp_rawlist($this->conn_id, $path);
	    $res = $this->_parse_rawlist( $buff) ;
	    static $flist = array();
	    if(count($res)>0){
	        foreach($res as $result){
	            // verify if is dir , if not add to the  list of files
				$result['deep'] = $deep;
				$result['f_path'] = $f_path;
				
	            if($result['size']== 0){
	                // recursively call the function if this file is a folder
	                $this->list_all_files("".$path.'/'.$result['name'], $deep+1, "".$f_path.$result['name'].'/');
	            }
	            else{
					// this is a file, add to final list
	                $flist[] = $result;
	            }    
	        }
	    }
	    return $flist;
	}

	function _parse_rawlist( $array )
	{
		$structure = array();
	    foreach($array as $curraw)
	    {
	        $struc = array();
	        $current = preg_split("/[\s]+/",$curraw,9);

	        $struc['perms']  = $current[0];
	        $struc['number'] = $current[1];
	        $struc['owner']  = $current[2];
	        $struc['group']  = $current[3];
			$struc['size']   = $current[4] / 1048576;
	        $struc['month']  = $current[5];
	        $struc['day']    = $current[6];
	        $struc['time']   = $current[7];
	        $struc['name']   = $current[8];
	        $struc['raw']    = $curraw;
	        $structure[$struc['name']] = $struc;
	    }
	   return $structure;
	}
}
// END Ftp Class
?>