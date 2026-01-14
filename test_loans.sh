#!/bin/bash

BASE_URL="http://localhost:8000"

echo "Testing Loans Service via Gateway..."

# 1. Create Author (needed for Book)
echo "1. Creating Author..."
AUTHOR_RESPONSE=$(curl -s -X POST $BASE_URL/authors \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Loan Test Author",
    "gender": "female",
    "country": "Test Land"
  }')
AUTHOR_ID=$(echo $AUTHOR_RESPONSE | grep -o '"id":[0-9]*' | head -1 | awk -F: '{print $2}')
echo "Author ID: $AUTHOR_ID"

# 2. Create Book (needed for Loan)
echo "2. Creating Book..."
BOOK_RESPONSE=$(curl -s -X POST $BASE_URL/books \
  -H "Content-Type: application/json" \
  -d "{
    \"title\": \"Loan Test Book\",
    \"description\": \"A book to test loans\",
    \"price\": 100,
    \"author_id\": $AUTHOR_ID
  }")
BOOK_ID=$(echo $BOOK_RESPONSE | grep -o '"id":[0-9]*' | head -1 | awk -F: '{print $2}')
echo "Book ID: $BOOK_ID"

# 3. Create Loan
echo "3. Creating Loan..."
LOAN_DATE=$(date '+%Y-%m-%d %H:%M:%S')
DUE_DATE=$(date -v+7d '+%Y-%m-%d %H:%M:%S' 2>/dev/null || date -d "+7 days" '+%Y-%m-%d %H:%M:%S') # MacOS vs Linux date

LOAN_Response=$(curl -s -X POST $BASE_URL/loans \
  -H "Content-Type: application/json" \
  -d "{
    \"user_id\": 1,
    \"book_id\": $BOOK_ID,
    \"library_id\": 1,
    \"loan_date\": \"$LOAN_DATE\",
    \"due_date\": \"$DUE_DATE\",
    \"status\": \"active\"
  }")
echo $LOAN_Response
LOAN_ID=$(echo $LOAN_Response | grep -o '"id":[0-9]*' | head -1 | awk -F: '{print $2}')
echo "Loan ID: $LOAN_ID"

# 4. Get Loan
echo "4. Getting Loan $LOAN_ID..."
curl -s $BASE_URL/loans/$LOAN_ID | grep "Loan Test Book" && echo "Loan found!"

# 5. List Loans
echo "5. Listing Loans..."
curl -s $BASE_URL/loans | wc -c

# 6. Return Loan (Update)
echo "6. Returning Loan..."
RETURN_DATE=$(date '+%Y-%m-%d %H:%M:%S')
curl -s -X PUT $BASE_URL/loans/$LOAN_ID \
  -H "Content-Type: application/json" \
  -d "{
    \"status\": \"returned\",
    \"return_date\": \"$RETURN_DATE\"
  }"
echo ""

# 7. Check Overdue (Empty expected)
echo "7. Checking Overdue..."
curl -s $BASE_URL/loans/overdue
echo ""

# 8. Failed Validation (Invalid Book)
echo "8. Testing Invalid Book Validation..."
curl -s -X POST $BASE_URL/loans \
  -H "Content-Type: application/json" \
  -d "{
    \"user_id\": 1,
    \"book_id\": 999999,
    \"library_id\": 1,
    \"loan_date\": \"$LOAN_DATE\",
    \"due_date\": \"$DUE_DATE\",
    \"status\": \"active\"
  }"
echo ""
