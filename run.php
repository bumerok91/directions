<?php

use Bumerok91\Directions\TestCase;

require 'vendor/autoload.php';

/**
 * @return TestCase[]
 * @throws Exception
 */
function buildTestCases(): array
{
    $input = fopen('sample.in', 'r');
    $testCases = [];
    $countDirections = 0;
    $isCountIteration = false;

    while (!feof($input)) {
        $row = fgets($input);
        $matches = [];
        if (preg_match('/^\d+\n$/', $row, $matches)) {
            //Add new testCase to testCases list if it exists
            if (isset($testCase)) {
                $testCases[] = $testCase;
            }
            $countDirections = (int)$matches[0];
            $isCountIteration = true;
        }
        //End input
        if ($countDirections === 0) {
            fclose($input);

            return $testCases;
        }
        if ($isCountIteration) {
            $testCase = new TestCase($countDirections);
            $isCountIteration = false;
            continue;
        }

        if (!isset($testCase)) {
            continue;
        }

        $testCase->addDirection($row);
    }
    fclose($input);

    return $testCases;
}


/** @var TestCase $testCase */
try {
    foreach (buildTestCases() as $testCase) {
        $testCase->calculateAverage();
        $testCase->calculateDistance();
        echo $testCase->getAverageX() . ' ' . $testCase->getAverageY() . ' ' . $testCase->getWorstDistance() . PHP_EOL;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}