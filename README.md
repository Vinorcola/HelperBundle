# VinorcolaHelperBundle

Provide some useful base classes and services.

## Translation system

The bundle provide a `TranslationModel` service which help building international apps by standardizing the way translation keys are built.

When you translate an application, you mostly end-up sorting messages in 2 categories: messages linked to an attribute of an object (an entity) and messages linked to the page context.

### Messages linked to page context

This translation system introduce a system of relative `keys` based on the route name. For example, if your action is triggered by a route called `myController.myAction`, then if you translate the message key `message` (using `TranslationModel::tr`), it will look for the key `myController.myAction.message` in Symfony translation system. This helps simplifying templates:

```twig
{% extends 'base.html.twig' %}

{% block title %}{{ pageTitle() }}{% endblock %}

{% block body %}
    <p>{{ 'message'|tr }}</p>

    {{ form_start(form) }}
        {{ form_widget(form) }}
        <div class="form-group">
            <button class="btn btn-primary">{{ 'submit'|tr }}</button>
            <a href="{{ path('...') }}" class="btn btn-secondary">{{ 'cancel'|tr }}</a>
        </div>
    {{ form_end(form) }}
{% endblock %}
```

In this example,

* `{{ 'message'|tr }}` is equivalent to `{{ myRouteName.message'|trans }}`
* `{{ 'submit'|tr }}` is equivalent to `{{ myRouteName.submit'|trans }}`
* `{{ 'cancel'|tr }}` is equivalent to `{{ myRouteName.cancel'|trans }}`
* `{{ pageTitle() }}` is equivalent to `{{ 'cancel'|tr }}` or `{{ myRouteName.title'|trans }}`

Furthermore, the system will auto-wrap translation parameters with percent (`%`):

```twig
{# With Symfony translation #}
{{ 'myRouteName.message'|trans({ '%title%': someTitle, '%date%': someDate }) }}

{# With VinorcolaHelper translation #}
{{ 'message'|tr({ title: someTitle, date: someDate }) }}
```

You can still keep the percent if you like (or for compatibility purposes):

```twig
{# Those lines are all equivalent: #}
{{ 'message'|tr({ title: someTitle, date: someDate }) }}
{{ 'message'|tr({ title: someTitle, '%date%': someDate }) }}
{{ 'message'|tr({ '%title%': someTitle, '%date%': someDate }) }}
```

If you require a specific message to be translated (instead of a relative message prepended with the route name), you can simply prefix your message key with a `=` sign:

```twig
{# With Symfony translation #}
{{ 'some.specific.message'|trans({ '%title%': someTitle, '%date%': someDate }) }}

{# With VinorcolaHelper translation #}
{{ '=some.specific.message'|tr({ title: someTitle, date: someDate }) }}
```

If you require a plural translation, you can suffix your message key with a `+` sign:

```twig
{# With Symfony translation #}
{{ 'myRouteName.message'|transchoice(5, { '%title%': someTitle, '%date%': someDate }) }}

{# With VinorcolaHelper translation #}
{{ 'message+'|tr({ count: 5, title: someTitle, date: someDate }) }}
```

### Messages linked to object attributes

The `TranslationModel::tra()` method help building translation keys base on object attributes. Your must provide an attribute name and an object name and it will translate the message `attribute.myObject.myAttribute` with Symfony translation system.

For example in templates:

```twig
<table>
    <thead>
        <tr>
            <th>{{ 'title'|tra('user') }}</th>
            <th>{{ 'emailAddress'|tra('user') }}</th>
            <th>{{ 'city'|tra('user') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {% for user in users %}
            <tr>
                <td>{{ user.title }}</td>
                <td>{{ user.emailAddress }}</td>
                <td>{{ user.city }}</td>
            </tr>
        {% endfor %}
    </tbody>
</table>
```

* `{{ 'title'|tra('user') }}` is equivalent to `{{ 'attribute.user.title'|trans }}`
* `{{ 'emailAddress'|tra('user') }}` is equivalent to `{{ 'attribute.user.emailAddress'|trans }}`
* `{{ 'city'|tra('user') }}` is equivalent to `{{ 'attribute.user.city'|trans }}`

You can then use this pattern in Symfony forms:

```php
<?php

namespace App\Form\Panel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('label_format', 'attribute.myObject.%name%');
    }
}
```

## Base controller

The bundle provide a base controller inheriting Symfony's base controller and helping working with the translation system. It provides methods to easily add error messages to form and flash messages.

Furthermore, it provides a `saveDatabase()` method that is basically doing `$em->flush()`. Your controller gain in readability and you don't need to inject the `EntityManager` anymore if you are working with repositories as services!

## Simple repository

This bundle provide a very simple repository that help you to declare your repositories as services. No more `$em->getRepository(MyEntity::class)`, simply inject your repository as you would do with any services.
