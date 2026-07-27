<?php
class Text extends Controller {

	var $_lang = array();
	// for texts type = 2
	var $_type = 2;
	
    function Text() {
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

		$content =& $this->load->model('content');
		$tree =& $this->load->model('tree');
    }
		
	function index() {
	}
	
	function show_text($id) {
		
		$query_parent = $this->tree->find_by_id($id);	
		$q = $this->db->get_where('content', array('pid' => $id, 
												   'type' => $this->_type));
		// there is no data - texts for menu by id
		if ($q->num_rows() == 0){

			foreach (array_keys($this->_lang) as $key){
				// getting value by reference =&
				$arr =& $this->_lang[$key];
				
				// let's find out one level up data to prefill in data
				$q = $this->db->get_where('content', array('pid' => $query_parent->pid, 
													'lang' => $arr['short'], 
													'type' => $this->_type));
				if ($q->num_rows() > 0){
					$arr['lang_text'] = $this->_get_full_text($q->row()->text, $query_parent->name);
				}
			}

			$data['id'] = $id;
			$data['action'] = 'add';
			$data['page'] = 'admin/content';
			$data['translations'] = $this->_lang;
			$data['nav_button'] = 'menu';

			$this->parser->parse('admin/container', $data);

		}else{

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
			
			$data['id'] = $id;
			$data['action'] = 'edit';
			$data['page'] = 'admin/content';
			$data['translations'] = $this->_lang;
			$data['nav_button'] = 'menu';
			$this->parser->parse('admin/container',$data);
		}
	}

	function _get_full_text($text = '', $foldername = '') {
		return 
		'<table style="width: 500px;" border="0" align="center">
			<tbody>
			<tr>
			<td style="text-align: center;">'.$text.'</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			</tr>
			<tr>
			<td>
			<table id="table_images" style="border: 0pt solid #a7def3; width: 500px;" border="0" align="center">
			<tbody>
			<tr>
			<td style="text-align: center;">
			<p><a title="'.$foldername.'" href="https://www.tvids.lv/system/www/up/images/Komplekty/'.$foldername.'/1_640.jpg">
			<img style="border: 3px solid #cbecf9;" src="https://www.tvids.lv/system/www/up/images/Komplekty/'.$foldername.'/1_120.jpg" alt="" width="120" height="80" /></a></p>
			</td>
			<td align="center">
			<p><a title="'.$foldername.'" href="https://www.tvids.lv/system/www/up/images/Komplekty/'.$foldername.'/2_640.jpg">
			<img style="border: 3px solid #cbecf9;" src="https://www.tvids.lv/system/www/up/images/Komplekty/'.$foldername.'/2_120.jpg" alt="" width="120" height="80" /></a></p>
			</td>
			<td align="center">
			<p><a title="'.$foldername.'" href="https://www.tvids.lv/system/www/up/images/Komplekty/'.$foldername.'/3_640.jpg">
			<img style="border: 3px solid #cbecf9;" src="https://www.tvids.lv/system/www/up/images/Komplekty/'.$foldername.'/3_120.jpg" alt="" width="120" height="80" /></a></p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
		</table>';
	}
				
	function edit($id) {
		foreach (array_values($this->_lang) as $val){
			$short = $val['short'];
			$data2 = array();
			$data2['text'] = $_POST['text_'.$short];
			$this->db->update('content', $data2, array('pid' => $id, 
													   'lang' => $short,
													   'type' => $this->_type));
		}
		redirect('action/treemenu');
	}
	
	function add($id) {
		foreach (array_values($this->_lang) as $val){
			$short = $val['short'];
			$data2 = array();
			$data2['pid'] =  $id;
			$data2['text'] = $_POST['text_'.$short];
			$data2['type'] =  $this->_type;
			$data2['lang'] =  $short;
			$this->db->insert('content', $data2); 
		}
		redirect('action/treemenu');
	}
}
?>