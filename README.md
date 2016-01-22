FlashyBundle
============

![screenshot](/../screenshots/screenshot.png?raw=true "Flash notifications for symfony2")

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require lexty/flashybundle "dev-master"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Lexty\FlashyBundle\LextyFlashyBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Include CSS and JS
-------------------------

Call the function `lexty_flashy_render()` in template:

```html
<!-- app/Resources/views/base.html.twig -->

<body>

...

{{ lexty_flashy_render()|raw }}

...

</body>

```

Usage
=====

### Server side

```php
// src/AppBundle/Controller/DefaultController.php

public function indexAction()
    {

        $flashy = $this->container->get('lexty_flashy.flashy');

        $flashy->add('Test message!', Flashy::TYPE_SUCCESS);

        // ...
    }
```

### Client side


```javascript
flashy.add('Success message!', 'success');
```

### Available styles

 - `Flashy::TYPE_INFO`         (`info`)
 - `Flashy::TYPE_SUCCESS`      (`success`)
 - `Flashy::TYPE_WARNING`      (`warning`)
 - `Flashy::TYPE_ERROR`        (`error`)
 - `Flashy::TYPE_MUTED`        (`muted`)
 - `Flashy::TYPE_MUTED_DARK`   (`muted-dark`)
 - `Flashy::TYPE_PRIMARY`      (`primary`)
 - `Flashy::TYPE_PRIMARY_DARK` (`primary-dark`)

License
=======
MIT
