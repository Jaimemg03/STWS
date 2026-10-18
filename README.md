# Práctica CRUD - Documentación de cambios

Este documento describe las modificaciones realizadas sobre el código base de la práctica CRUD ubicada en la carpeta `@CRUD/`.

## Aclaración sobre el uso de herramientas de IA

Este archivo **README.md** y los ficheros de estilo `css/normalize.css` y `css/mensaje.css` han sido generados con asistencia de inteligencia artificial, utilizando el siguiente prompt de referencia:

> "Actúa como un asistente de programación web. Crea un archivo README.md detallado y dos hojas de estilo CSS (normalize.css y mensaje.css) para un proyecto CRUD en PHP y MySQL. Los estilos deben seguir la estética del archivo estilo.css existente (tipografía Segoe UI, colores degradados #667eea a #764ba2, bordes redondeados de 12px, sombras suaves). Justifica en el README que estos archivos han sido creados empleando IA."

El resto de los archivos PHP (`conexion.php`, `index.php`, `formulario.php`, `acciones.php`, `insertando.php`, `autentifica.php`, `Lista.php`, `Mensajes.php`, `registro.php`) han sido revisados y modificados manualmente como parte de la práctica académica.

---

## 1. Claves de conexión en fichero `.env` y `.gitignore`

### Objetivo
Evitar dejar credenciales de base de datos hardcodeadas en el código fuente y evitar que el fichero `.env` se suba al repositorio de control de versiones.

### Pasos realizados
1. Se ha creado el fichero **`.env`** en la raíz del proyecto (`@CRUD/.env`) con las siguientes variables:
   - `DB_HOST`: servidor de base de datos (ej. `localhost`)
   - `DB_USUARIO`: usuario de MySQL
   - `DB_PASSWORD`: contraseña de MySQL
   - `DB_NOMBRE`: nombre de la base de datos (ej. `prueba`)
   - `DB_PUERTO`: puerto de conexión (ej. `3306`)

2. Se ha creado el fichero **`.gitignore`** con la entrada `.env` para que Git ignore este fichero y no lo rastree ni suba al repositorio remoto.

3. Se ha modificado **`conexion.php`** para que lea las variables del `.env` mediante `parse_ini_file()` y las incruste en la conexión `mysqli`, eliminando cualquier clave hardcodeada.

### Resultado
El código ya no contiene contraseñas ni usuarios visibles en el repositorio. La configuración sensible vive fuera del control de versiones.

---

## 2. Securización de consultas con parámetros en `mysqli`

### Objetivo
Eliminar vulnerabilidades de inyección SQL (SQL Injection) sustituyendo la concatenación directa de variables en las sentencias SQL por **prepared statements** con parámetros vinculados (`bind_param`).

### Pasos realizados por archivo

| Archivo | Operaciones securizadas |
|---------|------------------------|
| `index.php` | Borrar, modificar, insertar y buscador (`LIKE` con `CAST(altura AS CHAR)`) |
| `formulario.php` | Carga de datos del registro para editar (`SELECT ... WHERE id = ?`) |
| `acciones.php` | Borrar, editar e insertar registros |
| `insertando.php` | Borrar, editar e insertar registros |
| `autentifica.php` | Consulta de login por usuario (`SELECT id, clave FROM usuarios WHERE usuario = ?`) |
| `Lista.php` | Insertar mensaje, modificar mensaje y eliminar mensaje |
| `Mensajes.php` | Carga de mensaje existente para editar |

### Ejemplo del patrón aplicado
```php
$stmt = $Conexion->prepare("update datos_personales set nombre=?, nif=?, nacido=?, altura=? where id=?");
$stmt->bind_param("sssdi", $nombre, $nif, $nacido, $altura, $id);
$stmt->execute();
$stmt->close();
```

### Resultado
Todas las consultas que reciben datos del usuario (por `$_GET`, `$_POST` o `$_REQUEST`) ahora envían los valores por separado de la estructura SQL, impidiendo que un atacante inyecte código malicioso a través de los parámetros.

---

## 3. Uso de `isset()` en `$_GET`, `$_POST`, `$_REQUEST` y `$_SESSION`

### Objetivo
Evitar warnings y errores de tipo `Undefined array key` o `Undefined variable` al intentar acceder a claves que pueden no existir en los superglobales.

### Pasos realizados
- Se ha revisado que **cada lectura** de `$_GET`, `$_POST`, `$_REQUEST` o `$_SESSION` esté protegida con `isset()` o con una validación previa que garantice la existencia de la clave.
- Ejemplo aplicado:
  ```php
  $accion = isset($_REQUEST["Accion"]) ? $_REQUEST["Accion"] : '';
  $id     = isset($_REQUEST["ID"])     ? (int)$_REQUEST["ID"]     : 0;
  ```
- En `Lista.php` y `Mensajes.php` se valida al inicio la existencia de las claves de sesión (`USUARIO_ID`, `USUARIO_NOMBRE`) antes de usarlas en el resto del flujo.

### Resultado
La aplicación se ejecuta sin generar warnings de claves inexistentes ni errores de índices no definidos.

---

## Funcionalidad extra (punto adicional para nota 9 o 10)

Se ha implementado **autenticación segura** como funcionalidad adicional:

- **Formulario de login** (`index.html` → `autentifica.php`).
- **Contraseñas hasheadas** en la base de datos utilizando `password_hash()` de PHP.
- **Verificación segura** con `password_verify()` en el proceso de autenticación (`autentifica.php`).
- **Variables de sesión** (`$_SESSION["USUARIO_ID"]` y `$_SESSION["USUARIO_NOMBRE"]`) para mantener el estado del usuario autenticado entre páginas.
- Fichero `registro.php` para alta de nuevos usuarios con contraseña hasheada.

---

## Estilos CSS

Se han creado los siguientes archivos de estilos para dar coherencia visual a la aplicación:

- `css/normalize.css`
- `css/mensaje.css`

> **Justificación:** Estos archivos `.css` se han creado empleando asistencia de IA, siguiendo la estética definida en `@CRUD/estilo.css` (tipografía, paleta de colores, bordes redondeados, sombras y componentes de formulario).

---

## Estructura del proyecto

```
CRUD/
├── .env
├── .gitignore
├── README.md
├── conexion.php
├── index.php
├── index.html
├── formulario.php
├── acciones.php
├── insertar.php
├── insertando.php
├── Lista.php
├── Mensajes.php
├── autentifica.php
├── registro.php
├── estilo.css
└── css/
    ├── normalize.css
    └── mensaje.css
```

---

## Requisitos previos

1. Servidor web con PHP y MySQL (por ejemplo, XAMPP).
2. Base de datos llamada `prueba` con las tablas:
   - `datos_personales` (id, nombre, nif, nacido, altura)
   - `usuarios` (id, usuario, clave)
   - `mensajes` (id, Usuario, Para, Mensaje, Fecha)
3. El fichero `.env` configurado con las credenciales correctas de tu entorno local.
4. Permisos de lectura en la carpeta para el servidor web (ej. `chmod 755 CRUD/`).

---

## Cómo ejecutar

1. Colocar la carpeta `CRUD/` en el directorio público del servidor web (`htdocs` en XAMPP).
2. Crear la base de datos y tablas usando el SQL proporcionado en clase.
3. Configurar el fichero `.env` con los datos de conexión.
4. Acceder a `http://localhost/CRUD/index.php`.

---

## Subida a GitHub (comandos para ejecutar en tu terminal local)

No dispongo de acceso al repositorio remoto, por lo que los commits debes realizarlos tú en tu máquina local:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/CRUD
git init
git add .
git commit -m "Documentación y cambios de la práctica CRUD"
git remote add origin git@github.com:Jaimemg03/STWS.git
git push -u origin main
```

Si el repositorio ya existe y tienes configurada la clave SSH, omite la línea de `git remote add origin` y ejecuta directamente `git push`.