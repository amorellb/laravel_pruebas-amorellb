# Laravel CRUD

## Crear un proyecto en Laravel

En primer lugar creamos un nuevo propyecto de Laravel con el siguiente comando:

```bash
composer create-project --prefer-dist laravel/laravel project_name
```

En el archivo `.env` debemos especificar la base de datos con la que trabajaremos:

```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_name
DB_USERNAME=db_usr
DB_PASSWORD=db_psswd
```

## Añadir la dependencia breeze (login)

Para poder tener un sistema de _login_ instalamos la dependecia `breeze`. Para la instalación y configuración debemos ejecutar los siguientes comandos comandos:

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install
npm run dev
php artisan migrate
```

## Migrations & Seeders

### Migration Agenda

Para crear la migración de la tabla `agenda` debemos ejecutar el siguiente comando:

```bash
php artisan make:migration create_contacts_table
```

Este comando son creará el archivo `fecha_id_create_contacts_table.php` situado en `/database/migrations` y en el que deberemos añadir dentro de la función `up` el siguiente esquema:

```php
Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->date('birth_date')->nullable();               // type date
            $table->string('email')->nullable()->unique();
            $table->integer('phone')->unique();
            $table->string('country')->nullable();                // select
            $table->string('address')->nullable();                // type textarea
            $table->string('job_contact')->nullable();           // type radiobutton
        });
```

De esta forma se creará la tabla agenda que contendrá las columnas id (que contendrá un valor incremental), name, slug (para definir las rutas dinámicas), email, phone, address y dos columnas creadas mediante la función `timestamps` que contendrán la fecha en que se creó un contacto de la agenda.

#### Foreign key `user_id`

Como nos va a interesar tener una relación entre la tabla de usuarios creada por `breeze` y la tabla `agenda`, de forma que cada usuario tenga su lista de contactos y pueda realizar determinadas acciones como crear, editar o borrar contactos, a la tabla `agenda` debemos añadirle una clave foránea que será el `id` del usuario.

Para esto añadiremos un nuevo esquema que nos añadirá una columna a la tabla especificando que se trata de una clave foránea que está relacionada con la tabla `users`:

```php
Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->after('id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
```

Esta función añadirá una columan `user_id` que puede ser `null`. Para añadirla, especificamos que se trata de una clave foránea cuya referencia en la columna `id` de la tabla `users` y que en caso de actualizar la columna `id` de `users`, también se actualice la columna `user_id` de `agenda`, pero que en caso de eliminar algún valor de `id` de `users`, este no se borre en `agenda`, sinó que se actualice a `null`.

La clase `CreateContactsTable` quedará de la siguiente manera:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->date('birth_date')->nullable();               // type date
            $table->string('email')->nullable()->unique();
            $table->integer('phone')->unique();
            $table->string('country')->nullable();                // select
            $table->string('address')->nullable();                // type textarea
            $table->string('job_contact')->nullable();           // type radiobutton
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->after('id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
```

Al añadir la función `$table->softDeletes();`hacemos que se añada una colunma `deleted_at`, en la cual se añadirá la fecha en que se borró el registro en lugar de eliminarlo de la tabla.

Para crear la tabla agenda en la base de datos debemos ejecutar el comando `php artisan migrate`.

### Migration Users

Nos interesa hacer una modificación a la tabla `users` para que contenga una columna adicional denominada `role` la cual podrá tener los valores `super`, `admin`, `user` y `visitor`. Esta columna nos permitirá definir roles dentro de la aplicación, de manera que tan solo usuarios con determinados permisos puedan acceder a determinadas partes de la web o a determinadas funcionalidades.

Para ello, primero creamos una migración con el comando `php artisan make:migration add_role_to_users_table`, el cual nos creará la clase `AddRoleUsersTable` en `/database/migrations`.

Dentro de la clase, en la fiunción `up` debemos añadir una función que nos añada la columna `role` con los valores que nos interesan y además le diremos que el valor por defecto será `user`.

El archivo debe quedar de la siguiente manera:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super', 'admin', 'user', 'visitor'])
                ->default('user')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
```

### Seeder Agenda (_opcional_)

Adicionalmente podemos crear un `seeder` para la tabla `agenda`, para añadir algunos valores.

Para crear el `seeder` ejecutaríamos el siguiente código:

```bash
php artisan make:seeder ContactsSeeder
```

Esto nos creará un archivo `ContactsSeeder.php` en `/database/seeders` al cual le añadiremos las siguientes líneas dentro de la función `run`:

```php
$contacts = [
            ['name' => 'Bernat Smith', 'slug' => Str::slug('Bernat Smith', '-'), 'birth_date' => '1912-10-10',
                'email' => 'bernat@email.com', 'phone' => 123456784, 'country' => 'England', 'address' => 'Calle 123', 'job_contact' => 'yes', 'user_id' => '3'],
            ['name' => 'Margalida Johnson', 'slug' => Str::slug('Margalida Johnson', '-'), 'birth_date' => '1912-10-10',
                'email;' => 'mjohnson@email.com', 'phone' => 987654321, 'country' => 'Spain', 'address' => 'Calle calle 321', 'job_contact' => 'yes','user_id' => '2'],
            ['name' => 'Miquel Jackson', 'slug' => Str::slug('Miquel Jackson', '-'), 'birth_date' => '1912-10-10',
                'email' => 'mjackson@email.com', 'phone' => 123432123, 'country' => 'Spain', 'address' => 'calle 123, street', 'job_contact' => 'no', 'user_id' => '3'],
        ];

        DB::table('contacts')->insert($contacts);
```

Una vez tenemos el `seeder`, podemos poblar la tabla ejecutando `php artisan db:seed --class=ContactsSeeder`.

_Aparte de los `seeders` podríamos añadir datos falsos a la base de datos mediante Faker._

### Seeder Users (_opcional_)

Para la tabla users nos puede interesar tener unos usuarios con distintos roles para probar el acceso a las diferentes partes y funcionalidades de la apliación.

```php
$users = [
            ['name' => 'Super Bernat', 'email' => 'superbernat@email.com',  'password' => Hash::make('12345678'), 'role' => 'super'],
            ['name' => 'Bernat', 'email' => 'bernat@email.com',  'password' => Hash::make('12345678'), 'role' => 'admin'],
            ['name' => 'Margalida', 'email' => 'margalida@email.com',  'password' => Hash::make('12345678'), 'role' => 'user'],
            ['name' => 'Miquel', 'email' => 'miquel@email.com',  'password' => Hash::make('12345678'), 'role' => 'visitor'],
        ];

        DB::table('users')->insert($users);
```



**Queda pendiente (podríamos crear usuarios en la web y después cambiar sus roles a nivel de base de datos)**

## Controller, Model & Routes

Para crear el controlador `AgendaController` con los recursos necesarios y asociarlo a un modelo `Agenda`, ejecutamos el siguiente comando:

```bash
php artisan make:controller ContactsController --resource --model=Contact
```

Esto nos creará el archivo `ContactsController.php` ubicado en `/app/Http/Controllers` y con las funciones `index`, `create`, `store`, `show`, `edit`, `update` y `destroy` que están asociuadas a las rutas necesarias para el CRUD.

### Models

#### Contact model

En la ubicación `/app/Models` en contraremos el archivo `Contact.php` con la clase `Contact` que es el modelo para nuestra base de datos.

En este archivo especificaremos a que tabla debe enlazarse el modelo. Además, podemos añadir algunas propiedades que nos servirán para la creación de nuevos registros en la tabla o la actualización de los existentes, concretamente la propiedad `fillable` que será un array con los campos de la tabla `name`, `slug`, `birth_date` `email`, `phone`, `country`, `address`, `job_contact`, `user_id`, que son los únicos campos de la tabla `agenda` que deben poder ser accesible. Este parámetro debemos definirlo para poder utilizar la función `Contact::all()`.

Además, en este modelo añadiremos una función que nos servirá para determinar la relación con la tabla `users`:

```php
public function user()
    {
        return $this->belongsTo(User::class);
    }
```

Por último, hacemos un _override_ de la función `getRouteKeyName` de la clase `Model` para que las rutas dinámicas se creen mediante los valores de la columna `slug` de `agenda` y no mediante el `id`. Esto hará que además los diferentes contactos se identifiquen mediante el `slug` en las funciones del controlador `AgendaController`.

```php
public function getRouteKeyName()
    {
//    return parent::getRouteKeyName();
        return 'slug';
    }
```

La clase quedará así:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use softDeletes;

    public $table = 'contacts';

//  Esta propiedad hace que solo se puedan guardar en la base de datos los campos indicados en el array
    protected $fillable = ['name', 'slug', 'birth_date', 'email', 'phone',
        'country', 'address', 'job_contact', 'user_id'];

//    Esta propiedad sirve para evitar que se guarden o modifiquen en la base de datos los campos que se indican en el array
//    protected $guarded = ['status'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
```

`SoftDelete` evita borrar de forma definitiva los registros de la tabla.

#### User model

Además de definir la relación entre las tablas `agenda` y `users` en el modelo `Contact` debemos definirla en el modelo `User` con la siguiente función:

```php
public function contacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contact::class);
    }
```

La clase `User` quedará así:

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
```

Una vez que tenemos listas tanto las migraciones como los modelos debemos ejecutar el comando `php artisan migrate:refresh` para volver a generar las tablas con las nuevas columnas. _Este comando borrará todo lo que haya en las tablas y las generará de nuevo_.

### Routes

En el archivo `/routes/web.php` deberemos añadir una única ruta:

```php
Route::resource('contacts', ContactsController::class)
    ->middleware('auth')
    ->parameters(['contacts' => 'contact']);
```

En la ruta además especificamos que se ejecute el _middleware_ de autentificación de usuario y que las funciones del controlador acepten como parámetro `contact` en lugar de `contacts`, que sería el que aceptaría Laravel por defecto al llamarse la ruta `agenda`.

Podemos comprobar las rutas ejecutando el siguiente comando en la terminal:

```bash
php artisan r:l -c
```

El archivo `web.php` quedará así:

```php
<?php

use App\Http\Controllers\ContactsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::resource('contacts', ContactsController::class)
    ->middleware('auth')
    ->parameters(['contacts' => 'contact']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/set_language/{lang}', [App\Http\Controllers\Controller::class, 'set_language'])->name('set_language');

require __DIR__ . '/auth.php';
```

La ruta `dashboard` se crea al añadir `breeze` al proyecto.

En la ruta raíz `/` indicamos que se muestre la vista `home` y además que esta ruta tenga en nombre `home`.

### Controller

En la clase `ContactsController` debemos definir toda la lógica del CRUD

Cada función de esta clase tendrá su papel:

#### index

```php
public function index()
    {
//        Ejecutamos la autorización definida en AuthServiceProvider.php
//        1)
//        $this->authorize('access', 403);
//        2)
//        abort_unless(Gate::allows('access'), 403);
//        3)
//        if (Gate::allows('access')) {
//            $contacts = Contact::all();
//            return view('contacts.index', compact('contacts'));
//        }
//        Abort(403);

//        Mediante una Gate permitimos que un usuario 'admin' pueda ver los contactos de todos los usuarios
  
//			Gate para el usuario con role super
  			if (Gate::allows('viewAllAndDeleted')) {
//            QueryBuilder
//            $query = DB::table('contacts')->where('user_id', Auth::id())->get();
//            $contacts = $query->all();
//            Eloquent
            $contacts = Contact::with('user')->onlyTrashed()->orderBy('name')->get();
            return view('contacts.index', compact('contacts'));
        }

//			Gate para el usuario con role admin
        if (Gate::allows('viewAll')) {
            $contacts = Contact::with('user')->orderBy('name')->get();
            return view('contacts.index', compact('contacts'));
        }

//			Policies para el resto de roles
        $this->authorize('viewAny', Contact::class);
//        QueryBuilder
//        $query = DB::table('contacts')->where('user_id', Auth::id())->get();
//        $contacts = $query->all();

        $contacts = Contact::where('user_id', Auth::id())->get();
        return view('contacts.index', compact('contacts'));
    }
```

En primer lugar se comprueba si el usuario logeado tiene acceso a la vista mediante una `Policy`. En caso de tener acceso, se filtrarán los datos del modelo en función del `id` del usuario y se retornará la vista `index` con los contactos de ese usuario.

#### create

```php
public function create()
    {
        $this->authorize('create', Contact::class);

        return view('contacts.create');
    }
```

En esta función se comprueba si el usuario tiene permisos para crear nuevos contactos mediante una `Policy` y en caso de tenerlos se devuleve la vista `create`.

#### store

```php
public function store(StoreContacts $request): RedirectResponse
    {
//        Raw
//        $request['slug'] = Str::slug($request->name, '-');
//
//        $name = $request->name;
//        $slug = $request->slug;
//        $birth_date = $request->birth_date;
//        $email = $request->email;
//        $phone = $request->phone;
//        $country = $request->country;
//        $address = $request->address;
//        $job_contact = $request->job_contact;
//        $user_id = $request->user()->id;
//
//        DB::insert("insert into contacts (name, slug, birth_date, email, phone, country, address, job_contact, user_id)
//values ($name, $slug, $birth_date, $email, $phone, $country, $address, $job_contact, $user_id)");
//        -----------------------

//        QueryBuilder
//        $request['slug'] = Str::slug($request->name, '-');
//        DB::table('contacts')->insert([
//            'name' => $request->name,
//            'slug' => $request->slug,
//            'birth_date' => $request->birth_date,
//            'email' => $request->email,
//            'phone' => $request->phone,
//            'country' => $request->country,
//            'address' => $request->address,
//            'job_contact' => $request->job_contact,
//            'user_id' => $request->user()->id
//        ]);
//        ------------------------

//        Eloquent
        $request['slug'] = Str::slug($request->name, '-');
        $imgURL = $request->file('file')->storeAS('contacts_img', $request->file->getClientOriginalName());

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->slug = $request->slug;
        $contact->birth_date = $request->birth_date;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->country = $request->country;
        $contact->address = $request->address;
        $contact->job_contact = $request->job_contact;
        $contact->user_id = $request->user()->id;
        $contact->image = $imgURL;
        $contact->save();



//        Eloquent
//        $contact = Contact::create($request->all());
//        $contact['slug'] = Str::slug($request->name, '-');
//        $contact->user_id=Auth::id();
//        $contact->save();
//
        return redirect()->route('contacts.index');
    }
```

En esta función, en primer lugar se añade a la `request` un valor a la propiedad `slug`, que se forma mediante el nombre del contacto separando las palabras por guiones.

En segundo lugar, se instancia la clase `Contact` y se van definiendo las propiedades de la misma con los datos de la `request`.

Por último, se guardan los datos del nuevo contacto y se redirige al usuario a la vista `index`.

#### show

```php
public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        return view('contacts.show', compact('contact'));
    }
```

En esta función se comprueba si el usuario tiene acceso mediante una `Policy` y si es así se le redirige a la vista `show`.

#### edit

````php
public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);

        return view('contacts.edit', compact('contact'));
    }
````

La función comprueba si el usuario puede editar el contacto y si es así lo redirige a la vista  `edit`.

#### update

```php
public function update(StoreContacts $request, Contact $contact): RedirectResponse
    {
//        Validación del formulario
//        $request->validate([
//            'name' => 'required',
//            'phone' => 'required|size:9',
//        ]);

//        $contact->name = $request->name;
//        $contact->email = $request->email;
//        $contact->phone = $request->phone;
//        $contact->address = $request->address;
//        $contact->save();

        $this->authorize('update', $contact);

        $contact->update($request->all());
        return redirect()->route('contacts.index');
    }
```

La función comprueba que el usuario tenga permisos para actualizar registros y de ser así se actualizan los datos del contacto editado y se redirige al usuario a la vista `index`.

#### destroy

```php
public function destroy(Contact $contact): RedirectResponse
{
    $this->authorize('delete', $contact);

        $contact->delete();
        return redirect()->route('contacts.index');
}
```

La función comprueba que el usuario tenga permisos para borrar registros y si es así borrará el contacto especificado por el usuario y este será redirigido a la vista `index`.

La clase quedará tal que así:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContacts;
use App\Models\Contact;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        if (Gate::allows('viewAllAndDeleted')) {
//            QueryBuilder
//            $query = DB::table('contacts')->where('user_id', Auth::id())->get();
//            $contacts = $query->all();
//            Eloquent
            $contacts = Contact::with('user')->onlyTrashed()->orderBy('name')->get();
            return view('contacts.index', compact('contacts'));
        }

        if (Gate::allows('viewAll')) {
            $contacts = Contact::with('user')->orderBy('name')->get();
            return view('contacts.index', compact('contacts'));
        }

        $this->authorize('viewAny', Contact::class);
//        QueryBuilder
//        $query = DB::table('contacts')->where('user_id', Auth::id())->get();
//        $contacts = $query->all();

        $contacts = Contact::where('user_id', Auth::id())->get();
        return view('contacts.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Contact::class);

        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreContacts $request
     * @return RedirectResponse
     */
    public function store(StoreContacts $request): RedirectResponse
    {
//        Raw
//        $request['slug'] = Str::slug($request->name, '-');
//
//        $name = $request->name;
//        $slug = $request->slug;
//        $birth_date = $request->birth_date;
//        $email = $request->email;
//        $phone = $request->phone;
//        $country = $request->country;
//        $address = $request->address;
//        $job_contact = $request->job_contact;
//        $user_id = $request->user()->id;
//
//        DB::insert("insert into contacts (name, slug, birth_date, email, phone, country, address, job_contact, user_id)
//values ($name, $slug, $birth_date, $email, $phone, $country, $address, $job_contact, $user_id)");
//        -----------------------

//        QueryBuilder
//        $request['slug'] = Str::slug($request->name, '-');
//        DB::table('contacts')->insert([
//            'name' => $request->name,
//            'slug' => $request->slug,
//            'birth_date' => $request->birth_date,
//            'email' => $request->email,
//            'phone' => $request->phone,
//            'country' => $request->country,
//            'address' => $request->address,
//            'job_contact' => $request->job_contact,
//            'user_id' => $request->user()->id
//        ]);
//        ------------------------

//        Eloquent
        $request['slug'] = Str::slug($request->name, '-');
        $imgURL = $request->file('file')->storeAS('contacts_img', $request->file->getClientOriginalName());

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->slug = $request->slug;
        $contact->birth_date = $request->birth_date;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->country = $request->country;
        $contact->address = $request->address;
        $contact->job_contact = $request->job_contact;
        $contact->user_id = $request->user()->id;
        $contact->image = $imgURL;
        $contact->save();



//        Eloquent
//        $contact = Contact::create($request->all());
//        $contact['slug'] = Str::slug($request->name, '-');
//        $contact->user_id=Auth::id();
//        $contact->save();
//
        return redirect()->route('contacts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Contact $contact
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contact $contact
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);

        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreContacts $request
     * @param Contact $contact
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(StoreContacts $request, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $contact->update($request->all());
        return redirect()->route('contacts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contact $contact
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        $contact->delete();
        return redirect()->route('contacts.index');
    }
}
```

Podemos ver que los parámetros de las funciones son `Contact $contact` en lugar de `Contact $contacts` que sería lo que Laravel reconocería por defecto, pero en la ruta hemos definido que el parámetro se llamaría `contacto` en lugar de `agenda`. El otro parámetro que reciben algunas de las funciones es `StoreContacts $request` en lugar de `Request $request`, esto es porque se ha definido la validación de los formularios mediante una `FormRequest`.

## FormRequest (validación de formularios)

Las validaciones se pueden realizar en el mismo controlador, sin embargo es recomendable hacerlo mediente una clase específica para ello.

En primer lugar ejecutamos el siguiente comando en la terminal:

```bash
php artisan make:request StoreContacts
```

Esto nos creará la clase `StoreContacts` en `/app/Http/Requests`.

En esta clase, primero deberemos especificar que la función `authorize` devuelva `true` y en segundio lugar, especificar en la función `rules` las reglas que deberán cumplirse para validar los formularios (o un formulario específico). Podemos crear una FormRequest para cada formulario especificando unas reglas para cada uno.

El archivo tendrá las siguientes líneas (por ejemplo):

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContacts extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'required|size:9',
            'email' => 'email',
            'image' => [
                'required',
                'image',
                'max:2000'
            ] // jpeg, png, bmp, svg o webp
        ];
    }
}
```

Como ya se ha comentado, deberemos pasar este objeto como parámetro `request` a las funciones del controlador que deban recibir alguna `request`.

## Gate & Policy

### AuthServiceProvider

En este caso se han definido algunas _gates_ sin embargo se utilizan las _policies_ para las autorizaciones.

En el archivo `AuthServiceProvider.php` situado en `/app/Providers` es donde definiremos todas las _gates_ que queramos utilizar para las autorizaciones de acceso a la agenda y qué políticas hay definidas para las autorizaciones. Este archivo ya se encuentra en el proyecto al crearlo.

En este caso, dentro del _array_ `$policies` especificamos que utilizaremos la política `AgendaPolicy`:

```php
protected $policies = [
    // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    Contact::class => ContactsPolicy::class
];
```

El archivo quedará así:

```php
<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\User;
use App\Policies\ContactsPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Contact::class => ContactsPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

//        Definimos la autorización con Gate
//        Gate::define('access', function() {
//            return true;
//        });

//        Gate::define('access', function (User $user) {
//            return $user->role === 'user';
//        });
//
//        Gate::define('create', function (User $user) {
//            return $user->role === 'user';
//        });
//
//        Gate::define('update', function (User $user, Contact $contact) {
//            return $user->role === 'user' && $user->id === $contact->user_id;
//        });
//
//        Gate::define('delete', function (User $user, Contact $contact) {
//            return $user->role === 'user' && $user->id === $contact->user_id;
//        });

//        Gate::define('access', 'App\Policies\ContactsPolicy@view');
//        Gate::define('create', 'App\Policies\ContactsPolicy@create');
//        Gate::define('update', 'App\Policies\ContactsPolicy@update');
//        Gate::define('delete', 'App\Policies\ContactsPolicy@delete');
       
//        Esta Gate será la que permitirá al usuario 'admin' poder ver los contactos de todos los usuarios 
        Gate::define('viewAll', function (User $user) {
            return $user->role === 'super' || $user->role === 'admin';
        });

        Gate::define('viewAllAndDeleted', function (User $user) {
            return $user->role === 'super';
        });
    }
}
```

### Policy

Para crear una política ejecutamos el comando:

```bash
php artisan make:policy ContactsPolicy -m Contact
```

Donde además del nombre de la clase, se especifica que está realcionada con el modelo `Agenda`.

El comando anterior nos creará una clase `AgendaPolicy` en `/app/Policies`.

En este archivo es donde definiremos que condiciones deberán cumplirse para que se permita el acceso a los usuarios a las diferentes rutas, partes de la web o funcionalidades, en función de donde pidamos autorización.

Para autorizar el acceso utilizaremos tanto el `id` de los usuarios, el `user_id` de la agenda como los roles de los usuarios.

El archivo tendrá un código similar a este:

```php
<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactsPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
        if ($user->role === 'super' || $user->role === 'admin') {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'user' || $user->role === 'visitor';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Contact $contacts
     * @return bool
     */
    public function view(User $user, Contact $contacts): bool
    {
        return $user->role === 'user' && $user->id === $contacts->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Contact $contacts
     * @return bool
     */
    public function update(User $user, Contact $contacts): bool
    {
        return $user->role === 'user' && $user->id === $contacts->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Contact $contacts
     * @return bool
     */
    public function delete(User $user, Contact $contacts): bool
    {
        return $user->role === 'user' && $user->id === $contacts->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Contact $contacts
     * @return bool
     */
    public function restore(User $user, Contact $contacts): bool
    {
        return $user->role === 'user' && $user->id === $contacts->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Contact $contacts
     * @return bool
     */
    public function forceDelete(User $user, Contact $contacts): bool
    {
        return $user->role === 'admin';
    }
}
```

Después, en el controlador, al añadir las líneas `$this->authorize('viewAny', Contact::class);`, `$this->authorize('view', $contact);`, `$this->authorize('create', Contact::class);` , etc. a las funciones, se comprueba si se cumple la política especificada en cada caso.

## Views

Para cada una de las vistas que devuelven las funciones del controlador deberemos crear los archivos en `/resources/views`. Como las vistas están relacionadas con la agenda, en el directorio `views` creamos un directorio `agenda` que será donde añadiremos todas las vistas de la agenda. Por esta razón, en el controlador las vistas se devuleven haciendo referencia a ellas con la estructura `agenda.vista`.

### index

Creamos el archivo `index.blade.php` y en el añadimos las siguientes líneas:

```php+HTML
@include('layouts.plantilla')

<main>
    <section class="antialiased bg-gray-100 text-gray-600 h-screen px-4">
        <div class="mb-10 ">
            <div class="mb-5">
                <h2 class="text-xl">@lang("Contacts")</h2>
            </div>
            @auth
                @can('create', \App\Models\Contact::class)
                    <div class="mt-5">
                        <a class="text-green-400 no-underline border-solid border-2 border-green-400 rounded p-1 ml-5 hover:bg-green-400 hover:text-white"
                           href="{{ route('contacts.create') }}">➕ @lang("Add Contact")</a>
                    </div>
                @endcan
            @endauth
        </div>

        <div class="w-full max-w-8xl mx-auto bg-white shadow-lg rounded border border-gray-200">
            <div class="p-3">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead class="text-xs font-semibold uppercase text-gray-400">
                        <tr>
                            @auth
                                @can('viewAll', \App\Models\Contact::class)
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">@lang("User Name")</div>
                                    </th>
                                @endcan
                            @endauth
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Name")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Birth date")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Email")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Phone")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Country")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Address")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Job contact")?</div>
                            </th>
                            @auth
                                @can('viewAllAndDeleted', \App\Models\Contact::class)
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">@lang("Deleted at")</div>
                                    </th>
                                @endcan
                            @endauth
                        </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">

                        @foreach ($contacts as $contact)
                            <tr>
                                @auth
                                    @can('viewAll', \App\Models\Contact::class)
                                        <td class="p-2 whitespace-nowrap">{{ $contact->user->name }}</td>
                                    @endcan
                                @endauth
                                <td class="p-2 whitespace-nowrap">{{ $contact->name }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->birth_date }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->email }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->phone }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->country }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->address }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->job_contact }}</td>
                                @auth
                                    @can('viewAllAndDeleted', \App\Models\Contact::class)
                                        <td class="p-2 whitespace-nowrap">{{ $contact->deleted_at }}</td>
                                    @endcan
                                @endauth
                                <td class="p-2 whitespace-nowrap">
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                                        @auth
                                            @can('view', $contact)
                                                <a class="text-blue-400 no-underline border-solid border-2 border-blue-400 rounded p-1 px-3 ml-5 hover:bg-blue-400 hover:text-white"
                                                   href="{{ route('contacts.show', $contact) }}">👀 @lang("Show")</a>
                                            @endcan
                                        @endauth
                                        @auth
                                            @can('update', $contact)
                                                <a class="text-orange-400 no-underline border-solid border-2 border-orange-400 rounded p-1 px-3 ml-5 hover:bg-orange-400 hover:text-white"
                                                   href="{{ route('contacts.edit', $contact) }}">📝 @lang("Edit")</a>
                                            @endcan
                                        @endauth
                                        @csrf
                                        @method('DELETE')
                                        @auth
                                            @can('delete', $contact)
                                                <button type="submit"
                                                        class="text-red-400 no-underline border-solid border-2 border-red-400 rounded p-1 px-3 ml-5 hover:bg-red-400 hover:text-white">
                                                    💥 @lang("Delete")
                                                </button>
                                            @endcan
                                        @endauth
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
```

Esta vista recibe el objeto $contacto que contiene los datos de los contactos del usuario que serán los que se mostarán en la página.

### create

```php+HTML
@include('layouts.plantilla')

<main class="mt-5">
    <div class="w-full max-w-xl mx-auto bg-white shadow-lg rounded border border-gray-200">
        <h2 class="text-xl m-5">{{ __("Add new contacts to your Contacts list") }}</h2>
        @if ($errors->any())
            <div class="mx-auto max-w-md border-2 border-solid border-red-600 bg-red-300 rounded text-center">
                <strong>Whoops! </strong>{{ __("There were some problems with your input.") }}<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form class="m-5" method="POST" enctype="multipart/form-data" action="{{ route('contacts.store') }}">
            @csrf
            <label for="name"> {{ __("Contact name") }}:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="name"
                       value="{{old('name')}}" placeholder="Bernat Smith"/>
            </label>
            @error('name')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="birth_date"> {{ __("Birth date") }}:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="date" name="birth_date"
                       value="{{old('birth_date')}}"/>
            </label>
            <br>
            <br>
            <label for="email"> {{ __("Contact email") }}:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="email" name="email"
                       value="{{old('email')}}"
                       placeholder="bernat@email.com"/>
            </label>
            @error('email')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="phone"> {{ __("Contact phone") }}:
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="number" name="phone"
                       value="{{old('phone')}}"
                       placeholder="654321234"/>
            </label>
            @error('phone')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="country">{{ __("Country") }}: </label>
            <select class="border-2 border-solid border-gray-100 rounded-full px-2" name="country" id="country">
                <option value="England" @if (old('country') === 'England') selected @endif>{{ __("England") }}</option>
                <option value="Spain" @if (old('country') === 'Spain') selected @endif>{{ __("Spain") }}</option>
                <option value="Italy" @if (old('country') === 'Italy') selected @endif>{{ __("Italy") }}</option>
                <option value="Germany" @if (old('country') === 'Germany') selected @endif>{{ __("Germany") }}</option>
                <option value="France" @if (old('country') === 'France') selected @endif>{{ __("France") }}</option>
            </select>
            <br>
            <br>
            <label for="address"> {{ __("Contact address") }}:
                <br>
                <textarea class="border-2 border-solid border-gray-100 rounded px-2" name="address"
                          placeholder="Address 123, street">{{old('address')}}</textarea>
            </label>
            <br>
            <br>
            <label for="job_contact"> @lang("Job contact")?:<br>
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio"
                       name="job_contact" value="yes" {{ old('job_contact') === 'yes' ? 'checked='.'"checked"' : '' }}/> @lang("Yes")
                <br>
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio"
                       name="job_contact" value="no" {{ old('job_contact') === 'no' ? 'checked='.'"checked"' : '' }}/> @lang("No")
            </label>
            <br>
            <br>
            <label for="file">
                <input type="file" name="file"/>
            </label>
            <br>
            <br>
            <label for="terms">
                <input type="checkbox" id="terms" name="terms"> {{ __("Accept terms and conditions.") }}
            </label>
            <br>
            <button
                class="text-green-400 no-underline border-solid border-2 border-green-400 rounded p-1 px-5 ml-5 mt-5 hover:bg-green-400 hover:text-white"
                type="submit" name="add">➕ {{ __("Add Contact") }}
            </button>
        </form>
    </div>
</main>
```

Este archivo tansolo contiene un formulario con los campos necesarios para crear un nuevo contacto.

### edit

```php+HTML
@include('layouts.plantilla')

<main class="mt-5">
    <div class="w-full max-w-xl mx-auto bg-white shadow-lg rounded border border-gray-200">
        <h2 class="text-xl m-5">@lang("Update $contact->name's contact info")</h2>
        @if ($errors->any())
            <div class="mx-auto max-w-md border-2 border-solid border-red-600 bg-red-300 rounded text-center">
                <strong>Whoops! </strong>@lang("There were some problems with your input.")<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form class="m-5" method="POST" enctype="multipart/form-data"
              action="{{ route('contacts.update', $contact) }}">

            @csrf
            @method('PUT')

            <label for="name"> @lang("Contact name"):
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="text" name="name"
                       value="{{old('name', $contact->name)}}" placeholder="Bernat Smith"/>
            </label>
            @error('name')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="birth_date"> @lang("Birth date"):
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="date" name="birth_date"
                       value="{{old('birth_date', $contact->birth_date)}}"/>
            </label>
            <br>
            <br>
            <label for="email"> @lang("Contact email"):
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="email" name="email"
                       value="{{old('email', $contact->email)}}" placeholder="bernat@email.com"/>
            </label>
            @error('email')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="phone"> @lang("Contact phone"):
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="number" name="phone"
                       value="{{old('phone', $contact->phone)}}" placeholder="654321234"/>
            </label>
            @error('phone')
            <br>
            <small>*{{$message}}</small>
            <br>
            @enderror
            <br>
            <br>
            <label for="country">@lang("Country"):</label>
            <select class="border-2 border-solid border-gray-100 rounded-full px-2" name="country" id="country">
                <option value="England" @if (old('country') === 'England') selected @endif>@lang("England")</option>
                <option value="Spain" @if (old('country') === 'Spain') selected @endif>@lang("Spain")</option>
                <option value="Italy" @if (old('country') === 'Italy') selected @endif>@lang("Italy")</option>
                <option value="Germany" @if (old('country') === 'Germany') selected @endif>@lang("Germany")</option>
                <option value="France" @if (old('country') === 'France') selected @endif>@lang("France")</option>
            </select>
            <br>
            <br>
            <label for="address"> @lang("Contact address"): <br>
                <textarea class="border-2 border-solid border-gray-100 rounded px-2" name="address"
                          placeholder="Address 123, street">
                {{old('address', $contact->address)}}
            </textarea>
            </label>
            <br>
            <br>
            <label for="job_contact"> @lang("Job contact")?:<br>
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio"
                       name="job_contact" value="yes" {{ old('job_contact') === 'yes' ? 'checked='.'"checked"' : '' }}/> @lang("Yes")
                <br>
                <input class="border-2 border-solid border-gray-100 rounded-full px-2" type="radio"
                       name="job_contact" value="no" {{ old('job_contact') === 'no' ? 'checked='.'"checked"' : '' }}/> @lang("No")
            </label>
            <br>
            <button
                class="text-orange-400 no-underline border-solid border-2 border-orange-400 rounded p-1 px-5 ml-5 mt-5 hover:bg-orange-400 hover:text-white"
                type="submit" name="add">📝 @lang("Edit")
            </button>
        </form>
    </div>
</main>
```

Este archivo contiene un formulario para poder editar un contacto existente.

### show

```php+HTML
@include('layouts.plantilla')

<main class="mt-5">
    <div class="w-full max-w-xl mx-auto bg-white shadow-lg rounded border border-gray-200">
        <div>
            @if($contact->image)
                <img src="/storage/{{$contact->image}}" alt="{{$contact->name}}">
            @endif
        </div>
        <div class="text-center">
            <ul class="list-none m-5">
                <li class="font-bold">
                    <div class="flex">
                        {{$contact->name}}
                        <a class="pl-1 no-underline" href="{{ route('contacts.edit', $contact) }}">📝</a>
                        <form class="w-1" action="{{ route('contacts.destroy', $contact) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="b-0 pl-1 background-none">💥</button>
                        </form>
                    </div>
                </li>
                <ul class="ml-5">
                    <li>{{$contact->birth_date}}</li>
                    <li>{{$contact->email}}</li>
                    <li>{{$contact->phone}}</li>
                    <li>{{$contact->country}}</li>
                    <li>{{$contact->address}}</li>
                    <li>Job contact: {{$contact->job_contact}}</li>
                </ul>
            </ul>
        </div>
    </div>
</main>
```

Esta vista recibe los datos de un único contacto y son los que se mostrarán en la página.

## Layouts, Home y errors

En la ubicación `/resources/views/layouts` se ha definido una plantilla que contiene el documento html con la cabecera y que recibe un `header` y el contenido del `body` mediante la importación de archivos.

### plantilla layout

Creamos el archivo `plantilla.blade.php` y le añadimos el siguiente código:

```php+HTML
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Contacts</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- TAILWIND CND -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<header>
    @include('layouts.navigation')
</header>
@yield('content')
</body>
</html>
```

La línea `@include('layouts.navigation')` importa el _layout_ `navigation.blade.php` el cual contiene el código que genera el `header`.

Todo el CSS de la web funciona mediante Tailwind, por lo que es necesario añadir la línea `<script src="https://cdn.tailwindcss.com"></script>` dentro de la etiqueta `head`.

### navigation layout

```php+HTML
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600"/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                        {{ __('Contact') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            {{--  Language selector dropdown  --}}
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <div class="pt-2 pb-3 space-y-1">
                            <div class="hidden fixed top-0 @if (Route::has('login')) @auth right-20 @else right-50 @endauth @endif px-6 py-4 sm:block">
                                <a id="navbarDropdown"
                                   class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                   href="#" role="button"
                                   data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false" v-pre>
                                    @if (app()->getLocale() === 'en'){{"🇬🇧 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'es'){{"🇪🇸 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'ca'){{"🇪🇸🤷 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'it'){{"🇮🇹 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'de'){{"🇩🇪 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'fr'){{"🇫🇷 "}}{{ __("Language") }}@endif
                                </a>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="content">
                        <div>
                            <x-nav-link :href="route('set_language', ['en'])">{{ __("🇬🇧") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['es'])">{{ __("🇪🇸") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['ca'])">{{ __("🇪🇸🤷") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['it'])">{{ __("🇮🇹") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['de'])">{{ __("🇩🇪") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['fr'])">{{ __("🇫🇷") }}</x-nav-link>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <div class="pt-2 pb-3 space-y-1">
                            @if (Route::has('login'))
                                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                                    @auth
                                        <button
                                            class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                            <div>{{ Auth::user()->name }}</div>

                                            <div class="ml-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}"
                                           class="text-sm text-gray-700 dark:text-gray-500 underline hover:text-black">Log
                                            in</a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}"
                                               class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline hover:text-black">Register</a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        @if (Route::has('login'))
                            @auth
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                                     onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            @endauth
                        @endif
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                {{ __('Contact') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        {{--  Language selector  --}}
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden pt-4 pb-1 border-t border-gray-200">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false" v-pre>
                {{ __("Language") }}
            </a>
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('set_language', ['en'])"
                                       :active="request()->routeIs('en')">{{ __("🇬🇧") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['es'])"
                                       :active="request()->routeIs('es')">{{ __("🇪🇸") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['ca'])"
                                       :active="request()->routeIs('*ca*')">{{ __("🇪🇸🤷") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['it'])"
                                       :active="request()->routeIs('*it*')">{{ __("🇮🇹") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['de'])"
                                       :active="request()->routeIs('*de*')">{{ __("🇩🇪") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['fr'])"
                                       :active="request()->routeIs('*fr*')">{{ __("🇫🇷") }}</x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @if (Route::has('login'))
                @auth
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                @endauth
            @endif
            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    @if (Route::has('login'))
                        @auth
                            <x-responsive-nav-link :href="route('logout')"
                                                   onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        @else
                            <x-responsive-nav-link :href="route('login')"
                                                   onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                {{ __('Log In') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('register')"
                                                   onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                {{ __('Register') }}
                            </x-responsive-nav-link>
                        @endauth
                    @endif
                </form>
            </div>
        </div>
    </div>
</nav>
```

La línea `@yield('content')` es la que recibe las distintas vistas de la agenda o cualquier otro contenido de otra página como por ejemplo la vista `home`.

### home page

En este caso, el archivo `home.blade.php` irá dentro del directorio `/resources/views`.

```php+HTML
@include('layouts.plantilla')

<main>
    <h1 class="text-2xl text-center mt-5">Uep!
        @if (Route::has('login'))
            @auth
                <span>{{ ucfirst(Auth::user()->name) }}</span>
            @endauth
        @endif
    </h1>
</main>
```

Esta es la vista que se muestra al acceder a la ruta raíz que hemos definido en `web.php` como `home`.

Para que al realizar el login nos redirija a la página `home` en lugar de a `dashboard`, en la clase `RouteServiceProvider` situado en `/app/Providers`, debemos sustituir la línea `public const HOME = '/dashboard';` por `public const HOME = '/';`.

### Error pages (403 & 404)

Creamos el directorio `errors` en `/resources/views` y dentro del directorio añadimos los archivos `403.blade.php` y `404.blade.php`.

#### 403 error page

```php+HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<main style="text-align: center">
    <h1 style="font-weight: bold">Prohibit! Au fuig d'aquí!</h1>
</main>
</body>
</html>
```

#### 404 error page

```php+HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<main style="text-align: center">
    <h1 style="font-weight: bold">No s'ha trobat això!</h1>
</main>
</body>
</html>
```

## Languages

[Vídeo tutorial](https://www.youtube.com/watch?v=ajvscag7dSc&list=PLd3a4dr8oUsDAjQa8T0eKSyOxUCoiMVxO&index=31)

[Configuración multilenguaje](https://diarioprogramador.com/como-crear-proyecto-multi-idioma-en-laravel/)

Vamos a añadir soporte para varios lenguajes. Para ello en primer lugar descargamos la dependencia de Laravel `laravel-lang/lang` y `laravel-lang/publisher`:

```bash
composer require laravel-lang/lang laravel-lang/publisher
```

Esto nos añadirá una serie de directorios en la carpeta `/vendor/laravel-lang/lang/locales`. Cada una de estas carpetas corresponde a un idioma. Para poder utilizar los idiomas, debemos copiar el directorio del los idiomas que nos interesen y pegarlos dentro del directorio `/resources/lang`.

### View

Una vez que tenemos los archivos de idiomas, deberemos dejar el archivo `navigation.blade.php` así:

```php+HTML
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600"/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                        {{ __('Contact') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            {{--  Language selector dropdown  --}}
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <div class="pt-2 pb-3 space-y-1">
                            <div class="hidden fixed top-0 @if (Route::has('login')) @auth right-20 @else right-50 @endauth @endif px-6 py-4 sm:block">
                                <a id="navbarDropdown"
                                   class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                   href="#" role="button"
                                   data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false" v-pre>
                                    @if (app()->getLocale() === 'en'){{"🇬🇧 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'es'){{"🇪🇸 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'ca'){{"🇪🇸🤷 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'it'){{"🇮🇹 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'de'){{"🇩🇪 "}}{{ __("Language") }}@endif
                                    @if (app()->getLocale() === 'fr'){{"🇫🇷 "}}{{ __("Language") }}@endif
                                </a>
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="content">
                        <div>
                            <x-nav-link :href="route('set_language', ['en'])">{{ __("🇬🇧") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['es'])">{{ __("🇪🇸") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['ca'])">{{ __("🇪🇸🤷") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['it'])">{{ __("🇮🇹") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['de'])">{{ __("🇩🇪") }}</x-nav-link>
                            <x-nav-link :href="route('set_language', ['fr'])">{{ __("🇫🇷") }}</x-nav-link>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <div class="pt-2 pb-3 space-y-1">
                            @if (Route::has('login'))
                                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                                    @auth
                                        <button
                                            class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                            <div>{{ Auth::user()->name }}</div>

                                            <div class="ml-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}"
                                           class="text-sm text-gray-700 dark:text-gray-500 underline hover:text-black">Log
                                            in</a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}"
                                               class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline hover:text-black">Register</a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        @if (Route::has('login'))
                            @auth
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                                     onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            @endauth
                        @endif
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contacts.index')" :active="request()->routeIs('contacts.*')">
                {{ __('Contact') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        {{--  Language selector  --}}
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden pt-4 pb-1 border-t border-gray-200">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false" v-pre>
                {{ __("Language") }}
            </a>
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('set_language', ['en'])"
                                       :active="request()->routeIs('en')">{{ __("🇬🇧") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['es'])"
                                       :active="request()->routeIs('es')">{{ __("🇪🇸") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['ca'])"
                                       :active="request()->routeIs('*ca*')">{{ __("🇪🇸🤷") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['it'])"
                                       :active="request()->routeIs('*it*')">{{ __("🇮🇹") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['de'])"
                                       :active="request()->routeIs('*de*')">{{ __("🇩🇪") }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('set_language', ['fr'])"
                                       :active="request()->routeIs('*fr*')">{{ __("🇫🇷") }}</x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @if (Route::has('login'))
                @auth
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                @endauth
            @endif
            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    @if (Route::has('login'))
                        @auth
                            <x-responsive-nav-link :href="route('logout')"
                                                   onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        @else
                            <x-responsive-nav-link :href="route('login')"
                                                   onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                {{ __('Log In') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('register')"
                                                   onclick="event.preventDefault();
                                        this.closest('form').submit();">
                                {{ __('Register') }}
                            </x-responsive-nav-link>
                        @endauth
                    @endif
                </form>
            </div>
        </div>
    </div>
</nav>
```

Por otro lado, en el HTML debemos especificar los diferentes lenguajes, esto lo haremos cambiando el parámetro `lang` de la etiqueta `html` del archivo `plantilla.blade.php`, de `lang="en"` a `lang="{{ str_replace('_', '-', app()->getLocale()) }}"`.

```php+HTML
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Contacts</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- TAILWIND CND -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<header>
    @include('layouts.navigation')
</header>
@yield('content')
</body>
</html>
```

### Routes & Controller

Al archivo de rutas `web.php` le añadimos la ruta para la selección de idioma:

```php
Route::get('/set_language/{lang}', [App\Http\Controllers\Controller::class, 'set_language'])->name('set_language');
```

Y en la clase `Controller` ubicada en `/app/Http/Controllers` añadimos la siguiente función:

```php
public function set_language($language){
		if(array_key_exists($language, config('languages'))){
		    session()->put('applocale', $language);
		}
		return back();
}
```

### Config

En la carpeta `/config` añadimos el archivo `languages.php` en el cual añadiremos los ISOs de todos los idiomas de la aplicación:

```php
<?php

return [
    'en' => ['English', 'en_US'],
    'es' => ['Spanish', 'es_ES'],
    'ca' => ['Catalan', 'ca_CA'],
    'it' => ['Italian', 'it_IT'],
    'de' => ['German', 'de_DE'],
    'fr' => ['French', 'fr_FR'],
];
```

### Middleware

Creamos un `middleware` para los lenguages:

```bash
php artisan make:middleware Language
```

Esto nos creará un nuevo archivo `/app/Middleware/Language.php` en el cual deberemos añadir lo siguiente dentro de la función `handle`:

```php
if(session('applocale')){
    $configLanguage = config('languages')[session('applocale')];
    setlocale(LC_TIME, $configLanguage[1] . '.utf8');
    Carbon::setLocale(session('applocale'));
    App::setLocale(session('applocale'));
}else{
    session()->put('applocale', config('app.fallback_locale'));
    setlocale(LC_TIME, 'es_ES.utf8');
    Carbon::setLocale(session('applocale'));
    App::setLocale(session('applocale'));
}
return $next($request);
```

La clase deberá quedar así:

```php
<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(session('applocale')){
            $configLanguage = config('languages')[session('applocale')];
            setlocale(LC_TIME, $configLanguage[1] . '.utf8');
            Carbon::setLocale(session('applocale'));
            App::setLocale(session('applocale'));
        }else{
            session()->put('applocale', config('app.fallback_locale'));
            setlocale(LC_TIME, 'es_ES.utf8');
            Carbon::setLocale(session('applocale'));
            App::setLocale(session('applocale'));
        }
        return $next($request);
    }
}
```

Después de esto, para habilitar el `Middleware` que hemos creado, nos debemos dirigir al archivo `Kernel.php` en la ruta `app/Http/Kernel.php`. En este archivo, buscamos la variable `$middlewareGroups` y añadimos el elemento `Language::class` al array.

```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        // \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
//      Middleware para los idiomas
        Language::class
    ],

    'api' => [
        // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
```

Además, debemos importar la clase añadiendo la línea `use App\Http\Middleware\Language;` al inicio del archivo.

### JSON files

Si en la carpeta de cada uno de los idiomas tenemos un archivo `.json`, debemos poner este archivo directamente dentro de la carpeta `resources/lang`. Si no tenemos archivo `.json`lo creamos. El archivo deberá llamarse como el idioma que deba traducir, `es.json` para español, `en.json` para inglés, `it.json` para italiano, etc. Dentro del archivo debemos añadir las traducciones que queramos, por ejemplo en el archivo `es.json`añadiríamos:

```json
{		
		"Add new contacts to your Contacts list": "Añadir nuevos contactos a la Agenda",
		"There were some problems with your input.": "Hubo algunos problemas con tu input.",
		"Contact name": "Nombre del contacto",
    "Birth date": "Cumpleaños",
    "Contact email": "Correo electrónico",
    "Contact phone": "Teléfono",
    "Contact address": "Dirección",
    "Job contact": "Contacto de trabajo",
    "Accept terms and conditions.": "Acepta los términos y condiciones.",
    "Add Contact": "Añadir contacto",
    "Update :contact's contact info": "Editar la información de contacto de :contact",
    "Show": "Ver",
    "User Name": "Nombre de usuario",
    "Language": "Idioma",
    "English": "Inglés",
    "Spanish": "Español",
    "Catalan": "Catalán",
    "Italian": "Italiano",
    "German": "Alemán",
    "French": "Francés",
}
```

Por último en los archivos de las vistas deberemos utilizar la _flag_ `@lang()`o la función `__()` y poner dentro cada uno de los _string_ que queramos que se traduzcan.

```php
@lang("frase a traducir")
{{ __("frase a traducir") }}
```

Por ejemplo, la vista `index` quedaría así:

```php+HTML
@include('layouts.plantilla')

<main>
    <section class="antialiased bg-gray-100 text-gray-600 h-screen px-4">
        <div class="mb-10 ">
            <div class="mb-5">
                <h2 class="text-xl">@lang("Contacts")</h2>
            </div>
            @auth
                @can('create', \App\Models\Contact::class)
                    <div class="mt-5">
                        <a class="text-green-400 no-underline border-solid border-2 border-green-400 rounded p-1 ml-5 hover:bg-green-400 hover:text-white"
                           href="{{ route('contacts.create') }}">➕ @lang("Add Contact")</a>
                    </div>
                @endcan
            @endauth
        </div>

        <div class="w-full max-w-8xl mx-auto bg-white shadow-lg rounded border border-gray-200">
            <div class="p-3">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead class="text-xs font-semibold uppercase text-gray-400">
                        <tr>
                            @auth
                                @can('viewAll', \App\Models\Contact::class)
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">@lang("User Name")</div>
                                    </th>
                                @endcan
                            @endauth
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Name")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Birth date")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Email")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Phone")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Country")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Address")</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">@lang("Job contact")?</div>
                            </th>
                            @auth
                                @can('viewAllAndDeleted', \App\Models\Contact::class)
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">@lang("Deleted at")</div>
                                    </th>
                                @endcan
                            @endauth
                        </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100">

                        @foreach ($contacts as $contact)
                            <tr>
                                @auth
                                    @can('viewAll', \App\Models\Contact::class)
                                        <td class="p-2 whitespace-nowrap">{{ $contact->user->name }}</td>
                                    @endcan
                                @endauth
                                <td class="p-2 whitespace-nowrap">{{ $contact->name }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->birth_date }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->email }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->phone }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->country }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->address }}</td>
                                <td class="p-2 whitespace-nowrap">{{ $contact->job_contact }}</td>
                                @auth
                                    @can('viewAllAndDeleted', \App\Models\Contact::class)
                                        <td class="p-2 whitespace-nowrap">{{ $contact->deleted_at }}</td>
                                    @endcan
                                @endauth
                                <td class="p-2 whitespace-nowrap">
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                                        @auth
                                            @can('view', $contact)
                                                <a class="text-blue-400 no-underline border-solid border-2 border-blue-400 rounded p-1 px-3 ml-5 hover:bg-blue-400 hover:text-white"
                                                   href="{{ route('contacts.show', $contact) }}">👀 @lang("Show")</a>
                                            @endcan
                                        @endauth
                                        @auth
                                            @can('update', $contact)
                                                <a class="text-orange-400 no-underline border-solid border-2 border-orange-400 rounded p-1 px-3 ml-5 hover:bg-orange-400 hover:text-white"
                                                   href="{{ route('contacts.edit', $contact) }}">📝 @lang("Edit")</a>
                                            @endcan
                                        @endauth
                                        @csrf
                                        @method('DELETE')
                                        @auth
                                            @can('delete', $contact)
                                                <button type="submit"
                                                        class="text-red-400 no-underline border-solid border-2 border-red-400 rounded p-1 px-3 ml-5 hover:bg-red-400 hover:text-white">
                                                    💥 @lang("Delete")
                                                </button>
                                            @endcan
                                        @endauth
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
```

## Upload files

### Change drive from local to public

En el archivo `.env` indicamos que el directorio de subida queremos que sea public añadiendo la siuguiente línea:

```bash
FILESYSTEM_DRIVER=public
```

### View and Controller

En la vista `create` añadimos un input para subir archivo:

```html
<label for="file">
		<input type="file" name="file"/>
</label>
```

Y en el controlador añadimos las línea que nos permitirán almacenar y referencias en la base de datos las imágenes que se suban:

```php
$imgURL = $request->file('file')->storeAs('contacts_files', $request->file->getClientOriginalName());
$contact->image = $imgURL;
```

### Migration

Para poder almacenar la ubicación de la imagen en la base de datos, debemos añadir una nueva columna a la tabla `contacts`. Para ello, en primer lugar crearemos una nueva migración:

```bash
php artisan make:migration add_image_field_to_contacts_table
```

En el archivo `add_image_field_to_contacts_table.php` que se creará en `database/migrations` añadimos la línea `$table->string('image')->nullable();` a la función `up` y la línea `$table->dropColumn('image');` a la función `down`.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageFieldToContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
```

### storage and public/storage link

Para poder acceder a las imágenes desde el navegador, debemos crear un link entre las carpetas `storage/app/public` y `public/storage`. Para ello ejecutaremos elsiguiente comado:

```bash
php artisan storage:link
```

Nos devulve el siguiente mensaje:

```bash
The [contacts_crud/public/storage] link has been connected to [contacts_crud/storage/app/public]
```

**Este comando debe ejecutarse también en el servidor al hacer el despliegue!**
