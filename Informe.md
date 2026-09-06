# 🐬 MySQL — Resumen

## Crear una base de datos

```sql
CREATE DATABASE api;
```

Crea una base de datos llamada `api`.

---

## Seleccionar una base de datos

```sql
USE api;
```

Selecciona `api` para trabajar con ella.

---

## Crear un usuario

```sql
CREATE USER 'api_user'@'localhost' IDENTIFIED BY 'password';
```

Crea el usuario `api_user` con una contraseña.

---

## Usuario y Host

```text
'api_user'@'localhost'
```

* `api_user` → nombre del usuario.
* `localhost` → desde dónde puede conectarse.
* `localhost` → la misma computadora donde está MySQL.
* `%` → cualquier host.

---

## Dar permisos

```sql
GRANT ALL PRIVILEGES ON api.* TO 'api_user'@'localhost';
```

Da todos los permisos sobre todas las tablas de `api`.

### `api.*`

```text
base_de_datos.tabla

api.*          → todas las tablas de api
api.usuarios   → solamente la tabla usuarios
```

---

## Ver permisos

```sql
SHOW GRANTS FOR 'api_user'@'localhost';
```

Muestra los permisos de un usuario.

---

## Ver usuarios

```sql
SELECT User, Host FROM mysql.user;
```

Muestra los usuarios y sus hosts.

---

## `FLUSH PRIVILEGES`

```sql
FLUSH PRIVILEGES;
```

Recarga los permisos de MySQL.

> 💡 Normalmente no es necesario después de usar `CREATE USER` o `GRANT`.

---

## `127.0.0.1`


Representa la propia computadora.

También se suele utilizar:

```text
localhost
```

---

## Puerto `3306`

```text
3306
```

Es el puerto predeterminado de MySQL.

Una conexión típica:

```text
127.0.0.1:3306
│          │
│          └── Puerto de MySQL
└───────────── Computadora
```

---

## Host vs Puerto

```text
127.0.0.1:3306
     │      │
     │      └── Puerto
     └───────── Host
```

* **Host/IP** → indica dónde está MySQL.
* **Puerto** → indica por dónde acceder al servicio.

---

## Ejemplo completo

```sql
CREATE DATABASE api;

CREATE USER 'api_user'@'localhost'
IDENTIFIED BY 'password';

GRANT ALL PRIVILEGES
ON api.*
TO 'api_user'@'localhost';

SHOW GRANTS FOR 'api_user'@'localhost';
```

---

## 📖 Glosario

| Concepto               | Significado                               |
| ---------------------- | ----------------------------------------- |
| **Database**           | Base de datos.                            |
| **Tabla**              | Estructura donde se almacenan datos.      |
| **Usuario**            | Cuenta para acceder a MySQL.              |
| **Host**               | Origen desde donde se conecta el usuario. |
| **`localhost`**        | La propia computadora.                    |
| **`127.0.0.1`**        | IP de la propia computadora.              |
| **Puerto**             | Punto de acceso a un servicio.            |
| **`3306`**             | Puerto predeterminado de MySQL.           |
| **`GRANT`**            | Concede permisos.                         |
| **`api.*`**            | Todas las tablas de `api`.                |
| **`FLUSH PRIVILEGES`** | Recarga los permisos de MySQL.            |


# Instalando lo necesario

Laravel nos da una opccion para poder obtener los archivos necesarios par la api, este es el siguiente comando:

`php artisan install:api`. Esto nos creara, entre los archivos, `routes/ api.php`, este archivo es desde el cual colocaremos las rutas de la api, e ingresaremos asi: `../api/...`

## Controladores

`php artisan make:controller Api/V1/PostController --api --model=Post`. Con este ejemplo creamos una carpeta `Api`, con la version 1 (`V1`) conteniendo al archivo `PostController`. Además de todos los metodos necesarios implementados (`--api`) relacionados con el modelo Post (`--model=Post`)

# api.web

Podemos usar el metodo `ApiResource` de `Route` para por poder crear automáticamente las rutas de una API para trabajar, usando un controlador que definamos. Para esto necesitamos la ruta sobre la cual vamos a hacer las llamadas, y la clase del controlador a la que se enlazara los metodos de la Api.

`Route::apiResource('v1/posts', App\Http\Controllers\Api\V1\PostController::class)`

- `v1/posts`. Es la ruta que definimos para acceder a lo metodos de la API

- `App...PostController::class`. Traemos a la clase sobre la cual `apiResource` buscara conectar los metodos de la Api para crear las rutas.

Este metodo crea:
```
GET      v1/posts
POST     v1/posts
GET      v1/posts/{post}
PUT      v1/posts/{post}
PATCH    v1/posts/{post}
DELETE   v1/posts/{post}
```

y cada metodo que utiliza la ruta, la busca en `PostController::class`

# Ruta de trabajo

La API se define en api.php:

`<?php
Route::apiResource('v1/posts', PostController::class);`

Laravel añade automáticamente el prefijo "/api", por lo que crea estas rutas:

```GET /api/v1/posts → index()
POST /api/v1/posts → store()
GET /api/v1/posts/{post} → show()
PUT o PATCH /api/v1/posts/{post} → update()
DELETE /api/v1/posts/{post} → destroy()
```
Actualmente solo funciona show() en PostController.php

Veamos un ejemplo:

1. Al llamar GET `/api/v1/posts/1`, Laravel hace route model binding: toma el 1, busca Post::findOrFail(1), y entrega ese modelo en $post. (Si no existe, Laravel devuelve un error 404 como JSON).

2. Al retornar directamente $post, Laravel reconoce que es un modelo Eloquent (una clase de Laravel que representa una tabla de la base de datos) y lo serializa a JSON. Los datos salen de la tabla posts, definida en la migración: id, user_id, title, slug, content, created_at y updated_at.

El registro de API en app.php carga api.php, aplica el prefijo /api y configura que las excepciones de URLs api/* se rendericen como JSON. Por eso tanto las respuestas correctas como los errores de esta API usan JSON.

> Josue: Si nos damos cuenta, quien hace el trabajo internamente es el metodo "apiResource", ya que conecta los metodos http con los metodos que tiene el controlador (metodos que son instanciados cuando se crea el controlador con el "--api" al final), y maneja la manera de obtener la respuesta, es decir si Laravel recibe un modelo Eloquente, lo devuelve en json, o sino un 404

# Mejorando y definiendo la salida (Resource)

Para mejorar la salida de la api usaremos un Resource, este puede ser creado con el siguiente comando

`php artisan make:Resource **VERSION**/**NOMBRE**`
- VERSION es una gran practica, ya que la api suele mejorarse con el tiempo
- NOMBRE es el nombre que recibira el recurso, para nuestro caso usaremos "PostResource" (destinado al post)

Este se ubicara dentro de `app/Http/Resources`

## Instalacion 

### PostResource

En este archivo veremos que tendra una clase con su nombre y retornara una lista, por defecto es:
   ```     
   return parent::toArray($request);
   ```

Nosotros debemos reemplazar el return con una lista con los nombres de los campos que deseamos, a los cuales se les asigne el atributo del objeto deseado

```
return [
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
        ];
```

Seguido debemos unir este archivo con PostController, para eso usaremos el namespace, y lo importaremos en el PostController

### PostController

Importamos el PostResource con su namespace, y en el metodo correspondiente a Post (que seria show) es donde retornaremos al PostResource creado. 

Desde que usaremos el PostResource, sin importar su return (por defecto o personalizado), el json a recibir sera encapsulado en un objeto llamado 'data'

## Salida

Antes recibiamos todos los campos del dato solicitado, ahora solo recibiremos los campos que definimos encapsulado en un objeto llamado "data" (propio del PostResource).

# Collection (GET - Obteniendo todos los elementos)

Definiremos el metodo 'index' del controlador de la API, para poder obtener toda la bd, para ello tendremos que entrer al link:

`http:.../api/V1/post` . Aplicado a nuestro ejemplo, sin pasar una variable al final para indicar el numero del elemento de post, es decir traer todos.

Para definir el return tambien usaremos el Resource creado, es decir `PostResource`, pero con su metodo `collection`  y con metodos para ordenar del ultimo y con una paginacion. Quedaría de la siguiente manera

```
    public function index()
    {
        return PostResource::collection(Post::latest()->paginate(10));
    }
```

# Eliminando (DELETE)

Para eliminar, lo correcto es retornar un 204 en lugar de 200, para ello podemos agregar el siguiente codigo DESPUES de borrar el elemento en cuestion quedando de la siguiente manera segun nuestro ejemplo actual

```
public function destroy(Post $post)
{
    $post->delete();

    return response()->noContent(); # Esta linea es la que devuelve un 204
}
```

Tambien podemos retornar un response con algun mensaje de la siguiente manera:

```
return response('', 204);
```


