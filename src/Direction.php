<?php

namespace Bumerok91\Directions;

use Exception;
use SplObjectStorage;

/**
 * Class Direction
 * @package Bumerok91\Directions
 */
final class Direction
{
    private const WALK = 'walk';
    private const TURN = 'turn';

    /** @var float */
    private $x0;

    /** @var float */
    private $y0;

    /** @var float */
    private $currentX;

    /** @var float */
    private $currentY;

    /** @var float */
    private $startAngle;

    /** @var float */
    private $currentAngle;

    /** @var SplObjectStorage */
    private $actions;

    /**
     * Instruction constructor.
     * @param float $x0
     * @param float $y0
     * @param float $startAngle
     */
    public function __construct(float $x0, float $y0, float $startAngle)
    {
        $this->x0 = $this->currentX = $x0;
        $this->y0 = $this->currentY = $y0;
        $this->startAngle = $this->currentAngle = $startAngle;
    }

    /**
     * @param array $instructionsParts
     * @throws Exception
     */
    public function parse(array $instructionsParts): void
    {
        if (count($instructionsParts) % 2 !== 0) {
            throw new Exception('Invalid instruction');
        }

        $this->actions = new SplObjectStorage();
        //normalize input array
        $instructionsParts = array_values($instructionsParts);

        //Build Actions structure
        foreach ($instructionsParts as $index => $part) {
            if ($index % 2 === 0) {
                $type = $part;

                continue;
            }

            $action = new Action($type, (float)$part);
            $this->doAction($action);
            $this->actions->attach($action);
        }
    }

    /**
     * @return float
     */
    public function getCurrentX(): float
    {
        return $this->currentX;
    }

    /**
     * @return float
     */
    public function getCurrentY(): float
    {
        return $this->currentY;
    }

    /**
     * @param Action $action
     */
    private function doAction(Action $action): void
    {
        switch ($action->type) {
            case self::TURN:
                $this->turn($action);
                break;
            case self::WALK:
                $this->walk($action);
                break;
        }
    }

    /**
     * @param Action $action
     */
    private function turn(Action $action): void
    {
        $this->currentAngle += $action->value;
        $this->normalizeAngle();
    }

    /**
     * Used formulas
     * a = c * sin(alpha);
     * b = c * cos(alpha)
     * @param Action $action
     */
    private function walk(Action $action): void
    {
        $absoluteAngle = abs($this->currentAngle);
        $alpha = $this->currentAngle;

        //Move to east
        if ($absoluteAngle == 0) {
            $this->currentX += $action->value;

            return;
        }

        //Move to west
        if ($absoluteAngle - 180 == 0) {
            $this->currentX -= $action->value;

            return;
        }

        //Move to north
        if ($absoluteAngle == 90) {
            $this->currentY += $action->value;

            return;
        }

        //Move to south
        if ($absoluteAngle == 270) {
            $this->currentY -= $action->value;

            return;
        }

        //Move to north-east
        if (
            ($alpha > 0 && $alpha < 90) ||
            ($alpha < -270 && $alpha > -360)
        ) {
            $this->currentX += ($action->value * cos(deg2rad($alpha)));
            $this->currentY += ($action->value * sin(deg2rad($alpha)));

            return;
        }

        //Move to north-west
        if (
            ($alpha > 90 && $alpha < 180) ||
            ($alpha < -180 && $alpha > -270)
        ) {
            $this->currentX -= $action->value * sin(deg2rad($alpha - 90));
            $this->currentY += $action->value * cos(deg2rad($alpha - 90));

            return;
        }

        //Move to south-west
        if (
            ($alpha > 180 && $alpha < 270) ||
            ($alpha < -90 && $alpha > -180)
        ) {
            $this->currentX -= $action->value * cos(deg2rad($alpha - 180));
            $this->currentY -= $action->value * sin(deg2rad($alpha - 180));

            return;
        }

        //Move to south-east
        if (
            ($alpha > 270 && $alpha < 360) ||
            ($alpha < 0 && $alpha > -90)
        ) {
            $this->currentX += $action->value * cos(deg2rad(360 - $alpha));
            $this->currentY -= $action->value * sin(deg2rad(360 - $alpha));
        }
    }

    /**
     * Normalize current angle
     */
    private function normalizeAngle(): void
    {
        $angle = $this->currentAngle;
        while (abs($angle) >= 360) {
            if ($angle > 0) {
                $angle -= 360;
            } else {
                $angle += 360;
            }
        }

        $this->currentAngle = round($angle, 4);
    }
}