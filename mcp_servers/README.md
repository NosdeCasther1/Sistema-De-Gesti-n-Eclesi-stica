# Servidores MCP para Laravel (AD Rey de Reyes)

Este directorio contiene tres servidores MCP (Model Context Protocol) diseñados para optimizar el desarrollo local, depuración y auditoría de seguridad del proyecto monolítico.

## Estructura de Directorios

```text
mcp_servers/
├── db-artisan-agent/       # TypeScript/Node.js - MySQL & Laravel Artisan Tools
├── log-watcher-agent/      # Python - Log error tracing & stack extractor
└── sec-analyzer-agent/     # TypeScript/Node.js - Static analysis security engine
```

---

## 1. DB-Artisan Agent (TypeScript)
Interactúa de manera directa con la base de datos MySQL local y corre comandos Artisan.

### Dependencias y Compilación
```bash
cd mcp_servers/db-artisan-agent
npm install
npm run build
```

### Herramientas
* `execute_sql_query(query: string)`: Ejecuta una consulta SQL nativa en la base de datos.
* `run_artisan_command(command: string)`: Ejecuta comandos artisan (ej. `migrate:status`, `db:seed`).
* `generate_mock_data(table: string, count: number)`: Inspecciona la estructura de una tabla y hace inserción en masa de datos falsos contextualmente.

---

## 2. Log-Watcher Agent (Python)
Lee, filtra y extrae el stack trace de los errores de `laravel.log`.

### Dependencias
```bash
cd mcp_servers/log-watcher-agent
pip install -r requirements.txt
```

### Herramientas
* `read_latest_errors(lines: number)`: Retorna una lista estructurada de errores de tipo `ERROR`, `CRITICAL` o `EMERGENCY` de las últimas N líneas, asignándoles un ID hash de 12 caracteres.
* `extract_stack_trace(error_id: string)`: Devuelve el stack trace completo de un error específico mediante su ID.

---

## 3. Sec-Analyzer Agent (TypeScript)
Analizador estático de seguridad para vistas Blade y código PHP.

### Dependencias y Compilación
```bash
cd mcp_servers/sec-analyzer-agent
npm install
npm run build
```

### Herramientas
* `scan_vulnerabilities(directory: string)`: Analiza de manera recursiva buscando inyección SQL en consultas Raw, salidas vulnerables a XSS en Blade (`{!! !!}`), uso de superglobales nativos, y funciones de ejecución de comandos.
* `check_rbac_middleware(controller: string)`: Verifica si un controlador específico declara `RoleMiddleware` u otro middleware de roles en su constructor `__construct()`.

---

## Configuración para Clientes MCP (Claude Desktop o Cursor)

Agrega el siguiente bloque a tu archivo de configuración (`%APPDATA%/Claude/claude_desktop_config.json` para Claude Desktop en Windows o en el panel de configuración de servidores MCP en Cursor). Asegúrate de rellenar tus credenciales de base de datos correctas.

```json
{
  "mcpServers": {
    "db-artisan-agent": {
      "command": "node",
      "args": [
        "c:/xampp/htdocs/ProyectoIglesia/mcp_servers/db-artisan-agent/build/index.js"
      ],
      "env": {
        "DB_HOST": "127.0.0.1",
        "DB_PORT": "3306",
        "DB_DATABASE": "tu_db_name",
        "DB_USERNAME": "root",
        "DB_PASSWORD": "",
        "PROJECT_PATH": "c:/xampp/htdocs/ProyectoIglesia"
      }
    },
    "log-watcher-agent": {
      "command": "python",
      "args": [
        "c:/xampp/htdocs/ProyectoIglesia/mcp_servers/log-watcher-agent/server.py"
      ],
      "env": {
        "LARAVEL_LOG_PATH": "c:/xampp/htdocs/ProyectoIglesia/storage/logs/laravel.log"
      }
    },
    "sec-analyzer-agent": {
      "command": "node",
      "args": [
        "c:/xampp/htdocs/ProyectoIglesia/mcp_servers/sec-analyzer-agent/build/index.js"
      ],
      "env": {
        "PROJECT_ROOT_PATH": "c:/xampp/htdocs/ProyectoIglesia"
      }
    }
  }
}
```
