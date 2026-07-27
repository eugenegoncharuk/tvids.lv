<?php
class Web extends Controller {

	var $top_pid;
	var $_path="path_class";
	var $_num_rows_per_page_items = 8;
	var $_can_be_measured = false;
	var $_last_time_measured = 0;
	
    function Web() {
        parent::Controller();
		$tree =& $this->load->model('tree');
		$hier =& $this->load->model('hier');
		$content =& $this->load->model('content');
		$users =& $this->load->model('users');
		$info_users =& $this->load->model('info_users');
		$info_items =& $this->load->model('info_items');
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
    }
		
	function send($pid=0){
		$cur_lang = $this->lang->get_current_lang();
		$msg = "";
		include("system/www/work/contacts_$cur_lang.php");
		$this->show($pid, $msg);
	}

	function _print_tree($arr, $arr_pid, $ppid){

		$node = array_shift($arr); 
		if ($node==null){
			return "";
		}
		$add_str = "";
		if (sizeof($arr_pid)>0){
			if (in_array($node['pid'],$arr_pid)){
				while ($cur_node_id=array_pop($arr_pid)) {
					$add_str .= "</ul></li>";
					if ($cur_node_id==$node['pid'] || $cur_node_id==NULL){
						break;
					}
				}
			}  
		}
		
		$b_start = "";
		$b_end = "";
		if ($node['id'] == $ppid){
			$b_start = "<b><u>";
			$b_end = "</u></b>";
		}
		
		if (isset($node['branch'])){
			array_push($arr_pid, $node['pid']);
			return $add_str.'<li><div>
							 <a
							 href="'.$this->config->site_url().'web/show/'.$node['id'].'">'
							 .$b_start.$node['text'].$b_end.'</a>
							 </div><ul>'.
					$this->_print_tree($arr,$arr_pid,$ppid);
					
		} else {
			return $add_str.'<li class="node"><div>
							<a
							href="'.$this->config->site_url().'web/show/'.$node['id'].'">'
							.$b_start.$node['text'].$b_end.'</a>
							 </div></li>'.

					$this->_print_tree($arr,$arr_pid,$ppid);
		}
	}
	
	function _print_tree_without_leafs($arr, $arr_pid, $ppid){

		$node = array_shift($arr); 
		if ($node==null){
			return "";
		}
		$add_str = "";
		if (sizeof($arr_pid)>0){
			if (in_array($node['pid'],$arr_pid)){
				while ($cur_node_id=array_pop($arr_pid)) {
					$add_str .= "</ul></li>";
					if ($cur_node_id==$node['pid'] || $cur_node_id==NULL){
						break;
					}
				}
			}  
		}
		
		$b_start = "";
		$b_end = "";
		if ($node['id'] == $ppid){
			$b_start = "<b><u>";
			$b_end = "</u></b>";
		}

		if (isset($node['branch'])){
			array_push($arr_pid, $node['pid']);
			return $add_str.'<li><div>
							 <a
							 href="'.$this->config->site_url().'web/show/'.$node['id'].'">'
							 .$b_start.$node['text'].$b_end.'</a>
							 </div><ul>'.
					$this->_print_tree_without_leafs($arr,$arr_pid,$ppid);
					
		} else {
			return $add_str.$this->_print_tree_without_leafs($arr,$arr_pid,$ppid);
		}
	}
	
	function _print_measured_time($string2print){
		if ($this->_can_be_measured){
			if ( $this->_last_time_measured == 0 ){
				$this->_last_time_measured = microtime();
			} else {
				echo $string2print." time is ".(microtime() - $this->_last_time_measured)." msecs<br>";
			}
		}
	}
	
	function show($pid=1, $from=0 ,$msg_arr=''){
		// LOADING TIME MEASUREMENT
		if ($this->session->userdata('web_user_name')=='xavier'){
			$this->_can_be_measured = true;
		}
		// LOADING TIME MEASUREMENT
		$this->_print_measured_time("");
		
		if ($pid==1) {
			redirect('web/show/270');
		}
		$cur_lang = $this->lang->get_current_lang();        		
		$data = $this->lang->language;
		$data['cur_lang'] = $cur_lang;
		$data['menu'] = $this->_get_branch_by_pid(229, $cur_lang);
		if ($msg_arr != ''){
			foreach ($msg_arr as $key => $msg) {
			    $data[$key] = $msg;
			}
		}

		$show_first = false;
		if ($pid==1){
			// we need to find first - default page
			$pid = $data['menu'][0]['id'];
		}
		// we need to find first - default page

		$out = $this->_get_branch_by_pid(228,$cur_lang);
		$arr_pid = array();
		$data['menu_text'] = $this->_print_tree_without_leafs($out, $arr_pid, $pid);
		
		$out2 = $this->_get_branch_by_pid(229,$cur_lang);
		$arr_pid2 = array();
		$data['menu_text2'] = $this->_print_tree($out2, $arr_pid2, $pid);
	
		$data['popular'] = $this->_get_popular_items();
		
		$q = $this->content->get_content($pid,$cur_lang,'1');
		if ($q->num_rows()==0){
			$data['selected_menu'] = $this->lang->line('no_data_for_category');
		} else {
			$data['selected_menu'] = $q->row()->text;
		}

		// depending on whether Web content link was pressed or Products we can print differently
		// if it is under products , when we will need to print this infromation with pagination
		if ($this->hier->is_node_under($pid, 228)){
			if ($this->hier->is_leaf($pid)){
				$q = $this->content->get_content($pid, $cur_lang, '2');
				$res = $this->lang->language;
				$res['text'] = @$q->row()->text;
				$res['content_id'] = @$q->row()->pid;
				$res['price'] = @$this->info_items->get_item($q->row()->pid)->price;
				$data['text'] = $this->parser->parse('item_view', $res, TRUE);
				
				//we need to check, what item was ckilcked last time
				$clicked_arr = $this->session->userdata('web_user_clicked_arr');
				if ($clicked_arr==null || count($clicked_arr) == 0){
					$clicked_arr = array();
				}
				
				if (!in_array($pid, $clicked_arr)){
					// this is the case when user clicked on one popular product item
					// we need to increase clicks for it
					$clicks = $this->info_items->find_by_pid($pid);
					if ($clicks!=null && $clicks->exists()){
						$this->db->set('clicks', 'clicks + 1', FALSE);
						$this->db->where('pid', $pid);
						$this->db->update('info_items'); 
					} else {
						$this->db->set('pid', $pid);
						$this->db->set('price', '0');
						$this->db->set('clicks', '1');
						$this->db->insert('info_items'); 
					}
					$clicked_arr[] = $pid;
				}
				$this->session->set_userdata('web_user_clicked_arr', $clicked_arr);

			} else {
				$this->_print_measured_time("Till DB call");
				$res = $this->content->get_content_leafs_limit($pid, $from, $this->_num_rows_per_page_items, $cur_lang);
				$this->_print_measured_time("DB call");
				
				if (!$res || $res['num_rows']==0){
					$data['text'] = $this->lang->line('no_data_for_category');
				} else {
					$rows = &$res['rows'];
					for($i=0; $i<count($rows); $i++){
						$row = &$rows[$i];
						$row['price'] = @$this->info_items->get_item($row['pid'])->price;
					}
					$this->load->library('pagination');
					$config['base_url'] = $this->config->site_url()."web/show/$pid";
					$config['total_rows'] = $res['num_rows'];
					$config['per_page'] = $this->_num_rows_per_page_items;
					$config['uri_segment']= 5;
					$config['num_links']= 10;
					$this->pagination->initialize($config);
					$res = array_merge($res, $this->lang->language);
					$res['pagination'] = $this->pagination->create_links();
					
					// getting view of the items
					$data['text'] = $this->parser->parse('items_view', $res, TRUE);
				}
				$this->_print_measured_time("Page parse time");
			}
		} else {
			$q = $this->content->get_content($pid,$cur_lang,'2');
			if ($q->num_rows()==0){
				$data['text'] = $this->lang->line('no_data_for_category');
			} else {
				$data['text'] = $q->row()->text;
			}
			
			if ($pid==7){
				include("system/www/work/form_send_$cur_lang.php");
				$path = base_url()."$cur_lang/web/send/$pid";
				$form = getFormSend($path, $msg);
				$data['text'] = str_replace("\\\\(form_send)\\\\", $form, $data['text']);
			}
		}
		
		$data['pid'] = $pid;
		$data['lang'] = $cur_lang;
		$this->parser->parse('main',$data);
	}

	function index($pid=1) {
		$this->show();		
	}
		
	function _get_branch_by_pid($pid,$lang) {

		if (!$this->tree->exists_id($pid)){
			echo "Tree doesn't exist!";
		}
		
		$out = array();
		$t = $this->tree->get_branch_lang($pid,$lang);
		$ind = 0;
		//echo "<PRE>";
		//print_r($t->result_array());
		while ($ind < $t->num_rows()){
			$r_arr = $t->row_array($ind);
			if ($ind < $t->num_rows() &&
				$t->row($ind+1)->pid == $t->row($ind)->id){
				$r_arr['branch']=1;				
			}
			$out[] =$r_arr;
			$ind++;
		}
		//print_r($out);
		return $out;
	}
	
	function _get_menu_by_level($lang="",$level=1){
		
		$t = $this->tree->get_tree_lang_by_level($lang,$level);
		$menu_arr = array();
		$ind = 0;
		while ($ind < $t->num_rows()){
			$r_arr = $t->row_array($ind);
			$menu_arr[] = $r_arr;
			$ind++;
		}
		return $menu_arr;
	}
	
	function _get_basic_data_menu(){
		$data = $this->lang->language;
		$cur_lang = $this->lang->get_current_lang(); 
		
		// printing vertical menu with videos
		$out = $this->_get_branch_by_pid(228,$cur_lang);
		$arr_pid = array();
		$data['menu_text'] = $this->_print_tree_without_leafs($out, $arr_pid, 0);
		
		$out2 = $this->_get_branch_by_pid(229,$cur_lang);
		$arr_pid2 = array();
		$data['menu_text2'] = $this->_print_tree($out2, $arr_pid2, 0);
	
		$data['popular'] = $this->_get_popular_items();
		$data['cur_lang'] = $cur_lang;
		
		// let's print out horizontal menu - web content
		$web_content = $this->_get_branch_by_pid(229, $cur_lang);
		$data['hor_menu'] = $web_content;
		$data['hor_menu2'] = array();
		$data['hor_menu2'] = array_merge($data['hor_menu2'], $data['hor_menu']);
			
		return $data;
	}
	
	// getting 3 most popular items today
	function _get_popular_items(){
		
		// selecting 3 most popular items from te database
		$q = $this->db->query("SELECT *, COUNT(hier.hier_pid) AS childs_count, 
			SUM(jadmin_info_items.clicks) as clicks,
			jadmin_content.pid AS content_id FROM jadmin_nav_tree 
			LEFT OUTER JOIN jadmin_content ON (jadmin_content.pid = jadmin_nav_tree.id) 
			LEFT OUTER JOIN jadmin_info_items ON (jadmin_content.pid = jadmin_info_items.pid) 
			LEFT OUTER JOIN (SELECT jadmin_nav_hier.pid as hier_pid FROM jadmin_nav_hier) hier 
			ON (hier.hier_pid=jadmin_nav_tree.id) WHERE jadmin_content.type='2' 
			AND jadmin_content.lang='".$this->lang->get_current_lang()."' AND jadmin_nav_tree.id IN (
			SELECT jadmin_nav_hier.node_id  as node_id FROM `jadmin_nav_hier` 
			WHERE jadmin_nav_hier.pid='201') GROUP BY jadmin_nav_tree.id 
			HAVING childs_count=0 ORDER BY clicks DESC  LIMIT 0, 3 ");
			
		$populars = $q->result_array();
		$i=0;
		while($i<count($populars)){
			$populars[$i]['text'] = utf8_substr(strip_tags($populars[$i]['text']), 0, 100);
			$i++;
		}
		return $populars;
	}
	
	// searching in the content
	function search(){
	
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 

		$search_val = $_POST['search_val'];
		$data['search_val'] = $search_val;
		
		if ($search_val!=""){
			$q = $this->content->search_content($search_val, $cur_lang);
		}
		
		if ($search_val == "" || !$q || $q->num_rows()==0){
			$data['text'] = $this->lang->line('no_data_found');
		} else {
			$ind = 0;
			$res_arr = array();
			while ($ind < $q->num_rows()){
				$r_arr = $q->row_array($ind);
				// here we need to get for example first and last 100 characters of the statement + 
				// we need to make bold the statements search phrase
				$start = utf8_strpos($r_arr['text'], $search_val) - 150;
				if ($start < 0){
					$start = 0;
				}
				
				$len = utf8_strlen($search_val) + 300;
				$res['search_text'] = '... '.utf8_substr(strip_tags($r_arr['text']), $start, $len). ' ...';
				$res['node_id'] = $r_arr['pid'];
				$res['pos'] = $ind + 1;
				$res_arr[] = $res;
				$ind++;
			}
			
			$data2 = array();
			$data2['search_results'] = $res_arr;
			$data['text'] = $this->parser->parse('search_view', $data2, TRUE);
		}
		
		$data['lang'] = $cur_lang;
		$this->parser->parse('main',$data);
	}
	
	function view_reg($action="buy_items_noreg"){
	
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		
		$data2 = $this->lang->language;
		$data2['action'] = $action;
		$data2['cur_lang'] = $cur_lang;
		$data['text'] = $this->parser->parse('reg_form', $data2 , TRUE);
		$data['lang'] = $cur_lang;
		$this->parser->parse('main',$data);
	}
	
	// here we are passing action
	function view_reg_login($action="reg_login"){
	
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang();
		
		$data2 = $this->lang->language;
		$data2['action'] = $action;
		$data2['cur_lang'] = $cur_lang;
		$data['text'] = $this->parser->parse('reg_form_login', $data2 , TRUE);
		$data['lang'] = $cur_lang;
		$this->parser->parse('main',$data);
	}
	
	function _passwords_valid(){
		$pass1 = $_POST['password'];
		$pass2 = $_POST['password2'];
		if ($pass1!=$pass2){
			$this->form_validation->error_string = '<p>'.$this->lang->line('passwords_not_equal').'</p>';
			return FALSE;
		}
		return TRUE;
	}
	
	function _valid_reg_form(){
	
		$this->form_validation->set_rules('name', $this->lang->line('login'), 'trim|required|min_length[5]|max_length[30]|xss_clean');
		$this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|min_length[5]|max_length[30]');
		$this->form_validation->set_rules('password2', $this->lang->line('password_repeat'), 'trim|required|min_length[5]|max_length[30]');
		
		$this->form_validation->set_rules('contact_name', $this->lang->line('name_surname_contact'), 'trim|required|max_length[50]|min_length[4]');
		$this->form_validation->set_rules('fact_address',  $this->lang->line('fact_address'), 'trim|required|max_length[100]');
		$this->form_validation->set_rules('tel', $this->lang->line('tel'), 'trim|required|max_length[20]|min_length[7]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|min_length[8]');
		
		$where['name'] = @$_POST['name'];
		$where['password'] = @$_POST['password'];
		$where['password2'] = @$_POST['password2'];
		
		$where['contact_name'] = @$_POST['contact_name'];
		$where['fact_address'] = @$_POST['fact_address'];
		$where['tel'] = @$_POST['tel'];
		$where['email'] = @$_POST['email'];
		
		if ($this->form_validation->run() == FALSE || !$this->_passwords_valid()) {
			return FALSE;
		}
			
		return TRUE;	
	}
	
	function _valid_request_form(){
		
		$this->form_validation->set_rules('contact_name', $this->lang->line('name_surname_contact'), 'trim|required|max_length[50]|min_length[4]');		
		$this->form_validation->set_rules('fact_address',  $this->lang->line('fact_address'), 'trim|required|max_length[100]');
		$this->form_validation->set_rules('tel', $this->lang->line('tel'), 'trim|required|max_length[20]|min_length[7]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|min_length[8]');
		
		$where['name'] = @$_POST['name'];
		$where['password'] = @$_POST['password'];
		$where['password2'] = @$_POST['password2'];
		
		$where['contact_name'] = @$_POST['contact_name'];
		$where['fact_address'] = @$_POST['fact_address'];
		$where['tel'] = @$_POST['tel'];
		$where['email'] = @$_POST['email'];
		
		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		}
			
		return TRUE;	
	}

	// checking whether email or username already exist
	function _check_name_email_reg($user_id=""){
	
		$q = $this->users->find_by_name(@$_POST['name']);
		if ($q!=null && $q->exists()) {
			if ($user_id=="" || $q->id!=$user_id){
				$this->form_validation->error_string = $this->lang->line('user_already_exist');
				return FALSE;
			}
		}
		
		$this->db->select('*');
		$this->db->from('jadmin_info_users');
		$this->db->where('email =', @$_POST['email']); 
		$this->db->where('user_id !=', 0);
		$q2 = $this->db->get();
		if ($q2!=null && $q2->num_rows()>0) {
			if ($user_id=="" || $q2->row()->user_id!=$user_id){
				$this->form_validation->error_string = $this->lang->line('user_already_exist_email');
				return FALSE;
			}
		}
		return TRUE;
	}
	
	function reg_login() {

		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
		
		if ($this->_valid_reg_form() && $this->_check_name_email_reg()){		
			$this->_add_user_post();
			$users_internal_id = $this->db->insert_id();
			$this->_add_info_user_post($users_internal_id );
			
			$data['text'] = $this->lang->line('congrat_you_registered');
			$this->parser->parse('main',$data);
			return;
		}
		$this->view_reg_login();
	}
	
	function login($pid=1) {
        # Load language
        if ($this->session->userdata('web_user_id')!="" ){
			redirect('/web');	
		}
		
		$this->form_validation->set_rules('login', $this->lang->line('login'), 'trim|required|min_length[5]|max_length[30]|xss_clean');
		$this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|min_length[5]|max_length[30]');
		
		$this->load->library('encrypt');	
		$where['name'] = @$_POST['login'];
		$where['password'] = $this->encrypt->get_key(@$_POST['password']);
		if ( $this->form_validation->run() == FALSE || !$this->_login($where) ){
			$msg_arr['login_error'] = $this->lang->line('incorrect_user_password');
			$this->show($pid, 0, $msg_arr);
		} else {
   			 redirect('/web');			
		}
	}
	
	function logout()
    {	
		$this->session->sess_destroy();
		redirect('/web');
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
        $query = $this->db->get_where('users', $where, 1, 0);
        if (!$query || $query->num_rows != 1) return FALSE;
        $row = $query->row();
        $this->session->set_userdata('web_user_id', $row->id);
		$this->session->set_userdata('web_user_name', $row->name);

        return TRUE;
    }
	
	/**
	 * We buy items here
	 */
    function buy($id) {	
		
		// in this case there is some video, which we can show to user and actually user can by this video
		// first of all we need to get all the info from session and let's see what videos this user picked up in the cart
		$cart = $this->session->userdata('web_user_cart');
		if ($cart=='' || count($cart)==0 ) {
			$cart = array();
		}
				
		// let's find this item
		$item = $this->info_items->get_item($id);
		if (!$item) {
   			 redirect('/web');			
		}

		// so now we need to add this item to the cart and save it
		if (!@$cart){
			$cart['products'] = array();
			$cart['count'] = array();
			$cart['sum'] = array();
		}
		
		if (!in_array($id, @$cart['products'])){
			$cart['products'][] = $id;
		}
		$cart['count'][$id] = @$cart['count'][$id] + 1;
		$cart['sum'][$id] = @$cart['sum'][$id] + $item->price;
		$cart['price'][$id] = $item->price;
		
		// here we need to get how much items is there and what is the final sum for the items in the cart
		$this->session->set_userdata('web_user_itemscount', array_sum($cart['count']));
		$this->session->set_userdata('web_user_itemssum', array_sum($cart['sum']));
		
		//saving this in the session
		$this->session->set_userdata('web_user_cart', $cart);
		redirect("/web/show/".$id);
	}
	
	function _get_cart_items($cart){
		$cur_lang = $this->lang->get_current_lang();
		$data2 = $this->lang->language;
		$cart_keys = array_values(@$cart['products']);
		$items = array();
		$pos = 0;
		$summary = 0;
		foreach($cart_keys as $id) {
			$pos++;
			$price = $this->info_items->get_item($id)->price;
			$q2 = $this->db->get_where('content', array("pid" => $id, "type" => 1, "lang" => $cur_lang), 1, 0);
			//echo $this->db->last_query();
			$text = $q2->row()->text;
			$items[] = array('pos'=>$pos, 'price'=>$price, 'text'=>$text, 
							 'node_id'=>$id, 'count'=>$cart['count'][$id],
							 'sum'=>$cart['count'][$id] * $price);
			$summary += $cart['count'][$id] * $price;
		}
		$data2['cart_items'] = $items;
		$data2['summary'] = $summary;
		return $data2;
	}
	
	function view_cart(){
	
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
		
		// getting id' s of videos in the cart
		$cart = $this->session->userdata('web_user_cart');
		if (empty($cart)){
			$data['text'] = $this->lang->line('cart_empty');
			$this->session->set_userdata('web_user_itemscount', '');
			$this->session->set_userdata('web_user_itemssum','');
			$this->parser->parse('main',$data);
			return;
		}
				
		$data2 = $this->_get_cart_items($cart);
		// here we need to get how much items is there and what is the final sum for the items in the cart
		$this->session->set_userdata('web_user_itemscount', array_sum($cart['count']));
		$this->session->set_userdata('web_user_itemssum', $data2['summary']);
				
		$data['text'] = $this->parser->parse('cart_view', $data2, TRUE);
		$this->parser->parse('main',$data);
	}
	
	function add_item($id){
	
		// we need to search for the element in the cart and delete it
		$cart = $this->session->userdata('web_user_cart');
		if (empty($cart)){
			$data['text'] = $this->lang->line('cart_empty');
			$this->parser->parse('main',$data);
			return;
		}
		
		$cart['count'][$id] = $cart['count'][$id] + 1;
		$cart['sum'][$id] = $cart['sum'][$id] + $cart['price'][$id];
		
		$this->session->set_userdata('web_user_cart', $cart);
		redirect('/web/view_cart');
	}
	
	function delete_item($id){
	
		// we need to search for the element in the cart and delete it
		$cart = $this->session->userdata('web_user_cart');
		if (empty($cart)){
			$data['text'] = $this->lang->line('cart_empty');
			$this->parser->parse('main',$data);
			return;
		}
		
		$new_cart = array();
		foreach ($cart['products'] as $key => $id_old) {
			if($id_old!=$id) {
				$new_cart['products'][] = $id_old;
				$new_cart['count'][$id_old] = $cart['count'][$id_old];
				$new_cart['sum'][$id_old] = $cart['sum'][$id_old];
				$new_cart['price'][$id_old] = $cart['price'][$id_old];
			} else {
				if ($cart['count'][$id_old]>1){
					$new_cart['products'][] = $id_old;
					$new_cart['count'][$id_old] = $cart['count'][$id_old] - 1;
					$new_cart['sum'][$id_old] = $cart['sum'][$id_old] - $cart['price'][$id_old];
					$new_cart['price'][$id_old] = $cart['price'][$id_old];
				}
			}
		}
		
		$this->session->set_userdata('web_user_cart', $new_cart);
		redirect('/web/view_cart');
	}
	
	function delete_all_items($id){
	
		// we need to search for the element in the cart and delete it
		$cart = $this->session->userdata('web_user_cart');
		if (empty($cart)){
			$data['text'] = $this->lang->line('cart_empty');
			$this->parser->parse('main',$data);
			return;
		}
		
		$new_cart = array();
		foreach ($cart['products'] as $key => $id_old) {
			if($id_old!=$id) {
				$new_cart['products'][] = $id_old;
				$new_cart['count'][$id_old] = $cart['count'][$id_old];
				$new_cart['sum'][$id_old] = $cart['sum'][$id_old];
				$new_cart['price'][$id_old] = $cart['price'][$id_old];
			}
		}
		
		$this->session->set_userdata('web_user_cart', $new_cart);
		redirect('/web/view_cart');
	}
	
	// here we will buy items and make request
	function view_buy_items_reg() {
		$this->view_reg_login('buy_items_reg');
	}
	
	function _add_info_user_post($users_internal_id){
			
		// let's add data to the info_users table
		$data2['contact_name'] =  @$_POST['contact_name'];
		$data2['fact_address'] =  @$_POST['fact_address'];
		$data2['tel'] =  @$_POST['tel'];
		$data2['email'] =  @$_POST['email'];
		$data2['comments'] =  @$_POST['comments'];
		$data2['user_id'] = $users_internal_id;
		$this->db->insert('info_users', $data2);
	}
	
	// adding request to the DB
	function _add_requests_post($cart, $users_info_internal_id) {
		
		// we need to make one time for this requests
		$cur_time = time();
		$req_id_str = "".$cur_time.$users_info_internal_id;
		// User is added. Now we can add requests.
		$data3['req_id'] = $req_id_str;
		$data3['req_date'] = $cur_time;
		$data3['type'] = "1";
		$data3['status'] = "0";
		$data3['user_info_id'] = $users_info_internal_id;
		$this->db->insert('requests', $data3);
	}
	
	//adding requests items to the DB
	function _add_requests_items_post($cart, $request_internal_id){
	
		$cart_keys = array_values(@$cart['products']);
		// adding requests items
		foreach($cart_keys as $id) {
			$data3['pid'] = $request_internal_id;
			$data3['content_id'] = $id;
			$data3['price'] = @$cart['price'][$id];
			$data3['count'] = @$cart['count'][$id];
			$data3['sum'] =  @$cart['sum'][$id];
			$this->db->insert('requests_items', $data3);
		}
	}

	function _add_user_post(){
		$data1['name'] =  @$_POST['name'];
		$this->load->library('encrypt');
		$data1['password'] = $this->encrypt->get_key($_POST['password']);
		$this->db->insert('users', $data1); 
	}

	// buying items and creating request in the database
	function buy_items_reg() {
			
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
		
		// getting id' s of videos in the cart
		$cart = $this->session->userdata('web_user_cart');
		if (empty($cart)){
			$data['text'] = $this->lang->line('cart_empty');
			$this->session->set_userdata('web_user_itemscount', '');
			$this->session->set_userdata('web_user_itemssum','');
			$this->parser->parse('main',$data);
			return;
		}
		
		if ($this->_valid_reg_form() && $this->_check_name_email_reg()){
				// let' s add all the values in the tables
				$this->_add_user_post();
				$users_internal_id = $this->db->insert_id();
				$this->_add_info_user_post($users_internal_id );
				$users_info_internal_id = $this->db->insert_id();
				$this->_add_requests_post($cart, $users_info_internal_id);
				$request_internal_id = $this->db->insert_id();
				$this->_add_requests_items_post($cart, $request_internal_id);
			
				// we need to send request to the client email
				$q2 = $this->info_users->find_by_id($users_info_internal_id );
				$q3 = $this->content->get_content(253,$cur_lang,'2');
				$text = $q3->row()->text;
				
				$data2 = $this->_get_cart_items($cart);
				$text_items = $this->parser->parse('email_items_view', $data2, TRUE);
			
				$text = str_replace("\\\\(items_table)\\\\", $text_items, $text);
				$this->_send_email($q2->email, $text);
				$this->_send_sms();
			
				$data['text'] = $this->lang->line('request_sent_email');
				$this->session->set_userdata('web_user_itemscount', '');
				$this->session->set_userdata('web_user_itemssum','');
				$this->session->set_userdata('web_user_cart','');
				$this->parser->parse('main',$data);
				return;
		}
		$this->view_reg_login('buy_items_reg');
	}
	
	
	function view_buy_items() {
		$this->view_reg('buy_items_noreg');
	}
	
	// buying items and creating request in the database
	function buy_items_noreg() {
			
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
		
		// getting id' s of videos in the cart
		$cart = $this->session->userdata('web_user_cart');
		if (empty($cart)){
			$data['text'] = $this->lang->line('cart_empty');
			$this->session->set_userdata('web_user_itemscount', '');
			$this->session->set_userdata('web_user_itemssum','');
			$this->parser->parse('main',$data);
			return;
		}
		
		if ($this->_valid_request_form()){
			// let' s add all the values in the tables
			$this->_add_info_user_post("");
			$users_info_internal_id = $this->db->insert_id();
			$this->_add_requests_post($cart, $users_info_internal_id);
			$request_internal_id = $this->db->insert_id();
			$this->_add_requests_items_post($cart, $request_internal_id);
			
			// we need to send request to the client email
			$q2 = $this->info_users->find_by_id($users_info_internal_id );
			$q3 = $this->content->get_content(253,$cur_lang,'2');
			$text = $q3->row()->text;
			
			$data2 = $this->_get_cart_items($cart);
			$text_items = $this->parser->parse('email_items_view', $data2, TRUE);
			
			$text = str_replace("\\\\(items_table)\\\\", $text_items, $text);
			$this->_send_email($q2->email, $text);
			$this->_send_sms();

			$data['text'] = $this->lang->line('request_sent_email');
			$this->session->set_userdata('web_user_itemscount', '');
			$this->session->set_userdata('web_user_itemssum','');
			$this->session->set_userdata('web_user_cart','');
			$this->parser->parse('main',$data);
			return;
		}
		$this->view_reg('buy_items_noreg');
	}
	
	// buying items and creating request in the database
	function buy_items_already_reg() {
			
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
		
		// getting id' s of videos in the cart
		$cart = $this->session->userdata('web_user_cart');
		if (empty($cart)){
			$data['text'] = $this->lang->line('cart_empty');
			$this->session->set_userdata('web_user_itemscount', '');
			$this->session->set_userdata('web_user_itemssum','');
			$this->parser->parse('main',$data);
			return;
		}
		
		$user_id = $this->session->userdata('web_user_id');
		$q1 = $this->users->find_by_id($user_id);
		$q2 = $this->info_users->find_by_user_id($user_id);
		if (!$q1 || !$q1->exists() || !$q2 || !$q2->exists()){
			$data['text'] = "Ваши данные были удалены из сессии !";
			$this->session->set_userdata('web_user_itemscount', '');
			$this->session->set_userdata('web_user_itemssum','');
			$this->parser->parse('main',$data);
			return;			
		}
		
		$this->_add_requests_post($cart, $q2->id);
		$request_internal_id = $this->db->insert_id();
		$this->_add_requests_items_post($cart, $request_internal_id);
			
		// we need to send request to the client email
		$q3 = $this->content->get_content(253,$cur_lang,'2');
		$text = $q3->row()->text;
		
		$data2 = $this->_get_cart_items($cart);
		$text_items = $this->parser->parse('email_items_view', $data2, TRUE);
		
		$text = str_replace("\\\\(items_table)\\\\", $text_items, $text);
		$this->_send_email($q2->email, $text);
		$this->_send_sms();
		
		$data['text'] = $this->lang->line('request_sent_email');
		$this->session->set_userdata('web_user_itemscount', '');
		$this->session->set_userdata('web_user_itemssum','');
		$this->session->set_userdata('web_user_cart','');
		$this->parser->parse('main',$data);
	}
	
	function _send_sms() {
		$this->load->library('email');
		$config['mailtype'] = 'text';
		$this->email->initialize($config);

		$this->email->from('info@tvids.lv', 'tvids.lv');
		$this->email->to('37129558938@sms.tele2.lv');
		$this->email->subject('tvids.lv');
		$this->email->message('Order came to tvids.lv! Please check!');
		$this->email->send();		
	}
	
	function _send_email($who, $text = '', $bcc_to_us = true) {
	
		$this->load->library('email');
		$config['mailtype'] = 'html';
		$config['wordwrap'] = TRUE;
		$this->email->initialize($config);

		$this->email->from('tvids@inbox.lv', 'tvids.lv');
		$this->email->to($who);
		if ($bcc_to_us) {
			$this->email->bcc('tvids@inbox.lv');
		}
		
		$this->email->subject('tvids.lv');
		$this->email->message($text);

		$this->email->send();
		//echo $this->email->print_debugger();
	}
	
	function forgot_password(){
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
		
		$data2 = $this->_get_basic_data_menu();
		$text = $this->parser->parse('forgot_password', $data2, TRUE);
		$data['text'] = $text;
		
		$this->parser->parse('main',$data);
	}
	
	// we need to send an email to user
	function send_update(){

		$this->form_validation->set_rules('email', 'e-mail', 'trim|required|valid_email');
		$where['email'] = @$_POST['email'];
		
		if ($this->form_validation->run() == FALSE) {
			$this->forgot_password();
			return;
		}
		
		$q = $this->info_users->find_by_email(@$_POST['email']);
		if ($q==null || !$q->exists() || $q->user_id==0){
			$this->form_validation->error_string = '<p>'.$this->lang->line('email_not_correct').'</p>';
			$this->forgot_password();
			return;
		}
		
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
		
		// we need to send request to the client email
		$q2 = $this->users->find_by_id(@$q->user_id);
		$link = $this->config->site_url().'web/view_reg_info/'.$q2->name.'/'.$q2->password;
		$q3 = $this->content->get_content(269, $cur_lang, '2');
		$text = $q3->row()->text;
		
		$text = str_replace("\\\\(link_forgot)\\\\", $link, $text);
		$this->_send_email($q->email, $text, false);
		
		$data['text'] = $this->lang->line('request_sent_email');
		$this->parser->parse('main',$data);
	}
	
	// viewing registration information to update
	function view_reg_info($login, $pass) {
	    
		if ($login=='' || $pass==''){
			redirect('/web');
		}
		
		$query = $this->db->query("SELECT * FROM jadmin_users, jadmin_info_users WHERE 
								(jadmin_users.name=\"".$login."\" AND jadmin_users.password=\"".$pass."\" 
								AND jadmin_users.id=jadmin_info_users.user_id)");
								
		if ($query->num_rows() > 0){
			$row = $query->row_array();
			$where['name'] = $row['name'];
			$where['password'] = $row['password'];
			$this->_login($where);

			@$_POST = array_merge(@$_POST , $row);
			$this->view_reg_login('update_reg/'.$row['id'].'/'.$row['user_id']);
		} else {
			redirect('web');
		}
	}
	
	function update_reg($user_info_id, $user_id){
	
		if ($this->session->userdata('web_user_id')=="" || $user_info_id=="" || $user_id=="" ){
			redirect('web');
		}
	  
		$data = $this->_get_basic_data_menu();
		$cur_lang = $this->lang->get_current_lang(); 
		$data['lang'] = $cur_lang;
				
		if ($this->_valid_reg_form() && $this->_check_name_email_reg($user_id)){
				// let' s add all the values in the tables
				$this->_update_user_post($user_id);
				$this->_update_info_user_post($user_info_id);
			
				$data['text'] = $this->lang->line('congrat_you_updated');
				$this->parser->parse('main',$data);
				return;
		}
		$this->view_reg_login('update_reg/'.$user_info_id.'/'.$user_id);
	}
	
	function _update_info_user_post($id){
						
		// let's add data to the info_users table
		$data2['official_name'] =  @$_POST['official_name'];
		$data2['official_code'] =  @$_POST['official_code'];
		$data2['official_address'] =  @$_POST['official_address'];
		$data2['fact_address'] =  @$_POST['fact_address'];
		$data2['contact_name'] =  @$_POST['contact_name'];
		$data2['tel'] =  @$_POST['tel'];
		$data2['fax'] =  @$_POST['fax'];
		$data2['email'] =  @$_POST['email'];
		$data2['bank_name'] =  @$_POST['bank_name'];
		$data2['bank_swift'] =  @$_POST['bank_swift'];
		$data2['bank_iban'] =  @$_POST['bank_iban'];
		$data2['comments'] =  @$_POST['comments'];

		$this->db->where('id', $id);
		$this->db->update('info_users', $data2); 
	}

	function _update_user_post($id){
		$data1['name'] =  @$_POST['name'];
		$this->load->library('encrypt');
		$data1['password'] = $this->encrypt->get_key($_POST['password']);

		$this->db->where('id', $id);
		$this->db->update('users', $data1); 
	}
	
}
?>