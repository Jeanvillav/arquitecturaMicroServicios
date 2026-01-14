#!/bin/bash

# Function to kill processes on exit
cleanup() {
    echo "Stopping servers..."
    kill $AUTHORS_PID $BOOKS_PID $LOANS_PID $GATEWAY_PID
    exit
}

# Trap SIGINT (Ctrl+C)
trap cleanup SIGINT

echo "Starting Authors Service on 8001..."
(cd LumenAuthorsApi && php -S localhost:8001 -t public) &
AUTHORS_PID=$!

echo "Starting Books Service on 8002..."
(cd LumenBooksApi && php -S localhost:8002 -t public) &
BOOKS_PID=$!

echo "Starting Loans Service on 8018..."
(cd LumenLoansApi && php -S localhost:8018 -t public) &
LOANS_PID=$!

echo "Starting Gateway on 8000..."
(cd LumenGatewayApi && php -S localhost:8000 -t public) &
GATEWAY_PID=$!

echo "------------------------------------------------"
echo "✅ All services started!"
echo ""
echo "🔗 API Gateway: http://localhost:8000"
echo "🔗 Loans Endpoint: http://localhost:8000/loans"
echo ""
echo "📝 To see Swagger documentation, run: cd docs && php -S localhost:8080"
echo "------------------------------------------------"
echo "Press Ctrl+C to stop all servers."

wait
