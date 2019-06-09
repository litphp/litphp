---
id: air-dynamic
title: Dynamic Entries
sidebar_label: Dynamic Entries
---

In addition to simple & static [local](air-local) entry, there're two ways to implement advanced feature: recipe & delegate.

## Recipe

Recipe is an abstraction about how to provide a value for a certain key. When user lookup an entry in air container and a corresponding recipe is found, it's `resolve` method will be invoked to provide concrete value for user.

A recipe decorator decorates another recipe to provide extra functionality.

Here's a list of bundled recipes.

| Name               | Feature                              | Example                                                    |
| ------------------ | ------------------------------------ | ---------------------------------------------------------- |
| AliasRecipe        | get specified key from container     | $container->define("foo", Container::alias("bar"))         |
| FixedValueRecipe   | return fixed value                   | $container->define("foo", Container::value("value"))       |
| BuilderRecipe      | invoke a callable                    | $container->define("foo", Container::builder(function{…})) |
| SingletonDecorator | cache result of inner recipe         | $recipe->singleton()                                       |
| CallbackDecorator  | decorate inner recipe wih callback   | $recipe->wrap(function{...})                               |
| AutowireRecipe     | use `Factory` to *produce* value     | more detail in DI section                                  |
| InstanceRecipe     | use `Factory` to *instantiate* value | more detail in DI section                                  |

It's a relatively low-level way to construct recipe instance by hand and use `$container->define` to register it, we'll [look into](air-config) `Configurator` which provide a plain php array form of recipe(s) and thus can manage dozens of recipes in a more clean and easy-to-maintain way.

## Delegate

Register delegate container with `$container->setDelegateContainer($delegatedContainer)`. This provide a way to integrate with other PSR-11 compliant container. In air, we simplified the lookup precedence rule as **local => recipe => delegate**, that means delegated container are working as a (optional) fallback. When air failed to find local entry & recipe of a key, if there's a delegated container, air will try to find entry from it before complaining not found.

