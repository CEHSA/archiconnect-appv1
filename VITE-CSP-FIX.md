# Vite CSP and Asset Fixes

This document explains the changes made to fix the Content Security Policy (CSP) issues with Vite assets.

## Identified Issues

1. The production environment was attempting to load Vite assets from a development server (http://[::1]:5173).
2. CSP rules were blocking these requests, causing CSS and JS assets to fail to load.

## Applied Fixes

### 1. CSP Middleware Enhancement

The `AllowViteDevServerInCsp` middleware has been enhanced to:

- Only run in local development environments
- Support multiple Vite dev server URLs (localhost, 127.0.0.1, [::1])
- Properly modify all necessary CSP directives

### 2. Production Build Improvements

- Added a `build-for-production.sh` script to properly build Vite assets
- Modified `package.json` to add a `predeploy` script that builds assets before deployment
- Updated deployment scripts to verify that Vite assets were properly built
- Explicitly included public/build directory in deployment configuration

### 3. Blade Template Improvements

- Added conditional asset loading in Blade templates
- Created a ViteHelper class to assist with asset loading
- Added a custom Blade directive for easier asset management
- Added fallback to Vite dev server when necessary (development only)

### 4. Diagnostic Tools

- Added an `env-check.php` file to diagnose environment issues in production
- Added a `check-vite-build.sh` script to verify Vite assets are properly built

## Deployment Instructions

1. **For Local Development:**
   - Run `npm run dev` to start the Vite development server
   - The enhanced CSP middleware will allow loading resources from the Vite dev server

2. **For Production Deployment:**
   - Run `npm run deploy` which will:
     - Build Vite assets for production (via predeploy script)
     - Verify the build was successful
     - Deploy to your production server

3. **Post-Deployment Verification:**
   - Visit your production site and ensure assets load correctly
   - If issues persist, visit `/env-check.php` to diagnose environment settings

## Important Notes

- Always make sure `npm run build` completes successfully before deployment
- The production environment should have `APP_ENV=production` and `APP_DEBUG=false` in `.env`
- If you manually deploy, make sure to include the `public/build` directory

For additional help, contact your development team.
