<?php
class Adm extends Controller {

    function Adm() {
        parent::Controller();
    }
		
	function index() {

        # Load language
        if ($this->session->userdata('user_id')!="" ){
			redirect('/action/treemenu');	
		}
        $this->lang->load('main_admin');
	
		$this->load->helper(array('form'));
		$this->load->library('validation');
		# Load variables into the template parser
        $data = $this->lang->language;
		
		$rules['username']	= "required";
		$rules['password']	= "required";
		
		$this->validation->set_rules($rules);
		
		$where['username'] = @$_POST['username'];
		$this->load->library('encrypt');
		$where['password'] = $this->encrypt->get_key(@$_POST['password']);
		
		if ($this->validation->run() == FALSE){
			# Display view
			$this->parser->parse('admin/loginform', $data);
		}
		else if (!$this->_login($where) ) {
			$this->validation->error_string .= "<p>".$this->lang->language['admin_form_error']."</p>";
			# Display view
			$this->parser->parse('admin/loginform', $data);
		} else {
   			 redirect('action/treemenu');			
		}
	}
	
	/**
	 * Validate login using credentials (typically email/password or username/password)
	 * On succuess it sets the user_id field in the session userdata and returns the user object
	 *
	 * @access    public
	 * @param    associative array example ('email'=>$email, 'password'=>dohash($password))
	 * @return    mixed boolean:false or object with user record
	 */
    function _login($where = array())
    {	
        $query = $this->db->get_where('admin_users', $where, 1, 0);
		
        if (!$query || $query->num_rows != 1) return FALSE;
        $row = $query->row();
        $this->session->set_userdata('user_id', $row->id);

        return TRUE;
    }

    /**
	 * Get user information of current logged in user or a specific user by id
	 *
	 * @access    public
	 * @param    int user_id, default = current session user_id
	 * @return    mixed boolean:false or object with user record
	 */
    function _get_user($id = FALSE)
    {
        if ($id === FALSE)
        {
            if (($id = $this->session->userdata('user_id')) === FALSE)
            {
                return FALSE;
            }
        }

        $where = array(('admin_users' . '.' . $this->id) => $id);
        $query = $this->db->get_where('admin_users', $where, 1, 0);

        return ($query->num_rows() == 1) ? $query->row() : FALSE;
    }


    /**
	* Logout current user
	*
	* No parameter. Logout is done by destroying the current user session.
	*
	* @access    public
	* @return    void
	*/
    function logout()
    {
        $this->session->sess_destroy();
		redirect('adm');
    }
}
?>