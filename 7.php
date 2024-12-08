<?php

function calc($part, $test, $operands, $stack, $op = null) {
    if ($op !== null) {
        $stack[] = $op;
    }

    if (count($operands) > 1) {
        $s1 = calc($part, $test, [$operands[0]*$operands[1], ...array_slice($operands, 2)], $stack, '*');
        $s2 = calc($part, $test, [$operands[0]+$operands[1], ...array_slice($operands, 2)], $stack, '+');
        $s3 = false;
        if ($part === 'B') {
            $s3 = calc($part, $test, [
                (int)($operands[0].$operands[1]), ...array_slice($operands, 2)
            ], $stack, '|');
        }

        if ($s1 !== false) {
            return $s1;
        } elseif ($s2 !== false) {
            return $s2;
        } elseif ($s3 !== false) {
            return $s3;
        } else {
            return false;
        }
    }

    return $test === $operands[0] ? $stack : false;
}

$input = explode("\n", trim(file_get_contents('7-input.txt')));

$rows = [];
foreach ($input as $row) {
    preg_match_all('#([0-9]+): (.*)#', $row, $matches);

    $test = (int)$matches[1][0];

    preg_match_all('#([0-9]+)#', $matches[2][0], $matches);

    $operands = [];
    foreach ($matches[0] as $operand) {
        $operands[] = (int)$operand;
    }

    $rows[] = [
        'test' => $test,
        'operands' => $operands,
    ];
}

$sum = 0;
foreach ($rows as $row) {
    $test = $row['test'];
    $operands = $row['operands'];

    $stack = [];
    $ops = calc('A', $test, $operands, $stack);
    if ($ops !== false) {
        $sum += $test;
    }
}

echo 'Part A: ' . $sum . PHP_EOL; // 945512582195


$sum = 0;
foreach ($rows as $row) {
    $test = $row['test'];
    $operands = $row['operands'];

    $stack = [];
    $ops = calc('B', $test, $operands, $stack);
    if ($ops !== false) {
        $sum += $test;
    }
}

echo 'Part B: ' . $sum . PHP_EOL; // 271691107779347