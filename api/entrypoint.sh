#!/bin/bash

set -e  # Stop script execution if any command fails

echo "📌 Running entrypoint script..."

# Ensure Composer dependencies are installed
if [ ! -d "/var/www/html/vendor" ]; then
    echo "📦 Installing composer dependencies..."
    composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
else
    echo "✅ Dependencies already installed."
fi

# Ensure correct file permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

echo "🚀 Waiting for MySQL to be ready..."
while ! mysqladmin ping -h database.cc.localhost -u test_user -ptest_password --silent; do
    sleep 2
done

echo "✅ MySQL is ready. Running migrations..."

# Run migrations (Ensure the migrations folder exists)
if [ -d "/var/www/html/database/migrations" ]; then
    for file in /var/www/html/database/migrations/*.sql; do
        echo "📂 Running migration: $file"

        # Run MySQL command and capture potential errors
        mysql -h database.cc.localhost -u test_user -ptest_password course_catalog < "$file" 2>&1 | tee -a /var/www/html/migration_errors.log

        if [ $? -eq 0 ]; then
            echo "✅ Successfully applied: $file"
        else
            echo "❌ Error applying migration: $file (Check migration_errors.log)"
        fi
    done
    echo "✅ All migrations executed."
else
    echo "⚠️ Migration directory not found!"
fi

echo "🎉 Database setup complete! Starting Apache..."
exec apache2-foreground