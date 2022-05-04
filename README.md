### RESPUESTAS AL FORMULARIO PRE-ENTREVISTA

Para un mayor entendimiento de las respuestas a las preguntas dadas en el documento, he desarrollado la API correspondiente, donde cada una de estas respuestas estan implementadas.

- Desarrollado con Laravel 9
- haciendo uso de Request y Rules para validación
- Passport para autenticación con token
- Middleware para permisos de acceso como Cors
- Resource para la respuestas de datos Json
- Eloquent para interacción con la capa de datos(como se solicita en el formulario)


### PASOS PARA EL DESPLIEGUE DE LA API

Necesita requerimientos mínimos:
```requiere
PHP 8
Composer 2.*
```

Clonamos la api del Github:

`git clone https://github.com/lili080916/EzzingSolar.git`

Comandos para iniciar el proyecto:
```command
composer install
php artisan migrate
php artisan db:seed
php artisan passport:install
```

En la raíz del proyecto, la carpeta **client http** puede importarse una collection de **Postsman** para interactuar con los endpoints desarrollados. ***IMPORTANTE sustituir el secret  en el endpoint OAUTH TOKEN por el que se genere cuando se corra el comando referente al client_id = 2***

`php artisan passport:install`

### RESPUESTAS

####Tablas

| Users                   |
| ------------- | ------------------------------ |
| `ID`      | Integer       |
| `name`   | String     |
| `surname`      | String       |
| `birthday`   | Date     |


| Comments                   |
| ------------- | ------------------------------ |
| `ID`      | Integer       |
| `user_id`   | Integer     |
| `test`      | String       |

####Modelos

```models
App\Models\User
public function comments(): HasMany
{
    return $this->hasMany(‘App\Models\Comment’);
}

App\Models\Comment
public function user(): BelongsTo
{
    return $this->BelongsTo(‘App\Models\User);
}
```

####Pregunta 1

Tienes que crear una petición POST que a partir de unos datos guarde un usuario.
La ruta de la petición es /api/save-user.
Los argumentos de la petición son: name, surname y birthday.
En el controlador asociado a la ruta, tendrás 3 principales bloques de lógica:

1. Validación (validar que el usuario es mayor de edad)
2. Algoritmia (dependiendo del rango de edad del usuario haremos cierta lógica
con los datos)
3. Conexión a base de datos (insertar el usuario en la tabla usando eloquent)

¿Cómo organizarías estos tres bloques de código a nivel de arquitectura? En otras
palabras, en qué archivo o clase incluirías cada pieza de lógica

> Las validaciones están en los archivos de Request que se crean con el comando:
`php artisan make:request StoreUserRequest`
y en el llevamos la validación de los datos de entrada. Mirar desarrollo en: raiz/app/Http/Requests/StoreUserRequest.php

> Las validaciones personalizadas o Rules, como se denominan en Laravel, se crean con el comando:
`php artisan make:rule Adult`
y en él llevamos la validación personalizada para si el usuario es mayor de edad. Mirar desarrollo en: raiz/app/Rules/Adult.php

> Entonces la arquitecturas o archivos utilizados son:
**UserController**: Lógica de la función
**Models**: Es el objeto para interactuar con la capa de datos y se hidrata con la estructura e información de la BD, manejo del objeto, los atributos, relaciones, entre otros.
**StoreUserRequest**: Request para la validación en la entrada de los datos
**Adult.php**: Validación personalizada para tratamiento de los datos de fechas y toma de la edad del usuario.

####Pregunta 2

Tienes las siguientes rutas en tu archivo api.php de laravel.

1. POST /api/users/
2. POST /api/users/{id}/edit
3. GET /api/users/{id}
4. GET /api/users

Necesitas que todas las rutas implementen un middleware de autenticación (las
peticiones futuras que crees en el proyecto también deberán implementarlo). Dados
estos requisitos, ¿Qué forma conoces para conseguir esto mediante laravel?.

> Se crearon dos **Middleware**: uno para el **Cors** para que pueda ser accesible la api desde cualquier servidor externo y el segundo **auth:api** que permite a las rutas dentro de este grupo verificar que tenga un **access_token** con los permisos requeridos para acceder a los controlladores correspondientes. Estos **Middleware** deben ser insertados en el **Kernel** para que sean ejecutados.
`php artisan make:middleware Cors`

```middleware1
$response = $next($request);
$response->headers->set('Access-Control-Allow-Origin', '*');
$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
$response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-XSRF-TOKEN');

return $response;
```

> `php artisan make:middleware AllowMiddleware`

```middleware2
if (! $request->expectsJson()) {
   return route('err');
};
```
> Rutas: Si no tienes accesos porque las credeciales son incorrectas retorna un status 401 con acceso denegado

```routes
Route::get('err', function (Request $request) {
    return response(['data' => 'Access denied'], 401);
})->name('err');

Route::group(['middleware' => ['auth:api']], function () {
    //  user url
    Route::get('users-with-comments', 'UserController@getUsersWithComments');
    Route::group(['prefix' => 'users'], function () {
        Route::post('', 'UserController@saveUser');
        Route::get('', 'UserController@getUsers');
        Route::put('{id}','UserController@editUser');
        Route::get('{id}','UserController@getUserById');
    });
});
```

Todas las rutas que se deseen asegurar que haya un **access_token** con credenciales correctas deben estar dentro de grupo que tiene este **Middleware auth:api**

####Pregunta 3

Se te pide hacer una nueva petición para devolver los usuarios con sus
comentarios. Deberás devolver un json con los usuarios, cada usuario con sus
comentarios.

```pregunta3
Route::get('api/users-with-comments', 'UserController@getUsersWithComments');

class UserController
function getUsersWithComments()
{
    $usersWithComments = [];
    // Desarrollar lógica
    return json_response($usersWithComments);
}
```

> Recarcar que se esta devolviendo la información en formato **JSON** a traves de los **Resource de laravel** comando:
`php artisan make:resource UserResource`
`php artisan make:resource UserCollection`
Para la implementación de esta funcionaliad que se desea nos apoyamos en las relaciones que estan implementadas en los **models** implementación:

```respuesta3
public function getUsersWithComments(Request $request)
{
    $users = User::with(['comments'])->paginate(5);

    return UserCollection::make($users);
}
```
> Así de sencillo ILoveLaravel jeje: **Eloquent** permite atraves de lo modelos y las relaciones y esto implementado con los **Resource** facilitan mucho la implementación y también la eficiencia para obtener el producto final. Los invitos a ver la implementación realizada.

####Pregunta 4

Se te pide modificar el endpoint existente /api/users para que devuelva una nueva
propiedad dentro de cada usuario “fullname”, resultante de la concatenación de las
columnas de tabla “name” y “surname”. Esto sin generar una nueva columna en la
tabla. ¿Cómo lo implementarías?.

> Hay varias formas de ejecutar esta funcionalidad, por los mismos **Resource** es una pero implementé la vía que creo que es más eficiente y sencilla. En el modelo se crea una **$appends['fullname']** y este hace referencia a **getFullNameAttribute** el cual retorna la concatenación del **name** con **surname** esto le hace un appends con el dato  **fullname** al modelo **User**  y todo esto se termina devolviendo en el **UserResource** código:

```respuesta4
User Models
protected $appends = ['fullname'];

public function getFullNameAttribute() : string
{
    return $this->name . ' ' . $this->surname;
}
```

####Pregunta 5

¿Qué experiencia tienes en los siguientes aspectos de laravel o back-end? (Marcar
con una x la casilla adecuada).

####Laravel

| Concepto  | Básico  | Medio |Avanzado |
| :------------ |:---------------:| -----:|-----:|
| Eventos/listeners      |  | [x] | |
| Jobs/cron jobs (queues)     |  |  |[x] |
| Filesystem (Storage)     |  |  |[x] |
| Validaciones/Policies    |  | [x] | |
| Testing    | [x] |  | | |

####Backend

| Concepto  | Poca  | Normal |Mucha |
| :------------ |:---------------:| -----:|-----:|
| Proyectos grandes      |  |  |[x] |
| Crear APIs rest/graphql    |  | [x] | |
| Resolución de bugs     |  |  |[x] |

###Gracias por su tiempo los invito a ver el código
