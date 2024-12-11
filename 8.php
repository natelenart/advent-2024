<?php

function dist(Point $a, Point $b) {
    return [
        $a->x - $b->x,
        $a->y - $b->y,
    ];
}

class Point {
    public int $x;
    public int $y;
    public bool $anti;
    public ?string $value;

    public function __construct($x, $y, $value = null) {
        $this->x = $x;
        $this->y = $y;
        $this->anti = false;
        $this->value = $value;
    }

    public function p() {
        echo $this->x . ' | ' . $this->y . PHP_EOL;
    }

    public function dist(Point $b) {
        return dist($this, $b);
    }
}

class Grid
{
    public array $grid;
    public array $index;

    public function __construct() {
        $this->grid = [];
        $this->index = [];
    }

    public function addLine($i, $line) {
        $row = [];
        foreach (str_split($line) as $j => $item) {
            $pt = new Point($i, $j, $item);
            $row[$j] = $pt;
            if ($item !== '.') {
                if (!isset($this->index[$item])) {
                    $this->index[$item] = [];
                }
                $this->index[$item][] = $pt;
            }
        }

        $this->grid[] = $row;
    }

    public function get($x, $y) {
        if (isset($this->grid[$x][$y])) {
            return $this->grid[$x][$y];
        }

        return false;
    }

    public function p() {
        foreach ($this->grid as $row) {
            foreach ($row as $pt) {
                if ($pt->anti) {
                    echo '#';
                } else {
                    echo $pt->value;
                }
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }
}

$fh = fopen('8-input.txt', 'r');

$l = 0;
$grid = new Grid();
do {
    $line = trim(fgets($fh));
    if ($line !== false && $line !== '') {
        $grid->addLine($l, $line);
        $l++;
    }
} while ($line !== false && $line !== '');

fclose($fh);

$antis = [];
foreach ($grid->grid as $i => $row) {
    foreach ($row as $j => $pt) {
        $v = $pt->value;
        if ($v !== '.') {
            $locations = $grid->index[$v];
            foreach ($locations as $b) {
                if ($pt !== $b) {
                    $d = $pt->dist($b);
                    
                    $x = $pt->x + $d[0];
                    $y = $pt->y + $d[1];

                    $n = $grid->get($x, $y);
                    if ($n) {
                        $n->anti = true;
                        $antis[$x.'|'.$y] = $n;
                    }
                }
            }
        }
    }
}

echo 'Part A: ' . count($antis) . PHP_EOL;

$grid->p();

$antis = [];
foreach ($grid->grid as $i => $row) {
    foreach ($row as $j => $pt) {
        $v = $pt->value;
        if ($v !== '.') {
            $locations = $grid->index[$v];
            foreach ($locations as $b) {
                if ($pt !== $b) {
                    $x = $pt->x;
                    $y = $pt->y;

                    $pt->anti = true;
                    $antis[$x.'|'.$y] = $pt;

                    $d = $pt->dist($b);
                    
                    do {
                        $x = $x + $d[0];
                        $y = $y + $d[1];

                        $n = $grid->get($x, $y);
                        if ($n) {
                            $n->anti = true;
                            $antis[$x.'|'.$y] = $n;
                        }
                    } while ($n !== false);
                }
            }
        }
    }
}

$grid->p();

echo 'Part B: ' . count($antis) . PHP_EOL;