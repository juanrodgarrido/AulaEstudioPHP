<div align="center">

# AulaEstudio

**Formulario de inscripción con validación en servidor, PHP, MySQL y Docker**

![PHP](https://img.shields.io/badge/PHP-Apache-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-jQuery-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

</div>

---

## Índice

1. [Descripción](#descripción)
2. [Características](#características)
3. [Tecnologías](#tecnologías)
4. [Estructura del proyecto](#estructura-del-proyecto)
5. [Requisitos previos](#requisitos-previos)
6. [Puesta en marcha](#puesta-en-marcha)
7. [Variables de entorno](#variables-de-entorno)
8. [Base de datos](#base-de-datos)
9. [Flujo de la aplicación](#flujo-de-la-aplicación)
10. [Comandos útiles](#comandos-útiles)
11. [Resolución de problemas](#resolución-de-problemas)
12. [Decisiones de seguridad](#decisiones-de-seguridad)
13. [Autor](#autor)

---

## Descripción

Landing con un formulario de inscripción para **AulaEstudio**. El usuario introduce nombre, primer apellido, segundo apellido, DNI y correo electrónico. Los datos se validan en el servidor con PHP, se guardan en una base de datos MySQL y se muestran en una página de resultados leyéndolos de la propia base.

Todo el entorno funciona con Docker, sin necesidad de instalar PHP, Apache ni MySQL en el equipo.

## Características

- Validación en servidor de todos los campos (vacíos, longitud máxima y formato).
- Validación del DNI: formato de 8 números y 1 letra, con comprobación de la letra mediante el módulo 23.
- Validación del correo electrónico con `filter_var`.
- Envío por `POST`, para que el DNI no quede en la URL, el historial ni los logs.
- Conservación de lo escrito cuando hay errores y listado de los mismos.
- Inserción con **sentencias preparadas** (PDO) contra inyección SQL.
- Patrón *Post/Redirect/Get*: solo se redirige a la página de resultados si todo es válido.
- Gestión de errores de base de datos sin exponer detalles técnicos al usuario (el detalle va al log).
- Tabla creada automáticamente al iniciar el contenedor de base de datos.

## Tecnologías

| Capa | Tecnología |
| --- | --- |
| Frontend | HTML, CSS, JavaScript / jQuery |
| Backend | PHP con Apache (imagen `php:apache`) |
| Base de datos | MySQL 8.4 |
| Acceso a datos | PDO con la extensión `pdo_mysql` |
| Infraestructura | Docker y Docker Compose |

## Estructura del proyecto

```text
proyecto-ejercicio1/
├── mysql/                      # Infraestructura (no la sirve Apache)
│   ├── docker-compose.yml
│   ├── Dockerfile
│   ├── .env                    # Credenciales reales (NO se sube a Git)
│   ├── .env.example            # Plantilla con valores ficticios
│   └── init/
│       └── tabla.sql           # CREATE TABLE, se ejecuta al iniciar MySQL
├── src/                        # Código de la aplicación (lo sirve Apache)
│   ├── index.php               # Formulario y validación
│   ├── resultados.php          # Muestra los datos leídos de la base
│   ├── conexion.php            # Conexión PDO
│   ├── style.css
│   ├── SVG/
│   └── Fonts/
├── .gitignore
└── README.md
```

La infraestructura está en una carpeta distinta de la web a propósito: así Apache nunca sirve el `docker-compose.yml` ni el `.env` con las contraseñas.

## Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows y macOS) o Docker Engine con el plugin Compose (Linux).
- [Git](https://git-scm.com/) para clonar el repositorio.

Comprueba que ambos funcionan:

```bash
docker --version
docker compose version
git --version
```

## Puesta en marcha

### 1. Clonar el repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd proyecto-ejercicio1
```

### 2. Crear el archivo `.env`

Entra en la carpeta de infraestructura y copia la plantilla:

```bash
cd mysql
```

En Linux o macOS:

```bash
cp .env.example .env
```

En Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

Abre `.env` y sustituye los valores por contraseñas propias (ver [Variables de entorno](#variables-de-entorno)).

> En Windows, comprueba que el archivo se llama exactamente `.env` y no `.env.txt`.

### 3. Levantar los contenedores

Desde la carpeta `mysql/` (donde está el `.env`):

```bash
docker compose up -d --build
```

La primera vez descargará las imágenes y construirá la de PHP, por lo que tardará un poco. MySQL también necesita unos segundos para inicializarse.

### 4. Comprobar que todo está en marcha

```bash
docker compose ps
```

Los servicios `db` y `web` deben aparecer en estado `running`.

### 5. Abrir la aplicación

Visita [http://localhost:8080](http://localhost:8080) en el navegador.

### 6. Parar el entorno

```bash
docker compose down
```

Los datos se conservan en el volumen `mysql_data`. Para borrarlos también, consulta [Comandos útiles](#comandos-útiles).

## Variables de entorno

Se definen en `mysql/.env`. Compose lo lee automáticamente desde la carpeta en la que se ejecuta el comando.

| Variable | Descripción | Ejemplo |
| --- | --- | --- |
| `DB_ROOT_PASSWORD` | Contraseña del usuario `root` de MySQL. Solo llega al contenedor `db`. | `cambia_esta_clave_root` |
| `DB_DATABASE` | Nombre de la base de datos. | `formulario_ejercicio1` |
| `DB_USER` | Usuario que usa la aplicación (con permisos solo sobre su base). | `usuario_app` |
| `DB_PASSWORD` | Contraseña del usuario de la aplicación. | `cambia_esta_clave_app` |

Recomendaciones:

- Usa contraseñas **distintas entre sí** y distintas de `root`.
- No subas nunca el `.env` al repositorio. Debe estar en `.gitignore`.
- Las variables `MYSQL_*` solo se aplican cuando el volumen está vacío. Si cambias las credenciales después de la primera ejecución, hay que borrar el volumen con `docker compose down -v`.

## Base de datos

La tabla se define en `mysql/init/tabla.sql` y MySQL la crea sola la primera vez que arranca con el volumen vacío (carpeta especial `docker-entrypoint-initdb.d`).

```sql
CREATE TABLE formulario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido1 VARCHAR(50) NOT NULL,
    apellido2 VARCHAR(50) NOT NULL,
    dni CHAR(9) NOT NULL,
    email VARCHAR(255) NOT NULL
);
```

Para consultar los datos desde la terminal:

```bash
docker compose exec db mysql -u <DB_USER> -p
```

Y dentro de MySQL:

```sql
USE formulario_ejercicio1;
SELECT * FROM formulario;
```

El servicio `db` no publica puertos hacia el exterior: solo es accesible desde el contenedor `web`, donde se encuentra por el nombre del servicio (`db`).

## Flujo de la aplicación

```text
 Navegador                 index.php                       MySQL
    │  POST formulario         │                              │
    ├─────────────────────────►│  valida todos los campos     │
    │                          │  si hay errores: vuelve a    │
    │                          │  mostrar el formulario       │
    │                          │  INSERT (sentencia preparada)│
    │                          ├─────────────────────────────►│
    │                          │  guarda el id en la sesión   │
    │  302 → resultados.php    │                              │
    │◄─────────────────────────┤                              │
    │  GET                     │                              │
    ├────────────► resultados.php  SELECT ... WHERE id = ?    │
    │                          ├─────────────────────────────►│
    │  muestra los datos       │◄─────────────────────────────┤
    │◄─────────────────────────┤                              │
```

En la sesión solo se guarda el **id** de la fila insertada, no los datos personales. `resultados.php` lee la fila de la base con ese id, de modo que cada navegador solo puede ver la inscripción que él mismo ha enviado.

## Comandos útiles

Todos se ejecutan desde la carpeta `mysql/`.

| Comando | Qué hace |
| --- | --- |
| `docker compose up -d --build` | Levanta todo y reconstruye la imagen si cambió el `Dockerfile`. |
| `docker compose up -d` | Aplica cambios del compose sin reconstruir. |
| `docker compose ps` | Muestra el estado de los contenedores. |
| `docker compose logs web` | Logs de Apache y PHP (aquí aparecen los errores registrados con `error_log`). |
| `docker compose logs db` | Logs de MySQL. |
| `docker compose config` | Muestra el compose con las variables `${}` ya sustituidas. |
| `docker compose exec web printenv \| grep DB_` | Comprueba las variables de entorno del contenedor web. |
| `docker compose down` | Para y elimina los contenedores, conservando los datos. |
| `docker compose down -v` | Lo anterior y además **borra el volumen** (se pierden los datos). |

## Resolución de problemas

**La página muestra "Ha habido un error en la conexión".**
MySQL puede tardar unos segundos en aceptar conexiones tras un `up`. Espera y recarga. Si persiste, revisa `docker compose logs web` y `docker compose logs db`.

**Error `Access denied for user` en los logs.**
Las credenciales del `.env` no coinciden con las que MySQL guardó al crear el volumen. Borra el volumen con `docker compose down -v` y vuelve a levantar.

**La tabla `formulario` no existe.**
El script de `init/` solo se ejecuta con el volumen vacío. Ejecuta `docker compose down -v` y luego `docker compose up -d`. Comprueba también que el archivo termina en `.sql` (en Windows, que no se llame `tabla.sql.txt`).

**Compose no encuentra las variables (`variable is not set`).**
Ejecuta los comandos desde la carpeta `mysql/`, donde está el `.env`, y comprueba que el archivo no se llama `.env.txt`.

**El puerto 8080 está ocupado.**
Cambia el puerto de la izquierda en `docker-compose.yml` (por ejemplo `"8081:80"`) y accede por ese puerto.

**Los cambios en el PHP no se ven.**
La carpeta `src/` está enlazada con el contenedor, así que no hace falta reconstruir. Recarga el navegador con `Ctrl + F5`.

## Decisiones de seguridad

- **`POST` en lugar de `GET`**: el DNI es un dato personal y no debe aparecer en la URL ni en los logs.
- **Validación en servidor**: la validación del cliente se puede saltar con la URL, las herramientas de desarrollo o `curl`.
- **Sentencias preparadas con `EMULATE_PREPARES` desactivado**: consulta y datos viajan por separado, así que los datos nunca se interpretan como SQL.
- **Escape con `htmlspecialchars` al mostrar** los datos, para evitar XSS.
- **Credenciales fuera del código**: `.env`, variables de entorno del contenedor y `getenv()` en PHP.
- **Mínimo privilegio**: la aplicación usa un usuario con permisos solo sobre su base; la contraseña de `root` nunca llega al contenedor web.
- **Errores controlados**: el usuario recibe un mensaje genérico y el detalle técnico se escribe en el log.
- **Infraestructura fuera de la carpeta web**: Apache no puede servir el compose ni el `.env`.

## Autor

Juan Rodríguez Garrido. Proyecto de la asignatura de Cliente, 2º de Desarrollo de Aplicaciones Web (DAW).
