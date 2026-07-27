<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Requests_items extends ActiveRecord {

    function __construct()
    {
        parent::ActiveRecord();
        $this->_class_name = strtolower(get_class($this));
        $this->_table = 'requests_items';
        $this->_columns = $this->discover_table_columns();
    }
	
	// LEFT JOIN, not INNER: content_id may point at a tree node that has
	// since been deleted, or (for bed-linen orders) at a
	// bed_linen_image_leafs id that was never a tree node at all - an INNER
	// JOIN silently dropped those rows entirely, which is why order totals
	// and item lists were coming up empty/wrong. Raw query because the
	// ActiveRecord join() helper only reliably parses a single "a = b"
	// condition, not this compound ON clause.
	function get_items_by_pid($pid, $cur_lang='ru'){
		$q = $this->db->query('
			SELECT ri.*, c.text as text
			FROM v2_requests_items ri
			LEFT JOIN v2_content c ON (c.pid = ri.content_id AND c.type = 1 AND c.lang = \''.$this->db->escape_str($cur_lang).'\')
			WHERE ri.pid = '.(int)$pid.'
		');
		return $q->result_array();
	}
}
?>