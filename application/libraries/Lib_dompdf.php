<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Lib_dompdf
{
	protected $CI;
	protected $dompdf;
	protected $is_show_page_number = FALSE;
	protected $footer_notes = NULL;
	
	public function __construct()
	{
		$this->CI = &get_instance();
		
	    require_once(APPPATH."third_party/dompdf/dompdf_config.inc.php");
		
	    spl_autoload_register('DOMPDF_autoload');
 
	    $this->dompdf = new DOMPDF();
	}
	
	public function load_as_pdf($html, $file_name = 'result.pdf', $paper_size = 'a4', $orientation = 'portrait')
	{
		$this->CI->config->set_item('compress_output', FALSE);
		$encryption_key = $this->CI->config->item('encryption_key');
		
		$this->dompdf->set_paper($paper_size, $orientation);
		
		$this->dompdf->load_html($html);
	    $this->dompdf->render();
		
		if ($this->is_show_page_number)
			$this->_set_page_number();
		
		if (!empty($this->footer_notes))
			$this->_set_footer_notes();
		
		$this->dompdf->get_canvas()->get_cpdf()->setEncryption(NULL, $encryption_key, array('print'));
		
		$this->dompdf->stream($file_name, array("Attachment" => 0));
	}
	
	public function show_page_number($is_show = TRUE)
	{
		$this->is_show_page_number = $is_show;
	}
	
	protected function _set_page_number()
	{
		if (!isset($this->dompdf))
			return;
		
		$pdf = $this->dompdf->get_canvas();
		if (!isset($pdf))
			return;
		
		$font = Font_Metrics::get_font(NULL, 'normal');
		$size = 9;
		$y = $pdf->get_height() - 30;
		$x = $pdf->get_width() - 70 - Font_Metrics::get_text_width('1/1', $font, $size);
		$pdf->page_text($x, $y, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, $size);
	}
	
	public function set_footer_notes($notes)
	{
		$this->footer_notes = $notes;
	}
	
	protected function _set_footer_notes()
	{
		if (!isset($this->dompdf))
			return;
		
		$pdf = $this->dompdf->get_canvas();
		if (!isset($pdf))
			return;
		
		$font = Font_Metrics::get_font(NULL, 'normal');
		$size = 9;
		$y = $pdf->get_height() - 30;
		$x = 30;
		$pdf->page_text($x, $y, $this->footer_notes, $font, $size);
	}
}
