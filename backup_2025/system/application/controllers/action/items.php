<?php
class Items extends Controller {

	var $_num_rows_per_page = 30;
	
    function Items() {
        parent::Controller();
		if ($this->session->userdata('user_id')=="" ){
			redirect('/adm');	
		}	

		$info_items =& $this->load->model('info_items');
		$tree =& $this->load->model('tree');
    }
	
	function index() {
	}
		
	function show_info($id="") {
	
		if ($id=="") {
			redirect('/action/treemenu');
		}

		$item = $this->info_items->get_item($id);
		if (!$item) {
			$query_parent = $this->tree->find_by_id($id);
			$parent_item = $this->info_items->get_item($query_parent->pid);
			if (!$parent_item) {
				$price="";
				$visible="1";
				$short_lv = "";
				$short_ru = "";
			} else {
				$price = $parent_item->price;
				$visible="1";
				$short_lv = $this->_get_infotext($query_parent->name);
				$short_ru = $this->_get_infotext($query_parent->name);
			}
		} else {
			$price = $item->price;
			$visible = $item->visible;
			$short_lv = $item->short_lv;
			$short_ru = $item->short_ru;
		}
		
		$data['page'] = 'admin/item_info';
		$data['nav_button'] = 'menu';
		$data['price'] = $price;
		$data['visible'] = $visible;
		$data['short_lv'] = $short_lv;
		$data['short_ru'] = $short_ru;
		$data['id'] = $id;
		$this->parser->parse('admin/container', $data);
	}
	
	function _get_infotext($foldername = '') {
		return 
		'<table style="width: 160px; height: 140px;" border="0">
			<tbody>
			<tr>
			<td>
			<p style="text-align: center;"><img title="" src="https://www.tvids.lv/system/www/up/images/Komplekty/'.$foldername.'/1_120.jpg" alt="" width="120" height="80" /></p>
			</td>
			</tr>
			<tr>
			<td style="text-align: center;"><strong>'.$foldername.'<br /></strong></td>
			</tr>
			</tbody>
		</table>';
	}

	function edit_info($id="") {

		if ($id=="") {
			redirect('/action/treemenu');
		}
		$entered_price = @$_POST['price'];
		$this->info_items->save_item($id, $entered_price, 
						@$_POST['short_lv'], @$_POST['short_ru'], @$_POST['visible']);
		redirect('/action/treemenu');
	}
		
}
?>