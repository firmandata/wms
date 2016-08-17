<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Share extends CI_Controller 
{
	public function barcode()
	{
		$value = $this->input->get_post('value');
		$type = $this->input->get_post('type');
		
		$scale = $this->input->get_post('scale');
		if (!$scale)
			$scale = 2;
		
		$thickness = $this->input->get_post('thickness');
		if (!$thickness)
			$thickness = 30;
		
		$image_type = $this->input->get_post('image_type');
		if (!$image_type)
			$image_type = 'jpeg';
		
		$this->load->library('lib_barcode');
		
		$this->lib_barcode->setImageType($image_type);
		if ($type == 'code39')
			$this->lib_barcode->code39($value, $scale, $thickness);
		else
			$this->lib_barcode->code128($value, $scale, $thickness);
	}
}