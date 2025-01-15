# DummyImage

## Install

```
composer require ilaoniu/dummyimage --dev
```

## Laravel

First, add a new route:

```php
Route::middleware('cache.headers:public;max_age=2628000;etag')->get('__dummy-image/{size}/{bgColor?}/{textColor?}', function (Request $request, string $size, ?string $bgColor = null, ?string $textColor = null) {
    $dummyImage = new DummyImage;
    ['content' => $content, 'headers' => $headers] = $dummyImage->image($size, $bgColor, $textColor, $request->query('text'));

    return response($content)->withHeaders($headers);
});
```

Then, you can get image url like this:

```php
$dummyImage = new DummyImage;
$dummyImage->imageUrl(config('app.url') . '/__dummy-image');
```

## Usage

<a name="size"></a>

### Size

width x height

-   Height is optional, if no height is specified the image will be a square. Example: https://dummyimage.com/300
-   **Must** be the first option in the url
-   You can specify one dimension and a ratio and dummyimage will calculate the right value. Example: https://dummyimage.com/640x4:3 or https://dummyimage.com/16:9x1080

<a name="color"></a>

### Colors

background color / text color

-   Colors are represented as hex codes (#ffffff is white).
-   Colors always follow the dimensions, https://dummyimage.com/250/ffffff/000000 not https://dummyimage.com/ffffff/250/000000.
-   The first color is always the background color and the second color is the text color.
-   The background color is optional and defaults to gray (#cccccc)
-   The text color is optional and defaults to black (#000000)
-   There are shortcuts for colors
    -   3 digits will be expanded to 6, `09f` becomes `0099ff`
    -   1 digit will be repeated 6 times, `c` becomes `cccccc` Note: a single 0 will not work, use 00 instead.
-   Standard image sizes are also available. See the <a href="#standards">complete list</a>.
    -   https://dummyimage.com/qvga
    -   https://dummyimage.com/skyscraper/f0f/f

<a name="format"></a>

### Image Formats

.gif, .jpg, .png, .webp

-   Adding an image file extension will render the image in the proper format
-   Image format is optional and the default is a gif
-   jpg and jpeg are the same
-   The image extension can go at the end of size option in the url
    -   https://dummyimage.com/300.png/09f/fff

<a name="text"></a>

### Custom Text

?text=Hello+World

-   Custom text can be entered using a query string at the very end of the url
-   This is optional, default is the image dimensions (300&times;250)
-   a-z (upper and lowercase), numbers, and most symbols will work just fine.
-   Spaces become +
    -   https://dummyimage.com/200x300?text=dummyimage.com+rocks!

## Thanks

https://github.com/kingkool68/dummyimage, Dummy Image is written in PHP and distributed freely under a MIT License.

Source code behind https://dummyimage.com
