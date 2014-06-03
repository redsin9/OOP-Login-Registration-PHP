<?php

class Validation
{
	private $mPassed = true;
	private $mErrors = array();
	private $mDB = null;



	public function __construct()
	{
		$this->mDB = DB::GetInstance();
	}



	public function Check ($source, $items = array())
	{
		$mPassed = true;

		foreach ($items as $item => $rules) 
		{
			// get value submited
			$value = $source[$item];

			foreach ($rules as $rule => $rule_value) 
			{
				switch ($rule) 
				{
					case 'required':
						if (empty($value))
						{
							$this->mPassed = false;
							$this->AddError("{$item} is required");
						}
						break;

					case 'min':
						if (strlen($value) < $rule_value)
						{
							$this->mPassed = false;
							$this->AddError("{$item} must be minimum of {$rule_value} characters");
						}
						break;

					case 'max':
						if (strlen($value) > $rule_value)
						{
							$this->mPassed = false;
							$this->AddError("{$item} must be maximum of {$rule_value} characters");
						}
						break;

					case 'matches':
						if ($source[$item] != $source[$rule_value])
						{
							$this->mPassed = false;
							$this->AddError("{$item} must match {$rule_value}");
						}
						break;

					case 'unique':
						$this->mDB->Get($rule_value, array($item, '=', $value));
						if ($this->mDB->Count())
						{
							$this->mPassed = false;
							$this->AddError("{$item} {$value} already existed");
						}
						break;
				}

				
			}
		}

		return $this->mPassed;
	}



	private function AddError($error)
	{
		$this->mErrors[] = $error;
	}



	public function Errors()
	{
		return $this->mErrors;
	}



	public function Passed()
	{
		return $this->mPassed;
	}
}


?>

