<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Lib_barcode
{
	protected $CI;
	
	protected $show_text = FALSE;
	protected $image_type = 'png';
	protected $colorBack;
	protected $colorFront;
	
	public function __construct()
	{
		$this->CI =& get_instance();
		
		require_once(APPPATH.'third_party/phpbarcode/BCGColor.php');
		require_once(APPPATH.'third_party/phpbarcode/BCGDrawing.php');
		
		$this->setColorFront(0, 0, 0);
		$this->setColorBack(255, 255, 255);
	}
	
	public function setShowText()
	{
		$this->show_text = TRUE;
	}
	
	public function setHideText()
	{
		$this->show_text = TRUE;
	}
	
	public function setImageType($image_type)
	{
		$this->image_type = $image_type;
	}
	
	public function setColorBack($r, $g, $b)
	{
		$this->colorBack = new BCGColor($r, $g, $b);
	}
	
	public function setColorFront($r, $g, $b)
	{
		$this->colorFront = new BCGColor($r, $g, $b);
	}
	
	public function code39($value, $scale = 2, $thickness = 30)
	{
		require_once(APPPATH.'third_party/phpbarcode/BCGcode39.barcode.php');
		
		$code = new BCGcode39(); // Or another class name from the manual
		$code->setScale($scale); // Resolution
		$code->setThickness($thickness); // Thickness
		$code->setForegroundColor($this->colorFront); // Color of bars
		$code->setBackgroundColor($this->colorBack); // Color of spaces
		if ($this->show_text !== TRUE)
			$code->setFont(0);
		$code->parse($value); // Text
		
		$this->_showImage($code);
	}
	
	public function code128($value, $scale = 2, $thickness = 30)
	{
		require_once(APPPATH.'third_party/phpbarcode/BCGcode128.barcode.php');
		
		$code = new BCGcode128(); // Or another class name from the manual
		$code->setScale($scale); // Resolution
		$code->setThickness($thickness); // Thickness
		$code->setForegroundColor($this->colorFront); // Color of bars
		$code->setBackgroundColor($this->colorBack); // Color of spaces
		if ($this->show_text !== TRUE)
			$code->setFont(0);
		$code->parse($value); // Text
		
		$this->_showImage($code);
	}
	
	private function _showImage($code)
	{
		$drawing = new BCGDrawing('', $this->colorBack);
		$drawing->setBarcode($code);
		$drawing->draw();
		
		if ($this->image_type == 'png')
		{
			$this->CI->output->set_header('Content-Type: image/png');
			$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
		}
		elseif ($this->image_type == 'gif')
		{
			$this->CI->output->set_header('Content-Type: image/gif');
			$drawing->finish(BCGDrawing::IMG_FORMAT_GIF);
		}
		else
		{
			$this->CI->output->set_header('Content-Type: image/jpeg');
			$drawing->finish(BCGDrawing::IMG_FORMAT_JPEG);
		}
	}
}