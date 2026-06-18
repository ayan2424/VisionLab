#!/bin/bash
# ---------------------------------------------------------
# VisionLab Production Backup Script
# Usage: ./scripts/backup.sh
# ---------------------------------------------------------

set -e

BACKUP_DIR="/var/backups/visionlab"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
DB_BACKUP="$BACKUP_DIR/db_backup_$DATE.sql.gz"
STORAGE_BACKUP="$BACKUP_DIR/storage_backup_$DATE.tar.gz"

echo "Creating backup directory: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

echo "Backing up MySQL Database..."
# Run mysqldump inside the container and compress it
docker exec visionlab_db mysqldump -u visionlab -psecret visionlab | gzip > "$DB_BACKUP"
echo "Database backed up to: $DB_BACKUP"

echo "Backing up Storage (App/Public)..."
# Tar the storage folder
tar -czf "$STORAGE_BACKUP" storage/app/public
echo "Storage backed up to: $STORAGE_BACKUP"

echo "Cleaning up old backups (older than 7 days)..."
find "$BACKUP_DIR" -type f -name "*.gz" -mtime +7 -exec rm {} \;

echo "Backup completed successfully."
