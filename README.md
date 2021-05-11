# Gift Cards

Gift Cards es un sistema de venta de gift cards sincronizado con la plataforma TiendaNube.

Permite administrar usuarios, productos , importar ventas desde TiendaNube y generar ventas manuales.

Envía las giftcards a los clientes, ya sea en formato PDF o en formato .zip para que las descarguen.

## Requisitos
* mysql (5.6 - 8)
* php 7.3
* composer
* npm

## Installation

Clonar el repositorio, y una vez dentro de la carpeta raíz del mismo:

```bash
cp .env.example .env -> set up credenciales db, key tienda nube (de ser necesario)
composer install
npm run dev
```

## Contribuir
Pull request / issues / tests son bienvenidos

## Docker
Experimental, no se usa en producción (en el caso del autor)


## License
-

## Supervisor

copiar archivo de ejemplo a la ruta configurada en supervisor e indicar las rutas correctas hacia los archivos de log y la carpeta del proyecto

sudo supervisorctl reread
sudo supervisorctl update