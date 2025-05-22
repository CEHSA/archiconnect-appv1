#!/bin/bash

# Check if Vite manifest file exists in production
if [ -f "public/build/manifest.json" ]; then
    echo "Vite manifest exists - assets are properly built"
else
    echo "ERROR: Vite manifest is missing - assets were not built correctly"
    echo "Running build process now..."
    npm run build
    
    # Verify the build was successful
    if [ -f "public/build/manifest.json" ]; then
        echo "Build successful! Please redeploy the application."
    else
        echo "Build failed. Please check your build configuration."
    fi
fi
