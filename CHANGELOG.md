# Changelog

Todos los cambios relevantes de este proyecto se documentan aqui.

El formato sigue [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/)
y el proyecto usa [Versionado Semantico](https://semver.org/lang/es/).

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
