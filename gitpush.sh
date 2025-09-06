#!/bin/zsh
echo "=== Start ==="
git add .
echo "=== Add Done ==="
git commit -m "update"
echo "=== Commit Done ==="
git push origin main
echo "=== Push Done ==="