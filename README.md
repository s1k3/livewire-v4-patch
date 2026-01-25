##### This Library is for Converting Livewire 4 Class Component to MFC 

## Compatibility

This package will work with PHP >= 8.2 with CURL enabled.

## Installation

To install this library follow the following steps:
```bash
 composer require --dev s1k3/livewire-v4-patch
```



## Publish Config File

* Execute the following command from the command-line to publish the configuration file config/livewire-v4-patch.php. this command will generate a file as above 

``` php
php artisan vendor:publish --provider="LivewireV4\\LivewireV4PatchServiceProvider"
```



**Configuration Options**

| Key	| Type | Default | Description |
|--------|-----|--------|------------|
| excluded_directories	|array	| [] |	Array of directory names to exclude from Livewire component generation|
| class_component_path	|string	| base_path('app/Livewire')	|Path where Livewire class components are stored|
| mfc_component_path | string	| resource_path('views/components') |	Path for Model-Focused Components (MFC)|
| create_js	| boolean | false | Whether to create JavaScript files alongside components|
| create_css	| boolean | false	| Whether to create CSS files alongside components|
| global_css	|boolean    |false	    | Whether to use global CSS instead of component-specific CSS|
| emoji	| boolean	    | false	| Whether to use emoji in generated component output|




## Component Conversions Command


``` php
php artisan convert-class-to:mfc <Path/of/the/component.php>
```

All paths are being calculated from **class_component_path**. For example if your compoent is in **app/Livewire/Posts/Create.php**
then your command will be 

``` php
php artisan convert-class-to:mfc Posts/Create.php
```

If you want to convert a full directory for example, if Posts have three components

- Create.php
- Edit.php
- Show.php 

And you want to convert them all just pass the folder name in the command

``` php
php artisan convert-class-to:mfc Posts
```

***Please Note that this will delete old class component files and view files***

To keep the old component files and view files **--keep-class-files** option needs to be used. 

```php
php artisan convert-class-to:mfc <path/of/the/file/directory> --keep-class-files
```

In that case, **class and view files needs to be removed manually**


After Conversions you might have to clear all the caches and autoload everything.

``` php
php artisan optimize:clear
```

After Conversions you might have to clear all the caches and autoload everything.

``` php
php artisan optimize:clear
```

``` bash
composer dump-autoload
```

``` bash
composer install --optimize-autoloader
```
If those command still doesn't work then delete **composer.lock** and **vendor/** directory after that 
install all the packages

``` bash
composer install
```