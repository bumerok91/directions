<?php

namespace Bumerok91\Directions;

use Exception;
use SplObjectStorage;

/**
 * Class Directions
 * @package Bumerok91\Directions
 */
final class TestCase
{
    private const START = 'start';

    /** @var SplObjectStorage */
    private $directions;

    /** @var int */
    private $countDirections;

    /** @var float */
    private $averageX;

    /** @var float */
    private $averageY;

    /** @var float */
    private $worstDistance;

    public function __construct(int $countDirections)
    {
        $this->directions = new SplObjectStorage();
        $this->countDirections = $countDirections;
    }

    /**
     * @param string $rawInstruction
     * @throws Exception
     */
    public function addDirection(string $rawInstruction): void
    {
        $parts = explode(' ', $rawInstruction);
        $x0 = (float)array_shift($parts);
        $y0 = (float)array_shift($parts);

        if (array_shift($parts) !== self::START) {
            throw new Exception('Invalid instruction');
        }

        $angle = array_shift($parts);
        $direction = new Direction($x0, $y0, $angle);
        $direction->parse($parts);
        $this->directions->attach($direction);
    }

    /**
     * Calculate average destination
     */
    public function calculateAverage(): void
    {
        $summaryX = 0;
        $summaryY = 0;

        /** @var Direction $direction */
        foreach ($this->directions as $direction) {
            $summaryX += $direction->getCurrentX();
            $summaryY += $direction->getCurrentY();
        }

        $this->averageX = $summaryX / $this->directions->count();
        $this->averageY = $summaryY / $this->directions->count();
    }

    /**
     * Distance: |a| = √x^2 + y^2
     */
    public function calculateDistance(): void
    {
        $worstDistance = 0;

        /** @var Direction $direction */
        foreach ($this->directions as $direction) {
            $vectorX = $this->averageX - $direction->getCurrentX();
            $vectorY = $this->averageY - $direction->getCurrentY();

            $distance = sqrt(pow($vectorX, 2) + pow($vectorY, 2));
            if ($distance > $worstDistance) {
                $worstDistance = $distance;
            }
        }
        $this->worstDistance = $worstDistance;
    }

    /**
     * @return float
     */
    public function getAverageX(): float
    {
        return round($this->averageX, 4);
    }

    /**
     * @return float
     */
    public function getAverageY(): float
    {
        return round($this->averageY, 4);
    }

    /**
     * @return float
     */
    public function getWorstDistance(): float
    {
        return round($this->worstDistance, 4);
    }
}