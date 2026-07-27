<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// id 	name 	pid 	hid 	level
class Tree extends ActiveRecord {

    function __construct()
    {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'nav_tree';
        $this->_columns = $this->discover_table_columns();
    }
	
	function create_root(){
		$data = array(
               'name' => 'Menu',
               'pid' => 0,
               'hid' => 0,
			   'level' => 0
            );
		$this->db->insert($this->_table, $data); 
	}
	
	function check_root_create(){
		
		if (!$this->tree->check_root()){
			$this->tree->create_root();	
		}
	}
	
	function check_root(){
		$data = array(
               'pid' => 0,
               'hid' => 0,
			   'level' => 0
        );
		$res = true;
        $query = $this->db->get_where($this->_table, $data, 1, 0);
        if ($query->num_rows != 1){
        	$res = false;	
        }
		return $res;
	}
		
	function add_node_up($data){
		
		$pid = $data['pid'];
		// let's find out hid (node), that is nearest to this
		$query = $this->db->query('SELECT MIN(hid) as find_hid, level FROM '.
								  $this->db->dbprefix.$this->_table.
								  ' WHERE pid='.$pid.' GROUP BY pid ');
								  
		$find_hid=0;
		$level=0;
		if ($query->num_rows() > 0){
			$find_hid = $query->row()->find_hid;
			$level = $query->row()->level;
		}else{
			$q2 = $this->tree->find_by_id($pid);
			$find_hid = $q2->hid + 1;
			$level = $q2->level + 1;
		}
		
		// now we need to increment all the greater hids
		$query = $this->db->query('UPDATE '.$this->db->dbprefix.$this->_table.' 
								  SET hid = hid + 1 
								  WHERE hid>='.$find_hid);
		
		$data['hid'] = $find_hid;
		$data['level'] = $level;
		$query = $this->db->insert($this->_table,$data);
		$insert_id = mysql_insert_id();
		
		// now we need to update nav_hier
		// let's find out hid (node), that is nearest to this
		$query = $this->db->query('SELECT * FROM '.
								 $this->db->dbprefix.'nav_hier'.
								  ' WHERE node_id='.$pid);
			
		foreach ($query->result() as $row){	
			$data = array(
               'node_id' => $insert_id,
               'pid' => $row->pid
            );
			$this->db->insert('nav_hier',$data);	  
		}
		
		$data = array(
           'node_id' => $insert_id,
           'pid' => $pid
        );
		$this->db->insert('nav_hier',$data);
		return $insert_id;
	}
		
	function add_node_down($data){
		
		$pid = $data['pid'];
		// let's find out hid (node), that is nearest to this
		$query = $this->db->query('SELECT MAX('.$this->db->dbprefix.$this->_table.'.hid) AS find_hid, MIN(level) as level, 
									'.$this->db->dbprefix.'nav_hier.pid AS hier_pid FROM '.
									$this->db->dbprefix.$this->_table.
								   ' LEFT JOIN '. $this->db->dbprefix.'nav_hier ON 
								   ('.$this->db->dbprefix.$this->_table.'.id = '.$this->db->dbprefix.'nav_hier.node_id)
								   WHERE '.$this->db->dbprefix.'nav_hier.pid='.$pid.' GROUP BY hier_pid ');
		
		$find_hid=0;
		$level=0;
		if ($query->num_rows() > 0){
			$find_hid = $query->row()->find_hid + 1;
			$level = $query->row()->level;
		}else{
			$q2 = $this->tree->find_by_id($pid);
			$find_hid = $q2->hid + 1;
			$level = $q2->level + 1;
		}
		
		// now we need to increment all the greater hids
		$query = $this->db->query('UPDATE '.$this->db->dbprefix.$this->_table.' 
								  SET hid = hid + 1 
								  WHERE hid>='.$find_hid);
		
		$data['hid'] = $find_hid;
		$data['level'] = $level;
		$query = $this->db->insert($this->_table,$data);
		$insert_id = mysql_insert_id();
		
		// now we need to update nav_hier
		// let's find out hid (node), that is nearest to this
		$query = $this->db->query('SELECT * FROM '.
								 $this->db->dbprefix.'nav_hier'.
								  ' WHERE node_id='.$pid);
			
		foreach ($query->result() as $row){	
			$data = array(
               'node_id' => $insert_id,
               'pid' => $row->pid
            );
			$this->db->insert('nav_hier',$data);	  
		}
		
		$data = array(
           'node_id' => $insert_id,
           'pid' => $pid
        );
		$this->db->insert('nav_hier',$data);
		return $insert_id;
	}
	
	function update_node($data){
		
		$id = $data['id'];
		unset($data['id']);
		
		// let's find out node
		$q = $this->tree->find_by_id($id);			
								  
		if (!$q || !$q->exists()){
			return false;
		}	
		$this->db->where('id', $id);
		$this->db->update($this->_table, $data);
		return true; 
	}
	
	// making node greater in the tree
	function node_up($id){
		
		$q = $this->tree->find_by_id($id);
		if (!$q || !$q->exists()){
			return false;
		}
		
		$hid = $q->hid;
		$temp_hid = $hid;
		
		// let's find out node
		$this->db->select_max('hid');
		$this->db->select('id');
		$this->db->where('hid <', $hid); 
		$this->db->where('pid =', $q->pid);
		$this->db->group_by('id');
		$this->db->order_by('hid','desc');
		$q2 = $this->db->get($this->_table);
					
		if ($q2->num_rows() == 0){
			return false;
		}
		
		$cur_hid = $q2->row()->hid;
		$cur_id = $q2->row()->id;
		// we need to find max values in these 2 branches
		$max_hid = $this->_get_max_id_branch($id,$hid);
		$cur_max_hid = $this->_get_max_id_branch($cur_id,$cur_hid);
		$max_differ = abs($cur_max_hid - $max_hid); // for less node
		$min_differ = abs($cur_hid - $hid); // for greater node
		
		$this->_update_tree_branch_down($cur_hid,$cur_max_hid,999999);
		$this->_update_tree_branch_up($hid,$max_hid,$min_differ);
		$this->_update_tree_branch_up($cur_hid+999999,$cur_max_hid+999999,999999-$max_differ);
			
		return true; 
	}

	// making node lower in the tree
	function node_down($id){
		
		$q = $this->tree->find_by_id($id);
		if (!$q || !$q->exists()){
			return false;
		}
		
		$hid = $q->hid;
		$temp_hid = $hid;
		
		// let's find out node
		$this->db->select('id');
		$this->db->select_min('hid');
		$this->db->where('hid >', $q->hid); 
		$this->db->where('pid =', $q->pid);
		$this->db->group_by('id');
		$this->db->order_by('hid','asc');
		$q2 = $this->db->get($this->_table);
					
		if ($q2->num_rows() == 0){
			return false;
		}
		$cur_hid = $q2->row()->hid;
		$cur_id = $q2->row()->id;	 
		// we need to find max values in these 2 branches
		$max_hid = $this->_get_max_id_branch($id,$hid);
		$cur_max_hid = $this->_get_max_id_branch($cur_id,$cur_hid);
		$max_differ = abs($cur_max_hid - $max_hid); // for less node
		$min_differ = abs($cur_hid - $hid); // for greater node
		
		$this->_update_tree_branch_down($hid,$max_hid,999999);
		$this->_update_tree_branch_up($cur_hid,$cur_max_hid,$min_differ);
		$this->_update_tree_branch_up($hid+999999,$max_hid+999999,999999-$max_differ);
		
		return true; 
	}
	
	//delete the node
	function delete_node($id){
		
		$q = $this->tree->find_by_pid($id);
		if ($q!=null && $q->exists()){
			return false;
		}
		
		$this->db->where('id', $id);
		$this->db->delete($this->_table);
		 
		$this->db->where('node_id', $id);
		$this->db->delete('nav_hier');
		return true;
	}

	function get_all_tree(){
		$this->db->select('*');
		$this->db->order_by('hid','asc');
		$q = $this->db->get($this->_table);
		return $q;
	}

	function get_tree_lang($lang){
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->join('content', 'nav_tree.id = content.pid');
		$this->db->where('type', '1'); 
		$this->db->where('lang', $lang); 
		$this->db->order_by('hid','asc');
		$q = $this->db->get();
		return $q;
	}
	
	function get_tree_lang_by_level($lang, $level=1){
		$this->db->select('nav_tree.id as id, content.text as text, nav_tree.pid as pid');
		$this->db->from($this->_table);
		$this->db->join('content', 'nav_tree.id = content.pid');
		$this->db->where('type', '1'); 
		$this->db->where('level', $level); 
		$this->db->where('lang', $lang); 
		$this->db->order_by('hid','asc');
		$q = $this->db->get();
		return $q;
	}
	
	
	function get_tree_lang_by_id($lang, $id=0){
		$this->db->select('nav_tree.id as id, content.text as text, nav_tree.pid as pid, nav_tree.level as level');
		$this->db->from($this->_table);
		$this->db->join('content', 'nav_tree.id = content.pid');
		$this->db->where('type', '1'); 
		$this->db->where('nav_tree.id', $id); 
		$this->db->where('lang', $lang); 
		$this->db->order_by('hid','asc');
		$q = $this->db->get();
		return $q;
	}
	
	function get_tree_lang_by_pid_and_level($lang, $pid=0, $level=1){
		$this->db->select('nav_tree.id as id, content.text as text, nav_tree.pid as pid');
		$this->db->from($this->_table);
		$this->db->join('content', 'nav_tree.id = content.pid');
		$this->db->where('type', '1'); 
		$this->db->where('level', $level); 
		$this->db->where('nav_tree.pid', $pid); 
		$this->db->where('lang', $lang); 
		$this->db->order_by('hid','asc');
		$q = $this->db->get();
		return $q;
	}
	
	function get_branch_lang($id,$lang){
		
		// let's find out hid (node), that is nearest to this
		$q = $this->db->query('SELECT t.id, t.name, t.pid, t.hid, t.level, c.text as text
								FROM `v2_nav_hier` as h, `v2_nav_tree` as t
									, `v2_content` as c
								WHERE (h.node_id=t.id AND h.pid='.$id.' AND 
										t.id = c.pid AND c.type=1 
										AND c.lang=\''.$lang.'\')  ORDER BY t.hid ASC');

		return $q;
	}

	// Same as get_branch_lang, but never drops a node just because it has
	// no content/type=1 translation row (falls back to the raw tree name) -
	// used where every descendant must show regardless of content gaps,
	// e.g. the full "Категория комплектов" subtree in the sidebar menu.
	function get_branch_lang_all($id,$lang){

		$q = $this->db->query('SELECT t.id, t.name, t.pid, t.hid, t.level,
								COALESCE(c.text, t.name) as text
								FROM `v2_nav_hier` as h
								INNER JOIN `v2_nav_tree` as t ON h.node_id = t.id
								LEFT JOIN `v2_content` as c ON (c.pid = t.id AND c.type = 1 AND c.lang = \''.$lang.'\')
								WHERE h.pid = '.$id.'
								ORDER BY t.hid ASC');

		return $q;
	}

	function exists_id($id){
		$q = $this->tree->find_by_id($id);
		if ($q!=null && $q->exists()){
			return true;
		}
		return false;
	}

	function get_branch($id){
		
		// let's find out hid (node), that is nearest to this
		$q = $this->db->query('SELECT t.id, t.name, t.pid, t.hid, t.level
								FROM `v2_nav_hier` as h, `v2_nav_tree` as t 
								WHERE (h.node_id=t.id AND h.pid='.$id.') ORDER BY t.hid ASC');
		return $q;
	}
	
	function _update_tree_branch_up($min,$max,$dif){
		$q = $this->db->query('UPDATE `v2_nav_tree` as t SET t.hid=t.hid-'.$dif.'  
								WHERE (t.hid>='.$min.' AND t.hid<='.$max.')');
	}

	function _update_tree_branch_down($min,$max,$dif){
		$q = $this->db->query('UPDATE `v2_nav_tree` as t SET t.hid=t.hid+'.$dif.'  
								WHERE (t.hid>='.$min.' AND t.hid<='.$max.')');
	}
	
	function _get_max_id_branch($id,$hid){
		
		// let's find out hid (node), that is nearest to this
		$q = $this->db->query('SELECT MAX(t.hid) as max_hid 
								FROM `v2_nav_hier` as h, `v2_nav_tree` as t 
								WHERE (h.node_id=t.id AND h.pid='.$id.') ORDER BY t.hid ASC');
		if ($q->row()->max_hid==null)
			return $hid;								
		return $q->row()->max_hid;
	}
}

?>