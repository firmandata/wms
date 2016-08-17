<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Excel
{
	protected $CI;
	
	function __construct()
    {
		require_once APPPATH."third_party/phpexcel/PHPExcel.php";
		require_once APPPATH."third_party/phpexcel/PHPExcel/IOFactory.php";
		require_once APPPATH."third_party/phpexcel/PHPExcel/Cell.php";
		
		$this->CI =& get_instance();
	}
	
	/**
		Read an excel file or stream to array
		Input : 
			$file_address : file location in your server (if read via file)
			$file_stream : stream variable of excel file (if read via stream variable)
			$sheet_name : if set to sheet's name you'll get that sheet only
				set array('Sheet1', 'Sheet2') to open multiple sheet
				for default, we read the active sheet
		Output : 
			array
	*/
	protected function read($file_address = NULL, $file_stream = NULL, $sheet_name = NULL)
	{
		$result = array();
		try 
		{
			/* -- Identified the file type -- */
			$file_type = PHPExcel_IOFactory::identify($file_address);
			
			/* -- Load the file based on file type -- */
			$file_reader = PHPExcel_IOFactory::createReader($file_type);
			$file_reader->setReadDataOnly(TRUE);
			if ($sheet_name != NULL)
				$file_reader->setLoadSheetsOnly($sheet_name);
			$file_excel = $file_reader->load($file_address);
			
			/* -- Read the sheets -- */
			if ($sheet_name != NULL && is_array($sheet_name))
			{
				foreach($sheet_name as $index=>$sheet)
				{
					$file_excel->setActiveSheetIndex($index);
					$sheet = $file_excel->getActiveSheet();
					$result[$sheet_name] = $this->_read_sheet($sheet);
				}
			}
			else
			{
				$sheet_names = $file_excel->getSheetNames();
				foreach ($sheet_names as $sheet_name_idx=>$sheet_name)
				{
					$file_excel->setActiveSheetIndex($sheet_name_idx);
					$sheet = $file_excel->getActiveSheet();
					$result[$sheet_name] = $this->_read_sheet($sheet);
				}
			}
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		
		return $result;
	}
	
	/**
		Read an excel file to array
		Input : 
			$file_address : file location in your server
			$sheet_name : if set to sheet's name you'll get that sheet only
				set array('sheet 1', 'sheet 2') to open multiple sheet
				for default, we read the active sheet
		Output : 
			array
	*/
	public function read_file($file_address, $sheet_name = NULL)
	{
		$result = NULL;
		if (file_exists($file_address))
		{
			$result = $this->read($file_address, NULL, $sheet_name);
		}
		else
		{
			throw new Exception("File not found '$file_address'");
		}
		return $result;
	}
	
	public function read_file_acos($file_address, $sheet_name = NULL)
	{
		$result = array();
		
		$excel_data = $this->read_file($file_address, $sheet_name);
		foreach ($excel_data as $sheet=>$rows)
		{
			$header = NULL;
			$data = array();
			foreach($rows as $index_row=>$row)
			{
				if ($index_row == 0)
				{
					$header = $row;
				}
				else
				{
					if ($header != NULL && is_array($header))
					{
						$row_data = array();
						foreach ($header as $index_head=>$head)
						{
							if ($head != '')
								$row_data[$head] = $row[$index_head];
						}
						$data[] = $row_data;
					}
				}
			}
			$result[$sheet] = $data;
		}
		
		return $result;
	}
	
	public function read_value($sheet_data, $row, $col, $type = 'string')
	{
		if (!isset($sheet_data[$row][$col]))
			return NULL;
		
		$value = $sheet_data[$row][$col];
		if ($value === '')
			return NULL;
		
		if ($type == 'date')
			return $this->read_value_as_date_string($value);
		else
			return trim($value);
	}
	
	public function read_value_as_date_string($value, $format = 'YYYY-MM-DD')
	{
		return PHPExcel_Style_NumberFormat::toFormattedString($value, "YYYY-MM-DD");
	}
	
	private function _read_sheet($sheet)
	{
		$result = array();
		
		$end_row = $sheet->getHighestRow(); // num of row like 10
		$end_col = $sheet->getHighestColumn(); // name of columns like F
		$end_col_index = PHPExcel_Cell::columnIndexFromString($end_col); // like 5
		for ($row = 1; $row <= $end_row; ++$row)
		{
			for ($col = 0; $col <= $end_col_index; ++$col)
			{
				$value = $sheet->getCellByColumnAndRow($col, $row)->getValue();
				$result[$row - 1][$col] = $value;
			}
		}
		
		return $result;
	}
	
	/**
		Read an excel file in stream to array
		Input : 
			$file_stream : stream variable of excel file
			$sheet_name : if set to sheet's name you'll get that sheet only
				set array('sheet 1', 'sheet 2') to open multiple sheet
				for default, we read the active sheet
		Output : 
			array
	*/
	public function read_stream($file_stream, $sheet_name = NULL)
	{
		return $this->read(NULL, $file_stream, $sheet_name);
	}
	
	/**
		Write an excel file or stream from array
		Input :
			$data : your data in array
			$file_name : excel file name
			$path : impact to result stream if $path is null or file in your server if $path is not null 
		Output :
			Stream if $path is null or file in your server if $path is not null 
	*/
	protected function write($data, $file_name, $is_from_acos = FALSE, $acos_caption = array())
	{
		$result = NULL;
		try 
		{
			$this->CI->load->helper('date');
			
			/* -- Identified the file type -- */
			$file_names = explode(".", $file_name);
			$file_ext = trim(end($file_names));
			$file_type = NULL;
			switch (strtolower($file_ext)) {
				case 'xlsx':
					$file_type = 'Excel2007';
					break;
				case 'xls':
					$file_type = 'Excel5';
					break;
				case 'ods':
					$file_type = 'OOCalc';
					break;
				case 'slk':
					$file_type = 'SYLK';
					break;
				case 'xml':
					$file_type = 'Excel2003XML';
					break;
				case 'gnumeric':
					$file_type = 'Gnumeric';
					break;
				case 'csv':
					break;
				default:
					$file_type = 'Excel5';
					break;
			}
			
			/* -- Creating the data -- */
			$file_excel = new PHPExcel();
			
			$index_sheet = 0;
			foreach ($data as $sheet_name=>$rows)
			{
				$file_excel->setActiveSheetIndex($index_sheet);
				$sheet = $file_excel->getActiveSheet();
				$sheet->setTitle($sheet_name);
				
				if ($is_from_acos == FALSE)
				{
					foreach ($rows as $row_index=>$cells)
					{
						foreach ($cells as $col_index=>$value)
						{
							/* -- Set Value -- */
							$sheet->setCellValueByColumnAndRow($col_index, $row_index + 1, $value);
						}
					}
				}
				else
				{
					$header = array();
					if (count($acos_caption) > 0 && isset($acos_caption[$sheet_name]))
						$header = $acos_caption[$sheet_name];
					
					foreach ($rows as $row_index=>$cells)
					{
						$col_index = 0;
						
						if (count($header) > 0)
						{
							foreach ($header as $field_name=>$caption)
							{
								/* -- Build Header -- */
								if ($row_index == 0)
								{
									$sheet->setCellValueByColumnAndRow($col_index, 1, $caption);
								}
								/* -- Set Value -- */
								if (isset($cells->$field_name))
								{
									/* -- Date converter -- */
									$is_date_time = FALSE;
									
									if (is_server_datetime($cells->$field_name) !== FALSE)
									{
										// $cells->$field_name = PHPExcel_Shared_Date::PHPToExcel(strtotime($cells->$field_name));
										$date_value = strtotime($cells->$field_name);
										$cells->$field_name = PHPExcel_Shared_Date::FormattedPHPToExcel(
											date('Y', $date_value), date('n', $date_value), date('j', $date_value), 
											date('G', $date_value), (int)date('i', $date_value), (int)date('s', $date_value)
										);
										$is_date_time = TRUE;
									}
									
									$sheet->setCellValueByColumnAndRow($col_index, $row_index + 2, $cells->$field_name);
									
									/* -- Date converter -- */
									if ($is_date_time == TRUE)
									{
										$cell_name = PHPExcel_Cell::stringFromColumnIndex($col_index);
										$sheet->getStyle($cell_name.($row_index + 2))->getNumberFormat()->setFormatCode('dd-mm-yyyy');
									}
								}
								else
									$sheet->setCellValueByColumnAndRow($col_index, $row_index + 2, '');
								
								$col_index++;
							}
						}
						else
						{
							foreach ($cells as $caption=>$value)
							{
								/* -- Build Header -- */
								if ($row_index == 0)
								{
									$sheet->setCellValueByColumnAndRow($col_index, 1, $caption);
								}
								
								/* -- Date converter -- */
								$is_date_time = FALSE;
								if (is_server_datetime($value) !== FALSE)
								{
									// $value = PHPExcel_Shared_Date::PHPToExcel(strtotime($value));
									$date_value = strtotime($value);
									$value = PHPExcel_Shared_Date::FormattedPHPToExcel(
										date('Y', $date_value), date('n', $date_value), date('j', $date_value), 
										date('G', $date_value), (int)date('i', $date_value), (int)date('s', $date_value)
									);
									$is_date_time = TRUE;
								}
								
								/* -- Set Value -- */
								$sheet->setCellValueByColumnAndRow($col_index, $row_index + 2, $value);
								
								/* -- Date converter -- */
								if ($is_date_time == TRUE)
								{
									$cell_name = PHPExcel_Cell::stringFromColumnIndex($col_index);
									$sheet->getStyle($cell_name.($row_index + 2))->getNumberFormat()->setFormatCode('dd-mm-yyyy');
								}
								
								$col_index++;
							}
						}
					}
				}
				
				$index_sheet++;
			}
			
			/* -- Write the file based on file type -- */
			$file_writer = PHPExcel_IOFactory::createWriter($file_excel, $file_type);
			
			$result = $file_writer;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		
		return $result;
	}
	
	/**
		Write an excel file from array
		Input :
			$data : your data in array
			$file_name : excel file name
			$path : path the file in your server
		Output :
			Stream if $path is null or file in your server if $path is not null 
	*/
	public function write_file($data, $file_name, $path)
	{
		$file_writer = $this->write($data, $file_name);
		
		/* -- Output by file on server -- */
		$file_writer->save($path.$file_name);
	}
	
	public function write_file_acos($data, $file_name, $path, $acos_caption = array())
	{
		$file_writer = $this->write($data, $file_name, TRUE, $acos_caption);
		
		/* -- Output by file on server -- */
		$file_writer->save($path.$file_name);
	}
	
	/**
		Write an excel stream variable from array
		Input :
			$data : your data in array
			$file_name : excel file name
		Output :
			Stream variable
	*/
	public function write_stream($data, $file_name)
	{
		$file_writer = $this->write($data, $file_name);
		
		/* -- Output by streaming -- */
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$file_name.'"');
		header('Cache-Control: max-age=0');

		$file_writer->save('php://output'); 
	}
	
	public function write_stream_acos($data, $file_name, $acos_caption = array())
	{
		$file_writer = $this->write($data, $file_name, TRUE, $acos_caption);
		
		/* -- Output by streaming -- */
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$file_name.'"');
		header('Cache-Control: max-age=0');

		$file_writer->save('php://output'); 
	}
}