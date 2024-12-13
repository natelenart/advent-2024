<?php

class Point {
    public int $x;
    public int $y;
    public ?int $value;
    public int $score;
    public int $rating;
    public array $paths;
    public ?Point $next;

    public function __construct($x, $y, $value = null) {
        $this->x = $x;
        $this->y = $y;
        $this->value = $value;
        $this->score = 0;
        $this->rating = 0;
        $this->paths = [];
    }

    public function hash() {
        return $this->x . ',' . $this->y;
    }

    public function path() {
        foreach ($this->paths as $path) {
            $p = [];
            foreach ($path as $pt) {
                $p[] = $pt->hash();
            }
            echo implode('|', $p) . PHP_EOL;
        }
    }

    public function getRating() {
        return count($this->paths);
    }

    public function p() {
        echo $this->x . ' | ' . $this->y . ' (' . $this->value . ') = ' . $this->score . PHP_EOL;
    }
}

class Grid
{
    public array $grid;

    public function __construct() {
        $this->grid = [];
    }

    public function addLine($i, $line) {
        $row = [];
        foreach (str_split($line) as $j => $item) {
            if ($item === '.') {
                $item = -1;
            }
            $pt = new Point($i, $j, (int)$item);
            $row[] = $pt;
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
                echo $pt->value;
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }

    public function traverseA($head, $start, &$visited) {
        $visited[$start->hash()] = true;

        $num = $start->value;

        if ($num === 9) {
            $head->score++;

            return;
        }

        if ($num === -1) {
            return;
        }

        $x = $start->x;
        $y = $start->y;

        $positions = [
            $this->get($x+1, $y),
            $this->get($x-1, $y),
            $this->get($x, $y+1),
            $this->get($x, $y-1),
        ];

        foreach ($positions as $pos) {
            if ($pos instanceof Point && !isset($visited[$pos->hash()]) && $pos->value === $num+1) {
                $this->traverseA($head, $pos, $visited);
            }
        }
    }

    public function traverseB($head, $start, &$visited, $chain = []) {
        $chain[] = $start;

        $num = $start->value;

        if ($num === 9) {
            $head->paths[] = $chain;
        }

        $x = $start->x;
        $y = $start->y;

        $positions = [
            $this->get($x+1, $y),
            $this->get($x-1, $y),
            $this->get($x, $y+1),
            $this->get($x, $y-1),
        ];

        foreach ($positions as $pos) {
            if ($pos instanceof Point
                && $pos->value === $num+1
            ) {
                $this->traverseB($head, $pos, $visited, $chain);
            }
        }
    }
}

$fh = fopen('10-input.txt', 'r');

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

$heads = [];
foreach ($grid->grid as $i => $row) {
    foreach ($row as $j => $pt) {
        if ($pt->value === 0) {
            $heads[] = $pt;
        }
    }
}

$s = 0;
foreach ($heads as $head) {
    $visited = [];
    $grid->traverseA($head, $head, $visited);
    $s += $head->score;
}

echo 'Part A: ' . $s . PHP_EOL; // 822 is correct

$r = 0;
foreach ($heads as $head) {
    $visited = [];
    $grid->traverseB($head, $head, $visited);
    $r += $head->getRating();
}

echo 'Part B: ' . $r . PHP_EOL; // 1801