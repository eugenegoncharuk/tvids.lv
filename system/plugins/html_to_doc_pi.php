<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function doc_create($html, $filename)
{
	include("html_to_doc/html_to_doc.inc.php");
	$htmltodoc = new HTML_TO_DOC();
	$htmltodoc->createDoc($html, $filename, true);
}	
?>