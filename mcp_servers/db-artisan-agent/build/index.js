import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { CallToolRequestSchema, ListToolsRequestSchema, ErrorCode, McpError } from "@modelcontextprotocol/sdk/types.js";
import mysql from "mysql2/promise";
import { exec } from "child_process";
import { promisify } from "util";
import path from "path";
import dotenv from "dotenv";
// Load environment variables
dotenv.config();
const execAsync = promisify(exec);
// Retrieve and validate environment variables
const DB_HOST = process.env.DB_HOST || "127.0.0.1";
const DB_PORT = parseInt(process.env.DB_PORT || "3306", 10);
const DB_DATABASE = process.env.DB_DATABASE;
const DB_USERNAME = process.env.DB_USERNAME;
const DB_PASSWORD = process.env.DB_PASSWORD || "";
const PROJECT_PATH = process.env.PROJECT_PATH || process.cwd();
if (!DB_DATABASE || !DB_USERNAME) {
    console.error("Warning: DB_DATABASE and DB_USERNAME are required environment variables.");
}
// Establish a lazy connection pool for MySQL
let pool = null;
function getPool() {
    if (!pool) {
        pool = mysql.createPool({
            host: DB_HOST,
            port: DB_PORT,
            database: DB_DATABASE,
            user: DB_USERNAME,
            password: DB_PASSWORD,
            waitForConnections: true,
            connectionLimit: 10,
            queueLimit: 0
        });
    }
    return pool;
}
// Initialize the MCP Server
const server = new Server({
    name: "db-artisan-agent",
    version: "1.0.0",
}, {
    capabilities: {
        tools: {},
    },
});
/**
 * Declares the available tools for the DB-Artisan Agent.
 */
server.setRequestHandler(ListToolsRequestSchema, async () => {
    return {
        tools: [
            {
                name: "execute_sql_query",
                description: "Executes a raw SQL SELECT/INSERT/UPDATE/DELETE query on the configured MySQL database.",
                inputSchema: {
                    type: "object",
                    properties: {
                        query: {
                            type: "string",
                            description: "The complete SQL query to execute."
                        }
                    },
                    required: ["query"]
                }
            },
            {
                name: "run_artisan_command",
                description: "Runs a Laravel Artisan command inside the configured project path (e.g., migrate, db:seed, route:list).",
                inputSchema: {
                    type: "object",
                    properties: {
                        command: {
                            type: "string",
                            description: "The Artisan command arguments (e.g., 'migrate:status', 'db:seed --class=UserSeeder'). Do not prepend 'php artisan'."
                        }
                    },
                    required: ["command"]
                }
            },
            {
                name: "generate_mock_data",
                description: "Automatically inspects a table structure and inserts custom mock rows into the database.",
                inputSchema: {
                    type: "object",
                    properties: {
                        table: {
                            type: "string",
                            description: "The name of the database table to populate."
                        },
                        count: {
                            type: "number",
                            description: "Number of mock rows to insert.",
                            default: 10
                        }
                    },
                    required: ["table"]
                }
            }
        ]
    };
});
/**
 * Handles calling the tools.
 */
server.setRequestHandler(CallToolRequestSchema, async (request) => {
    const { name, arguments: args } = request.params;
    try {
        switch (name) {
            case "execute_sql_query": {
                const { query } = args;
                if (!query) {
                    throw new McpError(ErrorCode.InvalidParams, "SQL query is required.");
                }
                const dbPool = getPool();
                const [rows, fields] = await dbPool.execute(query);
                return {
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify({
                                status: "success",
                                affectedRows: rows.affectedRows ?? null,
                                insertId: rows.insertId ?? null,
                                results: Array.isArray(rows) ? rows : [rows]
                            }, null, 2)
                        }
                    ]
                };
            }
            case "run_artisan_command": {
                const { command } = args;
                if (!command) {
                    throw new McpError(ErrorCode.InvalidParams, "Artisan command parameters are required.");
                }
                // Sanitize command inputs minimally (to avoid chain injection but allow standard flags)
                if (command.includes(";") || command.includes("&&") || command.includes("||") || command.includes("|")) {
                    throw new McpError(ErrorCode.InvalidParams, "Artisan command contains invalid character tokens (shell chaining is disabled).");
                }
                const fullCommand = `php artisan ${command}`;
                try {
                    const { stdout, stderr } = await execAsync(fullCommand, {
                        cwd: path.resolve(PROJECT_PATH)
                    });
                    return {
                        content: [
                            {
                                type: "text",
                                text: JSON.stringify({
                                    status: "success",
                                    command: fullCommand,
                                    stdout: stdout.trim(),
                                    stderr: stderr.trim()
                                }, null, 2)
                            }
                        ]
                    };
                }
                catch (error) {
                    return {
                        isError: true,
                        content: [
                            {
                                type: "text",
                                text: JSON.stringify({
                                    status: "error",
                                    command: fullCommand,
                                    message: error.message,
                                    stdout: error.stdout?.trim(),
                                    stderr: error.stderr?.trim()
                                }, null, 2)
                            }
                        ]
                    };
                }
            }
            case "generate_mock_data": {
                const { table, count = 10 } = args;
                if (!table) {
                    throw new McpError(ErrorCode.InvalidParams, "Table name is required.");
                }
                const dbPool = getPool();
                // 1. Fetch table structure
                let columns;
                try {
                    const [descRows] = await dbPool.execute(`DESCRIBE \`${table}\``);
                    columns = descRows;
                }
                catch (err) {
                    throw new Error(`Failed to describe table '${table}': ${err.message}`);
                }
                if (!columns || columns.length === 0) {
                    throw new Error(`Table '${table}' has no columns or does not exist.`);
                }
                const insertedRecords = [];
                // 2. Generate and insert rows in a transaction
                const connection = await dbPool.getConnection();
                await connection.beginTransaction();
                try {
                    for (let i = 0; i < count; i++) {
                        const rowData = {};
                        const columnNames = [];
                        const placeholders = [];
                        const values = [];
                        for (const col of columns) {
                            const fieldName = col.Field;
                            const fieldType = col.Type.toLowerCase();
                            const isNullable = col.Null === "YES";
                            const isAutoIncrement = col.Extra.includes("auto_increment");
                            const defaultValue = col.Default;
                            // Skip auto-increment primary keys
                            if (isAutoIncrement)
                                continue;
                            let val = null;
                            // Simple logic to generate mock data based on type and field name
                            if (fieldType.includes("varchar") || fieldType.includes("text") || fieldType.includes("char")) {
                                if (fieldName.toLowerCase().includes("email")) {
                                    val = `mock.user.${Date.now()}.${i}@example.com`;
                                }
                                else if (fieldName.toLowerCase().includes("phone") || fieldName.toLowerCase().includes("celular")) {
                                    val = `555-019${i}`;
                                }
                                else if (fieldName.toLowerCase().includes("name") || fieldName.toLowerCase().includes("nombre")) {
                                    val = `Mock Name ${i + 1}`;
                                }
                                else if (fieldName.toLowerCase().includes("password")) {
                                    // Standard Laravel bcrypt pre-hashed value for 'password'
                                    val = "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"; // bcrypt hash for 'password'
                                }
                                else if (fieldName.toLowerCase().includes("role") || fieldName.toLowerCase().includes("rol")) {
                                    val = i % 2 === 0 ? "admin" : "usuario";
                                }
                                else {
                                    val = `Mock ${fieldName} ${i + 1}`;
                                }
                                // Trim value if necessary based on column limit
                                const sizeMatch = fieldType.match(/\((\d+)\)/);
                                if (sizeMatch && sizeMatch[1]) {
                                    const maxLen = parseInt(sizeMatch[1], 10);
                                    if (val.length > maxLen) {
                                        val = val.substring(0, maxLen);
                                    }
                                }
                            }
                            else if (fieldType.includes("int") || fieldType.includes("decimal") || fieldType.includes("float") || fieldType.includes("double")) {
                                if (fieldType.includes("tinyint(1)")) {
                                    // Treat as boolean
                                    val = Math.random() > 0.5 ? 1 : 0;
                                }
                                else if (fieldName.toLowerCase().includes("status") || fieldName.toLowerCase().includes("estado")) {
                                    val = 1;
                                }
                                else {
                                    val = Math.floor(Math.random() * 100) + 1;
                                }
                            }
                            else if (fieldType.includes("date") || fieldType.includes("timestamp") || fieldType.includes("datetime")) {
                                const now = new Date();
                                // Format as YYYY-MM-DD HH:mm:ss for MySQL compat
                                val = now.toISOString().slice(0, 19).replace('T', ' ');
                            }
                            else if (fieldType.includes("boolean")) {
                                val = Math.random() > 0.5 ? 1 : 0;
                            }
                            else {
                                // Default fallback
                                val = isNullable ? null : `Value_${i}`;
                            }
                            columnNames.push(`\`${fieldName}\``);
                            placeholders.push("?");
                            values.push(val);
                            rowData[fieldName] = val;
                        }
                        const insertQuery = `INSERT INTO \`${table}\` (${columnNames.join(", ")}) VALUES (${placeholders.join(", ")})`;
                        const [result] = await connection.execute(insertQuery, values);
                        insertedRecords.push({
                            insertId: result.insertId,
                            data: rowData
                        });
                    }
                    await connection.commit();
                    return {
                        content: [
                            {
                                type: "text",
                                text: JSON.stringify({
                                    status: "success",
                                    table,
                                    rowsInserted: count,
                                    records: insertedRecords
                                }, null, 2)
                            }
                        ]
                    };
                }
                catch (dbErr) {
                    await connection.rollback();
                    throw dbErr;
                }
                finally {
                    connection.release();
                }
            }
            default:
                throw new McpError(ErrorCode.MethodNotFound, `Tool not found: ${name}`);
        }
    }
    catch (error) {
        return {
            isError: true,
            content: [
                {
                    type: "text",
                    text: JSON.stringify({
                        status: "error",
                        error: error.message || String(error)
                    }, null, 2)
                }
            ]
        };
    }
});
/**
 * Server startup. Connects to standard input/output transport.
 */
async function startServer() {
    const transport = new StdioServerTransport();
    await server.connect(transport);
    console.error("DB-Artisan MCP server running on stdio");
}
startServer().catch((error) => {
    console.error("Fatal error starting server:", error);
    process.exit(1);
});
