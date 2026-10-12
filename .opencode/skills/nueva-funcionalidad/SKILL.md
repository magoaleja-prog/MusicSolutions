---
name: Nueva funcionalidad
description: Guia paso a paso para anadir un modulo o funcionalidad nueva (ruta, controlador, modelo, vista y estilos) siguiendo la arquitectura MVC de MusicSolutions
---

# Anadir una funcionalidad nueva

MusicSolutions es un MVC ligero sin framework. Sigue siempre estos pasos.

## 1. Base de datos (si aplica)

- Crea `database/migrations/NNNN_descripcion.sql` con el siguiente numero libre.
- Una sola sentencia por archivo, `ENGINE=InnoDB`, `utf8mb4_unicode_ci`.
- Aplica los cambios: `php bin/migrate.php`.
- Nunca edites una migracion ya aplicada; crea una nueva.

## 2. Modelo

- Crea `app/Models/MiModelo.php` extendiendo `App\Core\Model`.
- Define `protected string $table = 'nombre_tabla';`.
- Usa los metodos heredados (`all`, `find`, `firstBy`, `where`, `create`, `update`, `delete`).
- Para consultas propias usa `$this->query($sql, $params)` **siempre** con parametros nombrados.
- Ojo: un parametro nombrado no se puede reutilizar en la misma consulta; usa `:t1`, `:t2`...

## 3. Controlador

- Crea `app/Controllers/MiControlador.php` extendiendo `App\Core\Controller`.
- Devuelve `Response` con `$this->view(...)`, `$this->json(...)` o `$this->redirect(...)`.
- En cada POST: `Csrf::verify()` antes de leer datos.
- Valida con `Validator::make(...)` y, si falla, guarda errores con `Session::flash('errors', ...)`
  y datos con `Session::flash('_old', ...)`, y redirige.
- Usa `Auth::id()` para el autor y `Auth::check()` para permisos.

## 4. Ruta

- Registra la ruta en `routes/web.php`.
- Pon las rutas literales antes de las dinamicas (`/x/nuevo` antes de `/x/{id}`).
- Anade `[AuthMiddleware::class]` si requiere sesion.

## 5. Vista

- Crea `resources/views/mi_modulo/index.php` (y `create.php`, `show.php`...).
- Escapa **toda** salida con `e()`.
- Reutiliza el layout `layouts/app.php` (por defecto) y los parciales existentes.
- Usa clases CSS existentes; si anades nuevas, documentalas en `public/assets/css/app.css`.
- Para JS propio: crea `public/assets/js/mi_modulo.js` y declara `$pageScripts = ['js/mi_modulo.js'];` en la vista.

## 6. Cierre

- Ejecuta `php -l` sobre los archivos nuevos (usa `C:\xampp\php\php.exe`).
- Prueba el flujo completo (crear, listar, ver, borrar) con el servidor `php -S localhost:8000 -t public public/router.php`.
- Haz commit siguiendo la skill de commits convencionales.
