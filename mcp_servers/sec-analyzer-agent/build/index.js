import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { CallToolRequestSchema, ListToolsRequestSchema, ErrorCode, McpError } from "@modelcontextprotocol/sdk/types.js";
import fs from "fs";
import path from "path";
import dotenv from "dotenv";
// Load environment variables
dotenv.config();
const PROJECT_ROOT_PATH = process.env.PROJECT_ROOT_PATH || process.cwd();
// Directories to skip during analysis to optimize scan times
const IGNORED_DIRS = ["node_modules", "vendor", ".git", "storage", "bootstrap/cache", "public"];
/**
 * Recursively scans directories to collect all .php and .blade.php file paths.
 */
function walkDirectory(dir) {
    let results = [];
    let list;
    try {
        list = fs.readdirSync(dir);
    }
    catch (err) {
        return [];
    }
    for (const file of list) {
        const filePath = path.join(dir, file);
        let stat;
        try {
            stat = fs.statSync(filePath);
        }
        catch {
            continue;
        }
        if (stat.isDirectory()) {
            if (IGNORED_DIRS.includes(file))
                continue;
            results = results.concat(walkDirectory(filePath));
        }
        else {
            const ext = path.extname(file).toLowerCase();
            if (ext === ".php") {
                results.push(filePath);
            }
        }
    }
    return results;
}
/**
 * Scans a file for common Laravel security issues and returns found vulnerabilities.
 */
function analyzeFile(filePath, relativePath) {
    const content = fs.readFileSync(filePath, "utf-8");
    const lines = content.split("\n");
    const vulnerabilities = [];
    // Vulnerability definitions with detection patterns
    const rules = [
        {
            name: "Unsanitized raw SQL input",
            pattern: /->(whereRaw|selectRaw|orderByRaw|havingRaw|groupByRaw)\(\s*['"][^'"]*\$[a-zA-Z_0-9]+[^'"]*['"]/i,
            description: "Direct string interpolation detected in raw SQL clause. Use parameter binding (e.g., whereRaw('id = ?', [$id])) to prevent SQL injection.",
            severity: "HIGH"
        },
        {
            name: "Raw SQL concatenation",
            pattern: /->(whereRaw|selectRaw|orderByRaw|havingRaw|groupByRaw)\(\s*['"][^'"]*['"]\s*\.\s*\$[a-zA-Z_0-9]+/i,
            description: "String concatenation detected in raw SQL statement. This bypasses Laravel query parameter bindings and can lead to SQL injection.",
            severity: "HIGH"
        },
        {
            name: "Unescaped Blade output",
            // Matches {!! $var !!} but ignores calls that use escaping/sanitization helpers e() or clean()
            pattern: /\{\!\!\s*(?!\s*e\(|\s*clean\(|\s*htmlspecialchars\(|\s*sanitize\()\$[a-zA-Z_0-9_\[\]'"\-&>]+(?!\s*->\b(?:escape|sanitize|purify)\b)\s*\!\!\}/i,
            description: "Blade unescaped bracket output {!! !!} detected on raw variables. This is susceptible to Cross-Site Scripting (XSS). Wrap variables in standard {{ }} brackets or sanitize them.",
            severity: "HIGH"
        },
        {
            name: "Direct superglobal usage",
            pattern: /\B\$(?:_GET|_POST|_REQUEST)\b/g,
            description: "Direct reference to raw PHP superglobals. Standardize on Laravel Request injection (e.g., $request->input()) for unified sanitization and validation.",
            severity: "MEDIUM"
        },
        {
            name: "Weak cryptographic hash function",
            pattern: /\b(md5|sha1)\(/i,
            description: "Usage of MD5 or SHA1 hash functions. Use Laravel's secure Hash facade (Hash::make()) or bcrypt() for sensitive data or passwords.",
            severity: "MEDIUM"
        },
        {
            name: "Dangerous system execution functions",
            pattern: /\b(shell_exec|exec|system|passthru|popen)\(/i,
            description: "Calling native system execution shell tools. Can lead to Command Injection. Avoid dynamic execution or validate inputs exhaustively.",
            severity: "HIGH"
        }
    ];
    for (let i = 0; i < lines.length; i++) {
        const lineContent = lines[i];
        for (const rule of rules) {
            if (rule.pattern.test(lineContent)) {
                vulnerabilities.push({
                    file: relativePath,
                    line: i + 1,
                    pattern: rule.name,
                    description: rule.description,
                    severity: rule.severity,
                    snippet: lineContent.trim()
                });
            }
        }
    }
    return vulnerabilities;
}
// Initialize the MCP Server
const server = new Server({
    name: "sec-analyzer-agent",
    version: "1.0.0",
}, {
    capabilities: {
        tools: {},
    },
});
/**
 * Declares the tools for code auditing.
 */
server.setRequestHandler(ListToolsRequestSchema, async () => {
    return {
        tools: [
            {
                name: "scan_vulnerabilities",
                description: "Scans PHP files and Blade templates in a directory for security flaws, SQLi, and XSS risks.",
                inputSchema: {
                    type: "object",
                    properties: {
                        directory: {
                            type: "string",
                            description: "Directory path to scan (relative to project root, or absolute). Defaults to project root if empty."
                        }
                    }
                }
            },
            {
                name: "check_rbac_middleware",
                description: "Verifies if a specific Controller applies role/permissions middleware in its constructor.",
                inputSchema: {
                    type: "object",
                    properties: {
                        controller: {
                            type: "string",
                            description: "Controller name or path (e.g. 'TesoreriaController', 'App\\Http\\Controllers\\UserController.php')."
                        }
                    },
                    required: ["controller"]
                }
            }
        ]
    };
});
/**
 * Handles tool execution requests.
 */
server.setRequestHandler(CallToolRequestSchema, async (request) => {
    const { name, arguments: args } = request.params;
    try {
        switch (name) {
            case "scan_vulnerabilities": {
                const { directory = "" } = args;
                const scanRoot = directory
                    ? path.isAbsolute(directory)
                        ? directory
                        : path.resolve(PROJECT_ROOT_PATH, directory)
                    : path.resolve(PROJECT_ROOT_PATH);
                if (!fs.existsSync(scanRoot)) {
                    throw new McpError(ErrorCode.InvalidParams, `Target directory does not exist: ${scanRoot}`);
                }
                const files = walkDirectory(scanRoot);
                const allVulnerabilities = [];
                for (const file of files) {
                    const relativePath = path.relative(PROJECT_ROOT_PATH, file);
                    const fileVulns = analyzeFile(file, relativePath);
                    allVulnerabilities.push(...fileVulns);
                }
                return {
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify({
                                status: "success",
                                scanDirectory: scanRoot,
                                filesScanned: files.length,
                                vulnerabilitiesCount: allVulnerabilities.length,
                                vulnerabilities: allVulnerabilities
                            }, null, 2)
                        }
                    ]
                };
            }
            case "check_rbac_middleware": {
                const { controller } = args;
                if (!controller) {
                    throw new McpError(ErrorCode.InvalidParams, "Controller identifier is required.");
                }
                // Try to locate the controller
                let controllerPath = "";
                // 1. If it's a relative path starting with app/
                if (controller.startsWith("app/")) {
                    controllerPath = path.resolve(PROJECT_ROOT_PATH, controller);
                }
                else if (fs.existsSync(controller)) {
                    // 2. Direct absolute or local path
                    controllerPath = controller;
                }
                else {
                    // 3. Just a class name, check standard controller path
                    const cleanName = controller.replace(/Controller\.php$/, "").replace(/Controller$/, "");
                    const standardPath = path.resolve(PROJECT_ROOT_PATH, "app", "Http", "Controllers", `${cleanName}Controller.php`);
                    if (fs.existsSync(standardPath)) {
                        controllerPath = standardPath;
                    }
                    else {
                        // Let's search files in app/Http/Controllers recursively
                        const allFiles = walkDirectory(path.resolve(PROJECT_ROOT_PATH, "app", "Http", "Controllers"));
                        const match = allFiles.find(f => f.toLowerCase().includes(controller.toLowerCase()));
                        if (match) {
                            controllerPath = match;
                        }
                    }
                }
                if (!controllerPath || !fs.existsSync(controllerPath)) {
                    throw new McpError(ErrorCode.InvalidParams, `Controller class could not be resolved or found. Tried matching: '${controller}'`);
                }
                const content = fs.readFileSync(controllerPath, "utf-8");
                const lines = content.split("\n");
                // Analyze constructor
                let hasConstructor = false;
                let constructorStart = -1;
                let constructorEnd = -1;
                let bracketCount = 0;
                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i];
                    if (/public\s+function\s+__construct\b/i.test(line)) {
                        hasConstructor = true;
                        constructorStart = i;
                        // Trace the block brackets
                        const openIdx = line.indexOf("{");
                        if (openIdx !== -1) {
                            bracketCount = 1;
                        }
                        continue;
                    }
                    if (hasConstructor && constructorStart !== -1 && constructorEnd === -1) {
                        if (line.includes("{"))
                            bracketCount += (line.match(/{/g) || []).length;
                        if (line.includes("}"))
                            bracketCount -= (line.match(/}/g) || []).length;
                        if (bracketCount === 0 && constructorStart !== i) {
                            constructorEnd = i;
                        }
                    }
                }
                if (!hasConstructor) {
                    return {
                        content: [
                            {
                                type: "text",
                                text: JSON.stringify({
                                    status: "warning",
                                    file: path.relative(PROJECT_ROOT_PATH, controllerPath),
                                    secured: false,
                                    reason: "No constructor definition found inside the controller class. Middleware is likely defined at route level or completely omitted."
                                }, null, 2)
                            }
                        ]
                    };
                }
                // Grab constructor body lines
                const endLine = constructorEnd === -1 ? lines.length : constructorEnd + 1;
                const constructorBody = lines.slice(constructorStart, endLine).join("\n");
                // Patterns to check middleware registration
                const middlewareMatch = constructorBody.match(/\$this->middleware\(([^)]+)\)/gi);
                const registeredMiddleware = middlewareMatch
                    ? middlewareMatch.map(call => call.replace(/^\$this->middleware\(/, "").replace(/\)$/, "").trim())
                    : [];
                // Check if role or permission middleware is referenced
                const hasRoleMiddleware = registeredMiddleware.some(mw => mw.toLowerCase().includes("role") ||
                    mw.toLowerCase().includes("permission") ||
                    mw.toLowerCase().includes("rbac"));
                return {
                    content: [
                        {
                            type: "text",
                            text: JSON.stringify({
                                status: "success",
                                file: path.relative(PROJECT_ROOT_PATH, controllerPath),
                                secured: hasRoleMiddleware,
                                constructorDefined: true,
                                middlewareRegistered: registeredMiddleware,
                                roleProtectionDetected: hasRoleMiddleware,
                                notes: hasRoleMiddleware
                                    ? "Constructor contains middleware targeting roles or permissions."
                                    : "Constructor sets up general or no middleware, but RoleMiddleware references were not matched."
                            }, null, 2)
                        }
                    ]
                };
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
 * Main startup. Connects to standard input/output transport.
 */
async function startServer() {
    const transport = new StdioServerTransport();
    await server.connect(transport);
    console.error("Sec-Analyzer MCP server running on stdio");
}
startServer().catch((error) => {
    console.error("Fatal error starting server:", error);
    process.exit(1);
});
