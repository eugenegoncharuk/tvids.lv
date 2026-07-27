<?php
class Users extends Controller {

	var $_num_rows_per_page = 30;
	
    function Users() {
        parent::Controller();
		if ($this->session->userdata('user_id')=="" ){
			redirect('/adm');	
		}	
		$info_users =& $this->load->model('info_users');
    }
	
	function index() {
		$this->show_users();
	}
		
	function show_users($from=0) {

		$t = $this->info_users->get_all_users($from, $this->_num_rows_per_page);

		$data['users'] = $t['users'];
		$data['page'] = 'admin/users';
		$data['nav_button'] = 'users';
		
		$this->load->library('pagination');
		$config['base_url'] = $this->config->site_url().'action/users/show_users';
		$config['total_rows'] = $t['count'];
		$config['per_page'] = $this->_num_rows_per_page;
		$config['uri_segment']= 5;
		$config['num_links']= 10;
		$this->pagination->initialize($config);
	
		$data['pagination'] = $this->pagination->create_links();
		$this->parser->parse('admin/container',$data);
	}
	
	function show($id=0) {

		$q = $this->info_users->get_user($id);
		
		if ($q->num_rows() > 0) {
			$data = $q->row_array();
		} 

		$data['page'] = 'admin/show_user';
		$data['nav_button'] = 'users';
		
		$this->parser->parse('admin/container',$data);
	}
		
	// deleting user and their request
	function delete_user($id="") {
	
		$q = $this->info_users->find_by_id($id);
		if ($q && $q->exists()){
			if ($q->user_id > 0){
				$this->db->where('id', $q->user_id);
				$this->db->delete('users');
			}
			$this->db->where('id', $id);
			$this->db->delete('info_users');
		}

		$this->db->where('user_info_id', $id);
		$this->db->delete('requests');

		$this->show_users();
	}
}
?>