import os
import re
import json
import hashlib
from mcp.server.fastmcp import FastMCP

# Initialize the FastMCP Server
mcp = FastMCP("Log-Watcher Agent")

# Retrieve and sanitize target Laravel log path
LARAVEL_LOG_PATH = os.environ.get("LARAVEL_LOG_PATH")

def get_log_path():
    """Resolves and validates the log path from environment variables or default fallback."""
    if LARAVEL_LOG_PATH:
        return os.path.abspath(LARAVEL_LOG_PATH)
    # Default fallback: search for storage/logs/laravel.log in parent directories
    possible_paths = [
        os.path.join(os.getcwd(), "storage", "logs", "laravel.log"),
        os.path.join(os.getcwd(), "..", "storage", "logs", "laravel.log"),
        os.path.join(os.path.dirname(os.path.dirname(__file__)), "storage", "logs", "laravel.log")
    ]
    for path in possible_paths:
        if os.path.exists(path):
            return path
    # If not found, return default location relative to project root
    return os.path.join(os.getcwd(), "storage", "logs", "laravel.log")

def parse_errors_from_log(max_lines: int = 500):
    """
    Reads the last N lines of laravel.log, groups them into structured errors,
    and returns a list of dictionaries with error metadata and stack trace lines.
    """
    log_path = get_log_path()
    if not os.path.exists(log_path):
        raise FileNotFoundError(f"Laravel log file not found at: {log_path}. Check your LARAVEL_LOG_PATH env variable.")

    # Read the log file safely
    with open(log_path, "r", encoding="utf-8", errors="ignore") as f:
        # For efficiency, we read the last part of the file
        # Standard approach: read all lines, then slice
        lines = f.readlines()
        log_lines = lines[-max_lines:] if len(lines) > max_lines else lines

    errors = []
    current_error = None

    # Matches lines like: [2026-05-19 06:12:34] local.ERROR: Error message...
    # Levels: ERROR, CRITICAL, EMERGENCY, ALERT
    log_header_pattern = re.compile(
        r"^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(ERROR|CRITICAL|EMERGENCY|ALERT): (.*)"
    )

    for line in log_lines:
        line_str = line.strip()
        match = log_header_pattern.match(line_str)
        if match:
            # Save previous error if any
            if current_error:
                errors.append(current_error)
            
            timestamp, level, message = match.groups()
            
            # Generate a unique 12-char ID using the raw log header to allow extraction later
            error_hash = hashlib.md5(line_str.encode("utf-8")).hexdigest()[:12]
            
            current_error = {
                "id": error_hash,
                "timestamp": timestamp,
                "level": level,
                "message": message.strip(),
                "trace": [line_str]
            }
        else:
            # If we are currently collecting trace lines, append them
            if current_error:
                current_error["trace"].append(line_str)

    # Append the last error block
    if current_error:
        errors.append(current_error)

    return errors

@mcp.tool()
def read_latest_errors(lines: int = 200) -> str:
    """
    Reads the latest N lines from storage/logs/laravel.log and filters only ERROR, CRITICAL, or EMERGENCY.
    
    Args:
        lines: Number of lines from the end of the log file to parse (default 200).
    """
    try:
        errors = parse_errors_from_log(lines)
        summaries = []
        for err in errors:
            summaries.append({
                "id": err["id"],
                "timestamp": err["timestamp"],
                "level": err["level"],
                "message": err["message"]
            })
        
        return json.dumps({
            "status": "success",
            "log_file": get_log_path(),
            "errors_found": len(summaries),
            "errors": summaries
        }, indent=2)
    except FileNotFoundError as fnf_err:
        return json.dumps({
            "status": "error",
            "message": str(fnf_err),
            "suggestion": "Make sure to set the LARAVEL_LOG_PATH environment variable correctly."
        }, indent=2)
    except Exception as e:
        return json.dumps({
            "status": "error",
            "message": f"An unexpected error occurred: {str(e)}"
        }, indent=2)

@mcp.tool()
def extract_stack_trace(error_id: str) -> str:
    """
    Returns the complete stack trace for a specific error ID.
    
    Args:
        error_id: The 12-character hexadecimal error ID generated during log scanning.
    """
    try:
        # Search a larger log buffer (last 5,000 lines) to find the target error by its ID
        errors = parse_errors_from_log(5000)
        for err in errors:
            if err["id"] == error_id:
                return json.dumps({
                    "status": "success",
                    "id": err["id"],
                    "timestamp": err["timestamp"],
                    "level": err["level"],
                    "message": err["message"],
                    "stack_trace": "\n".join(err["trace"])
                }, indent=2)
                
        return json.dumps({
            "status": "error",
            "message": f"Error with ID '{error_id}' was not found in the last 5000 lines of laravel.log."
        }, indent=2)
    except Exception as e:
        return json.dumps({
            "status": "error",
            "message": f"An error occurred while extracting the stack trace: {str(e)}"
        }, indent=2)

if __name__ == "__main__":
    # Runs the server using stdio transport
    mcp.run()
