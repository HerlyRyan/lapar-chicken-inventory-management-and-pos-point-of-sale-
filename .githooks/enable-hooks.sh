#!/bin/sh
# Enable local git hooks and commit template (Unix)
printf "Setting git core.hooksPath to .githooks\n"
git config core.hooksPath .githooks
printf "Setting local commit template\n"
git config --local commit.template .github/COMMIT_TEMPLATE.md
printf "Making pre-commit executable (if git bash)\n"
if [ -f .githooks/pre-commit ]; then
  chmod +x .githooks/pre-commit || true
fi
printf "Done.\n"
