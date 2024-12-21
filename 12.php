<?php

class Side {
    public int $pos;
    public array $points;

    public function __construct() {
        $this->points = [];
    }
}

class Point {
    public int $row;
    public int $col;
    public string $value;
    public array $links;
    public array $borders;

    public function __construct($row, $col, $value) {
        $this->row = $row;
        $this->col = $col;
        $this->value = $value;
        $this->links = [];
        $this->borders = [
            'A' => true,
            'B' => true,
            'L' => true,
            'R' => true,
        ];
    }

    public function __debugInfo() {
        return [
            'row' => $this->row,
            'col' => $this->col,
            'value' => $this->value,
            'borders' => $this->borders,
        ];
    }

    public function link(Point $pt) {
        if (isset($this->links[$pt->hash()])) {
            return;
        }

        $this->links[$pt->hash()] = $pt;
        $pt->link($this);
    }

    public function follow(&$set) {
        $set[$this->hash()] = $this;
        foreach ($this->links as $pt) {
            if (isset($set[$pt->hash()])) {
                continue;
            }
            $set[$pt->hash()] = $pt;
            $pt->follow($set);
        }
    }

    public function hash() {
        return $this->row . ',' . $this->col;
    }

    public function sameRow(Point $pt)  {
        return $this->row === $pt->row;
    }

    public function sameCol(Point $pt) {
        return $this->col === $pt->col;
    }

    public function getPos(Point $pt) {
        if ($this->sameRow($pt)) {
            if ($this->col < $pt->col) {
                return 'R';
            } else {
                return 'L';
            }
        }

        if ($this->sameCol($pt)) {
            if ($this->row < $pt->row) {
                return 'B';
            } else {
                return 'A';
            }
        }
    }

    public function walk(&$chain) {
        if (isset($chain[$this->hash()])) {
            return;
        }

        $chain[$this->hash()] = $this;

        foreach ($this->links as $pt) {
            $pt->walk($chain);
        }
    }
}

class Grid {
    public array $grid;
    public array $index;
    public array $plots;

    public function __construct($filename) {
        $this->grid = [];
        $this->index = [];
        $this->plots = [];

        $input = explode("\n", trim(file_get_contents($filename)));

        foreach ($input as $row => $line) {
            $chars = str_split($line);
            foreach ($chars as $col => $value) {
                $this->add(new Point($row, $col, $value));
            }
        }
    }

    public function add(Point $pt) {
        if (!isset($this->grid[$pt->row])) {
            $this->grid[$pt->row] = [];
        }
        if (!isset($this->index[$pt->value])) {
            $this->index[$pt->value] = [];
        }

        $this->grid[$pt->row][$pt->col] = $pt;
        $this->index[$pt->value][] = $pt;
    }

    public function get($row, $col) {
        if (isset($this->grid[$row][$col])) {
            return $this->grid[$row][$col];
        }

        return null;
    }

    public function getByKey($key) {
        list($row, $col) = explode(',', $key);

        return $this->get($row, $col);
    }

    public function traverse() {
        foreach ($this->grid as $row => $line) {
            foreach ($line as $col => $pt) {
                $next = $this->get($pt->row-1, $pt->col);
                if ($next && $next->value === $pt->value) {
                    $pt->link($next);
                }
                $next = $this->get($pt->row+1, $pt->col);
                if ($next && $next->value === $pt->value) {
                    $pt->link($next);
                }
                $next = $this->get($pt->row, $pt->col-1);
                if ($next && $next->value === $pt->value) {
                    $pt->link($next);
                }
                $next = $this->get($pt->row, $pt->col+1);
                if ($next && $next->value === $pt->value) {
                    $pt->link($next);
                }
            }
        }
    }

    public function getPlots() {
        foreach ($this->grid as $row => $points) {
            foreach ($points as $col => $pt) {
                $set = [];
                $pt->follow($set);

                $keys = array_keys($set);
                sort($keys);
                $key = implode('#', $keys);

                $this->plots[$key] = $keys;
            }
        }
    }
}



$grid = new Grid('12-easy.txt');
$grid->traverse();
$grid->getPlots();

$cost = 0;
foreach ($grid->plots as $plot) {
    $area = count($plot);

    $perimeter = 0;
    foreach ($plot as $key) {
        $pt = $grid->getByKey($key);
        $perimeter += 4 - count($pt->links);
    }
    
    $price = $area * $perimeter;
    $cost += $price;   
}

echo 'Part A: ' . $cost . PHP_EOL; // 1319878

$cost = 0;
foreach ($grid->plots as $plot) {
    $area = count($plot);

    $value = '';
    $points = [];
    foreach ($plot as $key) {
        $pt = $grid->getByKey($key);
        $value = $pt->value;

        $points[] = $pt;
    }

    foreach ($points as $pt) {
        foreach ($pt->links as $link) {
            $pos = $pt->getPos($link);
            $pt->borders[$pos] = false;
        }
    }

    $borders = [
        'L' => [],
        'R' => [],
        'A' => [],
        'B' => [],
    ];
    foreach ($points as $pt) {
        foreach (array_keys($borders) as $dir) {
            if ($pt->borders[$dir]) {
                $borders[$dir][$pt->hash()] = $pt;
            }
        }
    }

    $sides = [
        'L' => 0,
        'R' => 0,
        'A' => 0,
        'B' => 0,
    ];
    foreach ($borders as $dir => $points) {
        $sets = [];
        foreach ($points as $a) {
            $set = [];
            if ($dir === 'A' || $dir === 'B') {
                $chain = [];
                $a->walk($chain);
                foreach ($chain as $idx => $pt) {
                    if (!$a->sameRow($pt)) {
                        unset($chain[$idx]);
                    }
                }
                foreach ($chain as $pt) {
                    $set[$pt->hash()] = $pt;
                }
            }
            if ($dir === 'L' || $dir === 'R') {
                $chain = [];
                $a->walk($chain);
                foreach ($chain as $idx => $pt) {
                    if (!$a->sameCol($pt)) {
                        unset($chain[$idx]);
                    }
                }
                foreach ($chain as $pt) {
                    $set[$pt->hash()] = $pt;
                }
            }

            $keys = array_keys($set);
            sort($keys);
            $key = implode('#', $keys);

            $sets[$key] = 1;
        }
        $sides[$dir] = $sets;

        echo 'Dir: ' . $dir . PHP_EOL;
        var_dump($sets);
    }

    $total = 0;
    foreach ($sides as $s) {
        $total += count($s);
    }

    $price = $area * $total;
    $cost += $price;   

    echo $value . ' plants with price ' . $area . ' * ' . $total . ' = ' . $price . PHP_EOL;
}

echo 'Part B: ' . $cost . PHP_EOL;

