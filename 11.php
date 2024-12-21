<?php

// $input = explode(' ', '2 72 8949 0 981038 86311 246 7636740');

// $blinks = 25;

// for ($b = 0; $b < $blinks; $b++) {
//     $output = [];
//     foreach ($input as $num) {
//         if ((int)$num === 0) {
//             $output[] = 1;
//         } elseif (strlen($num) % 2 === 0) {
//             $halves = str_split($num, strlen($num)/2);
//             $output[] = (int)$halves[0];
//             $output[] = (int)$halves[1];
//         } else {
//             $output[] = (int)$num*2024;
//         }
//     }

//     $input = $output;
// }

// echo 'Part A: ' . count($output) . PHP_EOL;

// 25 blinks = 202019

$filename = '11-input.txt';
$blinks = 10;


$fh = fopen($filename, 'r');


for ($b = 0; $b < $blinks; $b++) {
    $output = [];
    foreach ($input as $num) {
        if ((int)$num === 0) {
            $output[] = 1;
        } elseif (strlen($num) % 2 === 0) {
            $halves = str_split($num, strlen($num)/2);
            $output[] = (int)$halves[0];
            $output[] = (int)$halves[1];
        } else {
            $output[] = (int)$num*2024;
        }
    }

    $input = $output;
}

$fh = fopen('./temp/11-10.txt', 'w');
foreach ($input as $i) {
    fwrite($fh, $i . ' ');
}
fclose($fh);

unset($input);

$fh = fopen('./temp/11-10.txt', 'r');

$blinks = 10;

$input = [];

$file = 'a';
$continue = true;
do {
    do {
        $num = getNextNumber($fh);

        if (strlen($num) === 0) {
            $continue = false;
            break;
        }
        
        if (count($input) === 10) {
            break;
        }

        $input[] = $num;
    } while (true);


    for ($b = 0; $b < $blinks; $b++) {
        $output = [];
        foreach ($input as $num) {
            if ((int)$num === 0) {
                $output[] = 1;
            } elseif (strlen($num) % 2 === 0) {
                $halves = str_split($num, strlen($num)/2);
                $output[] = (int)$halves[0];
                $output[] = (int)$halves[1];
            } else {
                $output[] = (int)$num*2024;
            }
        }

        $input = $output;
    }

    $fh2 = fopen('./temp/11-20-' . $file . '.txt', 'w');
    foreach ($input as $i) {
        fwrite($fh2, $i . ' ');
    }
    fclose($fh2);

    $file++;

    $input = [];
} while ($continue);



fclose($fh);



die;

$input = explode(' ', '125 17');

$cnt = 0;

function recurse($num, &$cnt, $blinks) {
    if ($blinks === 25) {
        $cnt += 1;
        return;
    }


        if ((int)$num === 0) {
            recurse(1, $cnt, $blinks+1);
        } elseif (strlen($num) % 2 === 0) {
            $halves = str_split($num, strlen($num)/2);
            $a = (int)$halves[0];
            recurse($a, $cnt, $blinks+1);
            $b = (int)$halves[1];
            recurse($b, $cnt, $blinks+1);
        } else {
            recurse((int)$num*2024, $cnt, $blinks+1);
        }
}

$cnt = 0;
foreach ($input as $head) {
    echo 'Head: ' . $head . PHP_EOL;
    recurse($head, $cnt, 0);
}

echo 'Part B: ' . $cnt . PHP_EOL;
die;




function blink($num, $blinks, &$hash, &$cnt = null) {
    $key = "$num|$blinks";

    if (isset($hash[$key])) {
        return $hash[$key];
    }

    if ($blinks === 0) {
        // $cnt += 1;
        // $hash[$key] = 1;

        return;
    }

    $c = $cnt;
    if ((int)$num === 0) {
        $c += 1;
        blink(1, $blinks-1, $hash, $c);
    } elseif (strlen($num) % 2 === 0) {
        $halves = str_split($num, strlen($num)/2);
        $c += 2;
        blink((int)$halves[0], $blinks-1, $hash, $c);
        blink((int)$halves[1], $blinks-1, $hash, $c);
    } else {
        $c += 1;
        blink((int)$num*2024, $blinks-1, $hash, $c);
    }

    $hash[$key] = $c;
}

$hash = [];

$input = explode(' ', '125 17');
$blinks = 6;
foreach ($input as $i) {
    blink($i, $blinks, $hash);
}
// var_dump($hash);

$cnt = 0;
foreach ($input as $i) {
    $key = "$i|$blinks";

    if (isset($hash[$key])) {
        $cnt += $hash[$key];
    }
}

foreach ($hash as $key => $value) {
    if ($key === "125|6" || $key === "17|6") {
        echo $hash[$key] . PHP_EOL;

        // want 125|6 = 7 and 17|6 = 15
    }
}

var_dump($hash);

echo 'Part B: ' . $cnt . PHP_EOL;






die;











$cnt = 0;
foreach ($input as $head) {
    //run($cnt, $i, $blinks);

    $i = [$head];
    for ($b = 0; $b < $blinks; $b++) {
        $output = [];
        foreach ($i as $num) {
            if ((int)$num === 0) {
                $output[] = 1;
            } elseif (strlen($num) % 2 === 0) {
                $halves = str_split($num, strlen($num)/2);
                $output[] = (int)$halves[0];
                $output[] = (int)$halves[1];
            } else {
                $output[] = (int)$num*2024;
            }
        }
        $i = $output;
        echo $b . PHP_EOL;
    }

    $cnt += count($i);
}

echo $cnt . PHP_EOL;

die;




for ($b = 0; $b < $blinks; $b++) {
    $output = [];
    foreach ($input as $num) {
        if ((int)$num === 0) {
            $output[] = 1;
        } elseif (strlen($num) % 2 === 0) {
            $halves = str_split($num, strlen($num)/2);
            $output[] = (int)$halves[0];
            $output[] = (int)$halves[1];
        } else {
            $output[] = (int)$num*2024;
        }
    }

    $input = $output;
}


die;



function getNextNumber($fh) {
    $str = '';
    do {
        $byte = fread($fh, 1);
        if ($byte !== '' && $byte !== ' ') {
            $str .= $byte;
        }
    } while (!feof($fh) && $byte !== ' ');

    return $str;
}

$input = '2 72 8949 0 981038 86311 246 7636740';
$last = '11-a.txt';
$next = '11-b.txt';
file_put_contents($last, $input);
file_put_contents($next, '');

$blinks = 25;

for ($b = 0; $b < $blinks; $b++) {
    $t1 = microtime(true);

    $in = fopen($last, 'r');
    $out = fopen($next, 'w');

    $continue = true;
    do {
        $num = getNextNumber($in);
        
        if (strlen($num) === 0) {
            $continue = false;
            break;
        }

        if ((int)$num === 0) {
            fwrite($out, "1 ");
        } elseif (strlen($num) % 2 === 0) {
            $halves = str_split($num, strlen($num)/2);
            $one = (int) $halves[0];
            $two = (int) $halves[1];
            fwrite($out, "$one $two ");
        } else {
            $n = (int)$num*2024;
            fwrite($out, "$n ");
        }
    } while ($continue);

    fclose($in);
    fclose($out);

    $temp = $next;
    $next = $last;
    $last = $temp;

    $t2 = microtime(true);

    echo 'Finished blink ' . ($b+1) . ' (' . ($t2-$t1) . ')' . PHP_EOL;
}

$cnt = 0;
$fh = fopen($last, 'r');
do {
    $num = getNextNumber($fh);
    if (strlen($num) > 0) {
        $cnt++;
    }
} while (!feof($fh));

fclose($fh);

echo 'Part B: ' . $cnt . PHP_EOL;

unlink('11-a.txt');
unlink('11-b.txt');