@echo off
REM Enable local git hooks and commit template (Windows)
echo Setting git core.hooksPath to .githooks
git config core.hooksPath .githooks
echo Setting local commit template
git config --local commit.template .github/COMMIT_TEMPLATE.md
echo Done.
