<?php
class Adminusers extends Controller {
	
    function Adminusers() {
        parent::Controller();
		if ($this->session->userdata('user_id')=="" ){
			redirect('/adm');	
		}
		$admin_users =& $this->load->model('admin_users');
		$this->load->helper(array('form'));
		$this->load->library('validation');
    }
		
	function index() {
		// let's show all users for admin panel
		$q = $this->db->get('admin_users');
		foreach ($q->result_array() as $row){
			$data['users'][] = $row; 
		}
		$data['page'] = 'admin/admin_users';
		$data['nav_button'] = 'adminusers';
		$this->parser->parse('admin/container',$data);
	}
	
	function show_add() {
		$data['action'] = 'add';
		$data['page'] = 'admin/edit_users';
		$data['nav_button'] = 'adminusers';
		$this->parser->parse('admin/container',$data);
	}
		
	function show_edit($id) {
		
		$q = $this->admin_users->find_by_id($id);
		if (!$q || !$q->exists()) {
			redirect('action/adminusers');
		}
		
		$data['name'] = $q->name;
		$data['username'] = $q->username;
		$data['id'] = $id;
		$data['action'] = 'edit';
		$data['page'] = 'admin/edit_users';
		$data['nav_button'] = 'adminusers';
		$this->parser->parse('admin/container',$data);
		
	}	
	
	function _valid_form(){
		$rules['name']	= "trim|required|min_length[5]";
		$rules['username']	= "trim|required|min_length[5]";
		$rules['password']	= "trim|required|min_length[6]|max_length[12]";
		$rules['password2']	= "trim|required|min_length[6]|max_length[12]";
		$this->validation->set_rules($rules);
		
		$where['name'] = @$_POST['name'];
		$where['username'] = @$_POST['username'];
		$where['password'] = @$_POST['password'];
		$where['password2'] = @$_POST['password2'];
		
		if ($this->validation->run() == FALSE || !$this->_passwords_valid()) {
			return FALSE;
		}
			
		return TRUE;	
	}
	
	function edit($id) {

		if ($this->_valid_form()){
			
			$q = $this->admin_users->find_by_username(@$_POST['username']);
			if ($q==null || !$q->exists() || $q->id==$id) {
				$data['name'] =  @$_POST['name'];
				$data['username'] = @$_POST['username'];
				
				$this->load->library('encrypt');
				$data['password'] = $this->encrypt->get_key($_POST['password']);
				$this->db->where('id', $id);
				$this->db->update('admin_users', $data); 
				
				redirect('action/adminusers');
			} else {
				$this->validation->error_string .= "<p>User with this username already exists.</p>";
			}
		}
		
		# Display view
		# Load variables into the template parser
        $data = $this->lang->language;
		$data['page'] = 'admin/edit_users';
		$data['id'] = $id;
		$data['action'] = 'edit';
		$data['name'] =  @$_POST['name'];
		$data['username'] = @$_POST['username'];
		$data['nav_button'] = 'adminusers';
		$this->parser->parse('admin/container', $data);
	}
	
	function add() {
		
		if ($this->_valid_form()){
			$q = $this->admin_users->find_by_username(@$_POST['username']);
			if (!$q || !$q->exists()) {
				$data['name'] =  @$_POST['name'];
				$data['username'] = @$_POST['username'];
				
				$this->load->library('encrypt');
				$data['password'] = $this->encrypt->get_key($_POST['password']);
				$this->db->insert('admin_users', $data); 
				redirect('action/adminusers');
			} else {
				$this->validation->error_string .= "<p>User with this username already exists.</p>";
			}
		}	

		# Display view
        $data = $this->lang->language;
		$data['page'] = 'admin/edit_users';
		$data['action'] = 'add';
		$data['name'] =  @$_POST['name'];
		$data['username'] = @$_POST['username'];
		$data['nav_button'] = 'adminusers';
		$this->parser->parse('admin/container', $data);
	}
	
	function _passwords_valid(){
		$pass1 = $_POST['password'];
		$pass2 = $_POST['password2'];
		if ($pass1!=$pass2){
			$this->validation->error_string .= "<p>Passwords are not equal.</p>";
			return FALSE;
		}
		return TRUE;
	}
	
	function delete($id){
		
		$this->db->where('id', $id);
		$this->db->delete('admin_users');
		redirect('action/adminusers');
	}
	
}

?>