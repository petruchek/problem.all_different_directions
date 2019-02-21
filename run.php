<?php

/*
 * This file is part of the https://github.com/petruchek/problem.all_different_directions
 *
 * @author Val Petruchek <petruchek@gmail.com>
 */

require_once(__DIR__."/class.all_different_directions.php");

$handle = fopen ("php://stdin", "r");

while (true)
{
	fscanf($handle, "%d", $t);
	if (!$t)
		break;

	$alldd = new All_Different_Directions();
	
	for ($i=0;$i<$t;$i++)
	{
		$s = fgets($handle);
		$alldd->process_direction($s);
	}

	$destination = $alldd->get_average_destination();
	printf("%.4f %.4f %.4f\n",$destination[0],$destination[1],$destination[2]);
}