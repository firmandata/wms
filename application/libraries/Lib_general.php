<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Lib_general
{
	public function set_model_fields_values($model, $data)
	{
		foreach ($data as $field=>$value)
		{
			$this->set_model_field_value($model, $field, $value);
		}
	}
	
	public function set_model_field_value($model, $field, $value)
	{
		if ($value === '')
		{
			$model->$field = NULL;
		}
		else
		{
			if ($value === NULL)
				$model->$field = NULL;
			else
			{
				if (is_string($value))
					$model->$field = trim($value);
				else
					$model->$field = $value;
			}
		}
	}	
}