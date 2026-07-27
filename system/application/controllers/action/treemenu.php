<?php
class Treemenu extends Controller {

	// for texts type = 2
	var $_type = 2;
	
    function Treemenu() {
        parent::Controller();
		if ($this->session->userdata('user_id')=="" ){
			redirect('/adm');	
		}	
		$tree =& $this->load->model('tree');
		$content =& $this->load->model('content');
    }
	
	function _print_tree($arr,$arr_pid=array()){
		
		$node = array_shift($arr);
		//echo "<PRE>";
		//print_r($arr_pid); 
		if ($node==null){
			return "";
		}
		$add_str = "";
		if (sizeof($arr_pid)>0){
			if (in_array($node['pid'], $arr_pid)){
				while ($cur_node_id=array_pop($arr_pid)) {
					if ($cur_node_id==$node['pid'] || $cur_node_id==NULL){
						$add_str .= "</ul></li>";
						break;
					} else {
						$add_str .= "</ul></li>";
					}
				}
			}  
		}
		
		array_push($arr_pid, $node['pid']);
		return $add_str.'<li><div>
						 <a class="tdi1"
						 href="'.$this->config->site_url().'action/text/show_text/'.$node['id'].'">'
						 .$node['name'].'</a>
						 [<a href="'.$this->config->site_url().'action/items/show_info/'.$node['id'].'">
						 Info</a>]
						 [<a href="'.$this->config->site_url().'action/menu/show_addup/'.$node['id'].'">
						 Add up</a>]
						 [<a href="'.$this->config->site_url().'action/menu/show_addbottom/'.$node['id'].'">
						 Add down</a>]
						 [<a href="'.$this->config->site_url().'action/menu/show_edit/'.$node['id'].'">
						 Edit</a>]
						 [<a href="'.$this->config->site_url().'action/treemenu/deletenode/'.$node['id'].'">
						 Delete</a>]
						 <a href="'.$this->config->site_url().'action/treemenu/nodeup/'.$node['id'].'">
						 <img src="tree/moveup.gif"></a>
							 <a href="'.$this->config->site_url().'action/treemenu/nodedown/'.$node['id'].'">
						 <img src="tree/movedown.gif"></a>
						 </div><ul>'.
				$this->_print_tree($arr,$arr_pid);
					
	}
		
	function index() {
		$this->_show_tree();
	}
			
	function _get_tree() {
		
		$out = array();
		
		$t = $this->tree->get_all_tree();
		$ind = 0;
		while ($ind < $t->num_rows()){
			$r_arr = $t->row_array($ind);
			if ($ind < $t->num_rows() &&
				$t->row($ind+1)->pid == $t->row($ind)->id){
				$r_arr['branch']=1;				
			}
			$out[] =$r_arr;
			$ind++;
		}
		return $out;
	}
	
	function deletenode($id){
		$data=array();
		if (!$this->tree->delete_node($id)){
			$data['message'] = "Node contains other nodes! Please delete them first!";
		}
		$data2 = array();
		$data2['pid'] =  $id;
		$this->db->delete('content', $data2); 
		
		$this->_show_tree($data);
	}
	
	function nodeup($id) {
		$this->tree->node_up($id);
		$this->_show_tree();
	}
	
	function nodedown($id) {
		$this->tree->node_down($id);
		$this->_show_tree();
	}
	
	function _show_tree($data=array()){
		$this->tree->check_root_create();
		
		/*<li><span id="item">Folder 1</span>
			<ul>
			<li>Sub Item 1.1</li>
			<li>Sub Item 1.2</li>
			</ul>
		</li>*/
		$out = $this->_get_tree();
		
		$arr_pid = array();
		$data['text'] = $this->_print_tree($out,$arr_pid);
		$data['page'] = 'admin/tree';
		$data['nav_button'] = 'menu';
		$this->parser->parse('admin/container',$data);
	}
	
}
?>