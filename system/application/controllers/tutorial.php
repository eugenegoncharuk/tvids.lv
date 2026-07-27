<?php

class Tutorial extends controller
{

	function Tutorial()
	{
		parent::Controller();
		
		$this->load->helper('url');
	}
	
	
	function index() 
	{	
		$this->hello_world();
	}


	function hello_world()
	{
		$this->load->plugin('html_to_doc');
		$html =  $this->load->view('kvit', array(), TRUE);
		doc_create($html, 'test');
	}
}
?>