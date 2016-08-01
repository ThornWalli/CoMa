# handlebars.php-agency-boilerplate

Handlebar.php Helper for [agency-boilerplate](https://github.com/StephanGerbeth/agency-boilerplate).

> Use `if_var` for conditions, `if` helper is not compactible with `var` helper.

> ```mustache
> {{if_var conditional}}
>   true
> {{else}}
>   false
> {{/if_var}}
> ```

## Options

`\AgencyBoilerplate\Handlebars\Core::init($options)`

```javascript
[
  'partialDir' => '.',
  'extension' => '.hbs',
  'prefix' => ''
]
```

### partialDir

Type: `mixed|string`

Sets partial dirs for loading partial via mixin helper.

### extension

Type: `string`<br>
Default: `.handlebars`

### prefix

Type: `string`<br>
Default: `empty`

Sets prefix for template files.

#### Example:

> prefix: "_" -> filename: "_partial.hbs"

## Features

> yaml (in progress) <https://hbs-php-agency-boilerplate.herokuapp.com/tests/yaml>

### Helpers

#### lookup

<https://hbs-php-agency-boilerplate.herokuapp.com/tests/lookup>

#### mixin

<https://hbs-php-agency-boilerplate.herokuapp.com/tests/mixin>

#### stringfy

<https://hbs-php-agency-boilerplate.herokuapp.com/tests/stringify>

#### with

<https://hbs-php-agency-boilerplate.herokuapp.com/tests/with>

#### def

<https://hbs-php-agency-boilerplate.herokuapp.com/tests/def>

**Pattern**:

```mustache
{{{def name="PropertyName" type="PropertyType"}}}
```

Properties:

Property      | Description
------------- | -------------
`group`       | Group
`name`        | Name
`type`        | Type
`title`       | Title
`description` | Description
`default`     | Default Value

##### name

Type: `string`

Sets property name.

##### type

Type: `string`

Sets property type.

##### default

Type: `mixed`

Sets default property value.

## Use

### Handlebars index.hbs

```mustache
<h1>stringify example</h1>

<h2>scope: this</h2>
<p>{{{stringify this}}}</p>

<h2>scope: object</h2>
<p>{{{stringify object}}}</p>

<h2>scope: array</h2>
<p>{{{stringify array}}}</p>
```

### PHP index.php

```php
<?php

// require via composer
require __DIR__ . '/../../vendor/autoload.php';

// init core
$core = \AgencyBoilerplate\Handlebars\Core::init([
  'partialDir' => __DIR__ . '/tmpl/'
]);

// defined data object
$data = [
  'object' => [
    'hello' => 'world'
  ],
  'array' => [
    0, 1, 2, 3
  ]
];

// render with engine
echo $core->getEngine()->render('index', $data);

?>
```

## TODOS

- [x] mixin helper with block and content
- [x] implement var helper, for define properties
- [x] yaml placeholder data
