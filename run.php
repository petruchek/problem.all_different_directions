<?php

class All_Different_Directions
{
	var $directions = [];
		
	function __construct()
	{
	}

	public function parse_direction($s)
	{
		//remove double spaces and tidy the string up
		$s = trim(preg_replace('!\s+!', ' ', $s));
		$_p = explode(' ', $s);
		$dir = [];
		$dir['x'] = $_p[0];
		$dir['y'] = $_p[1];
		$dir['commands'] = [];
		for ($i=2;$i<count($_p);$i+=2)
		{
			$dir['commands'][] = [$_p[$i],$_p[$i+1]];
		}
		return $dir;
	}

	public function remember_direction($dir)
	{
		$this->directions[] = $dir;
	}

	private function compute_direction($dir)
	{
		$x = $dir['x'];
		$y = $dir['y'];
		$a = 0;
		
		foreach ($dir['commands'] as $command)
		{
			list ($keyword, $parameter) = $command;
			switch ($keyword)
			{
				case 'start':
					$a = $parameter;
					break;
				case 'turn':
					$a += $parameter;
					break;
				case 'walk':
					$deg = deg2rad($a);
					$x += cos($deg)*$parameter;
					$y += sin($deg)*$parameter;
					break;
			}
		}
		
		return [$x, $y];
	}

	private function get_distance($x1,$y1,$x2,$y2)
	{
		return sqrt(($x1-$x2)**2+($y1-$y2)**2);
	}

	public function get_average_destination()
	{
		$n = count($this->directions);
		$x_sum = $y_sum = 0;
		for ($i=0;$i<$n;$i++)
		{
			$dir = &$this->directions[$i];
			list($_x,$_y) = $this->compute_direction($dir);
			$dir['dest_x'] = $_x;
			$dir['dest_y'] = $_y;
			$x_sum += $_x;
			$y_sum += $_y;
		}
		$x = $x_sum/$n;
		$y = $y_sum/$n;
		$worst = 0;
		for ($i=0;$i<$n;$i++)
		{
			$offset = $this->get_distance($this->directions[$i]['dest_x'],$this->directions[$i]['dest_y'],$x,$y);
			if ($offset > $worst)
				$worst = $offset;
		}
		return [$x,$y,$worst];
	}
}

$handle = fopen ("php://stdin", "r");

while (true)
{
	fscanf($handle, "%i", $t);
	if (!$t)
		break;

	$alldd = new All_Different_Directions();
	
	for ($i=0;$i<$t;$i++)
	{
		$s = fgets($handle);
		$dir = $alldd->parse_direction($s);
		$alldd->remember_direction($dir);
	}

	$destination = $alldd->get_average_destination();
	printf("%.4f %.4f %.4f\n",$destination[0],$destination[1],$destination[2]);
//	print_r($destination);
}