<div align="center">

# AulaEstudio

**Formulario de inscripción con validación en servidor, PHP y Docker, en dos versiones: MySQL y MongoDB Atlas**

![PHP](https://img.shields.io/badge/PHP-Apache-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![MongoDB](https://img.shields.io/badge/MongoDB-Atlas-47A248?style=for-the-badge&logo=mongodb&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-2-885630?style=for-the-badge&logo=composer&logoColor=white)

</div>

---

## Índice

1. [Descripción](#descripción)
2. [Ramas del repositorio](#ramas-del-repositorio)
3. [Características](#características)
4. [Tecnologías](#tecnologías)
5. [Estructura del proyecto](#estructura-del-proyecto)
6. [Requisitos previos](#requisitos-previos)
7. [Puesta en marcha](#puesta-en-marcha)
8. [Variables de entorno](#variables-de-entorno)
9. [Base de datos](#base-de-datos)
10. [Flujo de la aplicación](#flujo-de-la-aplicación)
11. [Comandos útiles](#comandos-útiles)
12. [Resolución de problemas](#resolución-de-problemas)
13. [Decisiones de seguridad](#decisiones-de-seguridad)
14. [Pendiente](#pendiente)
15. [Autor](#autor)

---

## Descripción

Landing con un formulario de inscripción para **AulaEstudio**. El usuario introduce nombre, primer apellido, segundo apellido, DNI y correo electrónico. Los datos se validan en el servidor con PHP, se guardan en una base de datos y se muestran en una página de resultados leyéndolos de la propia base.

El proyecto existe en **dos versiones**, una por rama, que comparten la validación, el formulario y el flujo de la aplicación, y solo cambian la base de datos y la forma de conectarse a ella.

## Ramas del repositorio

| Rama | Base de datos | Dónde está | Acceso desde PHP |
| --- | --- | --- | --- |
| `main` | MySQL 8.4 | Contenedor propio en Docker Compose | PDO (`pdo_mysql`) |
| `mongodb` | MongoDB | MongoDB Atlas (nube) | Extensión `mongodb` + librería `mongodb/mongodb` (Composer) |

Para cambiar de versión:

```bash
git checkout main      # versión MySQL
git checkout mongodb   # versión MongoDB Atlas
```

> **Importante al cambiar de rama:** el archivo `.env` no viaja con Git y las dos versiones usan variables distintas. Cada versión necesita su propio `.env` (ver [Variables de entorno](#variables-de-entorno)). Lo mismo ocurre con la carpeta `vendor/` de Composer, que solo se usa en la rama `mongodb`.

Este mismo README está en las dos ramas.

## Características

Comunes a las dos versiones:

- Validación en servidor de todos los campos (vacíos, longitud máxima y formato).
- Validación del DNI: formato de 8 números y 1 letra, con comprobación de la letra mediante el módulo 23.
- Validación del correo electrónico con `filter_var`.
- Envío por `POST`, para que el DNI no quede en la URL, el historial ni los logs.
- Conservación de lo escrito cuando hay errores y listado de los mismos.
- Patrón *Post/Redirect/Get*: solo se redirige a la página de resultados si todo es válido.
- Escape con `htmlspecialchars` al mostrar los datos.
- Gestión de errores de base de datos sin exponer detalles técnicos al usuario (el detalle va al log).

Específicas de cada versión:

- **MySQL (`main`)**: inserción con sentencias preparadas (PDO) contra inyección SQL, y tabla creada automáticamente al iniciar el contenedor de base de datos.
- **MongoDB (`mongodb`)**: documentos guardados con `insertOne()` en la colección `inscripciones`, sin esquema que crear de antemano, y conexión cifrada (TLS) con Atlas.

## Tecnologías

| Capa | `main` (MySQL) | `mongodb` (MongoDB Atlas) |
| --- | --- | --- |
| Frontend | HTML y CSS | HTML y CSS |
| Backend | PHP con Apache (`php:apache`) | PHP con Apache (`php:apache`) |
| Base de datos | MySQL 8.4 | MongoDB en Atlas |
| Acceso a datos | PDO con `pdo_mysql` | Extensión `mongodb` y librería `mongodb/mongodb` |
| Dependencias PHP | Ninguna | Composer |
| Infraestructura | Docker y Docker Compose (`web` + `db`) | Docker y Docker Compose (solo `web`) |

## Estructura del proyecto

### Rama `main` (MySQL)

```text
proyecto-ejercicio1/
├── mysql/                      # Infraestructura (no la sirve Apache)
│   ├── docker-compose.yml      # Servicios web y db
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

### Rama `mongodb` (MongoDB Atlas)

```text
proyecto-ejercicio1/
├── mysql/                      # Infraestructura (carpeta con el nombre original)
│   ├── docker-compose.yml      # Solo el servicio web
│   ├── Dockerfile              # PHP + extensión mongodb + Composer
│   ├── .env                    # Credenciales reales (NO se sube a Git)
│   └── .env.example            # Plantilla con valores ficticios
├── src/                        # Código de la aplicación (lo sirve Apache)
│   ├── index.php               # Formulario, validación e insertOne()
│   ├── resultados.php          # Busca el documento con findOne()
│   ├── conexion.php            # Cliente de MongoDB y colección
│   ├── composer.json           # Dependencias PHP
│   ├── composer.lock           # Versiones exactas
│   ├── vendor/                 # Generada por Composer (NO se sube a Git)
│   ├── style.css
│   ├── SVG/
│   └── Fonts/
├── .gitignore
└── README.md
```

La infraestructura está en una carpeta distinta de la web a propósito: así Apache nunca sirve el `docker-compose.yml` ni el `.env` con las credenciales.

## Requisitos previos

Comunes:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows y macOS) o Docker Engine con el plugin Compose (Linux).
- [Git](https://git-scm.com/) para clonar el repositorio.

Solo para la rama `mongodb`:

- Una cuenta de [MongoDB Atlas](https://www.mongodb.com/atlas) con un clúster (sirve el gratuito M0).
- Un **usuario de base de datos** creado en Atlas (Database Access).
- Tu **IP pública** añadida en Atlas (Network Access). Si tu IP cambia, hay que actualizarla.
- Opcional: [MongoDB Compass](https://www.mongodb.com/products/tools/compass) para ver los datos con interfaz gráfica.

Comprueba que lo común funciona:

```bash
docker --version
docker compose version
git --version
```

## Puesta en marcha

### 1. Clonar el repositorio y elegir la rama

```bash
git clone https://github.com/juanrodgarrido/AulaEstudioPHP
cd AulaEstudioPHP
git checkout main        # o: git checkout mongodb
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

Abre `.env` y sustituye los valores por los tuyos (ver [Variables de entorno](#variables-de-entorno)).

> En Windows, comprueba que el archivo se llama exactamente `.env` y no `.env.txt`.

### 3. Levantar los contenedores

Desde la carpeta `mysql/` (donde está el `.env`):

```bash
docker compose up -d --build
```

La primera vez construirá la imagen de PHP, por lo que tardará un poco. En la rama `mongodb` tarda más, porque se compila la extensión `mongodb`. En la rama `main`, MySQL también necesita unos segundos para inicializarse.

### 4. Instalar las dependencias de PHP (solo rama `mongodb`)

```bash
docker compose exec web composer install
```

Esto descarga la carpeta `vendor/` a partir de `composer.lock`. En la rama `main` no hace falta.

### 5. Comprobar que todo está en marcha

```bash
docker compose ps
```

- `main`: los servicios `db` y `web` deben aparecer en estado `running`.
- `mongodb`: el servicio `web` debe aparecer en estado `running`.

### 6. Abrir la aplicación

Visita [http://localhost:8080](http://localhost:8080) en el navegador.

### 7. Parar el entorno

```bash
docker compose down
```

En `main`, los datos se conservan en el volumen `mysql_data`. En `mongodb`, los datos están en Atlas y no se ven afectados. Para más opciones, consulta [Comandos útiles](#comandos-útiles).

## Variables de entorno

Se definen en `mysql/.env`. Compose lo lee automáticamente desde la carpeta en la que se ejecuta el comando.

### Rama `main` (MySQL)

| Variable | Descripción | Ejemplo |
| --- | --- | --- |
| `DB_ROOT_PASSWORD` | Contraseña del usuario `root` de MySQL. Solo llega al contenedor `db`. | `cambia_esta_clave_root` |
| `DB_DATABASE` | Nombre de la base de datos. | `formulario_ejercicio1` |
| `DB_USER` | Usuario que usa la aplicación (con permisos solo sobre su base). | `usuario_app` |
| `DB_PASSWORD` | Contraseña del usuario de la aplicación. | `cambia_esta_clave_app` |

### Rama `mongodb` (MongoDB Atlas)

| Variable | Descripción | Ejemplo |
| --- | --- | --- |
| `MONGODB_URI` | Cadena de conexión de Atlas (Connect > Drivers), con usuario y contraseña. | `mongodb+srv://usuario:clave@cluster.xxxxx.mongodb.net` |
| `MONGODB_DB` | Nombre de la base de datos. MongoDB la crea al guardar el primer documento. | `aulaestudio` |

Recomendaciones:

- No subas nunca el `.env` al repositorio. Debe estar en `.gitignore`.
- En `main`, usa contraseñas **distintas entre sí** y distintas de `root`. Las variables `MYSQL_*` solo se aplican con el volumen vacío: si cambias las credenciales después de la primera ejecución, hay que borrar el volumen con `docker compose down -v`.
- En `mongodb`, usa una contraseña solo con letras y números; si lleva caracteres especiales (`@`, `:`, `/`, `?`, `#`), hay que codificarlos en la URI. Si la contraseña se ha compartido o pegado en algún sitio, cámbiala en Atlas (Database Access) y actualiza `MONGODB_URI`.
- Tras editar el `.env`, recrea el contenedor (`docker compose up -d --force-recreate`): Compose solo lo lee al crearlo.

## Base de datos

### Rama `main` (MySQL)

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

### Rama `mongodb` (MongoDB Atlas)

No hay que crear nada de antemano: la base de datos y la colección `inscripciones` se crean al insertar el primer documento. Cada inscripción se guarda así:

```json
{
  "_id": "ObjectId generado por MongoDB",
  "nombre": "...",
  "apellido1": "...",
  "apellido2": "...",
  "dni": "...",
  "email": "..."
}
```

Equivalencias con MySQL:

| MySQL (`main`) | MongoDB (`mongodb`) |
| --- | --- |
| Contenedor `db` y volumen `mysql_data` | Clúster en Atlas |
| Tabla `formulario` | Colección `inscripciones` |
| Fila | Documento |
| `id` autoincremental | `_id` de tipo `ObjectId` |
| `lastInsertId()` | `getInsertedId()` |
| `SELECT ... WHERE id = ?` | `findOne(['_id' => $objectId])` |

Para ver los datos, usa **Browse Collections** en la web de Atlas o MongoDB Compass conectado con la misma cadena de conexión.

## Flujo de la aplicación

```text
 Navegador                 index.php                    Base de datos
    │  POST formulario         │                              │
    ├─────────────────────────►│  valida todos los campos     │
    │                          │  si hay errores: vuelve a    │
    │                          │  mostrar el formulario       │
    │                          │  guarda la inscripción       │
    │                          │  (INSERT / insertOne)        │
    │                          ├─────────────────────────────►│
    │                          │  guarda el id en la sesión   │
    │  302 → resultados.php    │                              │
    │◄─────────────────────────┤                              │
    │  GET                     │                              │
    ├────────────► resultados.php  busca por id               │
    │                          │  (SELECT / findOne)          │
    │                          ├─────────────────────────────►│
    │  muestra los datos       │◄─────────────────────────────┤
    │◄─────────────────────────┤                              │
```

En la sesión solo se guarda el **id** de la inscripción, no los datos personales: un número en MySQL y el `_id` convertido a texto en MongoDB. `resultados.php` lee el registro con ese id (en MongoDB, reconstruyendo antes el `ObjectId`), de modo que cada navegador solo puede ver la inscripción que él mismo ha enviado.

## Comandos útiles

Todos se ejecutan desde la carpeta `mysql/`.

| Comando | Qué hace |
| --- | --- |
| `docker compose up -d --build` | Levanta todo y reconstruye la imagen si cambió el `Dockerfile`. |
| `docker compose up -d` | Aplica cambios del compose sin reconstruir. |
| `docker compose up -d --force-recreate` | Recrea los contenedores para que lean el `.env` actualizado. |
| `docker compose ps` | Muestra el estado de los contenedores. |
| `docker compose logs web` | Logs de Apache y PHP (aquí aparecen los errores registrados con `error_log`). |
| `docker compose logs db` | Logs de MySQL (solo rama `main`). |
| `docker compose config` | Muestra el compose con las variables `${}` ya sustituidas. Ojo: puede mostrar contraseñas. |
| `docker compose exec web printenv \| grep DB_` | Comprueba las variables de entorno del contenedor web (rama `main`). |
| `docker compose exec web php -m` | Lista los módulos de PHP; en `mongodb` debe aparecer `mongodb`. |
| `docker compose exec web composer install` | Instala las dependencias de PHP (rama `mongodb`). |
| `docker compose down` | Para y elimina los contenedores, conservando los datos. |
| `docker compose down -v` | Lo anterior y además **borra el volumen** (en `main` se pierden los datos). |

## Resolución de problemas

### Comunes

**Compose no encuentra las variables (`variable is not set`).**
Ejecuta los comandos desde la carpeta `mysql/`, donde está el `.env`, y comprueba que el archivo no se llama `.env.txt`.

**El puerto 8080 está ocupado.**
Cambia el puerto de la izquierda en `docker-compose.yml` (por ejemplo `"8081:80"`) y accede por ese puerto.

**Los cambios en el PHP no se ven.**
La carpeta `src/` está enlazada con el contenedor, así que no hace falta reconstruir. Recarga el navegador con `Ctrl + F5`.

**Aparece `vendor/` o el `.env` equivocado al cambiar de rama.**
Git no toca los archivos ignorados al cambiar de rama. Restaura el `.env` correspondiente a la rama en la que estés.

### Rama `main` (MySQL)

**La página muestra "Ha habido un error en la conexión".**
MySQL puede tardar unos segundos en aceptar conexiones tras un `up`. Espera y recarga. Si persiste, revisa `docker compose logs web` y `docker compose logs db`.

**Error `Access denied for user` en los logs.**
Las credenciales del `.env` no coinciden con las que MySQL guardó al crear el volumen. Borra el volumen con `docker compose down -v` y vuelve a levantar.

**La tabla `formulario` no existe.**
El script de `init/` solo se ejecuta con el volumen vacío. Ejecuta `docker compose down -v` y luego `docker compose up -d`. Comprueba también que el archivo termina en `.sql`.

### Rama `mongodb` (MongoDB Atlas)

**La página muestra "Ha habido un error en la conexión" o un error de base de datos.**
Revisa `docker compose logs web`, que contiene el mensaje real. Las causas habituales:

- **IP no permitida**: tu IP pública ha cambiado. Añádela en Atlas, en Network Access. Suele verse como un error de timeout o *No suitable servers found*.
- **Autenticación fallida**: usuario o contraseña incorrectos en `MONGODB_URI`.
- **`.env` modificado sin recrear el contenedor**: ejecuta `docker compose up -d --force-recreate`.
- **Clúster pausado**: Atlas puede pausar los clústeres gratuitos tras un periodo largo de inactividad. Se reanuda desde la web de Atlas.

**`Failed to parse URI options: Can't create SSL client, SSL not enabled in this build`.**
La extensión `mongodb` se compiló sin OpenSSL. El `Dockerfile` instala `libssl-dev` para evitarlo. Si aparece, reconstruye con `docker compose up -d --build`. Para comprobar el soporte: `docker compose exec web php --ri mongodb`.

**Composer falla al descargar paquetes (`unzip` o `git` no encontrados).**
La imagen necesita `unzip`, que el `Dockerfile` ya instala. Si cambias la imagen base, comprueba que sigue estando.

**Error `Class "MongoDB\Client" not found`.**
Falta la carpeta `vendor/`: ejecuta `docker compose exec web composer install`.

## Decisiones de seguridad

Comunes:

- **`POST` en lugar de `GET`**: el DNI es un dato personal y no debe aparecer en la URL ni en los logs.
- **Validación en servidor**: la validación del cliente se puede saltar con la URL, las herramientas de desarrollo o `curl`.
- **Escape con `htmlspecialchars` al mostrar** los datos, para evitar XSS.
- **Credenciales fuera del código**: `.env`, variables de entorno del contenedor y `getenv()` en PHP, con el `.env` fuera de Git.
- **Errores controlados**: el usuario recibe un mensaje genérico y el detalle técnico se escribe en el log.
- **Infraestructura fuera de la carpeta web**: Apache no puede servir el compose ni el `.env`.

Rama `main` (MySQL):

- **Sentencias preparadas con `EMULATE_PREPARES` desactivado**: consulta y datos viajan por separado, así que los datos nunca se interpretan como SQL.
- **Mínimo privilegio**: la aplicación usa un usuario con permisos solo sobre su base; la contraseña de `root` nunca llega al contenedor web.
- El contenedor `db` no publica ningún puerto.

Rama `mongodb` (MongoDB Atlas):

- **Lista de IPs permitidas** en Atlas: solo se aceptan conexiones desde las IPs autorizadas, aunque alguien tuviera las credenciales. Se evita `0.0.0.0/0`.
- **Conexión cifrada (TLS)** con Atlas.
- **La cadena de conexión** contiene la contraseña, por eso vive solo en el `.env`, nunca en el código ni en Git.
- MongoDB no interpreta texto como SQL, así que la inyección SQL clásica no aplica; aun así, la validación en servidor sigue siendo necesaria porque la base de datos no impone un esquema.

## Pendiente

- Hacer único el DNI: restricción `UNIQUE` en MySQL e índice único en MongoDB, y mostrar un mensaje al usuario cuando esté duplicado.
- En la rama `mongodb`, sustituir el usuario administrador de Atlas por uno con permiso `readWrite` limitado a esta base de datos.

## Autor

Juan Rodríguez Garrido. Proyecto de la asignatura de Desarrollo web en Entorno Cliente, 2º de Desarrollo de Aplicaciones Web (DAW).
