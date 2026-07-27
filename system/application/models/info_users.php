<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Info_users extends ActiveRecord {

    function __construct()
    {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'info_users';
        $this->_columns = $this->discover_table_columns();
    }
	
	function get_all_users($from=0, $num_rows=30) {

		$res = array();
		$this->db->select();
		$this->db->from($this->_table);
		$res['count'] = $this->db->count_all_results();
		
		$this->db->select();
		$this->db->from($this->_table);
		$this->db->order_by("id", "desc");
		$this->db->limit($num_rows, $from);
		$q = $this->db->get();
		
		$reqs = array();
		$ind = 0;
		while ($ind < $q->num_rows()){
			$r_arr = $q->row_array($ind);
			$reqs[] = $r_arr;
			$ind++;
		}
		$res['users'] = $reqs;
		
		return $res;
	}
	
	function get_user($id=0) {
		$this->db->select('*, users.id AS users_id, info_users.id AS infousers_id');
		$this->db->from($this->_table);
		$this->db->join('users', 'users.id = '.$this->_table.'.user_id', 'left');
		$this->db->having('infousers_id = \''.$id.'\''); 
		$q = $this->db->get();

		return $q;
	}
}
?>