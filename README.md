Demo of Symfony
========================

## Version

Symfony 3.3.5

## Description

This project is a proof of concept and a boilerplate.

A simple blog with post and categories and user.

## Bundles

I'm not using FOSUserBundle for users.

- JMSSerializerBundle
- StofDoctrineExtensionsBundle

## Fixtures 

```
php app/console doctrine:fixtures:load
```

## TODO

- [ ] Send Email for actions
- [ ] Fix service Injection