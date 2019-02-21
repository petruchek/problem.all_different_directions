<?php

/*
 * This file is part of the https://github.com/petruchek/problem.all_different_directions
 *
 * @author Val Petruchek <petruchek@gmail.com>
 */

class All_Different_Directions
{
	var $directions = [];
		
	function __construct()
	{
	}

	private function parse_direction($s)
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

	public function process_direction($s)
	{
		$dir = $this->parse_direction($s);
	    $destination = $this->compute_direction($dir);
		list($dir['end_x'], $dir['end_y']) = $destination; 
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
			$x_sum += $this->directions[$i]['end_x'];
			$y_sum += $this->directions[$i]['end_y'];
		}
		$x = $x_sum/$n;
		$y = $y_sum/$n;
		$worst = 0;
		for ($i=0;$i<$n;$i++)
		{
			$offset = $this->get_distance($this->directions[$i]['end_x'],$this->directions[$i]['end_y'],$x,$y);
			if ($offset > $worst)
				$worst = $offset;
		}
		return [$x,$y,$worst];
	}
}
