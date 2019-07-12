<?php

namespace Jackal\Driverizzalo\Model;

use Jackal\Driverizzalo\Exception\InvalidColor;

/**
 * Class Color
 * @package Jackal\ImageMerge\Model
 */
class Color
{
    const BLACK = '000000';
    const WHITE = 'FFFFFF';

    /**
     * @var string
     */
    private $red;

    /**
     * @var string
     */
    private $green;

    /**
     * @var string
     */
    private $blue;

    /**
     * @var string
     */
    private $colorHex;

    /**
     * Color constructor.
     * @param $colorHex
     * @throws InvalidColor
     */
    public function __construct($colorHex)
    {
        if (substr($colorHex, 0, 1) == '#') {
            $colorHex = substr($colorHex, 1);
        }

        preg_match('/[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3}/', $colorHex, $matches);

        if (!$matches or strlen($colorHex) != strlen($matches[0])) {
            throw new InvalidColor(sprintf('Color "%s" is invalid', $colorHex));
        }

        $colorHex = $matches[0];

        if (strlen($colorHex) == 3) {
            $c1 = str_repeat(substr($colorHex, 0, 1), 2);
            $c2 = str_repeat(substr($colorHex, 1, 1), 2);
            $c3 = str_repeat(substr($colorHex, 2, 1), 2);
            $colorHex = $c1.$c2.$c3;
        }

        $this->colorHex = $colorHex;

        $this->red = hexdec(substr($colorHex, 0, 2));
        $this->green = hexdec(substr($colorHex, 2, 2));
        $this->blue = hexdec(substr($colorHex, 4, 2));
    }

    /**
     * @return string
     */
    public function red()
    {
        return $this->red;
    }

    /**
     * @return string
     */
    public function green()
    {
        return $this->green;
    }

    /**
     * @return string
     */
    public function blue()
    {
        return $this->blue;
    }

    public function rgb()
    {
        return $this->red().$this->green().$this->blue();
    }

    public function getHex()
    {
        return $this->colorHex;
    }
}
