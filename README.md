Demo of Symfony
========================

This project is a proof of concept and a boilerplate.

A simple blog with post and categories and user.

I'm not using FOSUserBundle for users.

## Fixtures 

```
php app/console doctrine:fixtures:load
```

## TODO

- [x] Design post show
- [x] post slug
- [x] Delete comment
- [x] Change Delete categorie
- [ ] Send Email for actions
- [x] User profile picture (table file reusable)
- [x] Manage profile pictures
- [x] Auto generate password in user creation admin
- [x] Complexe password required when change