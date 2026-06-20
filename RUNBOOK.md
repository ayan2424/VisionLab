# VisionLab Enterprise Runbook

This document details the operational procedures for deploying, maintaining, monitoring, and scaling the VisionLab Enterprise application in a production environment.

## 1. System Architecture

VisionLab operates as a containerized microservice stack:
- **visionlab-app**: PHP 8.3 FPM running Laravel 11.
- **visionlab-queue**: Laravel Horizon worker for asynchronous job processing.
- **visionlab-reverb**: WebSocket server for real-time presence and AI cursor tracking.
- **visionlab-scheduler**: Cron job container for scheduled commands.
- **visionlab-nginx**: Reverse proxy handling TLS 1.3 termination and serving static assets.
- **visionlab-mysql**: MySQL 8.0 Primary Database.
- **visionlab-redis**: Redis 7 cache and session store.
- **visionlab-jitsi**: Embedded real-time video conferencing (JWT-authenticated).

## 2. Deployment Procedures

### 2.1 First-time Server Setup
1. Ensure Docker and Docker Compose are installed.
2. Clone the repository to the production server.
3. Configure `.env` using `.env.example` as a template.
   - **Crucial:** Set `APP_ENV=production` and `APP_DEBUG=false`.
   - Setup Jitsi keys: `JITSI_APP_ID`, `JITSI_APP_SECRET`.
   - Set up Anthropic Keys: `CLAUDE_API_KEY`.
4. Generate SSL Certificates via Let's Encrypt or your Certificate Authority. Place them in `docker/nginx/ssl/server.crt` and `docker/nginx/ssl/server.key`.
5. Run deployment:
   ```bash
   docker compose -f docker-compose.prod.yml up -d --build
   docker compose -f docker-compose.prod.yml exec visionlab-app php artisan key:generate
   docker compose -f docker-compose.prod.yml exec visionlab-app php artisan migrate --force
   ```

### 2.2 Routine CI/CD Deployments
Deployment is fully automated via GitHub Actions (`.github/workflows/deploy.yml`).
The pipeline builds the Docker image, pushes it to GHCR, and executes a remote SSH deployment script that handles:
- Code pull.
- Image pull.
- `docker compose up -d`.
- `php artisan migrate --force`.
- Cache clearing and route optimization.

### 2.3 Rollback Procedure
If a deployment fails or introduces critical bugs:
1. Revert the commit in GitHub to trigger a fresh CI/CD run.
2. OR, SSH into the server and rollback manually:
   ```bash
   git fetch
   git checkout <previous_commit_hash>
   docker compose -f docker-compose.prod.yml build
   docker compose -f docker-compose.prod.yml up -d
   docker compose -f docker-compose.prod.yml exec visionlab-app php artisan migrate:rollback --step=1
   ```

## 3. Operations & Observability

### 3.1 Structured Logging (ELK/Datadog)
Laravel is configured to use the `Monolog\Formatter\JsonFormatter` for standard logs (`storage/logs/laravel.log`). This ensures that logs are structured correctly for external log aggregators.

### 3.2 System Health Monitoring
VisionLab exposes an endpoint for UptimeRobot/Datadog:
- **Endpoint:** `GET /api/health`
- **Output:** Returns JSON containing 6 dependency probes (Database, Redis, Reverb, Storage, AI Config, Jitsi Config).
- **Behavior:** Returns HTTP 200 if OK, HTTP 503 if Database/Redis is down.

### 3.3 Worker & Queue Health
- Navigate to `https://<domain>/horizon` to monitor queues.
- If Horizon is paused or failed, restart the worker container:
  ```bash
  docker compose -f docker-compose.prod.yml restart visionlab-queue
  ```

## 4. Disaster Recovery & Troubleshooting

### 4.1 OOM Crashes (Out of Memory)
Docker containers are configured with `restart: unless-stopped`. If `CodeServerManager` detects a child IDE crash (`SIGABRT`/OOM), the user interface will display "Workspace Connection Lost". 
**Fix:** The backend will automatically reboot the Docker workspace. No manual intervention is needed.

### 4.2 WebSockets Disconnected
If users report they cannot see each other typing or AI patches aren't streaming:
1. Verify `visionlab-reverb` is running: `docker compose ps`
2. Restart it: `docker compose -f docker-compose.prod.yml restart visionlab-reverb`

### 4.3 Database Backups
Automate SQL dumps via a cronjob on the host machine:
```bash
docker compose -f docker-compose.prod.yml exec -T visionlab-mysql mysqldump -u visionlab -psecret visionlab > /backup/visionlab_$(date +%F).sql
```
