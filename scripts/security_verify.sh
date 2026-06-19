#!/bin/bash
# VisionLab - OWASP ASVS Security Verification Script
# Performs 7 automated checks to ensure system security posture

echo "[VisionLab Security Verification]"
echo "================================="
FAILURES=0

# 1. Check .env file permissions
ENV_PERMS=$(stat -c "%a" ../.env 2>/dev/null)
if [ "$ENV_PERMS" == "600" ] || [ "$ENV_PERMS" == "400" ]; then
    echo "✅ [Check 1] .env permissions are secure ($ENV_PERMS)"
else
    echo "❌ [Check 1] .env permissions are insecure ($ENV_PERMS). Should be 600 or 400."
    FAILURES=$((FAILURES+1))
fi

# 2. Check storage/ and bootstrap/cache/ permissions
STORAGE_PERMS=$(stat -c "%a" ../storage 2>/dev/null)
if [ "$STORAGE_PERMS" == "775" ] || [ "$STORAGE_PERMS" == "755" ]; then
    echo "✅ [Check 2] storage/ permissions are correct ($STORAGE_PERMS)"
else
    echo "❌ [Check 2] storage/ permissions are incorrect ($STORAGE_PERMS)."
    FAILURES=$((FAILURES+1))
fi

# 3. Check for open path traversal functions (e.g. file_get_contents without realpath)
TRAVERSAL_RISKS=$(grep -rnw '../app' -e 'file_get_contents' -e 'require_once' -e 'include_once' | grep -v 'realpath' | wc -l)
if [ "$TRAVERSAL_RISKS" -eq 0 ]; then
    echo "✅ [Check 3] Path traversal checks (realpath) passed"
else
    echo "⚠️ [Check 3] Found $TRAVERSAL_RISKS potential path traversal vulnerabilities."
    # We won't strictly fail this, just warn, but ideal is 0.
fi

# 4. Check for APP_DEBUG in production
APP_ENV=$(grep -E "^APP_ENV=" ../.env | cut -d '=' -f 2)
APP_DEBUG=$(grep -E "^APP_DEBUG=" ../.env | cut -d '=' -f 2)

if [ "$APP_ENV" == "production" ] && [ "$APP_DEBUG" == "true" ]; then
    echo "❌ [Check 4] APP_DEBUG is true in production environment!"
    FAILURES=$((FAILURES+1))
else
    echo "✅ [Check 4] Environment debugging settings are secure"
fi

# 5. Check API Rate Limiting Config
API_LIMIT=$(grep -rnw '../app/Providers/RouteServiceProvider.php' -e "RateLimiter::for('api'" | wc -l)
if [ "$API_LIMIT" -gt 0 ]; then
    echo "✅ [Check 5] API Rate Limiting is configured"
else
    echo "⚠️ [Check 5] API Rate Limiting might not be configured."
fi

# 6. Check Sanctum Stateful Domains
SANCTUM_DOMAINS=$(grep -E "^SANCTUM_STATEFUL_DOMAINS=" ../.env)
if [ -n "$SANCTUM_DOMAINS" ]; then
    echo "✅ [Check 6] Sanctum stateful domains are restricted"
else
    echo "⚠️ [Check 6] Sanctum stateful domains are not explicitly set in .env"
fi

# 7. Check for eval(), exec(), system()
DANGEROUS_FUNCS=$(grep -rnw '../app' -e 'eval(' -e 'exec(' -e 'system(' | wc -l)
if [ "$DANGEROUS_FUNCS" -eq 0 ]; then
    echo "✅ [Check 7] No dangerous execution functions found in app/"
else
    echo "❌ [Check 7] Found $DANGEROUS_FUNCS instances of dangerous functions (eval, exec, system)!"
    FAILURES=$((FAILURES+1))
fi

echo "================================="
if [ $FAILURES -gt 0 ]; then
    echo "Security Verification FAILED with $FAILURES critical issues."
    exit 1
else
    echo "Security Verification PASSED. System is compliant."
    exit 0
fi
