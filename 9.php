<?php

function cksum($blocks) {
    $cksum = 0;
    foreach ($blocks as $i => $block) {
        $cksum += $i*(int)$block;
    }

    return $cksum;
}

function partA($disk) {
    do {
        $front = array_search('.', $disk, true);
        $back = 0;
        for ($i = count($disk)-1; $i >= 0; $i--) {
            if ($disk[$i] !== '.') {
                $back = $i;
                break;
            }
        }
        $disk[$front] = $disk[$back];
        $disk[$back] = '.';
    
        $d = implode("", $disk);
    } while (preg_match('#\.[0-9]#', $d));

    return cksum($disk);
}

// $input = "2333133121414131402"; // example data

$input = trim(file_get_contents('9-input.txt'));

$disk = [];
$phase = 'file';
$c = 0;
for ($i = 0; $i < strlen($input); $i++) {
    $x = (int)$input[$i];

    for ($j = 0; $j < $x; $j++) {
        if ($phase === 'file') {    
            $disk[] = "$c";
        } else {
            $disk[] = ".";
        }
    }

    if ($phase === 'file') {
        $phase = 'disk';
        $c++;
    } else {
        $phase = 'file';
    }
}

// 92625219623 is too low
// 6519155389266 is correct

// echo 'Part A: ' . partA($disk) . PHP_EOL;


function partB($disk) {
    $checked = [];
    do {
        $front = array_search('.', $disk, true);
        $back = false;
        $size = 0;
        $value = false;
        for ($i = count($disk)-1; $i >= 0; $i--) {
            if ($value !== false && $value !== $disk[$i]) {
                break;
            }
            if (isset($checked[$disk[$i]])) {
                continue;
            }
            if ($disk[$i] !== '.') {
                if ($back === false) {
                    $value = $disk[$i];
                    $back = $i;
                }
                $size++;
            }
        }
        if ($value === false) {
            break;
        }

        $enough = null;
        for ($j = 0; $j < count($disk)-1; $j++) {
            if ($j === $back) {
                break;
            }
            if ($disk[$j] === '.') {
                $enough = true;
                for ($k = 0; $k < $size; $k++) {
                    if (!isset($disk[$j+$k])) {
                        $enough = false;
                        break;
                    } elseif ($disk[$j+$k] !== '.') {
                        $enough = false;
                        break;
                    }
                }
                if ($enough) {
                    for ($k = 0; $k < $size; $k++) {
                        $disk[$j+$k] = $disk[$back-$k];
                        $disk[$back-$k] = '.';
                    }
                    break;
                }
            }
        }

        $checked[(int)$value] = true;
    } while ($value !== false);

    return cksum($disk);
}

// 6547228115826 is correct

echo 'Part B: ' . partB($disk) . PHP_EOL;