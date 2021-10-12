# Map Drawing Field for Backpack 4

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

This package provides a ```Map Drawing``` field type for the [Backpack for Laravel](https://backpackforlaravel.com/) administration panel. The ```Map Drawing``` field allows admins to **draw coordinates for specific areas on the map directly**. It uses [Google Map (Drawing) API V3](https://developers.google.com/maps/documentation/javascript/drawinglayer).


## Video

https://user-images.githubusercontent.com/1247248/136907629-c975068a-5bd8-4d97-b278-a77a48914b78.mov


## Requirements

- [Laravel MySQL Spatial extension](https://github.com/grimzy/laravel-mysql-spatial)

## Installation

Via Composer

``` bash
composer require imokhles/map-drawing-field-for-backpack
```

## Usage

Inside your custom CrudController:

```php
$this->crud->addField([
    'name' => 'coordinates',
    'label' => 'Coordinates',
    'type' => 'map-drawing',
    'default_lat' => 30.193000747841246, // default latitude
    'default_lng' => 31.139526309011586, // default longitude
    'api_key' => 'GOOGLE_MAP_API_KEY',
    'view_namespace' => 'map-drawing-field-for-backpack::fields',
]);
```

Notice the ```view_namespace``` attribute - make sure that is exactly as above, to tell Backpack to load the field from this _addon package_, instead of assuming it's inside the _Backpack\CRUD package_.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email [the author](composer.json) instead of using the issue tracker.

## Credits

- [iMokhles](https://github.com/imokhles) - created the map-drawing field;
- [Cristian Tabacitu](https://github.com/tabacitu) - Backpack for Laravel;
- [Google](https://developers.google.com/maps/documentation/javascript/drawinglayer) - Google Map API;
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/imokhles/map-drawing-field-for-backpack.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/imokhles/map-drawing-field-for-backpack.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/imokhles/map-drawing-field-for-backpack
[link-downloads]: https://packagist.org/packages/imokhles/map-drawing-field-for-backpack
[link-author]: https://imokhles.com
[link-contributors]: ../../contributors
