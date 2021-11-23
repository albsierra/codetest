# Traducciones

[![Crowdin](https://badges.crowdin.net/code-test/localized.svg)](https://crowdin.com)

La aplicación se encuentra traducida a los siguientes idiomas:

- Inglés (idioma principal)
- Español

Las traducciones se encuentran en el archivo `/locale/**/codetest.php`, pero el proyecto está preparado para recoger cualquier archivo `.php` en el directorio `/locale`

## Crowdin

---

Para gestionar las traducciones usamos el gestor de traducciones colaborativas [Crowdin](https://crowdin.com/)

Para la sincronización de las traducciones usamos la interfaz de comandos de Crowdin, [Crowdin CLI](https://support.crowdin.com/cli-tool/), disponible para todos los sistemas operativos.

### Configuración de Crowdin

La configuración principal de Crowdin se encuentra en el archivo `/crowdin.yml`

Para interactuar con el proyecto a través del CLI, es necesario configurar un token personal en el archivo `.env` (existe un archivo `.env.example`)

### Generar un token personal:

- Acceder a los [ajustes de cuenta de Crowdin](https://crowdin.com/settings#api-key) (pestaña "API")

- Generar un "Personal Access Token" (y copiarlo)

- Introducir el token copiado en el archivo `.env` como valor de la propiedad `CROWDIN_PERSONAL_TOKEN`

### Comandos Crowdin

> Estos comandos se deben ejecutar en la carpeta base del proyecto

 - Subir las traducciones base

        crowdin upload sources

 - Descargar el estado de las traducciones al repositorio

        crowdin download

 - Subir las traducciones locales a Crowdin

        crowdin upload translations
