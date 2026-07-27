<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Request extends ActiveRecord {

	var $_access_days = 14;

    function __construct() {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'requests';
        $this->_columns = $this->discover_table_columns();
    }
	
	function approve($id="") {
		if ($id!="") {
			$data['approve_date'] = time();
			$data['status'] = "1" ;
			$this->db->where('id', $id);
			$this->db->update('requests', $data);
		}
	}
		
	function decline($id="") {
		if ($id!="") {
			$data['approve_date'] = time();
			$data['status'] = 2;
			$this->db->where('id', $id);
			$this->db->update('requests', $data);
		}
	}
	
	// getting requests by type
	function get_reqs_by_type($status=0, $from=0, $num_rows=30, $search_req="") {
		
		$res = array();
		$this->db->select();
		$this->db->from($this->_table);
		$this->db->where('status', $status);	
		$res['count'] = $this->db->count_all_results();
		
		if ($search_req!=""){
			$q = $this->db->query('SELECT *, reqs.id AS request_id, info.id as info_id FROM `jadmin_requests` as reqs, 
								`jadmin_info_users` as info
								WHERE (reqs.user_info_id=info.id AND reqs.status='.$status.' AND (
								UPPER(reqs.req_id) LIKE \'%'.strtoupper($search_req).'%\' OR 
								UPPER(info.official_name) LIKE \'%'.strtoupper($search_req).'%\' OR 
								UPPER(info.official_code) LIKE \'%'.strtoupper($search_req).'%\' OR
								UPPER(info.bank_iban) LIKE \'%'.strtoupper($search_req).'%\'
								)) ORDER BY reqs.req_date DESC');			
		} else {
			$q = $this->db->query('SELECT *, reqs.id AS request_id, info.id as info_id FROM `jadmin_requests` as reqs, `jadmin_info_users` as info
								WHERE (reqs.user_info_id=info.id AND reqs.status='.$status.') 
								ORDER BY reqs.req_date DESC LIMIT '.$from.', '.$num_rows.'');
		}
		
		$reqs = array();
		$ind = 0;
		while ($ind < $q->num_rows()){
			$r_arr = $q->row_array($ind);
			$r_arr['req_date'] = $this->_convert_to_datetime($r_arr['req_date']);
			$r_arr['approve_date'] = $this->_convert_to_datetime($r_arr['approve_date']);
			$reqs[] = $r_arr;
			$ind++;
		}
		$res['reqs'] = $reqs;
		
		return $res;
	}
	
	function _convert_to_datetime($value){
		if ($value<1236111164){
			return "";
		} 
		
		return date('d-m-Y H:i:s', $value);
	}
	
	function get_request($id=0) {

		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->where('id', $id); 
		$q = $this->db->get();
		
		if ($q->num_rows() > 0) {
			$data = $q->row_array();
			$data['req_date'] = $this->_convert_to_datetime($data['req_date']);
			$data['approve_date'] = $this->_convert_to_datetime($data['approve_date']);
			return $data;
		} 
		return false;
	}
	
	function add_request($data){
		$this->db->insert('requests',$data);
	}
}
?>