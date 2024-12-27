<?php

class Point {
    public ?string $v;

    public function __construct($v = null) {
        $this->v = $v;
    }
}

class Grid {
    public array $grid;
    public array $moves;
    public array $pos;

    public function __construct($fh, $partB = false) {
        $this->grid = [];
        $this->moves = [];

        $y = 0;
        $mode = 'map';
        do {
            $line = trim(fgets($fh));
        
            if ($line === '') {
                $mode = 'moves';
                continue;
            }
        
            $chars = str_split($line);

            if ($mode === 'map') {
                $row = [];
                foreach ($chars as $x => $v) {
                    if ($partB && $v === 'O') {
                        $v = '[';
                    }
                    $pt = new Point($v);
                    if ($v === '@') {
                        $this->pos = [$x, $y];
                    }
                    $row[] = $pt;
                    if ($partB) {
                        if ($v === '@') {
                            $row[] = new Point('.');
                        } elseif ($v === '[') {
                            $row[] = new Point(']');
                        } else {
                            $row[] = new Point($v);
                        }
                    }
                }
                $this->grid[] = $row;
            }

            if ($mode === 'moves') {
                if ($partB) {
                    $this->moves[] = $line;
                } else {
                    foreach ($chars as $x => $v) {
                        $this->moves[] = $v;
                    }
                }
            }

            $y++;
        } while (!feof($fh));
    }

    public function get($x, $y) {
        if (isset($this->grid[$y][$x])) {
            return $this->grid[$y][$x];
        }

        return false;
    }

    public function walkA($steps = null) {
        $cnt = 0;
        foreach ($this->moves as $m) {
            list($x, $y) = $this->pos;
            $oX = $x;
            $oY = $y;

            $chain = [];
            $chain[] = ['x' => $x, 'y' => $y, 'p' => $this->get($x, $y)];

            $blocked = false;
            do {
                switch ($m) {
                    case '^':
                        $y = $y-1;
                        break;
                    case 'v':
                        $y = $y+1;
                        break;
                    case '<':
                        $x = $x-1;
                        break;
                    case '>':
                        $x = $x+1;
                        break;
                }

                $p = $this->get($x, $y);
                
                if ($p->v === '#') {
                    $blocked = true;
                    break;
                }

                $chain[] = ['x' => $x, 'y' => $y, 'p' => $p];

                if ($p->v === 'O') {
                    continue;
                }

                if ($p->v === '.') {
                    $next = array_pop($chain);
                    $o = $next['p'];
                    do {
                        $x = $next['x'];
                        $y = $next['y'];
                        $next = array_pop($chain);
                        $this->grid[$y][$x] = $next['p'];
                        if (count($chain) === 0) {
                            $this->pos = [$x, $y];
                        }
                    } while (count($chain) > 0);
                    
                    $this->grid[$oY][$oX] = $o;
                    break;
                }
            } while (true);

            $cnt++;
            if ($steps !== null && $cnt === $steps) {
                break;
            }
        }
    }

    public function walkB($steps = null) {
        $cnt = 0;
        foreach($this->moves as $m) {
            if ($steps !== null) {
                echo 'Move ' . $m . ':' . PHP_EOL;
            }

            list($x, $y) = $this->pos;
            $oX = $x;
            $oY = $y;

            $chain = [];
            $chain[] = ['x' => $x, 'y' => $y, 'p' => $this->get($x, $y)];

            $boxes = [];
            $blocked = false;
            do {
                if ($this->isRobotBlocked($m, $x, $y)) {
                    $blocked = true;
                    break;
                }

                $p = $this->getNextPoint($m, $x, $y);

                if ($p->v === '[' || $p->v === ']') {
                    $box = [[$x, $y]];
                    if ($p->v === '[') {
                        $box[] = [$x+1, $y];
                        $this->isBoxBlocked($m, $x, $y, $x+1, $y);
                        $pair = $this->get($x+1, $y);
                        // $chain[] = ['x' => $x+1, 'y' => $y, 'p' => $pair];
                    } else {
                        $box[] = [$x-1, $y];
                        $pair = $this->get($x-1, $y);
                        $this->isBoxBlocked($m, $x, $y, $x-1, $y);
                        // $chain[] = ['x' => $x-1, 'y' => $y, 'p' => $pair];
                    }
                    var_dump($box);die;
                    
                    $chain[] = ['x' => $x, 'y' => $y, 'p' => $p, 'pair' => $pair];
                    continue;
                }

                if ($p->v === '.') {
                    $chain[] = ['x' => $x, 'y' => $y, 'p' => $p];

                    $next = array_pop($chain);
                    $o = $next['p'];
                    do {
                        $x = $next['x'];
                        $y = $next['y'];
                        $next = array_pop($chain);
                        $this->grid[$y][$x] = $next['p'];
                        if (count($chain) === 0) {
                            $this->pos = [$x, $y];
                        }
                    } while (count($chain) > 0);
                    
                    $this->grid[$oY][$oX] = $o;
                    break;
                }
            } while (true);

            $cnt++;
            if ($steps !== null) {
                $this->p();
                if ($cnt === $steps) {
                    break;
                }
            }
        }
    }

    private function isRobotBlocked($m, $x, $y) {
        $p = $this->getNextPoint($m, $x, $y);

        return $p->v !== '#';
    }

    private function isBoxBlocked($m, $x1, $y1, $x2, $y2) {
        if ($this->getNextPoint($m, $x1, $y1)->v === '#') {
            return true;
        }

        if ($this->getNextPoint($m, $x2, $y2)->v === '#') {
            return true;
        }

        return false;
    }

    private function getNextPoint($m, $x, $y): Point {
        switch ($m) {
            case '^':
                $y = $y-1;
                break;
            case 'v':
                $y = $y+1;
                break;
            case '<':
                $x = $x-1;
                break;
            case '>':
                $x = $x+1;
                break;
        }

        return $this->get($x, $y);
    }

    public function p() {
        foreach ($this->grid as $row) {
            foreach ($row as $p) {
                echo $p->v;
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }
}

// $fh = fopen('15-input.txt', 'r');
// $grid = new Grid($fh);
// fclose($fh);

// $grid->walkA();

// $sum = 0;
// foreach ($grid->grid as $y => $row) {
//     foreach ($row as $x => $pt) {
//         if ($pt->v === 'O') {
//             $gps = 100*$y+$x;
//             $sum += $gps;
//         }
//     }
// }

// echo 'Part A: ' . $sum . PHP_EOL;



// $fh = fopen('15-easy.txt', 'r');
// $grid = new Grid($fh, true);
// fclose($fh);

// ob_start();
// $grid->p();
// $map = ob_get_clean();
// $fh = fopen('15-easy-b.txt', 'w');
// foreach (explode("\n", $map) as $line) {
//     fputs($fh, $line.PHP_EOL);
// }
// foreach ($grid->moves as $line) {
//     fputs($fh, $line.PHP_EOL);
// }
// fclose($fh);

$fh = fopen('15-easy-b.txt', 'r');
$grid = new Grid($fh);
fclose($fh);

$grid->p();
$grid->walkB(6);
die;

$sum = 0;
foreach ($grid->grid as $y => $row) {
    foreach ($row as $x => $pt) {
        if ($pt->v === 'O') {
            $gps = 100*$y+$x;
            $sum += $gps;
        }
    }
}

echo 'Part B: ' . $sum . PHP_EOL;