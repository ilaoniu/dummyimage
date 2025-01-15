<?php

namespace ILaoniu\DummyImage;

class DummyImage
{
    public function imageUrl(string $baseUrl, ?int $width = null, ?int $height = null, ?string $bgColor = null, ?string $textColor = null, ?string $text = null, ?string $format = null): string
    {
        return (new Image)->url($baseUrl, $width, $height, $bgColor, $textColor, $text, $format);
    }

    protected function parseDimensions(string $size): array
    {
        $dimensions = explode('x', $size);
        $width = preg_replace('/[^\d:\.]/i', '', $dimensions[0]);
        $height = $width;
        if (! empty($dimensions[1])) {
            $height = preg_replace('/[^\d:\.]/i', '', $dimensions[1]);
        }

        if (substr_count($size, ':') > 1) {
            throw new \InvalidArgumentException($size.' has too many colons in the dimension parameter! There should be 1 at most.');
        }

        if (strstr($size, ':') && ! strstr($size, 'x')) {
            throw new \InvalidArgumentException('To calculate a ratio a height is needed.');
        }

        if (preg_match('/:/', $height)) {

            $ratio = explode(':', $height);

            if (empty($ratio[1])) {
                $ratio[1] = $ratio[0];
            }

            if (empty($ratio[0])) {
                $ratio[0] = $ratio[1];
            }

            $width = abs((float) $width);
            $ratio[0] = abs((float) $ratio[0]);
            $ratio[1] = abs((float) $ratio[1]);

            $height = ($width * $ratio[1]) / $ratio[0];

        } elseif (preg_match('/:/', $width)) {
            $ratio = explode(':', $width);
            if (empty($ratio[1])) {
                $ratio[1] = $ratio[0];
            }

            if (empty($ratio[0])) {
                $ratio[0] = $ratio[1];
            }

            $height = abs((float) $height);
            $ratio[0] = abs((float) $ratio[0]);
            $ratio[1] = abs((float) $ratio[1]);

            $width = ($height * $ratio[0]) / $ratio[1];
        }

        $width = abs((float) $width);
        $height = abs((float) $height);

        if ($width < 1 || $height < 1) {
            throw new \InvalidArgumentException('Too small of an image!');
        }

        $area = $width * $height;
        if ($area > 33177600 || $width > 9999 || $height > 9999) {
            throw new \InvalidArgumentException('Too big of an image!');
        }

        $width = (int) round($width);
        $height = (int) round($height);

        return ['width' => $width, 'height' => $height];
    }

    protected function parseFormat(string $size): string
    {
        $format = Image::$defaultFormat;

        preg_match_all('/\.('.implode('|', Image::$supportedFormats).')/', $size, $result);
        if (! empty($result[1][0])) {
            $format = $result[1][0];
        }

        return $format;
    }

    protected function parseParameters(string $size, ?string $bgColor = null, ?string $textColor = null, ?string $text = null): array
    {
        ['width' => $width, 'height' => $height] = $this->parseDimensions($size);
        $format = $this->parseFormat($size);

        return [
            'width' => $width,
            'height' => $height,
            'bgColor' => $bgColor,
            'textColor' => $textColor,
            'text' => $text,
            'format' => $format,
        ];
    }

    public function image(string $size, ?string $bgColor = null, ?string $textColor = null, ?string $text = null): array
    {
        $parameters = $this->parseParameters($size, $bgColor, $textColor, $text);

        return (new Image)->create(
            $parameters['width'],
            $parameters['height'],
            $parameters['bgColor'],
            $parameters['textColor'],
            $parameters['text'],
            $parameters['format'],
        );
    }
}
