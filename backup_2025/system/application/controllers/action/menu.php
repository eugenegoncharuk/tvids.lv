<?php
class Menu extends Controller {

	var $_lang = array();
	var $_type = 1;
	
    function Menu() {
        parent::Controller();
		if ($this->session->userdata('user_id')=="" ){
			redirect('/adm');	
		}
		
		global $RTR;
		$langs_arr = $RTR->config->item('lang_uri_abbr');
		foreach (array_keys($langs_arr) as $key){
			$new_arr = array(); 
			$new_arr['short'] = $key;
			$new_arr['full'] = $langs_arr[$key];
			$new_arr['lang_text'] = "";
			$this->_lang[] = $new_arr;
		}
		$tree =& $this->load->model('tree');
		$content =& $this->load->model('content');
    }
		
	function index() {
		$this->show_addup(0);
	}
	
	function show_addup($id) {
		
		$data['id'] = $id;
		$data['action'] = 'addup';
		$data['page'] = 'admin/webmenu';
		$data['nav_button'] = 'menu';
		$data['translations'] = $this->_lang;
		$this->parser->parse('admin/container',$data);
	}
	
	function show_addbottom($id) {
		
		$data['id'] = $id;
		$data['action'] = 'addbottom';
		$data['page'] = 'admin/webmenu';
		$data['nav_button'] = 'menu';
		$data['translations'] = $this->_lang;
		$this->parser->parse('admin/container',$data);
	}	
		
	function show_edit($id) {
		
		$q = $this->tree->find_by_id($id);
		$data2['type'] =  $this->_type;
		$data['name'] = $q->name;
		foreach (array_keys($this->_lang) as $key){
			// getting value by reference =&
			$arr =& $this->_lang[$key];
			$q = $this->db->get_where('content', array('pid' => $id, 
												'lang' => $arr['short'], 
												'type' => $this->_type));
			if ($q->num_rows() > 0){
				$arr['lang_text'] = $q->row()->text;
			}
		}
		$data['translations'] = $this->_lang;
		$data['id'] = $id;
		$data['action'] = 'edit';
		$data['page'] = 'admin/webmenu';
		$data['nav_button'] = 'menu';

		$this->parser->parse('admin/container',$data);
	}	
	
	function edit($id) {
		$data['name']=$_POST['name'];
		$data['id']=$id;
		$this->tree->update_node($data);
		
		foreach (array_values($this->_lang) as $val){
			$short = $val['short'];
			$data2 = array();
			$data2['text'] = $_POST['lang_text_'.$short];
			$this->db->update('content', $data2, array('pid' => $id, 
													   'lang' => $short,
													   'type' => $this->_type));
		}
		redirect('action/treemenu');
	}
	
	function addup($id) {
		$data['name']=$_POST['name'];
		$data['pid']=$id;
		$menu_id = $this->tree->add_node_up($data);
		foreach (array_values($this->_lang) as $val){
			$short = $val['short'];
			$data2 = array();
			$data2['pid'] =  $menu_id;
			$data2['text'] = $_POST['lang_text_'.$short];
			$data2['type'] =  $this->_type;
			$data2['lang'] =  $short;
			$this->db->insert('content', $data2); 
		}
		redirect('action/treemenu');
	}
	
	function addbottom($id) {
		$data['name']=$_POST['name'];
		$data['pid']=$id;
		$menu_id = $this->tree->add_node_down($data);
		foreach (array_values($this->_lang) as $val){
			$short = $val['short'];
			$data2 = array();
			$data2['pid'] =  $menu_id;
			$data2['type'] =  $this->_type;
			$data2['text'] = $_POST['lang_text_'.$short];
			$data2['lang'] =  $short;
			$this->db->insert('content', $data2); 
		}
		redirect('action/treemenu');
	}	

}

?>