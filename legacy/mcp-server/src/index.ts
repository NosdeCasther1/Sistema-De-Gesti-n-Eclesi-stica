import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import sharp from "sharp";
import puppeteer from "puppeteer";
import { readFileSync } from "fs";
import path from "path";
import fs from "fs/promises";

/**
 * OptiBill MCP Server
 * Automates asset optimization and A11y contrast audits.
 */

const server = new Server(
  {
    name: "optibill-automation",
    version: "1.0.0",
  },
  {
    capabilities: {
      tools: {},
    },
  }
);

// --- Tool 1: Optimize Assets ---
async function handleOptimizeAssets(directoryPath: string) {
  const absolutePath = path.resolve(directoryPath);
  
  try {
    const files = await fs.readdir(absolutePath);
    const imageFiles = files.filter(f => /\.(jpe?g|png)$/i.test(f));
    
    const results = [];

    for (const file of imageFiles) {
      const filePath = path.join(absolutePath, file);
      const stats = await fs.stat(filePath);
      const originalSize = stats.size;

      // Generate WebP
      const webpName = `${path.parse(file).name}.webp`;
      const webpPath = path.join(absolutePath, webpName);
      await sharp(filePath).webp({ quality: 80 }).toFile(webpPath);
      const webpStats = await fs.stat(webpPath);

      // Generate AVIF
      const avifName = `${path.parse(file).name}.avif`;
      const avifPath = path.join(absolutePath, avifName);
      await sharp(filePath).avif({ quality: 65 }).toFile(avifPath);
      const avifStats = await fs.stat(avifPath);

      results.push({
        original: file,
        originalSizeKB: (originalSize / 1024).toFixed(2),
        webp: {
          name: webpName,
          sizeKB: (webpStats.size / 1024).toFixed(2),
          saving: (((originalSize - webpStats.size) / originalSize) * 100).toFixed(2) + "%"
        },
        avif: {
          name: avifName,
          sizeKB: (avifStats.size / 1024).toFixed(2),
          saving: (((originalSize - avifStats.size) / originalSize) * 100).toFixed(2) + "%"
        }
      });
    }

    return {
      content: [{ type: "text", text: JSON.stringify(results, null, 2) }]
    };
  } catch (error: any) {
    return {
      content: [{ type: "text", text: `Error processing directory: ${error.message}` }],
      isError: true
    };
  }
}

// --- Tool 2: Analyze Contrast ---
async function handleAnalyzeContrast(targetUrl: string, themeMode: string) {
  let browser;
  try {
    browser = await puppeteer.launch({ headless: "new" });
    const page = await browser.newPage();
    
    await page.goto(targetUrl, { waitUntil: "networkidle0" });

    // Inject Theme and Axe
    await page.evaluate((mode) => {
      document.documentElement.setAttribute('data-theme', mode);
    }, themeMode);

    const axeSource = readFileSync(path.resolve('node_modules/axe-core/axe.min.js'), 'utf8');
    await page.evaluate(axeSource);

    // Run Audit
    const results = await page.evaluate(async () => {
      // @ts-ignore
      return await axe.run({
        runOnly: {
          type: 'tag',
          values: ['color-contrast']
        }
      });
    });

    const violations = results.violations.map(v => ({
      description: v.description,
      impact: v.impact,
      nodes: v.nodes.map(n => ({
        selector: n.target,
        html: n.html,
        failureSummary: n.failureSummary
      }))
    }));

    await browser.close();

    return {
      content: [{ type: "text", text: JSON.stringify(violations, null, 2) }]
    };
  } catch (error: any) {
    if (browser) await browser.close();
    return {
      content: [{ type: "text", text: `A11y Audit Failed: ${error.message}` }],
      isError: true
    };
  }
}

// --- MCP Request Handlers ---

server.setRequestHandler(ListToolsRequestSchema, async () => ({
  tools: [
    {
      name: "optimize_assets",
      description: "Compress images (JPG/PNG) to WebP and AVIF formats in a given directory.",
      inputSchema: {
        type: "object",
        properties: {
          directory_path: { type: "string", description: "Path to the directory containing raw images." }
        },
        required: ["directory_path"]
      }
    },
    {
      name: "analyze_contrast",
      description: "Perform a WCAG color contrast audit using Puppeteer and Axe-core.",
      inputSchema: {
        type: "object",
        properties: {
          target_url: { type: "string", default: "http://localhost/ProyectoIglesia/inicio" },
          theme_mode: { type: "string", enum: ["light", "dark"], default: "dark" }
        },
        required: ["theme_mode"]
      }
    }
  ]
}));

server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;

  switch (name) {
    case "optimize_assets":
      return await handleOptimizeAssets(args?.directory_path as string);
    case "analyze_contrast":
      return await handleAnalyzeContrast(
        (args?.target_url as string) || "http://localhost/ProyectoIglesia/inicio",
        (args?.theme_mode as string) || "dark"
      );
    default:
      throw new Error(`Tool not found: ${name}`);
  }
});

async function main() {
  const transport = new StdioServerTransport();
  await server.connect(transport);
  console.error("OptiBill MCP Server running on stdio");
}

main().catch(error => {
  console.error("Server fatal error:", error);
  process.exit(1);
});
