<?php

namespace ILaoniu\DummyImage;

class Color
{
    public int $red = 0;

    public int $green = 0;

    public int $blue = 0;

    public string $hex = '';

    public function setRgb($red, $green, $blue): self
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;

        return $this->rgb2hex();
    }

    public function setHex(string $hex): self
    {
        $hex = strtolower($hex);
        $hex = preg_replace('/#/', '', $hex);
        $length = strlen($hex);
        $input = $hex;
        $this->hex = match ($length) {
            1 => $input.$input.$input.$input.$input.$input,
            3 => $input[0].$input[0].$input[1].$input[1].$input[2].$input[2],
            6 => $input
        };

        return $this->hex2rgb();
    }

    public function getRgb(?string $part = null): array|int
    {
        if ($part) {
            return $this->$part;
        } else {
            return [$this->red, $this->green, $this->blue];
        }
    }

    public function getHex(): string
    {
        return $this->hex;
    }

    public function rgb2hex(): self
    {
        $this->hex = sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);

        return $this;
    }

    public function hex2rgb(): self
    {
        $red = substr($this->hex, 0, 2);
        $green = substr($this->hex, 2, 2);
        $blue = substr($this->hex, 4, 2);

        $this->red = hexdec($red);
        $this->green = hexdec($green);
        $this->blue = hexdec($blue);

        return $this;
    }
}
