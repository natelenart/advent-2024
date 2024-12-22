<?php

class Button {
    public int $x;
    public int $y;
    public string $value;
    public int $cost;

    public function __construct($x, $y, $value) {
        $this->x = $x;
        $this->y = $y;
        $this->value = $value;

        if ($value === 'A') {
            $this->cost = 3;
        } elseif ($value === 'B') {
            $this->cost = 1;
        }
    }

    public function isA() {
        return $this->value === 'A';
    }

    public function isB() {
        return $this->value === 'B';
    }
}

class Prize {
    public int $x;
    public int $y;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function addOffset() {
        $offset = 10000000000000;

        $this->x += $offset;
        $this->y += $offset;
    }
}

class Game {
    public Button $a;
    public Button $b;
    public Prize $p;
    public array $winnable;
    public array $checked = [];

    public function __construct() {
        $this->winnable = [];
        $this->checked = [];
    }

    public function check($x, $y) {
        return $x === $this->p->x && $y === $this->p->y;
    }

    public function pressA($x, $y) {
        $x += $this->a->x;
        $y += $this->a->y;

        return [$x, $y];
    }

    public function pressB($x, $y) {
        $x += $this->b->x;
        $y += $this->b->y;

        return [$x, $y];
    }

    public function cost($i, $j) {
        return $i * $this->a->cost + $j * $this->b->cost;
    }
}

function partA($filename) {
    $fh = fopen($filename, 'r');

    $games = [];
    $game = new Game;
    do {
        $line = trim(fgets($fh));
        if ($line === false || $line === '') {
            continue;
        }
    
        if (preg_match_all('#Button ([A|B]): X\+([0-9]+), Y\+([0-9]+)#', $line, $matches)) {
            $button = new Button((int)$matches[2][0], (int)$matches[3][0], $matches[1][0]);
            if ($button->isA()) {
                $game->a = $button;
            }
            if ($button->isB()) {
                $game->b = $button;
            }
        }
    
        if (preg_match_all('#Prize: X=([0-9]+), Y=([0-9]+)#', $line, $matches)) {
            $prize = new Prize((int)$matches[1][0], (int)$matches[2][0]);
            $game->p = $prize;
    
            $games[] = $game;
            $game = new Game;
        }
    
    } while (!feof($fh));
    
    fclose($fh);
    
    
    foreach ($games as $game) {
        $x = 0;
        $y = 0;
    
        if ($game->check($x, $y)) {
            $game->winnable[] = [0, 0];
        }
    
        for ($i = 0; $i < 100; $i++) {
            $tempX = $x;
            $tempY = $y;
            for ($j = 0; $j < 100; $j++) {
                list($x, $y) = $game->pressB($x, $y);
                if ($game->check($x, $y)) {
                    $game->winnable[] = [$i, $j+1];
                }
            }
            list($x, $y) = $game->pressA($tempX, $tempY);
            if ($game->check($x, $y)) {
                $game->winnable[] = [$i+1, 0];
            }
        }
    }
    
    $total = 0;
    foreach ($games as $game) {
        $lowest = null;
        foreach ($game->winnable as $w) {
            $cost = $game->cost($w[0], $w[1]);
            if ($lowest === null) {
                $lowest = $cost;
            }
            if ($cost < $lowest) {
                $lowest = $cost;
            }
        }
    
        if ($lowest !== null) {
            $total += $lowest;
        }
    }
    
    echo 'Part A: ' . $total . PHP_EOL;
}

function partB($filename) {
    $fh = fopen($filename, 'r');

    $games = [];
    $game = new Game;
    do {
        $line = trim(fgets($fh));
        if ($line === false || $line === '') {
            continue;
        }
    
        if (preg_match_all('#Button ([A|B]): X\+([0-9]+), Y\+([0-9]+)#', $line, $matches)) {
            $button = new Button((int)$matches[2][0], (int)$matches[3][0], $matches[1][0]);
            if ($button->isA()) {
                $game->a = $button;
            }
            if ($button->isB()) {
                $game->b = $button;
            }
        }
    
        if (preg_match_all('#Prize: X=([0-9]+), Y=([0-9]+)#', $line, $matches)) {
            $prize = new Prize((int)$matches[1][0], (int)$matches[2][0]);
            $prize->addOffset();
            $game->p = $prize;
    
            $games[] = $game;
            $game = new Game;
        }
    
    } while (!feof($fh));
    
    fclose($fh);
    
    foreach ($games as $i => $game) {
        $xA = $game->a->x;
        $xB = $game->b->x;
        $yA = $game->a->y;
        $yB = $game->b->y;
        $xP = $game->p->x;
        $yP = $game->p->y;
        $b = ($yP/$yA*$xA - $xP) / (($yB*$xA)/$yA - $xB);
        $a = ($xP-$b*$xB) / $xA;

        $strA = (string)$a;
        $strB = (string)$b;
        if (!strpos($strA, '.') && !strpos($strB, '.')) {
            $game->winnable[] = [$a, $b];
        }

        // $a * $xA + $b * $xB = $xP
        // $a * $yA + $b * $yB = $yP
        //
        // a*xA = xP - b*xB
        // a = (xP-b*xB)/xA
        // (xP-b*xB)/xA*yA + b*yB = yP
        // (xP-b*xB)/xA + (b*yB)/yA = yP/yA
        // (xP-b*xB) + (b*yB)/yA*xA = yP/yA*xA
        // xP + (b*yB)/yA*xA - b*xB = yP/yA*xA
        // (b*yB)/yA*xA - b*xB = yP/yA*xA - xP
        // (b*yB*xA)/yA - b*xB = yP/yA*xA - xP
        // b*((yB*xA)/yA - xB) = yP/yA*xA - xP
        // b = (yP/yA*xA - xP) / ((yB*xA)/yA - xB)        
    }
    
    $total = 0;
    foreach ($games as $game) {
        $lowest = null;
        foreach ($game->winnable as $w) {
            $cost = $game->cost($w[0], $w[1]);
            if ($lowest === null) {
                $lowest = $cost;
            }
            if ($cost < $lowest) {
                $lowest = $cost;
            }
        }
    
        if ($lowest !== null) {
            $total += $lowest;
        }
    }
    
    echo 'Part B: ' . $total . PHP_EOL;
}

partA('13-input.txt'); // 28887
partB('13-input.txt'); // 96979582619758