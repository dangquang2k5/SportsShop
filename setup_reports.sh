#!/bin/bash
# Script to set up the reports directory with proper permissions

echo "Setting up reports directory..."

# Create reports directory if it doesn't exist
if [ ! -d "reports" ]; then
    mkdir -p reports
    echo "✓ Created reports directory"
else
    echo "✓ Reports directory already exists"
fi

# Set proper permissions
chmod 755 reports
echo "✓ Set directory permissions to 755"

# Create necessary files
touch reports/.htaccess
touch reports/index.php
touch reports/.gitignore
touch reports/README.md

# Set file permissions
chmod 644 reports/.htaccess
chmod 644 reports/index.php
chmod 644 reports/.gitignore
chmod 644 reports/README.md

echo "✓ Created and set permissions for configuration files"

# If running in Docker, set owner to www-data
if [ -f "/.dockerenv" ]; then
    chown -R www-data:www-data reports
    echo "✓ Set owner to www-data (Docker environment)"
fi

echo ""
echo "✅ Reports directory setup complete!"
echo ""
echo "Directory structure:"
ls -la reports/
