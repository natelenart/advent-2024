<?php

class Grid {
    public array $grid;
    public Point $curr;
    public string $dir;
    public bool $valid;
    public bool $looping;

    public function __construct($grid, $obsX = null, $obsY = null) {
        $this->valid = true;
        $this->looping = false;

        $this->grid = [];
        foreach ($grid as $row => $line) {
            foreach ($line as $col => $sq) {
                $point = new Point($col, $row, $sq);
                if ($sq === '^' || $sq === '>' || $sq === '<' || $sq === 'v') {
                    $this->dir = $sq;
                    $point->visit($this->dir);
                    $this->curr = $point;
                }
                if ($obsX !== null && $obsY !== null
                    && $obsX === $col && $obsY === $row
                ) {
                    if (in_array($point->value, ['#', '^', '>', '<', 'v'])) {
                        $this->valid = false;
                    } else {
                        $point->value = 'O';
                    }
                }
                $this->grid[$row][$col] = $point;
            }
        }
    }

    public function get($x, $y) {
        if (isset($this->grid[$y][$x])) {
            return $this->grid[$y][$x];
        }

        return false;
    }

    public function move($partB = false) {
        $x = $this->curr->x;
        $y = $this->curr->y;
        switch ($this->dir) {
            case '^':
                $y--;
                break;
            case '>':
                $x++;
                break;
            case '<':
                $x--;
                break;
            case 'v':
                $y++;
                break;
        }

        $pt = $this->get($x, $y);
        if ($pt instanceof Point) {
            if ($partB) {
                if ($pt->visited && $pt->visit_dir === $this->dir) {
                    $this->looping = true;
                    $this->valid = false;    
                }
            }
            $pt->visit($this->dir);
            $this->curr = $pt;
            do {
                $changed = $this->changeDir();
            } while ($changed);
        } else {
            $this->valid = false;
        }
    }

    public function changeDir() {
        $changed = false;

        $x = $this->curr->x;
        $y = $this->curr->y;
        switch ($this->dir) {
            case '^':
                $pt = $this->get($x, $y-1);
                if ($pt instanceof Point && $pt->obstructed()) {
                    $this->dir = '>';
                    $changed = true;
                }
                break;
            case '>':
                $pt = $this->get($x+1, $y);
                if ($pt instanceof Point && $pt->obstructed()) {
                    $this->dir = 'v';
                    $changed = true;
                }
                break;
            case '<':
                $pt = $this->get($x-1, $y);
                if ($pt instanceof Point && $pt->obstructed()) {
                    $this->dir = '^';
                    $changed = true;
                }
                break;
            case 'v':
                $pt = $this->get($x, $y+1);
                if ($pt instanceof Point && $pt->obstructed()) {
                    $this->dir = '<';
                    $changed = true;
                }
                break;
        }

        return $changed;
    }

    public function p() {
        foreach ($this->grid as $row) {
            foreach ($row as $pt) {
                if ($this->curr == $pt) {
                    echo $this->dir;
                } else {
                    echo $pt->visited ? 'X' : $pt->value;
                }
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }
}

class Point {
    public int $x;
    public int $y;
    public string $value;
    public bool $visited;
    public string $visit_dir;

    public function __construct($x, $y, $value) {
        $this->x = $x;
        $this->y = $y;
        $this->value = $value;
        $this->visited = false;
    }

    public function visit($dir) {
        $this->visited = true;
        $this->visit_dir = $dir;
    }

    public function obstructed() {
        return $this->value === '#' || $this->value === 'O';
    }
}

$fh = fopen('6-input.txt', 'r');

$contents = [];
do {
    $line = trim(fgets($fh));
    if ($line !== false && $line !== '') {
        $contents[] = str_split($line);
    }
} while ($line !== false && $line !== '');

fclose($fh);

$grid = new Grid($contents);
do {
    $grid->move();
} while ($grid->valid);

$cnt = 0;
foreach ($grid->grid as $row) {
    foreach ($row as $pt) {
        if ($pt->visited) {
            $cnt++;
        }
    }
}

echo 'Part A: ' . $cnt . PHP_EOL; // 4982 is correct

$rows = count($grid->grid);
$cols = count($grid->grid[0]);

$cnt = 0;
for ($i=0; $i<$rows; $i++) {
    for ($j=0; $j<$cols; $j++) {
        $grid = new Grid($contents, $j, $i);

        if (!$grid->valid) {
            unset($grid);
            continue;
        }

        do {
            $grid->move(true);
        } while ($grid->valid);

        if ($grid->looping) {
            $cnt += 1;
        }
        
        unset($grid);
    }
}

// 1601 is too low

echo 'Part B: ' . $cnt . PHP_EOL; // 1663 is correct