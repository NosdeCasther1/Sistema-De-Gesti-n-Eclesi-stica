import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { ListToolsRequestSchema, CallToolRequestSchema } from "@modelcontextprotocol/sdk/types.js";
import mysql from "mysql2/promise";
import dotenv from "dotenv";

dotenv.config();

// Conexión a la base de datos local
const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'iglesia_db',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

const server = new Server(
    {
        name: "mysql-local-mcp",
        version: "1.0.0",
    },
    {
        capabilities: {
            tools: {},
        },
    }
);

// 1. Definir qué herramientas tiene este MCP
server.setRequestHandler(ListToolsRequestSchema, async () => {
    return {
        tools: [
            {
                name: "execute_query",
                description: "Ejecuta una consulta SQL en la base de datos MySQL local (solo lectura recomendada).",
                inputSchema: {
                    type: "object",
                    properties: {
                        query: {
                            type: "string",
                            description: "La consulta SQL a ejecutar",
                        },
                    },
                    required: ["query"],
                },
            },
            {
                name: "get_schema",
                description: "Obtiene la estructura de todas las tablas de la base de datos.",
                inputSchema: {
                    type: "object",
                    properties: {},
                },
            }
        ],
    };
});

// 2. Ejecutar las herramientas cuando la IA lo pida
server.setRequestHandler(CallToolRequestSchema, async (request) => {
    if (request.params.name === "execute_query") {
        const query = request.params.arguments.query;
        try {
            const [rows] = await pool.query(query);
            return {
                content: [{ type: "text", text: JSON.stringify(rows, null, 2) }],
            };
        } catch (error) {
            return {
                content: [{ type: "text", text: `Error: ${error.message}` }],
                isError: true,
            };
        }
    }

    if (request.params.name === "get_schema") {
        try {
            const [tables] = await pool.query("SHOW TABLES");

            let schema = "";
            for (const tableObj of tables) {
                const tableName = Object.values(tableObj)[0];
                const [columns] = await pool.query(`DESCRIBE ${tableName}`);
                schema += `Table: ${tableName}\n`;
                columns.forEach(col => {
                    schema += `  - ${col.Field} (${col.Type})\n`;
                });
                schema += "\n";
            }

            return {
                content: [{ type: "text", text: schema }],
            };
        } catch (error) {
            return {
                content: [{ type: "text", text: `Error al obtener esquema: ${error.message}` }],
                isError: true,
            };
        }
    }

    throw new Error("Tool no encontrada");
});



// 3. Iniciar el servidor
async function main() {
    const transport = new StdioServerTransport();
    await server.connect(transport);
    console.error("Servidor MCP de MySQL iniciado y conectado a iglesia_db.");
}

main().catch(console.error);
