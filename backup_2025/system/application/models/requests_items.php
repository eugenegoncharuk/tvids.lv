<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Requests_items extends ActiveRecord {

    function __construct()
    {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'requests_items';
        $this->_columns = $this->discover_table_columns();
    }
	
	function get_items_by_pid($pid, $cur_lang='ru'){
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->join('content', $this->_table.'.content_id = content.pid');
		$this->db->where($this->_table.'.pid', $pid); 
		$this->db->where('content.type', 1);
		$this->db->where('content.lang', $cur_lang);

		$q = $this->db->get();
		return $q->result_array();
	}
}
?>