#!/bin/bash

# Initialize databases for distributor application

echo "Initializing databases..."

# Wait for MySQL to be ready
while ! mysqladmin ping -h mysql -u root -p'' --silent; do
    echo "Waiting for MySQL to start..."
    sleep 2
done

echo "MySQL is ready. Creating databases..."

# Create alamat_db database (not created automatically by MySQL image)
mysql -h mysql -u root -p'' -e "CREATE DATABASE IF NOT EXISTS alamat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import database schemas if they exist
if [ -f "/docker-entrypoint-initdb.d/distribusi.sql" ]; then
    echo "Importing distribusi.sql..."
    mysql -h mysql -u root -p'' distributor < /docker-entrypoint-initdb.d/distribusi.sql
fi

if [ -f "/docker-entrypoint-initdb.d/distributor.sql" ]; then
    echo "Importing distributor.sql..."
    mysql -h mysql -u root -p'' distributor < /docker-entrypoint-initdb.d/distributor.sql
fi

# Import alamat_db schema if exists
if [ -f "/docker-entrypoint-initdb.d/schema_migration_orang_user.sql" ]; then
    echo "Importing alamat_db schema..."
    mysql -h mysql -u root -p'' alamat_db < /docker-entrypoint-initdb.d/schema_migration_orang_user.sql
fi

echo "Database initialization completed!"

# Create user for application
mysql -h mysql -u root -p'' -e "CREATE USER IF NOT EXISTS 'distributor_user'@'%' IDENTIFIED BY 'distributor_pass';"
mysql -h mysql -u root -p'' -e "GRANT ALL PRIVILEGES ON distributor.* TO 'distributor_user'@'%';"
mysql -h mysql -u root -p'' -e "GRANT ALL PRIVILEGES ON alamat_db.* TO 'distributor_user'@'%';"
mysql -h mysql -u root -p'' -e "FLUSH PRIVILEGES;"

echo "User created and privileges granted!"
