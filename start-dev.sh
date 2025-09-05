#!/bin/bash

# Cold Storage Development Server Startup Script

echo "🚀 Starting Cold Storage Development Environment..."

# Check if Laravel server is already running
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "✅ Laravel server already running on port 8000"
else
    echo "🔄 Starting Laravel server on port 8000..."
    php artisan serve --port=8000 &
    LARAVEL_PID=$!
    echo "Laravel PID: $LARAVEL_PID"
fi

# Check if Frontend server is already running
if lsof -Pi :3000 -sTCP:LISTEN -t >/dev/null ; then
    echo "✅ Frontend server already running on port 3000"
else
    echo "🔄 Starting Frontend development server on port 3000..."
    cd resources/frontend
    npm run dev &
    FRONTEND_PID=$!
    echo "Frontend PID: $FRONTEND_PID"
    cd ../..
fi

echo ""
echo "🎉 Development servers started!"
echo "📱 Frontend: http://localhost:3000"
echo "🔧 Backend API: http://127.0.0.1:8000"
echo "🌐 Full App: http://127.0.0.1:8000 (serves frontend + API)"
echo ""
echo "Press Ctrl+C to stop all servers"

# Wait for user to stop
wait
