# twig-to-wpml

This tools scans a provided directory for Twig templates and extracts strings 
from gettext function calls. Then it produces a PHP file with those translatable strings
in a way that it can be parsed by [WPML String Translation](https://wpml.org/documentation/getting-started-guide/string-translation/) 
or tools like POEdit.

The main goal is to allow developers from the WordPress ecosystem to define custom
Twig functions for gettext functions and use them directly in templates:

```php
$twig->addFunction( 
    new \Twig\TwigFunction( '__', '__' )
);		
```

```twig
<p>{{ __( 'This is a translatable string in a Twig template', 'my-textdomain' ) }}</p>
``` 

The output of this tool will be a file like this:

```text
<?php
// Template: mytemplate.twig
//
//
__( 'This is a translatable string in a Twig template', 'my-textdomain' )
```

## Installation

1. Clone the repository.
2. Run `composer install`.

## Usage

At the moment, only a commandline usage is supported.

```bash
./bin/index.php --input /path/to/dir/with/templates -t 'DEFAULT-TEXTDOMAIN' --output /path/to/output.php
```

Run the command with a `--help` argument to receive the full usage information.

## To do

- Better error handling and logging.
- Unit tests.
- WP CLI command.

_Contributions are very welcome._

Made with :heart: for [Toolset](http://toolset.com) and [OnTheGoSystems](http://onthegosystems.com).
