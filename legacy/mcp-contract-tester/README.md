# MCP API Contract Tester

Este es un servidor local de Model Context Protocol (MCP) diseñado para realizar pruebas de contrato sobre una API (backend PHP).

## Herramientas Incluidas

### 1. `snapshot_endpoint`
- **Uso**: Genera un esquema Zod basado en la respuesta actual de un endpoint.
- **Parámetros**: `endpoint` (ej. `/api/usuarios`).
- **Salida**: Código TypeScript con el esquema Zod generado.

### 2. `authenticate_session`
- **Uso**: Realiza login en el backend PHP y captura las cookies de sesión (`PHPSESSID`) y el token CSRF.
- **Parámetros**:
  - `login_endpoint`: Ruta del login (ej. `/Vistas/html/Login.php`).
  - `credentials`: Objeto con `email` y `password`.
- **Salida**: Un objeto `extracted_headers` listo para ser usado en otras herramientas.

### 3. `test_api_contract`
- **Uso**: Valida que un endpoint responda correctamente y contenga las llaves especificadas.
- **Parámetros**:
  - `method`: GET, POST, etc.
  - `endpoint`: Ruta del endpoint.
  - `payload`: (Opcional) Datos para la petición.
  - `expected_schema_keys`: Array de strings con las llaves que deben existir en la raíz del JSON.
- **Salida**: Resultado JSON con `passed`, `responseTimeMs` y lista de `errors`.

## Configuración y Ejecución

### Requisitos
- Node.js instalado.
- Servidor PHP (backend) corriendo.

### Instalación
```bash
cd mcp-contract-tester
npm install
npm run build
```

### Ejecución Directa
Debes definir la variable de entorno `API_BASE_URL`.
```bash
# Windows (PowerShell)
$env:API_BASE_URL="http://localhost/ProyectoIglesia"; npm start

# Linux/macOS
API_BASE_URL=http://localhost/ProyectoIglesia npm start
```

### Configuración en Claude Desktop
Añade esto a tu `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "api-contract-tester": {
      "command": "node",
      "args": ["C:\\xampp\\htdocs\\ProyectoIglesia\\mcp-contract-tester\\dist\\index.js"],
      "env": {
        "API_BASE_URL": "http://localhost/ProyectoIglesia"
      }
    }
  }
}
```

## Tecnologías
- TypeScript
- @modelcontextprotocol/sdk
- Axios
- Zod
