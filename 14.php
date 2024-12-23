<?php

class Point {
    public int $x;
    public int $y;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function matches($x, $y) {
        return $this->x === $x && $this->y === $y;
    }
}

class Velocity {
    public int $x;
    public int $y;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }
}

class Robot {
    public Point $p;
    public Velocity $v;

    public function __construct(Point $p, Velocity $v) {
        $this->p = $p;
        $this->v = $v;
    }
}

class Grid {
    public int $width;
    public int $height;
    public array $robots;

    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
        $this->robots = [];
    }

    public function __clone() {
        $robots = [];
        foreach ($this->robots as $r) {
            $robots[] = new Robot(
                new Point($r->p->x, $r->p->y),
                new Velocity($r->v->x, $r->v->y)
            );
        }
        $this->robots = $robots;
    }

    public function get($x, $y) {
        if (isset($grid[$y][$x])) {
            return $grid[$y][$x];
        }

        return false;
    }

    public function addRobot(Robot $r) {
        $this->robots[] = $r;
    }

    public function step() {
        foreach ($this->robots as $r) {
            $x = $r->p->x + $r->v->x;
            $y = $r->p->y + $r->v->y;

            if ($x > $this->width-1) {
                $x = $x - $this->width;
            } elseif ($x < 0) {
                $x = $this->width + $x;
            }

            if ($y > $this->height-1) {
                $y = $y - $this->height;
            } elseif ($y < 0) {
                $y = $this->height + $y;
            }

            $r->p->x = $x;
            $r->p->y = $y;
        }
    }

    public function p($q = false) {
        $midX = ($this->width-1) / 2;
        $midY = ($this->height-1) / 2;

        $grid = [];
        for ($y = 0; $y < $this->height; $y++) {
            if ($q && $y === $midY) {
                $grid[] = null;
                continue;
            }
            $row = [];
            for ($x = 0; $x < $this->width; $x++) {
                if ($q && $x === $midX) {
                    $row[] = null;
                    continue;
                }
                $set = [];
                foreach ($this->robots as $r) {
                    if ($r->p->matches($x, $y)) {
                        $set[] = $r;
                    }
                }
                $row[] = $set;
            }
            $grid[] = $row;
        }

        foreach ($grid as $row) {
            if ($row === null) {
                for ($x = 0; $x < $this->width; $x++) {
                    echo ' ';
                }
                echo PHP_EOL;
                continue;
            }
            foreach ($row as $col) {
                if ($col === null) {
                    echo ' ';
                } elseif (count($col) === 0) {
                    echo '.';
                } else {
                    echo count($col);
                }
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }

    public function partA() {
        $midX = ($this->width-1) / 2;
        $midY = ($this->height-1) / 2;

        $quadrants = [
            0, 0, 0, 0
        ];
        foreach ($this->robots as $r) {
            if ($r->p->x < $midX && $r->p->y < $midY) {
                $quadrants[0]++;
            } elseif ($r->p->x < $midX && $r->p->y > $midY) {
                $quadrants[2]++;
            } elseif ($r->p->x > $midX && $r->p->y < $midY) {
                $quadrants[1]++;
            } elseif ($r->p->x > $midX && $r->p->y > $midY) {
                $quadrants[3]++;
            }
        }

        $total = 1;
        foreach ($quadrants as $q) {
            $total *= $q;
        }

        return $total;
    }

    public function partB() {
        $rows = [];
        foreach ($this->robots as $r) {
            $x = $r->p->x;
            $y = $r->p->y;
            if (!isset($rows[$y])) {
                $rows[$y] = [];
            }
            $rows[$y][] = $x;
        }

        foreach ($rows as $y => $cols) {
            sort($cols);
            $rows[$y] = $cols;
        }

        foreach ($rows as $y => $cols) {
            $consec = 1;
            $last = null;
            foreach ($cols as $col) {
                if ($last === null) {
                    $last = $col;
                    continue;
                }
                if ((int)$col == (int)$last+1) {
                    $consec++;
                    if ($consec > 4) {
                        echo $consec . PHP_EOL;
                        return true;
                    }
                    $last = $col;
                } else {
                    $consec = 1;
                }
            }
        }
        
        return false;
    }

    public function sameAs(Grid $grid) {
        $index = [];
        foreach ($this->robots as $r) {
            $x = $r->p->x;
            $y = $r->p->y;
            $vA = $r->v->x;
            $vB = $r->v->y;

            $index["$x|$y|$vA|$vB"] = $r;
        }

        foreach ($grid->robots as $r) {
            $x = $r->p->x;
            $y = $r->p->y;
            $vA = $r->v->x;
            $vB = $r->v->y;

            if (!isset($index["$x|$y|$vA|$vB"])) {
                return false;
            }
        }

        return true;
    }
}

$grid = new Grid(101, 103);
$fh = fopen('14-input.txt', 'r');

// $grid = new Grid(11,7);
// $fh = fopen('14-easy.txt', 'r');

do {
    $line = trim(fgets($fh));
    if ($line === false || $line === '') {
        break;
    }

    if (preg_match_all('#p=([-0-9]+),([-0-9]+) v=([-0-9]+),([-0-9]+)#', $line, $matches)) {
        $p = new Point((int)$matches[1][0], (int)$matches[2][0]);
        $v = new Velocity((int)$matches[3][0], (int)$matches[4][0]);
        $grid->addRobot(new Robot($p, $v));
    }
} while ($line !== false && $line !== '');

fclose($fh);

for ($i = 0; $i < 100; $i++) {
    $grid->step();
}

$total = $grid->partA();

echo 'Part A: ' . $total . PHP_EOL;

$grid->p();


// ---- PART B ----


$g = new Grid(101, 103);
$fh = fopen('14-input.txt', 'r');

// $g = new Grid(11, 7);
// $fh = fopen('14-easy.txt', 'r');

do {
    $line = trim(fgets($fh));
    if ($line === false || $line === '') {
        break;
    }

    if (preg_match_all('#p=([-0-9]+),([-0-9]+) v=([-0-9]+),([-0-9]+)#', $line, $matches)) {
        $p = new Point((int)$matches[1][0], (int)$matches[2][0]);
        $v = new Velocity((int)$matches[3][0], (int)$matches[4][0]);
        $g->addRobot(new Robot($p, $v));
    }
} while ($line !== false && $line !== '');

fclose($fh);

// // Get count until looping
// 
// $orig = clone $g;
// $steps = 0;
// do {
//     $g->step();
//     $steps++;
// } while (!$orig->sameAs($g));
// echo $steps . PHP_EOL;
// die;

$steps = 0;
for ($i = 0; $i < 10403; $i++) {
    if ($i % 100 === 0) {
        echo 'Iter: ' . $i . PHP_EOL;
    }

    if ($g->partB()) {
        echo 'Steps: ' . $steps . PHP_EOL;
        $g->p();
        usleep(800000);
    }

    $g->step();
    $steps++;
}

// 98? Nope
// 7774 -- Yes!