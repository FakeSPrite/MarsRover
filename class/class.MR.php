<?php
class MR{

	private $upperX = 0;
	private $upperY = 0;
	private $currentX = 0;
	private $currentY = 0;
	private $direction = null;
	private $isDebugChecked = false;


	public function getValue($value) {
		return $this->$value;
	}


	public function moveAction() {
		global $northDirection, $eastDirection, $southDirection, $westDirection;
		switch ($this->direction) {
			case $northDirection:
					$this->currentY = $this->currentY + 1;
				break;
			case $eastDirection:
				$this->currentX = $this->currentX + 1;
				break;
			case $southDirection:
					$this->currentY = $this->currentY - 1;
				break;
			case $westDirection:
						$this->currentX = $this->currentX - 1;
				break;
		}
	}
	public function spinAction($d)
	{
		global $validDirections;
		$this->direction = ( (strpos($validDirections, $d) >= 0)) ? $d : $this->direction;
	}


	public function commandAction($com)
	{
		global $leftCommand, $rightCommand, $moveCommand, $northDirection, $westDirection, $southDirection, $eastDirection;

		switch ($com) {
			case $leftCommand:
				switch ($this->direction) {
					case $northDirection:
						$this->spinAction($westDirection);
						break;
					case $westDirection:
						$this->spinAction($southDirection);
						break;
					case $southDirection:
						$this->spinAction($eastDirection);
						break;
					case $eastDirection:
						$this->spinAction($northDirection);
						break;
				}
				break;
			case $rightCommand:
				switch ($this->direction) {
					case $northDirection:
						$this->spinAction($eastDirection);
						break;
					case $eastDirection:
						$this->spinAction($southDirection);
						break;
					case $southDirection:
						$this->spinAction($westDirection);
						break;
					case $westDirection:
						$this->spinAction($northDirection);
						break;
				}
				break;
			case $moveCommand:
				$this->moveAction();
				break;
		}
	}
	
	public function commandInterpretation($com)
	{
		global $validDirections,$validCommands,$format_error;

		$this->upperX = $_POST["upperX"];
		$this->upperY = $_POST["upperY"];
		$this->currentX = $_POST["currentX"];
		$this->currentY = $_POST["currentY"];
		$this->direction = $_POST["direction"];
		$this->isDebugChecked = $_POST["isDebugChecked"];

		$items = array();
		$com_array = explode(" ",preg_replace('/\s+/',' ', trim($com)));//trim the extra space
	
		foreach ($com_array as $single_com) 
		{

			/*if this command is a bunch of series moves:*/
			if (strlen($single_com) > 1)
			{
				foreach (str_split($single_com) as $move) 
				{
					$this->commandAction($move);
				}
			}
			else
			{
				/*This is for assign upper-right coordinates or original position*/
				if (is_numeric($single_com))
				{
					array_push($items, $single_com);
					//This is set upper-right coordinates
					if(count($com_array) == 2 && count($items)==2){
						list($this->upperX,$this->upperY) = $items;
					}
					//This is set original position
					elseif(count($com_array) == 3 && count($items)==2)
					{
						list($this->currentX,$this->currentY) = $items;
					}
				}
				else
				{
					//this is set direction
					if (strpos($validDirections,$single_com) !== false)
					{
						$this->direction = $single_com;
					}
					else
					{
						//this is doing one move
						if (strpos($validCommands,$single_com) !== false) {
							$this->commandAction($single_com);
						}
					}
				}
			}
		}
	}

}
