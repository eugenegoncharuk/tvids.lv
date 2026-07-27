<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Info_items extends ActiveRecord {

    function __construct()
    {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'info_items';
        $this->_columns = $this->discover_table_columns();
    }
	
	// getting item price
	function get_item($id=0) {
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->where("pid", $id);
		$q = $this->db->get();
		if ($q!=null && $q->num_rows()>0) {
			return $q->row();
		}
		return FALSE;
	}
	
	// saving items price
	function save_item($id=0, $price=0, $short_lv="", $short_ru="", $visible=1) {
		$this->db->select('price');
		$this->db->from($this->_table);
		$this->db->where("pid", $id);
		$q = $this->db->get();
		if ($q!=null && $q->num_rows()>0) {
			$data['price'] = $price;
			$data['visible'] = $visible;
			$data['short_lv'] = $short_lv;
			$data['short_ru'] = $short_ru;
			$this->db->where('pid', $id);
			$this->db->update($this->_table, $data);
		} else {
			// here we will add new rows
			$data['pid'] = $id;
			$data['price'] = $price;
			$data['visible'] = $visible;
			$data['short_lv'] = $short_lv;
			$data['short_ru'] = $short_ru;
			$this->db->insert($this->_table,$data);	
		}
	}
}
?>