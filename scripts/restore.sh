#!/bin/bash
# ---------------------------------------------------------
# VisionLab Production Restore Script
# Usage: ./scripts/restore.sh <path_to_db_dump.sql.gz> <path_to_storage_backup.tar.gz>
# ---------------------------------------------------------

set -e

if [ "$#" -ne 2 ]; then
    echo "Usage: $0 <path_to_db_dump.sql.gz> <path_to_storage_backup.tar.gz>"
    exit 1
fi

DB_BACKUP=$1
STORAGE_BACKUP=$2

echo "WARNING: This will completely overwrite the existing production database and storage!"
read -p "Are you absolutely sure you want to continue? (y/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Restore aborted."
    exit 1
fi

echo "Restoring MySQL Database..."
# Decompress and stream into the container
zcat "$DB_BACKUP" | docker exec -i visionlab_db mysql -u visionlab -psecret visionlab
echo "Database restored."

echo "Restoring Storage (App/Public)..."
tar -xzf "$STORAGE_BACKUP" -C ./
echo "Storage restored."

echo "Restore completed successfully."
