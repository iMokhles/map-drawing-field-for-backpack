# Map Drawing Field for Backpack 4

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

This package provides a ```Map Drawing``` field type for the [Backpack for Laravel](https://backpackforlaravel.com/) administration panel. The ```Map Drawing``` field allows admins to **draw coordinates for specific areas on the map directly**. It uses [Google Map (Drawing) API V3](https://developers.google.com/maps/documentation/javascript/drawinglayer).


## Video

https://user-images.githubusercontent.com/1247248/136907629-c975068a-5bd8-4d97-b278-a77a48914b78.mov


## Requirements

- [Laravel MySQL Spatial extension][link-required-package]

## How to use ( Polygon Example )

- Edit your Model after installing [Laravel MySQL Spatial extension][link-required-package]
- Use `SpatialTrait` within your model 
- Add your area column name inside `$spatialFields`

```php
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    // You need to use SpatialTrait
    use HasFactory, SoftDeletes, SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_active',
    ];
    
    // area's column name
    protected $spatialFields = [
        'coordinates'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
    ];
}
```
- Edit your xxxCrudController
- Import `LineString`, `Point`, `Polygon`
```php
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
```
- Overwrite `CreateOperation's` and `UpdateOperation's` `store` and `update` functions to reformat the data before saving it
```php
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }

    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $req = $this->crud->getRequest();

        // do something before validation, before save, before everything
        $this->crud->setRequest($req);
        $this->crud->unsetValidation(); // validation has already been run
        $response = $this->traitStore();
        // do something after save
        $this->handleCoords($req, $this->crud->getCurrentEntry());
        return $response;
    }

    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $req = $this->crud->getRequest();

        // do something before validation, before save, before everything
        $this->crud->setRequest($req);
        $this->crud->unsetValidation(); // validation has already been run
        $response = $this->traitUpdate();
        // do something after save
        $this->handleCoords($req, $this->crud->getCurrentEntry());

        return $response;
    }

    /**
     * @param $request
     * @param Zone $item
     */
    protected function handleCoords($request, Zone $item) {
        $value = $request->coordinates;
        foreach(explode('),(',trim($value,'()')) as $index=>$single_array){
            if($index == 0)
            {
                $lastcord = explode(',',$single_array);
            }
            $coords = explode(',',$single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }

        $polygon[] = new Point($lastcord[0], $lastcord[1]);
        $item->coordinates = new Polygon([new LineString($polygon)]);
        $item->save();
    }
```

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
[link-required-package]: https://github.com/grimzy/laravel-mysql-spatial
