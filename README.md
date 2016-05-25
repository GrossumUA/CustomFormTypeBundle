Extended Form Type Bundle
=========================

[![Latest Stable Version](https://poser.pugx.org/grossum/extended-form-type/v/stable)](https://packagist.org/packages/grossum/extended-form-type) [![Total Downloads](https://poser.pugx.org/grossum/extended-form-type/downloads)](https://packagist.org/packages/grossum/extended-form-type) [![Latest Unstable Version](https://poser.pugx.org/grossum/extended-form-type/v/unstable)](https://packagist.org/packages/grossum/extended-form-type) [![License](https://poser.pugx.org/grossum/extended-form-type/license)](https://packagist.org/packages/grossum/extended-form-type)

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require grossum/extended-form-type
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

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

            new Grossum\ExtendedFormTypeBundle\GrossumExtendedFormTypeBundle(),
        );

        // ...
    }

    // ...
}
```


Step 3: Configure the Bundle
----------------------------

Add routing to your project in `app/config/routing.yml`:

```
// app/config/routing.yml

# Routing Configuration Example

grossum_extended_form_type:
    resource: "@GrossumExtendedFormTypeBundle/Resources/config/routing.yml"
    prefix:   /

```

Register twig form template in `app/config/config.yml`

```
// app/config/config.yml

# Twig Configuration Example

twig:
    // ...
    form:
        resources:
            // ...
            - 'GrossumExtendedFormTypeBundle::dependent_filtered_entity.html.twig'
```


Enable bundle for your entity  in `app/config/config.yml`:

```
// app/config/config.yml

# Entity Configuration Example

grossum_extended_form_type:
    dependent_filtered_entities:
        test_type:
            class: Your\Bundle\Entity\EntityName
            parent_property: test 
            property: name
            no_result_msg: 'No type found'
            order_property: name 
            order_direction: ASC
```


Step 4: Usage
-------------

```php

<?php

// ...

class TestAdmin extends Admin
{

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            
            // ...
            
            ->add(
                'yourType',
                'grossum_dependent_filtered_entity',
                [
                    'entity_alias' => 'your_alias',
                    'empty_value'  => 'Select some value',
                    'parent_field' => 'test',
                    'label'        => 'Your label',
                ]
            )
            
            // ...
           
            ->end();
    }
    
     // ...
    
}
```
