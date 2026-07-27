<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hier extends ActiveRecord {

    function __construct()
    {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'nav_hier';
        $this->_columns = $this->discover_table_columns();
    }
	
	function is_node_under($id, $pid) {
		
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->where('pid', $pid); 
		$this->db->where('node_id', $id); 
		$q = $this->db->get();
		if ( $q && $q->num_rows() > 0 ) {
			return TRUE;
		}
		return FALSE;
	}
	
	function is_leaf($id) {
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->where('pid', $id); 
		$q = $this->db->get();
		if ( $q && $q->num_rows() > 0 ) {
			return FALSE;
		}
		return TRUE;
	}
}
?>