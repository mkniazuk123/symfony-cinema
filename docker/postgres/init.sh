#!/bin/bash
set -e
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" -c 'ALTER DATABASE "postgres" SET log_statement = '\''all'\'''
