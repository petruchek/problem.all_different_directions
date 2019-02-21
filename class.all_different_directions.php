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

	/**
	 * Parse input string into array into special structure [x,y,[commands]]
	 *
	 * @param string $s one line containing both person's location [x,y] and that person's directions
	 * @return array
	 */
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

	/**
	 * Process direction from single person by parsing it and computing that person's direction.
	 *
	 * Saves destination coordinates [end_x, end_y] into that same array of that person.
	 *
	 * @param string $s one line containing both person's location [x,y] and that person's directions
	 * @return void
	 */
	public function process_direction($s)
	{
		$dir = $this->parse_direction($s);
		$destination = $this->compute_direction($dir);
		list($dir['end_x'], $dir['end_y']) = $destination; 
		$this->directions[] = $dir;
	}

	/**
	 * Compute single person's destination from their position and set of commands
	 *
	 * @param array $dir [x,y,[commands]] special structure as returned by parse_direction() 
	 * @return array [x,y] destination coordinates
	 */
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

	/**
	 * The power of linear algebra - get distance between two points.
	 *
	 * @param float $x1
	 * @param float $y1
	 * @param float $x2
	 * @param float $y2
	 * @return float
	 */
	private function get_distance($x1,$y1,$x2,$y2)
	{
		return sqrt(($x1-$x2)**2+($y1-$y2)**2);
	}

	/**
	 * 1. Calculate the average point of all advised destinations.
	 *
	 * 2. Calculate the worst offset among all advised destinations.
	 *
	 * @return array [x,y,worst]
	 */
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
