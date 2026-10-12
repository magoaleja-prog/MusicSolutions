---
name: Commits convencionales
description: Convencion de mensajes de commit (Conventional Commits) y flujo de ramas usado en MusicSolutions
---

# Commits convencionales

Formato:

```
<tipo>(<alcance>): <descripcion en imperativo>

[cuerpo opcional]

[footer opcional]
```

## Tipos permitidos

| Tipo       | Uso                                             |
| ---------- | ----------------------------------------------- |
| `feat`     | Nueva funcionalidad                             |
| `fix`      | Correccion de un error                          |
| `docs`     | Solo documentacion                              |
| `style`    | Formato, sin cambio de comportamiento           |
| `refactor` | Cambio de codigo que no anade ni corrige        |
| `perf`     | Mejora de rendimiento                           |
| `test`     | Tests                                           |
| `chore`    | Mantenimiento, build, dependencias              |

## Alcances habituales

`core`, `auth`, `tuner`, `scores`, `search`, `developer`, `ui`, `db`, `opencode`, `ci`.

## Reglas

1. Descripcion en minusculas, imperativo y sin punto final.
2. Maximo ~72 caracteres en la primera linea.
3. Un commit = un cambio coherente (no mezcles refactor con feature).
4. Los cambios incompatibles llevan `BREAKING CHANGE:` en el footer.
5. Antes de commitear: `php -l` en los archivos tocados y `git status` limpio de basura.

## Ejemplos del proyecto

```
feat(afinador): detecta la nota con la Web Audio API
fix(buscar): usa parametros unicos en las consultas LIKE de PDO
docs(readme): explica la instalacion en XAMPP
chore(db): anade la migracion de la tabla scores
```

## Flujo de ramas

- `main` siempre estable.
- Ramas de trabajo: `feat/descripcion-corta`, `fix/descripcion-corta`.
- Abre Pull Request hacia `main`; la revision la puede hacer el agente `reviewer`.
