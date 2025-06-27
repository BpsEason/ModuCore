#!/bin/bash
echo "Running load test on /api/users..."
echo "This requires 'ab' (Apache Bench) to be installed."
ab -n 1000 -c 100 http://localhost:8000/api/users
