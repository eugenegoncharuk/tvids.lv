<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bedlinenimages extends Model {

    function __construct()
    {
        parent::Model();
    }

	// nav_tree nodes marked as "Категория комплектов"
	function get_categories(){
		$this->db->select('id, name');
		$this->db->from('nav_tree');
		$this->db->where('node_type', 'set_category');
		$this->db->order_by('name', 'asc');
		return $this->db->get();
	}

	function get_category($id){
		$this->db->select('id, name');
		$this->db->from('nav_tree');
		$this->db->where('id', $id);
		$this->db->where('node_type', 'set_category');
		$q = $this->db->get();
		if ($q->num_rows() == 0) return false;
		return $q->row();
	}

	// direct child subcategories of the category (e.g. "Под.145x210,..."),
	// used as the checkbox columns in the images table
	function get_child_categories($category_id){
		$this->db->select('id, name');
		$this->db->from('nav_tree');
		$this->db->where('pid', $category_id);
		$this->db->order_by('hid', 'asc');
		return $this->db->get()->result();
	}

	function get_images($category_id){
		$this->db->select('*');
		$this->db->from('bed_linen_images');
		$this->db->where('category_id', $category_id);
		$this->db->order_by('id', 'desc');
		return $this->db->get()->result();
	}

	function get_image($id){
		$this->db->select('*');
		$this->db->from('bed_linen_images');
		$this->db->where('id', $id);
		$q = $this->db->get();
		if ($q->num_rows() == 0) return false;
		return $q->row();
	}

	// subcat_id => price for every subcategory currently checked for this image
	function get_leaf_prices_for_image($image_id){
		$this->db->select('leaf_id, price');
		$this->db->from('bed_linen_image_leafs');
		$this->db->where('image_id', $image_id);
		$q = $this->db->get();
		$prices = array();
		foreach ($q->result() as $row) $prices[$row->leaf_id] = $row->price;
		return $prices;
	}

	function insert_image($category_id, $name, $image_path, $thumb_path){
		$data = array(
			'category_id' => $category_id,
			'name' => $name,
			'image_path' => $image_path,
			'thumb_path' => $thumb_path,
			'created_at' => time(),
		);
		$this->db->insert('bed_linen_images', $data);
		return $this->db->insert_id();
	}

	function update_image($image_id, $name, $image_path = null, $thumb_path = null){
		$data = array('name' => $name);
		if ($image_path !== null) {
			$data['image_path'] = $image_path;
			$data['thumb_path'] = $thumb_path;
		}
		$this->db->where('id', $image_id);
		$this->db->update('bed_linen_images', $data);
	}

	// $leaf_prices: array(subcat_id => price) for every checked subcategory
	function set_leafs($image_id, $leaf_prices){
		$this->db->where('image_id', $image_id);
		$this->db->delete('bed_linen_image_leafs');
		foreach ($leaf_prices as $leaf_id => $price) {
			$this->db->insert('bed_linen_image_leafs', array(
				'image_id' => $image_id,
				'leaf_id' => (int)$leaf_id,
				'price' => (float)$price,
			));
		}
	}

	function delete_image($image_id){
		$this->db->where('id', $image_id);
		$this->db->delete('bed_linen_images');
		$this->db->where('image_id', $image_id);
		$this->db->delete('bed_linen_image_leafs');
	}

	// curated images for a subcategory (front-end product grid)
	function get_images_for_subcat($subcat_id){
		$this->db->select('bed_linen_images.*, bed_linen_image_leafs.price AS leaf_price, bed_linen_image_leafs.id AS leaf_row_id');
		$this->db->from('bed_linen_images');
		$this->db->join('bed_linen_image_leafs', 'bed_linen_image_leafs.image_id = bed_linen_images.id');
		$this->db->where('bed_linen_image_leafs.leaf_id', $subcat_id);
		$this->db->order_by('bed_linen_images.id', 'asc');
		return $this->db->get()->result();
	}

	// the single (image, subcategory) pairing behind a public product page -
	// bed_linen_image_leafs.id is the unique, stable identifier used in
	// web/show/bedlinen/<id> and web/buy/bedlinen/<id>, since the old
	// per-pattern tree leaf nodes no longer exist to link to.
	function get_bed_linen_leaf($id){
		$this->db->select('bed_linen_image_leafs.id, bed_linen_image_leafs.leaf_id AS subcat_id, bed_linen_image_leafs.price,
							bed_linen_images.name, bed_linen_images.image_path, bed_linen_images.thumb_path, bed_linen_images.category_id');
		$this->db->from('bed_linen_image_leafs');
		$this->db->join('bed_linen_images', 'bed_linen_images.id = bed_linen_image_leafs.image_id');
		$this->db->where('bed_linen_image_leafs.id', $id);
		$q = $this->db->get();
		if ($q->num_rows() == 0) return false;
		return $q->row();
	}

	// the actual leaf tree node (e.g. "20-1579-blue" under this subcategory)
	// so item pages/links can still be built from the underlying tree node
	function find_leaf_id($subcat_id, $name){
		$this->db->select('id');
		$this->db->from('nav_tree');
		$this->db->where('pid', $subcat_id);
		$this->db->where('name', $name);
		$q = $this->db->get();
		if ($q->num_rows() == 0) return false;
		return $q->row()->id;
	}

	// the curated image for one specific pattern under a subcategory
	// (used to fill the {bed_linen_image} placeholder on the item page)
	function get_image_for_subcat_and_name($subcat_id, $name){
		$this->db->select('bed_linen_images.*, bed_linen_image_leafs.price AS leaf_price');
		$this->db->from('bed_linen_images');
		$this->db->join('bed_linen_image_leafs', 'bed_linen_image_leafs.image_id = bed_linen_images.id');
		$this->db->where('bed_linen_image_leafs.leaf_id', $subcat_id);
		$this->db->where('bed_linen_images.name', $name);
		$q = $this->db->get();
		if ($q->num_rows() == 0) return false;
		return $q->row();
	}
}
?>
