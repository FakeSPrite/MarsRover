<?php
require_once( "../class/class.MR.php");

$validDirections = 'NSEW';
$northDirection = 'N';
$southDirection = 'S';
$eastDirection = 'E';
$westDirection = 'W';
$validCommands = 'LRM';
$leftCommand = 'L';
$rightCommand = 'R';
$moveCommand = 'M';
$format_error = false;


		$MR = new MR();

	$MR->commandInterpretation($_POST['command']);


include('../view/MR.html');

?>
