<?php

namespace ILaoniu\DummyImage;

class Image
{
    public static array $supportedFormats = [
        'png',
        'gif',
        'webp',
        'jpg',
        'jpeg',
    ];

    public static string $defaultFormat = 'png';

    protected int $defaultWidth = 800;

    protected int $defaultHeight = 600;

    protected string $fontPath = 'fonts/MiSans-Light.ttf';

    protected function safeHexColor(): string
    {
        $color = str_pad(dechex(mt_rand(0, 255)), 3, '0', STR_PAD_LEFT);

        return '#'.$color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
    }

    public function url(string $baseUrl, ?int $width = null, ?int $height = null, ?string $bgColor = null, ?string $textColor = null, ?string $text = null, ?string $format = null): string
    {
        $format = $format ?? self::$defaultFormat;

        if (! in_array($format, self::$supportedFormats)) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported image format "%s". Supported formats are: %s',
                $format,
                implode(', ', $this->supportedFormats)
            ));
        }

        $width = $width ?? $this->defaultWidth;
        $height = $height ?? $this->defaultHeight;

        $size = $width === $height ? $width : $width.'x'.$height;
        $size = $size.($format === 'png' ? '' : '.'.$format);
        $bgColor = str_replace('#', '', $bgColor ?? $this->safeHexColor());
        $textColor = str_replace('#', '', $textColor ?? '#ffffff');
        $textColor = $textColor === 'ffffff' ? '' : $textColor;
        $text = $text ? '?text='.urlencode($text) : '';

        $url = $baseUrl.'/'.$size;
        if ($bgColor) {
            $url = $url.'/'.$bgColor;

            if ($textColor) {
                $url = $url.'/'.$textColor;
            }
        }

        if ($text) {
            $url = $url.$text;
        }

        return $url;
    }

    public function create(?int $width = null, ?int $height = null, ?string $bgColor = null, ?string $textColor = null, ?string $text = null, ?string $format = null): array
    {
        $format = $format ?? self::$defaultFormat;
        if (! in_array($format, self::$supportedFormats)) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported image format "%s". Supported formats are: %s',
                $format,
                implode(', ', $this->supportedFormats)
            ));
        }

        $width = $width ?? $this->defaultWidth;
        $height = $height ?? $this->defaultHeight;
        $bgColor = str_replace('#', '', $bgColor ?? $this->safeHexColor());
        $textColor = str_replace('#', '', $textColor ?? '#ffffff');
        $text = $text ?? $width.'&#215;'.$height;

        $image = imagecreate($width, $height);
        $bgColors = (new Color)->setHex($bgColor)->getRgb();
        $imageBgColor = imagecolorallocate($image, $bgColors[0], $bgColors[1], $bgColors[2]);
        $textColors = (new Color)->setHex($textColor)->getRgb();
        $imageTextColor = imagecolorallocate($image, $textColors[0], $textColors[1], $textColors[2]);

        imagefilledrectangle($image, 0, 0, $width, $height, $imageBgColor);
        if ($text) {
            $angle = 0.0;
            $fontFile = __DIR__.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $this->fontPath);

            $fontSize = (float) max(min($width / strlen($text) * 1.15, $height * 0.5), 5);
            $textBox = imagettfbbox($fontSize, $angle, $fontFile, $text);
            $textWidth = ceil(($textBox[4] - $textBox[1]) * 1.07);
            $textHeight = ceil((abs($textBox[7]) + abs($textBox[1])) * 1);
            $textX = ceil(($width - $textWidth) / 2);
            $textY = ceil(($height - $textHeight) / 2 + $textHeight);

            imagettftext($image, $fontSize, 0.0, $textX, $textY, $imageTextColor, $fontFile, $text);
        }

        ob_start(function ($buffer = '') {
            $buffer = trim($buffer);

            return strlen($buffer) ? $buffer : '';
        });

        match ($format) {
            'gif' => imagegif($image),
            'png' => imagepng($image),
            'webp' => imagewebp($image),
            'jpg', 'jpeg' => imagejpeg($image),
        };

        $content = ob_get_contents();
        ob_end_clean();

        return [
            'content' => $content,
            'format' => $format,
            'headers' => [
                'Content-Type' => 'image/'.$format,
                'Content-Length' => strlen($content),
            ],
        ];
    }
}
