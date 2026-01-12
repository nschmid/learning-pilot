#!/bin/bash

# SSH Tunnel to Laravel Forge Database
# Usage: ./scripts/forge-tunnel.sh

FORGE_SERVER="forge@164.90.243.243"
LOCAL_PORT=3308
REMOTE_PORT=3306

echo "Starting SSH tunnel to Forge database..."
echo "Local port: $LOCAL_PORT -> Remote MySQL: $REMOTE_PORT"
echo "Press Ctrl+C to stop the tunnel"
echo ""

ssh -N -L ${LOCAL_PORT}:127.0.0.1:${REMOTE_PORT} ${FORGE_SERVER}
