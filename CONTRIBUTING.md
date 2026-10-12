# Contribuir a MusicSolutions

Gracias por tu interes. Estas son las normas del proyecto.

## Flujo de trabajo

1. Crea una rama desde `main`:

   ```bash
   git checkout -b feat/mi-funcionalidad
   ```

2. Haz cambios pequenos y coherentes.
3. Comprueba la sintaxis y prueba el flujo afectado.
4. Haz commits con **Conventional Commits** (ver abajo).
5. Abre un Pull Request hacia `main`.

## Convencion de commits

```
<tipo>(<alcance>): <descripcion en imperativo>
```

Tipos: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `chore`.

Alcances habituales: `core`, `auth`, `tuner`, `scores`, `search`, `developer`,
`ui`, `db`, `opencode`, `ci`.

Ejemplos:

```
feat(tuner): anade presets de violin y violonchelo
fix(search): corrige el filtrado por genero
docs(readme): explica el despliegue con Apache
```

## Estilo de codigo

- `declare(strict_types=1);` en todos los archivos PHP.
- Sigue PSR-12 en indentacion (4 espacios) y nombres.
- Clases y metodos en ingles; textos de interfaz en espanol.
- Nunca `echo` sin escapar en las vistas: usa `e()`.

## Checklist antes de enviar

- [ ] `php -l` sin errores en los archivos tocados.
- [ ] Consultas con parametros preparados.
- [ ] CSRF en todos los POST.
- [ ] Vistas escapadas y responsive.
- [ ] `php bin/migrate.php` aplica sin errores (si cambiaste el esquema).
- [ ] Commits con el formato correcto.

## Reportar problemas

Abre un issue describiendo:

- Que esperabas que pasara.
- Que paso realmente.
- Pasos para reproducirlo.
- Entorno (PHP, navegador, sistema operativo).
