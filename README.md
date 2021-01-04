## Herramienta para administrar ventas mayoritas y validar giftcards vendidas a travéz de TiendaNube

Autor: Pedro Scarselletta

## Deploy

Clone Repo
```git clone https://github.com/pe-sca/gift_cards.git```

Enter dir
```cd gift_cards```

Copiar .env de ejemplo y compĺetar:
1. credenciales de bd
2. credenciales mail
3. credenciales TiendaNube
4.  setear app url

```cp .env.example .env```

Permisos para .env
```sudo chown www-data:www-data .env```

Permisos para storage
```sudo chown -R www-data:www-data storage/```

Build docker app
```docker-compose build app```

Run containers
```docker-compose up```

Enter app container
```docker-compose exec app /bin/bash```

Install composer dependencies
```composer install```

Generate app key
```php artisan key:generate```

Migrate & seed db
```php artisan migrate:fresh --seed```

Install node dependencies
```npm install```

Build for prod
```npm run production```

## Requerimentos

- **[Docker Compose.](https://docs.docker.com/compose/)**


- [ ] probando
