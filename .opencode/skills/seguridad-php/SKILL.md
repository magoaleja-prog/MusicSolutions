---
name: Seguridad PHP
description: Reglas de seguridad de MusicSolutions: PDO con consultas preparadas, CSRF, escapado XSS, subida segura de archivos, sesiones y contrasenas
---

# Checklist de seguridad

Aplica esto a cualquier codigo que toque datos del usuario.

## Base de datos

- **Siempre** consultas preparadas. Nunca interpolies entrada del usuario en SQL.
- Si necesitas el nombre de una tabla/columna dinamica, validalo contra una lista blanca.
- No reutilices un parametro nombrado dos veces en la misma consulta (usa `:t1`, `:t2`).
- La conexion (`App\Core\Database`) ya desactiva la emulacion de prepares.

## Formularios y CSRF

- Todo `<form method="post">` incluye `<?= csrf_field() ?>`.
- Todo metodo POST llama a `Csrf::verify()` como primera linea.
- El token se compara con `hash_equals`.

## Salida (XSS)

- Escapa **todo** con `e()` en las vistas PHP.
- No uses `|raw` ni `echo` directo de datos del usuario.
- En JS, escapa al construir HTML (mira `search.js`).

## Subidas de archivos

- Usa siempre `App\Support\Uploader::store()`.
- Respeta la lista blanca de extensiones (ya definida por controlador).
- Nombres aleatorios, sin depender del nombre original.
- Limite de tamano desde `config('app.upload_max_size')`.
- Al borrar un registro, borra tambien su archivo con `@unlink`.

## Sesiones y autenticacion

- Contrasenas con `password_hash` (bcrypt) y verificacion con `password_verify`.
- Regenera el id de sesion al iniciar sesion (`Auth::login` ya lo hace).
- Cookies `HttpOnly` y `SameSite=Lax` (configurado en `Session::start`).

## Autorizacion

- Comprueba propiedad antes de editar/borrar: `(int) $row['user_id'] === (int) Auth::id()`.
- Los recursos privados no deben ser accesibles por URL directa (comprueba `visibility`).

## Antes de terminar

Busca patrones peligrosos:

```
grep -rn "\$_GET\|\$_POST\|\$_REQUEST" app
grep -rn "->query(\|->exec(" app
```

Cada hallazgo debe ir por parametros o por lista blanca.
