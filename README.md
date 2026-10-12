# MusicSolutions 🎵

Plataforma web para musicos: **afinador en tiempo real**, **biblioteca de
partituras**, **buscador de musica** y una **zona para programadores** donde
compartir snippets, librerias, herramientas y proyectos.

Construida en **PHP 8.2 sin framework** (MVC ligero, PSR-4, PDO) con HTML, CSS
y JavaScript propios, y conectada a una base de datos MySQL/MariaDB llamada
`solutions` donde se guardan los datos de inicio de sesion.

---

## Funcionalidades

| Modulo | Que hace |
| ------ | -------- |
| 🔐 Autenticacion | Registro e inicio de sesion con contrasenas cifradas (bcrypt) en la BD `solutions`. |
| 🎚️ Afinador | Detecta la nota con el microfono (Web Audio API, algoritmo probado con Node) y muestra la desviacion en cents, con presets por instrumento y **nota de referencia sonora** para afinar de oido. |
| 🔊 Musica de verdad | Catalogo de **musica real**: el seed descarga titulos de iTunes con su **preview de audio y portada**; **reproductor global** en la barra inferior (progreso, volumen y arte del tema) en la portada, el buscador y el panel. |
| 🎼 Partituras | Sube PDF, MusicXML, MuseScore, MIDI o imagenes; organiza por instrumento y comparte o mantén privado. |
| 🔎 Buscar musica | Combina un catalogo local con resultados publicos de iTunes (sin API key) y busca tambien en partituras y recursos. |
| 🧩 Programadores | Publica snippets, enlaces, librerias o archivos descargables, con **consola Playground** y **detector de errores** (lint PHP, JavaScript y Python). |
| ♿ Accesibilidad | Panel fijo con alto contraste, texto grande, guia de lectura y modo sin animaciones (se guarda en el dispositivo). |

---

## Requisitos

- **PHP 8.1+** con extensiones `pdo_mysql`, `json`, `fileinfo` y `curl` (recomendada).
- **MySQL o MariaDB** (incluido en XAMPP).
- **Composer** (opcional; el proyecto funciona sin el gracias a un autoloader propio).

En Windows con XAMPP, PHP esta en `C:\xampp\php\php.exe` y MySQL en el panel de XAMPP.

---

## Instalacion rapida

1. **Arranca MySQL** desde el panel de control de XAMPP.

2. **Configura el entorno**: copia `.env.example` a `.env` y ajusta los datos.

   ```bash
   cp .env.example .env
   ```

   ```ini
   DB_DATABASE=solutions
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Crea la base de datos y las tablas** (crea `solutions` si no existe):

   ```bash
   C:\xampp\php\php.exe bin/migrate.php
   ```

4. **Carga datos de ejemplo** (usuario demo, catalogo de canciones y recursos):

   ```bash
   C:\xampp\php\php.exe bin/seed.php
   ```

5. **Levanta el servidor de desarrollo**:

   ```bash
   C:\xampp\php\php.exe -S localhost:8000 -t public public/router.php
   ```

6. Abre <http://localhost:8000> y entra con:

   ```
   Usuario:  demo@musicsolutions.test
   Password: password
   ```

> Con Apache (XAMPP) apunta el VirtualHost a la carpeta `public/`. El archivo
> `public/.htaccess` ya redirige todo al front controller.

---

## Arranque de un clic (Windows)

Si usas XAMPP, hay un script que hace todo: enciende MySQL si esta apagado,
levanta el servidor web y abre el navegador.

1. **Doble clic en `iniciar.bat`** (dentro de la carpeta del proyecto).
2. El script **elige el primer puerto libre** (80 → 8080 → 8001). Con 80 libre
   entras en <http://localhost> sin escribir puerto.
3. Entra con `demo@musicsolutions.test` / `password`.

Para detener: cierra la ventana *MusicSolutions PHP* (el servidor) y usa el
panel de XAMPP para MySQL si quieres apagarlo.

> Las URLs se construyen solas a partir del host, puerto y carpeta del momento
> (`APP_URL` vacio en `.env`), asi el mismo proyecto funciona en local, en
> <http://localhost> o en un dominio de hosting sin cambios.

---

## Comandos utiles

Si tienes Composer instalado:

```bash
composer install     # dependencias de desarrollo (PHPUnit)
composer serve       # servidor en localhost:8000
composer migrate     # ejecuta migraciones
composer seed        # datos de ejemplo
composer test        # tests
```

Equivalente sin Composer:

```bash
C:\xampp\php\php.exe bin/migrate.php
C:\xampp\php\php.exe bin/seed.php
```

---

## Estructura del proyecto

```
MusicSolutions/
├── app/
│   ├── Core/          Núcleo MVC (Router, Request, Response, View, Session, Auth, Csrf, Validator, Database, Model)
│   ├── Controllers/   Controladores HTTP
│   ├── Models/        Acceso a datos (PDO)
│   ├── Middleware/    AuthMiddleware, GuestMiddleware
│   └── Support/       helpers.php, Uploader, MusicSearch, Linter (detector de errores)
├── bin/               migrate.php, seed.php
├── bootstrap/         autoload.php
├── config/            app.php, database.php
├── database/          migrations/ (esquema SQL versionado) + solutions.sql (dump para hosting)
├── public/            Document root: index.php, assets/, uploads/
├── resources/views/   Vistas PHP (layouts, partials, paginas)
├── routes/web.php     Definicion de rutas + API /api/lint
├── storage/           Logs, cache y temporales del linter
├── tests/unit/        Prueba del algoritmo de deteccion de tono (Node)
└── .opencode/         Agentes y skills para el desarrollo con IA
```

---

## Despliegue (hosting)

1. **Sube el proyecto** dejando como carpeta publica (document root) el
   directorio `public/`: `index.php` y `router.php` ya estan preparados para
   servir la app en la raiz o en una subcarpeta del dominio.
2. **Crea la base de datos** e importa el dump completo:

   ```bash
   mysql -u USUARIO -p solutions < database/solutions.sql
   ```

   O, si prefieres crear las tablas desde cero, importa las migraciones de
   `database/migrations/` en orden y ejecuta `bin/seed.php`.

   > **Sobre el catalogo**: `bin/seed.php` descarga musica real de iTunes
   > (titulo, artista, genero, portada y preview) cuando hay conexion, y
   > reemplaza los temas de demostracion. Sin red, se queda el catalogo demo.

3. **Configura `.env`** con los datos del hosting:

   ```ini
   APP_URL=https://tu-dominio.com
   DB_HOST=localhost
   DB_DATABASE=solutions
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_clave
   ```

   Con `APP_URL=` vacio las URLs se calculan solas.
4. Los previews y portadas del catalogo usan URLs publicas de iTunes, no
   exigen almacenamiento propio. Los archivos que suban los usuarios van a
   `public/uploads/`, que ya esta en el `.gitignore`.
5. Requisitos: PHP 8.1+, extensiones `pdo_mysql`, `json`, `fileinfo` y
   `curl`. El detector de errores del Playground usa `php`, `node` y/o
   `python` del servidor; si uno no existe, la vista lo avisa y sigue
   funcionando el resto.

---

## Seguridad

- Contrasenas con `password_hash` / `password_verify` (bcrypt).
- Todas las consultas usan **PDO con sentencias preparadas**.
- Formularios protegidos con **token CSRF**.
- Salida escapada con `e()` para evitar XSS.
- Subidas con lista blanca de extensiones, nombre aleatorio y limite de tamano.
- Sesiones con cookies `HttpOnly` y `SameSite=Lax`.

Consulta la skill `seguridad-php` para el checklist completo.

---

## Desarrollo con IA (OpenCode)

El repositorio incluye agentes y skills en `.opencode/`:

- Agentes: `backend`, `frontend`, `database`, `reviewer`.
- Skills: `nueva-funcionalidad`, `seguridad-php`, `commits-convencionales`.

Lee `AGENTS.md` para las convenciones del proyecto.

---

## Licencia

[MIT](LICENSE) © María Alejandra
