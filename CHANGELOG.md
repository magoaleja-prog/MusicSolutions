# Changelog

Todos los cambios relevantes de este proyecto se documentan aqui.

El formato sigue [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/)
y el proyecto usa [Versionado Semantico](https://semver.org/lang/es/).

## [1.1.0] - 2026-10-12

### Anadido

- **Rediseno rock**: tema oscuro con tipografias Anton/Outfit/JetBrains Mono,
  neones, grano, ecualizadores animados y cinta marquee en la portada.
- **Musica escuchable**: columna `preview_url` en el catalogo con MP3 reales
  (SoundHelix), reproductor global en la barra inferior y botones de play en la
  portada, el buscador, el panel y los resultados de iTunes.
- **Panel de accesibilidad** (boton flotante): alto contraste, texto grande,
  guia de lectura y modo sin animaciones; preferencias persistentes por
  dispositivo. Salto de navegacion y foco visible en toda la interfaz.
- **Playground de programadores** (`/programadores/playground`): consola para
  probar codigo con ejemplo por lenguaje, ejecucion de JavaScript en un iframe
  aislado (sandbox), previsualizacion de HTML/CSS y **detector de errores**
  (`POST /api/lint` con `php -l`, `node --check` y `python -m py_compile`).
- **Diferenciacion visual por zona**: skin `skin-music` (rojo/violeta/cian) con
  `skin-code` (verde terminal/cian) en toda la zona de programadores.
- **Afinador reforzado**: algoritmo extraido a `pitch-detection.js` (modulo puro
  probado con Node: 8/8 tonos detectados) y notas de referencia que suenan para
  afinar de oido.
- Herramientas de despliegue: `database/solutions.sql` (dump completo),
  seccion de hosting en el README y `iniciar.bat` que elige puerto libre
  (80 → 8080 → 8001) para entrar sin teclear el puerto.
- URLs calculadas automaticamente segun host, puerto y carpeta (`APP_URL`
  vacio por defecto): la app funciona en local, con Apache o en hosting.

### Corregido

- El renderizador de vistas ahora pasa al layout las variables declaradas por
  la vista (`pageScripts`, `bodyClass`), lo que habilitaba scripts y clases de
  pagina que antes no se cargaban.
- `Request` ya lee cuerpos JSON (necesario para la API de lint).
- La redireccion de `DeveloperController::destroy` apuntaba a una ruta inexistente.
- `is_writable()` daba falsos negativos en directorios de OneDrive: el linter
  valida la escritura al crearla, no antes.
- `url()` generaba URLs con backslashes (`http://host\/assets/...`) en Windows
  porque `dirname()` de `SCRIPT_NAME` devuelve `\`; ahora normaliza el separador
  y los estilos y scripts se cargan con diseno.
- `iniciar.bat` fallaba con un error de parseo de cmd ("No se esperaba...") al
  hacer doble clic; reescrito sin bloques parentizados, con comprobacion de que
  el servidor responde antes de abrir el navegador.

## [1.0.0] - 2026-10-12

### Anadido

- Nucleo MVC ligero: router, request/response, vistas, sesiones y validacion.
- Autenticacion con registro e inicio de sesion contra la base de datos `solutions`
  (contrasenas con bcrypt, proteccion CSRF y sesiones seguras).
- Afinador en tiempo real con Web Audio API y presets de guitarra, bajo, ukelele,
  violin, violonchelo y modo cromatico.
- Biblioteca de partituras: subida (PDF, MusicXML, MuseScore, MIDI, imagenes),
  vista previa, descarga y visibilidad publica/privada.
- Buscador de musica con catalogo local y resultados publicos de iTunes, ademas
  de busqueda en partituras y recursos.
- Zona de programadores para publicar snippets, enlaces, librerias y archivos.
- Interfaz responsive con tema oscuro, animaciones y accesibilidad basica.
- Migraciones SQL versionadas y datos de ejemplo (`bin/migrate.php`, `bin/seed.php`).
- Agentes y skills de OpenCode, documentacion y flujo de commits convencionales.

[1.0.0]: https://example.com/releases/1.0.0
