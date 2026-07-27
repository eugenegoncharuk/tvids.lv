<?php
class Bedlinen extends Controller {

	var $_upload_dir = 'system/www/up/images/bed_linen';
	var $_thumb_max_width = 150;

    function Bedlinen() {
        parent::Controller();
		if ($this->session->userdata('user_id')=="" ){
			redirect('/adm');
		}
		$bedlinenimages =& $this->load->model('bedlinenimages');
		$tree =& $this->load->model('tree');
    }

	function index() {
		$this->show(0);
	}

	function show($category_id = 0) {
		$category_id = (int)$category_id;

		$data['page'] = 'admin/bedlinen';
		$data['nav_button'] = 'bedlinen';
		$data['content'] = $this->_render($category_id, '');
		$this->parser->parse('admin/container', $data);
	}

	function upload($category_id) {
		$category_id = (int)$category_id;
		$category = $this->bedlinenimages->get_category($category_id);
		if (!$category) {
			redirect('action/bedlinen');
		}

		$error = '';
		$name = isset($_POST['name']) ? $_POST['name'] : '';
		list($image_path, $thumb_path, $error) = $this->_handle_upload($category_id, 'image');

		if ($error === '') {
			$image_id = $this->bedlinenimages->insert_image($category_id, $name, $image_path, $thumb_path);
			$this->bedlinenimages->set_leafs($image_id, $this->_collect_leaf_prices());
		}

		$data['page'] = 'admin/bedlinen';
		$data['nav_button'] = 'bedlinen';
		$data['content'] = $this->_render($category_id, $error);
		$this->parser->parse('admin/container', $data);
	}

	function update($image_id) {
		$image_id = (int)$image_id;
		$image = $this->bedlinenimages->get_image($image_id);
		if (!$image) {
			redirect('action/bedlinen');
		}

		$name = isset($_POST['name']) ? $_POST['name'] : $image->name;
		$error = '';
		$image_path = null;
		$thumb_path = null;

		if (!empty($_FILES['image']['name'])) {
			list($image_path, $thumb_path, $error) = $this->_handle_upload($image->category_id, 'image');
			if ($error === '') {
				if (file_exists($image->image_path)) unlink($image->image_path);
				if (file_exists($image->thumb_path)) unlink($image->thumb_path);
			}
		}

		if ($error === '') {
			$this->bedlinenimages->update_image($image_id, $name, $image_path, $thumb_path);
			$this->bedlinenimages->set_leafs($image_id, $this->_collect_leaf_prices());
		}

		$data['page'] = 'admin/bedlinen';
		$data['nav_button'] = 'bedlinen';
		$data['content'] = $this->_render($image->category_id, $error);
		$this->parser->parse('admin/container', $data);
	}

	function delete($image_id) {
		$image_id = (int)$image_id;
		$image = $this->bedlinenimages->get_image($image_id);
		if (!$image) {
			redirect('action/bedlinen');
		}
		$category_id = $image->category_id;

		if (file_exists($image->image_path)) unlink($image->image_path);
		if (file_exists($image->thumb_path)) unlink($image->thumb_path);
		$this->bedlinenimages->delete_image($image_id);

		redirect('action/bedlinen/show/'.$category_id);
	}

	// array(subcat_id => price) for every checked subcategory checkbox,
	// ignoring price values posted for boxes that weren't actually checked
	function _collect_leaf_prices() {
		$leaf_ids = isset($_POST['leaf_ids']) ? $_POST['leaf_ids'] : array();
		$posted_prices = isset($_POST['leaf_prices']) ? $_POST['leaf_prices'] : array();
		$leaf_prices = array();
		foreach ($leaf_ids as $leaf_id) {
			$leaf_prices[$leaf_id] = isset($posted_prices[$leaf_id]) ? $posted_prices[$leaf_id] : 0;
		}
		return $leaf_prices;
	}

	// returns array($image_path, $thumb_path, $error) - paths are null on error
	function _handle_upload($category_id, $field) {
		$target_dir = $this->_upload_dir.'/'.$category_id;
		if (!is_dir($target_dir)) {
			mkdir($target_dir, 0775, true);
		}

		$config['upload_path'] = './'.$target_dir.'/';
		$config['allowed_types'] = 'jpg|jpeg|png|gif';
		$config['max_size'] = '5120';
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload($field)) {
			return array(null, null, $this->upload->display_errors('', ''));
		}

		$upload_data = $this->upload->data();
		$image_path = $target_dir.'/'.$upload_data['file_name'];
		$thumb_path = $target_dir.'/thumb_'.$upload_data['file_name'];
		$this->_make_thumbnail($image_path, $thumb_path, $upload_data['image_type']);

		return array($image_path, $thumb_path, '');
	}

	function _make_thumbnail($image_path, $thumb_path, $image_type) {
		switch ($image_type) {
			case 'jpeg': $src = @imagecreatefromjpeg($image_path); break;
			case 'png':  $src = @imagecreatefrompng($image_path); break;
			case 'gif':  $src = @imagecreatefromgif($image_path); break;
			default: $src = false;
		}
		if (!$src) {
			copy($image_path, $thumb_path);
			return;
		}

		$width = imagesx($src);
		$height = imagesy($src);
		$new_width = min($width, $this->_thumb_max_width);
		$new_height = (int)round($height * ($new_width / $width));

		$dst = imagecreatetruecolor($new_width, $new_height);
		if ($image_type == 'png') {
			imagealphablending($dst, false);
			imagesavealpha($dst, true);
		}
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		switch ($image_type) {
			case 'jpeg': imagejpeg($dst, $thumb_path, 85); break;
			case 'png':  imagepng($dst, $thumb_path); break;
			case 'gif':  imagegif($dst, $thumb_path); break;
		}
		imagedestroy($src);
		imagedestroy($dst);
	}

	function _category_options($selected_id) {
		$html = '<option value="0">-- select category --</option>';
		foreach ($this->bedlinenimages->get_categories()->result() as $cat) {
			$sel = ($cat->id == $selected_id) ? ' selected' : '';
			$html .= '<option value="'.$cat->id.'"'.$sel.'>'.htmlspecialchars($cat->name).'</option>';
		}
		return $html;
	}

	function _render($category_id, $error) {
		$category_options = $this->_category_options($category_id);

		$html = '';
		$html .= '<style>
			#bl-modal { display:none; position:fixed; z-index:1000; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); text-align:center; }
			#bl-modal img { max-width:90%; max-height:90%; margin-top:2%; }
			.bl-thumb { max-width:150px; cursor:pointer; border:1px solid #ccc; display:block; }
			.bl-table { border-collapse:collapse; table-layout:auto; }
			.bl-table td, .bl-table th { border:1px solid #ccc; padding:8px; vertical-align:middle; text-align:left; white-space:normal; }
			.bl-table th { background:#f3f3f3; text-align:center; font-size:0.9em; }
			.bl-table td.bl-check { text-align:center; width:70px; }
			.bl-table td.bl-check input[type=number] { width:60px; box-sizing:border-box; margin-top:4px; }
			.bl-table th.bl-subcat { width:125px; box-sizing:border-box; overflow-wrap:anywhere; word-break:break-word; }
			.bl-table td.bl-actions { text-align:center; white-space:nowrap; padding:8px 18px; }
			.bl-table input[type=text] { width:140px; box-sizing:border-box; }
			.bl-table input[type=file] { max-width:140px; margin-top:6px; }
			.bl-add-form p { margin-bottom:14px; }
			.bl-add-form label { display:inline-block; }
			.bl-add-item { display:inline-block; margin:0 15px 10px 0; }
			.bl-add-item input[type=number] { width:70px; box-sizing:border-box; }
		</style>';
		$html .= '<div id="bl-modal" onclick="this.style.display=\'none\'"><img id="bl-modal-img" src=""></div>';
		$html .= '<script>function blShow(src){document.getElementById("bl-modal-img").src=src;document.getElementById("bl-modal").style.display="block";}</script>';

		$html .= '<form method="get" action="'.site_url().'action/bedlinen/show" onsubmit="return false;">';
		$html .= '<label>Category: <select onchange="location.href=\''.site_url().'action/bedlinen/show/\'+this.value">'.$category_options.'</select></label>';
		$html .= '</form>';

		if ($category_id <= 0) {
			return $html;
		}

		if ($error !== '') {
			$html .= '<div style="color:#900; margin:10px 0;">'.$error.'</div>';
		}

		$subcats = $this->bedlinenimages->get_child_categories($category_id);

		// add form
		$html .= '<h3>Add image</h3>';
		$html .= '<form class="bl-add-form" method="post" action="'.site_url().'action/bedlinen/upload/'.$category_id.'" enctype="multipart/form-data">';
		$html .= '<p>Name: <input type="text" name="name"></p>';
		$html .= '<p>Image: <input type="file" name="image" required></p>';
		$html .= '<p>';
		if (!empty($subcats)) {
			$html .= '<button type="button" onclick="var cbs=document.querySelectorAll(\'.bl-add-cb\'); for(var i=0;i<cbs.length;i++){cbs[i].checked=true;}">Select all</button><br>';
		}
		foreach ($subcats as $subcat) {
			$html .= '<span class="bl-add-item">'
				.'<label><input type="checkbox" class="bl-add-cb" name="leaf_ids[]" value="'.$subcat->id.'"> '.htmlspecialchars($subcat->name).'</label> '
				.'<input type="number" step="0.01" min="0" name="leaf_prices['.$subcat->id.']" placeholder="price">'
				.'</span>';
		}
		if (empty($subcats)) {
			$html .= 'No subcategories found under this category.';
		}
		$html .= '</p>';
		$html .= '<input type="submit" value="Upload">';
		$html .= '</form>';

		// existing images table
		$images = $this->bedlinenimages->get_images($category_id);
		$html .= '<h3>Images</h3>';
		if (empty($images)) {
			$html .= '<p>No images uploaded yet for this category.</p>';
		} else {
			// Row forms are emitted here, in normal document flow, rather than
			// between <tr>s: a <form> tag encountered while the parser is
			// "in table" mode only ever registers once (the HTML5 spec's
			// form-element-pointer rule silently drops every one after the
			// first), which was breaking Save for every row past the first
			// and corrupting the table's layout.
			foreach ($images as $image) {
				$form_id = 'f'.$image->id;
				$html .= '<form id="'.$form_id.'" method="post" action="'.site_url().'action/bedlinen/update/'.$image->id.'" enctype="multipart/form-data"></form>';
			}

			$html .= '<div style="overflow-x:auto;">';
			$html .= '<table class="bl-table"><tr><th>Name</th><th>Image</th>';
			foreach ($subcats as $subcat) {
				$html .= '<th class="bl-subcat">'.htmlspecialchars($subcat->name).'</th>';
			}
			$html .= '<th>&nbsp;</th></tr>';

			foreach ($images as $image) {
				$leaf_prices = $this->bedlinenimages->get_leaf_prices_for_image($image->id);
				$form_id = 'f'.$image->id;

				$html .= '<tr>';
				$html .= '<td><input type="text" name="name" form="'.$form_id.'" value="'.htmlspecialchars($image->name).'"></td>';
				$html .= '<td><img class="bl-thumb" src="'.base_url().$image->thumb_path.'" onclick="blShow(\''.base_url().$image->image_path.'\')"><br>'
					.'<input type="file" name="image" form="'.$form_id.'"></td>';
				foreach ($subcats as $subcat) {
					$checked = array_key_exists($subcat->id, $leaf_prices) ? ' checked' : '';
					$price = array_key_exists($subcat->id, $leaf_prices) ? $leaf_prices[$subcat->id] : '';
					$html .= '<td class="bl-check">'
						.'<input type="checkbox" name="leaf_ids[]" form="'.$form_id.'" value="'.$subcat->id.'"'.$checked.'><br>'
						.'<input type="number" step="0.01" min="0" name="leaf_prices['.$subcat->id.']" form="'.$form_id.'" value="'.htmlspecialchars($price).'">'
						.'</td>';
				}
				$html .= '<td class="bl-actions">'
					.'<input type="submit" value="Save" form="'.$form_id.'"><br>'
					.'<a href="'.site_url().'action/bedlinen/delete/'.$image->id.'" onclick="return confirm(\'Delete this image?\');">Delete</a>'
					.'</td>';
				$html .= '</tr>';
			}
			$html .= '</table></div>';
		}

		return $html;
	}
}
?>
