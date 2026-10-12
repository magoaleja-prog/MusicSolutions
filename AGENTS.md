# AGENTS.md

Guia para agentes de IA y colaboradores que trabajen en **MusicSolutions**.

## Que es este proyecto

Plataforma web para musicos construida en **PHP 8.2 sin framework**, con
arquitectura MVC ligera, PSR-4 y PDO. Incluye:

- **Afinador** en tiempo real (Web Audio API).
- **Partituras**: subir, guardar, ver y descargar documentos.
- **Buscar musica**: catalogo local + catalogo publico (iTunes Search API).
- **Zona de programadores**: publicar snippets, enlaces, librerias y archivos.
- **Autenticacion**: registro e inicio de sesion contra la base de datos `solutions`.

## Mapa del repositorio

```
app/Core/          Nucleo: App, Router, Request, Response, View, Session, Auth, Csrf, Validator, Database, Model
app/Controllers/   Controladores HTTP
app/Models/        Acceso a datos (extienden App\Core\Model)
app/Middleware/    AuthMiddleware, GuestMiddleware
app/Support/       helpers.php, Uploader, MusicSearch
config/            app.php, database.php (leen del .env)
database/          migrations/*.sql
resources/views/   Vistas PHP (layouts, partials, paginas)
routes/web.php     Definicion de rutas
public/            Document root (index.php, assets, uploads)
bin/               migrate.php, seed.php
.opencode/         Agentes y skills de OpenCode
```

## Convenciones

- `declare(strict_types=1);` en todos los archivos PHP.
- Clases y metodos en ingles; textos de interfaz en espanol.
- Consultas preparadas siempre; parametros nombrados unicos por consulta.
- Validar la entrada (`Validator`) y escapar la salida (`e()`).
- Todo POST lleva `csrf_field()` + `Csrf::verify()`.
- Las subidas pasan por `App\Support\Uploader`.
- Respeta los estilos y clases CSS existentes en `public/assets/css/app.css`.

## Comandos

Ejecuta PHP desde XAMPP:

```bash
# Servidor de desarrollo
C:\xampp\php\php.exe -S localhost:8000 -t public public/router.php

# Base de datos
C:\xampp\php\php.exe bin/migrate.php
C:\xampp\php\php.exe bin/seed.php

# Comprobar sintaxis
C:\xampp\php\php.exe -l app/Core/App.php
```

Con Composer instalado puedes usar `composer serve`, `composer migrate`, `composer seed`, `composer test`.

## Agentes disponibles

- `backend` — logica PHP (MVC, PDO, seguridad).
- `frontend` — HTML, CSS y JavaScript.
- `database` — esquema, migraciones y consultas.
- `reviewer` — revision de cambios (solo lectura).

## Skills disponibles

- `nueva-funcionalidad` — pasos para anadir un modulo completo.
- `seguridad-php` — checklist de seguridad.
- `commits-convencionales` — formato de commits y ramas.

## Antes de terminar una tarea

1. `php -l` sobre los archivos tocados.
2. Prueba manual del flujo afectado.
3. Commit con Conventional Commits.
