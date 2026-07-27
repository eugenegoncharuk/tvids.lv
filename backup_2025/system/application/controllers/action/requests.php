<?php
class Requests extends Controller {

	// for texts type = 1
	var $_status_unapproved = 0;
	var $_status_approved = 1;
	var $_status_declined = 2;
	
	var $_num_rows_per_page = 30;
	
    function Requests() {
        parent::Controller();
		if ($this->session->userdata('user_id')=="" ){
			redirect('/adm');	
		}	
		$request =& $this->load->model('request');
		$requests_items =& $this->load->model('requests_items');
		$info_users =& $this->load->model('info_users');
    }
	
	function index() {
		// need to use    time() and    date('d-m-Y H:i:s', $value)
		$this->show_unapproved();
	}
	
	// approve request
	function approve_unaproved($id) {
		if ($id!=''){
			$this->request->approve($id);
		}
		redirect('action/requests');		
	}
	
	// decline request
	function decline_unaproved($id) {
		if ($id!=''){
			$this->request->decline($id);
		}
		redirect('action/requests');		
	}
		
	// approve request
	function approve_declined($id) {
		if ($id!=''){
			$this->request->approve($id);
		}
		redirect('action/requests/show_declined');		
	}
	
	// decline request
	function decline_approved($id) {
		if ($id!=''){
			$this->request->decline($id);
		}
		redirect('action/requests/show_approved');		
	}
	
	function show_approved($from=0) {

		// need to use    time() and    date('d-m-Y H:i:s', $value)
		if (@$_POST['search_str']!=''){
			$t = $this->request->get_reqs_by_type($this->_status_approved, $from, $this->_num_rows_per_page, $_POST['search_str']);
			$data['search_str'] = $_POST['search_str'];
		} else {
			$t = $this->request->get_reqs_by_type($this->_status_approved, $from, $this->_num_rows_per_page);
			
			$this->load->library('pagination');
			$config['base_url'] = $this->config->site_url().'action/requests/show_approved';
			$config['total_rows'] = $t['count'];
			$config['per_page'] = $this->_num_rows_per_page;
			$config['uri_segment']= 5;
			$config['num_links']= 10;
			$this->pagination->initialize($config);
			$data['pagination'] = $this->pagination->create_links();
		}

		$data['reqs'] = $t['reqs'];
		$data['page'] = 'admin/requests';
		$data['nav_button'] = 'requests';
		$data['action'] =  'show_approved';
		$data['approve']= '';
		$data['decline']= 'decline_approved';
		$this->parser->parse('admin/container',$data);
	}
	
	function show_unapproved($from=0) {

		// need to use    time() and    date('d-m-Y H:i:s', $value)
		if (@$_POST['search_str']!=''){
			$t = $this->request->get_reqs_by_type($this->_status_unapproved, $from, $this->_num_rows_per_page, $_POST['search_str']);
			$data['search_str'] = $_POST['search_str'];
		} else {
			$t = $this->request->get_reqs_by_type($this->_status_unapproved, $from, $this->_num_rows_per_page);
			
			$this->load->library('pagination');
			$config['base_url'] = $this->config->site_url().'action/requests/show_unapproved';
			$config['total_rows'] = $t['count'];
			$config['per_page'] = $this->_num_rows_per_page;
			$config['uri_segment']= 5;
			$config['num_links']= 10;
			$this->pagination->initialize($config);
		
			$data['pagination'] = $this->pagination->create_links();
		}

		$data['reqs'] = $t['reqs'];
		$data['page'] = 'admin/requests';
		$data['nav_button'] = 'requests';
		$data['approve']= 'approve_unaproved';
		$data['decline']= 'decline_unaproved';
		$data['action'] =  'show_unapproved';
		$this->parser->parse('admin/container',$data);
	}
	
	function show_declined($from=0) {

		// need to use    time() and    date('d-m-Y H:i:s', $value)
		if (@$_POST['search_str']!=''){
			$t = $this->request->get_reqs_by_type($this->_status_declined, $from, $this->_num_rows_per_page, $_POST['search_str']);
			$data['search_str'] = $_POST['search_str'];
		} else {
			$t = $this->request->get_reqs_by_type($this->_status_declined, $from, $this->_num_rows_per_page);
			
			$this->load->library('pagination');
			$config['base_url'] = $this->config->site_url().'action/requests/show_declined';
			$config['total_rows'] = $t['count'];
			$config['per_page'] = $this->_num_rows_per_page;
			$config['uri_segment']= 5;
			$config['num_links']= 10;
			$this->pagination->initialize($config);
			$data['pagination'] = $this->pagination->create_links();
		}

		$data['reqs'] = $t['reqs'];
		$data['page'] = 'admin/requests';
		$data['nav_button'] = 'requests';
		$data['action'] =  'show_declined';
		$data['approve']= 'approve_declined';
		$data['decline']= 'delete_request';
		$this->parser->parse('admin/container',$data);
	}
	
	function show_request($id="") {

		$data = $this->request->get_request($id);
		if (!$data){
			redirect('action/requests/show_approved');
			return;
		}
		$cur_lang = $this->lang->get_current_lang();
		$data['items'] = $this->requests_items->get_items_by_pid($id, $cur_lang);
		$data['page'] = 'admin/show_request';
		$data['nav_button'] = 'requests';
		
		$this->parser->parse('admin/container',$data);
	}
	
	// deleting request
	function delete_request($id="") {
	
		$q = $this->request->find_by_id($id);
		$q1 = $this->info_users->find_by_id($q->user_info_id);
		
		$this->db->where('req_id', $q->req_id);
		$this->db->delete('requests');
		$this->db->where('pid', $id);
		$this->db->delete('requests_items');
		
		if ($q1 && $q1->exists() && $q1->user_id==0){
			$this->db->where('id', $q->user_info_id);
			$this->db->delete('info_users');
		}
		$this->show_declined();
	}
}
?>