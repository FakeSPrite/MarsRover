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
				//if rover have not reached the upper right ege,move 1 step,antherwise stay the same
				if($this->currentY<$this->upperY)
				{
					$this->currentY = $this->currentY + 1;
					$this->debugAction("Move 1 to N->$this->currentX  $this->currentY $this->direction");
				}
				else
				{
					$this->debugAction("Reached the edge,stay same -> $this->currentX  $this->currentY $this->direction");
				}
				break;
			case $eastDirection:
				//if rover have not reached the upper right ege,move 1 step,antherwise stay the same
				if($this->currentX<$this->upperX)
				{
				$this->currentX = $this->currentX + 1;
				$this->debugAction("Move 1 to E->$this->currentX  $this->currentY $this->direction");
				}
				else
				{
					$this->debugAction("Reached the edge,stay same -> $this->currentX  $this->currentY $this->direction");
				}
				break;
			case $southDirection:
				//if rover have not reached the bottom left corner,move 1 step,antherwise stay the same
				if($this->currentY>0)
				{
					$this->currentY = $this->currentY - 1;
					$this->debugAction("Move 1 to S->$this->currentX  $this->currentY $this->direction");
				}
				else
				{
					$this->debugAction("Reached the edge,stay same -> $this->currentX  $this->currentY $this->direction");
				}
				break;
			case $westDirection:
				//if rover have not reached the bottom left corner,move 1 step,antherwise stay the same
				if($this->currentX>0)
					{
						$this->currentX = $this->currentX - 1;
						$this->debugAction("Move 1 to W->$this->currentX  $this->currentY $this->direction");
					}
					else
					{
						$this->debugAction("Reached the edge,stay same -> $this->currentX  $this->currentY $this->direction");
					}
				break;
		}
	}
	public function spinAction($d)
	{
		global $validDirections;
		$this->direction = ( (strpos($validDirections, $d) >= 0)) ? $d : $this->direction;
		$this->debugAction("Now direction is : $this->direction");
	}

	public function debugAction($msg)
	{
		if ($this->isDebugChecked)
		{
			echo $msg.'<BR/>';
		}
	}

	public function commandAction($com)
	{
		global $leftCommand, $rightCommand, $moveCommand, $northDirection, $westDirection, $southDirection, $eastDirection;

		switch ($com) {
			case $leftCommand:
				switch ($this->direction) {
					case $northDirection:
						$this->debugAction("spin from N to W");
						$this->spinAction($westDirection);
						break;
					case $westDirection:
						$this->debugAction("spin from W to S");
						$this->spinAction($southDirection);
						break;
					case $southDirection:
						$this->debugAction("spin from W to S");
						$this->spinAction($eastDirection);
						break;
					case $eastDirection:
						$this->debugAction("spin from E to N");
						$this->spinAction($northDirection);
						break;
				}
				break;
			case $rightCommand:
				switch ($this->direction) {
					case $northDirection:
						$this->debugAction("spin from N to E");
						$this->spinAction($eastDirection);
						break;
					case $eastDirection:
						$this->debugAction("spin from E to S");
						$this->spinAction($southDirection);
						break;
					case $southDirection:
						$this->debugAction("spin from S to W");
						$this->spinAction($westDirection);
						break;
					case $westDirection:
						$this->debugAction("spin from W to N");
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
		$this->debugAction("Original: $this->currentX  $this->currentY $this->direction");
	
		foreach ($com_array as $single_com) 
		{
			// checking the input format is digital,direction or MOVE action
			if(strpos('0123456789'.$validCommands,$single_com[0]) === false && strpos($validDirections,$single_com) === false )
			{
				$format_error = true;
				 return;
			}
			else
			{
				$format_error = false;
			}

			/*if this command is a bunch of series moves:*/
			if (strlen($single_com) > 1)
			{
				$this->debugAction("this is a move command:".$single_com);
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
						$this->debugAction("this is an upper-right coordinates setting command :$this->upperX $this->upperY");
					}
					//This is set original position
					elseif(count($com_array) == 3 && count($items)==2)
					{
						list($this->currentX,$this->currentY) = $items;
						$this->debugAction("this is an original position setting command :$this->currentX $this->currentY");
					}
				}
				else
				{
					//this is set direction
					if (strpos($validDirections,$single_com) !== false)
					{
						$this->direction = $single_com;
						$this->debugAction("direction setting :$this->direction");
					}
					else
					{
						//this is doing one move
						if (strpos($validCommands,$single_com) !== false) {
							$this->debugAction("Give one move command for move or spin: $single_com");
							$this->commandAction($single_com);
						}
					}
				}
			}
		}
	}

}
