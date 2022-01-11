# Laravel CRUD

En primer lugar creamos un proyecto de Laravel con el nombre laravel_crud:
```
composer create-project --prefer-dist laravel/laravel laravel_crud
```

## Instalamos breeze

[laravel-breeze](https://laravel.com/docs/8.x/starter-kits)

Para ello ejecutamos los siguientes comandos:

```
composer require laravel/breeze --dev
php artisan breeze:install
npm install
npm run dev
```

Después de ejecutar esos comandos en la terminal, creamos una base de datos que enlazaremos con el proyecto de Laravel.

En el archivo `.env` debemos añadir la información de la base de datos y las credenciales de acceso:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=amorell_db
DB_USERNAME=amorell_usr
DB_PASSWORD=abc123.
```

Una vez que tenemos en el archivo `.env` la información de la base de datos, hacemos la migración que añadirá las tablas
que utilizará `breeze` para el _login_.
```
php artisan migrate
```

## Controlador y Rutas

Añadimos al rpoyecto un controlador denominado AgendaController que tendrá todos los recursos:
```
php artisan make:controller AgendaController --resource
```

En el archivo `web.php` añadimos una ruta `/agenda` que ejecutará el controlador:
```injectablephp
Route::resource('agenda', AgendaController::class);
```

En el archivo `AuthServiceProvider.php` que encontramos en la ruta `/app/Providers/` definimos la Gate:
```injectablephp
public function boot()
    {
        $this->registerPolicies();

//        Definimos la autorización con Gate
        Gate::define('access', function() {
            return false;
        });
    }
```

En la función `index` del controlador añadimos lo que queramos que se muestre al acceder a la ruta creada y añadimos la
Gate que hemos definido previamente:
```injectablephp
public function index()
    {
//        Ejecutamos la autorización definida en AuthServiceProvider.php
        Gate::allows('access');
        echo 'blablabla breeze';
    }
```

Y como era de esperar la Gate no hace nada 👌
