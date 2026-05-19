import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import axios, { AxiosError } from "axios";
import { z } from "zod";

const API_BASE_URL = process.env.API_BASE_URL || "http://localhost";

/**
 * Generates a string representation of a Zod schema from a JSON object.
 */
function generateZodSchema(data: any, indent: string = ""): string {
  if (data === null) return "z.null()";
  if (Array.isArray(data)) {
    if (data.length === 0) return "z.array(z.any())";
    return `z.array(${generateZodSchema(data[0], indent)})`;
  }
  if (typeof data === "object") {
    const keys = Object.keys(data);
    if (keys.length === 0) return "z.object({})";
    
    let schema = "z.object({\n";
    for (const key of keys) {
      schema += `${indent}  ${key}: ${generateZodSchema(data[key], indent + "  ")},\n`;
    }
    schema += `${indent}})`;
    return schema;
  }
  if (typeof data === "string") return "z.string()";
  if (typeof data === "number") return "z.number()";
  if (typeof data === "boolean") return "z.boolean()";
  return "z.any()";
}

const server = new Server(
  {
    name: "api-contract-tester",
    version: "1.0.0",
  },
  {
    capabilities: {
      tools: {},
    },
  }
);

/**
 * Tool definitions
 */
server.setRequestHandler(ListToolsRequestSchema, async () => {
  return {
    tools: [
      {
        name: "snapshot_endpoint",
        description: "Realiza un snapshot de un endpoint y genera su esquema Zod de referencia.",
        inputSchema: {
          type: "object",
          properties: {
            endpoint: {
              type: "string",
              description: "El path del endpoint (ej. /usuarios/lista)",
            },
          },
          required: ["endpoint"],
        },
      },
      {
        name: "test_api_contract",
        description: "Valida el contrato de un endpoint verificando status y llaves requeridas.",
        inputSchema: {
          type: "object",
          properties: {
            method: {
              type: "string",
              enum: ["GET", "POST", "PUT", "DELETE", "PATCH"],
              description: "Método HTTP",
            },
            endpoint: {
              type: "string",
              description: "El path del endpoint",
            },
            payload: {
              type: "object",
              description: "Cuerpo de la petición (opcional)",
            },
            headers: {
              type: "object",
              description: "Cabeceras personalizadas (ej. Cookie: PHPSESSID=..., X-CSRF-TOKEN: ...)",
            },
            expected_schema_keys: {
              type: "array",
              items: { type: "string" },
              description: "Lista de llaves que DEBEN estar en la raíz del JSON de respuesta",
            },
          },
          required: ["method", "endpoint", "expected_schema_keys"],
        },
      },
      {
        name: "authenticate_session",
        description: "Realiza login en el backend PHP, captura el PHPSESSID y el token CSRF para uso en pruebas posteriores.",
        inputSchema: {
          type: "object",
          properties: {
            login_endpoint: {
              type: "string",
              description: "Ruta del endpoint de login (ej. /login o /_/auth/login)",
            },
            credentials: {
              type: "object",
              description: "Credenciales de acceso (ej. { email: 'admin@iglesia.com', password: '...' })",
            }
          },
          required: ["login_endpoint", "credentials"],
        },
      },
    ],
  };
});

/**
 * Tool handlers
 */
server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;

  try {
    if (name === "authenticate_session") {
      const { login_endpoint, credentials } = args as any;
      const url = `${API_BASE_URL}${login_endpoint.startsWith("/") ? "" : "/"}${login_endpoint}`;

      try {
        // Convertimos las credenciales a URLSearchParams para que PHP las reciba en $_POST
        const params = new URLSearchParams();
        for (const key in credentials) {
          params.append(key, credentials[key]);
        }

        const response = await axios.post(url, params, {
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          maxRedirects: 0, 
          validateStatus: (status) => status >= 200 && status < 400 
        });

        const cookies = response.headers['set-cookie'] || [];
        let sessionId = '';
        cookies.forEach(cookie => {
          if (cookie.includes('PHPSESSID=')) {
            sessionId = cookie.split(';')[0];
          }
        });

        // El token CSRF podría venir en las cabeceras o en el cuerpo. 
        // Intentamos extraerlo del JSON de respuesta si existe.
        const csrfToken = response.data?.csrf_token || 'TOKEN_NO_ENCONTRADO_EN_JSON';

        return {
          content: [
            {
              type: "text",
              text: JSON.stringify({
                status: "success",
                message: "Sesión capturada correctamente.",
                extracted_headers: {
                  "Cookie": sessionId,
                  "X-CSRF-TOKEN": csrfToken
                },
                instruction: "Pasa el objeto 'extracted_headers' en el campo 'headers' de test_api_contract."
              }, null, 2)
            }
          ]
        };
      } catch (error: any) {
        return {
          content: [
            {
              type: "text",
              text: `Error de autenticación: ${error.message}${error.response ? `\nStatus: ${error.response.status}\nData: ${JSON.stringify(error.response.data)}` : ""}`
            }
          ],
          isError: true
        };
      }
    }

    if (name === "snapshot_endpoint") {
      const endpoint = String(args?.endpoint);
      const url = `${API_BASE_URL}${endpoint.startsWith("/") ? "" : "/"}${endpoint}`;

      try {
        const response = await axios.get(url);
        const schemaString = generateZodSchema(response.data);
        
        return {
          content: [
            {
              type: "text",
              text: `Esquema Zod generado para ${endpoint}:\n\nimport { z } from 'zod';\n\nconst ResponseSchema = ${schemaString};\n\n// Muestra de datos recibida:\n${JSON.stringify(response.data, null, 2)}`,
            },
          ],
        };
      } catch (error) {
        const axiosError = error as AxiosError;
        return {
          content: [
            {
              type: "text",
              text: `Error al realizar snapshot: ${axiosError.message}${axiosError.response ? `\nStatus: ${axiosError.response.status}\nData: ${JSON.stringify(axiosError.response.data)}` : ""}`,
            },
          ],
          isError: true,
        };
      }
    }

    if (name === "test_api_contract") {
      const { method, endpoint, payload, headers, expected_schema_keys } = args as any;
      const url = `${API_BASE_URL}${endpoint.startsWith("/") ? "" : "/"}${endpoint}`;
      const startTime = Date.now();

      try {
        const response = await axios({
          method,
          url,
          data: payload,
          headers: headers,
          validateStatus: () => true,
        });

        const responseTime = Date.now() - startTime;
        const errors: string[] = [];

        // Validar Status
        if (![200, 201].includes(response.status)) {
          errors.push(`Status HTTP esperado 200/201, se recibió ${response.status}`);
        }

        // Validar llaves
        const responseData = response.data;
        if (typeof responseData !== "object" || responseData === null) {
          errors.push("La respuesta no es un objeto JSON válido");
        } else {
          for (const key of expected_schema_keys) {
            if (!(key in responseData)) {
              errors.push(`Falta la llave requerida: "${key}"`);
            }
          }
        }

        return {
          content: [
            {
              type: "text",
              text: JSON.stringify(
                {
                  passed: errors.length === 0,
                  responseTimeMs: responseTime,
                  status: response.status,
                  errors: errors,
                },
                null,
                2
              ),
            },
          ],
        };
      } catch (error) {
        const axiosError = error as AxiosError;
        return {
          content: [
            {
              type: "text",
              text: JSON.stringify({
                passed: false,
                error: `Error de red o servidor: ${axiosError.message}`,
              }),
            },
          ],
          isError: true,
        };
      }
    }

    throw new Error(`Tool not found: ${name}`);
  } catch (error: any) {
    return {
      content: [
        {
          type: "text",
          text: `Error interno del servidor MCP: ${error.message}`,
        },
      ],
      isError: true,
    };
  }
});

/**
 * Start the server
 */
async function main() {
  const transport = new StdioServerTransport();
  await server.connect(transport);
  console.error("MCP API Contract Tester Server running on stdio");
}

main().catch((error) => {
  console.error("Fatal error in main():", error);
  process.exit(1);
});
