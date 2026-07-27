<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Content extends ActiveRecord {

    function __construct()
    {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'content';
        $this->_columns = $this->discover_table_columns();
    }
	
	function get_content_leafs_limit($id, $from=0, $num_rows=3, $lang='ru') {
	
		// selecting all the available childs for this pid
		$q = $this->db->query("SELECT *, COUNT(hier.hier_pid) AS childs_count,
		    v2_info_items.short_$lang as text,
			v2_info_items.price as price,
			v2_info_items.visible as visible,
			v2_info_items.pid AS content_id FROM v2_nav_tree 
			LEFT OUTER JOIN v2_info_items ON (v2_info_items.pid = v2_nav_tree.id AND v2_info_items.price != 0.0) 
			LEFT OUTER JOIN (SELECT v2_nav_hier.pid as hier_pid FROM v2_nav_hier) hier 
			ON (hier.hier_pid=v2_nav_tree.id) WHERE v2_nav_tree.id IN (
					SELECT v2_nav_hier.node_id  as node_id FROM `v2_nav_hier` 
							WHERE v2_nav_hier.pid='$id') 
			GROUP BY v2_nav_tree.id HAVING (childs_count=0 AND price!=0 AND visible=1) ");	
	
		$res = array();
		$res['num_rows'] = $q->num_rows();
		// selecting all the available childs for this pid
		$q = $this->db->query("SELECT *, COUNT(hier.hier_pid) AS childs_count,
			v2_info_items.short_$lang as text,
			v2_info_items.price as price,
			v2_info_items.visible as visible,
			v2_info_items.pid AS content_id FROM v2_nav_tree 
			LEFT OUTER JOIN v2_info_items ON (v2_info_items.pid = v2_nav_tree.id)
			LEFT OUTER JOIN (SELECT v2_nav_hier.pid as hier_pid FROM v2_nav_hier) hier 
			ON (hier.hier_pid=v2_nav_tree.id) WHERE
			v2_nav_tree.id IN (
					SELECT v2_nav_hier.node_id  as node_id FROM `v2_nav_hier` 
							WHERE v2_nav_hier.pid='$id') 
			GROUP BY v2_nav_tree.id HAVING (childs_count=0 AND price!=0 AND visible=1) ORDER BY hid LIMIT $from, $num_rows ");
		$res['rows'] = $q->result_array();
		
		return $res;
	}
			
	function get_content($pid,$lang,$type=1){
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->where('type', $type); 
		$this->db->where('pid', $pid); 
		$this->db->where('lang', $lang); 
		$q = $this->db->get();
		return $q;
	}
	
	function search_content($search, $lang, $type=2){
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->like('text', '%'.mysql_real_escape_string($search).'%');
		$this->db->where('lang', $lang);
		$this->db->where('type', $type); 
		$q = $this->db->get();
		return $q;
	}	
}
?>