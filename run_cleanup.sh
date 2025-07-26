#!/bin/bash

echo "=== SuiteCRM AI Error Cleanup Script ==="
echo "This will remove all AI-related data and clear sessions"
echo ""

# Find PHP executable
PHP_PATH=""
if command -v php >/dev/null 2>&1; then
    PHP_PATH="php"
elif command -v php8.1 >/dev/null 2>&1; then
    PHP_PATH="php8.1" 
elif command -v php8.0 >/dev/null 2>&1; then
    PHP_PATH="php8.0"
elif command -v php7.4 >/dev/null 2>&1; then
    PHP_PATH="php7.4"
elif [ -f "/usr/bin/php" ]; then
    PHP_PATH="/usr/bin/php"
elif [ -f "/usr/local/bin/php" ]; then
    PHP_PATH="/usr/local/bin/php"
else
    echo "PHP not found. Please run the cleanup scripts manually:"
    echo "1. Run cleanup_ai_data.php through your web server"
    echo "2. Run clear_sessions.php through your web server"
    exit 1
fi

echo "Using PHP: $PHP_PATH"
echo ""

# Change to SuiteCRM directory
cd "$(dirname "$0")"

echo "Step 1: Cleaning AI data from database..."
$PHP_PATH cleanup_ai_data.php

echo ""
echo "Step 2: Clearing sessions and cache..."
$PHP_PATH clear_sessions.php

echo ""
echo "Step 3: Final cache clear..."
rm -rf cache/*
echo "✓ Cache directory cleared"

echo ""
echo "=== CLEANUP COMPLETE ==="
echo ""
echo "IMPORTANT NEXT STEPS:"
echo "1. Close your browser completely"
echo "2. Reopen browser in private/incognito mode"
echo "3. Log into SuiteCRM"
echo "4. Try accessing Cases module"
echo ""
echo "If errors persist, the issue may be in browser cache."
echo "Try: Ctrl+Shift+R (or Cmd+Shift+R on Mac) to hard refresh"