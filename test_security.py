import requests
import json
import time

BASE_URL = "http://localhost:8081/api"
COMPANY_CODE = f"TEST_{int(time.time())}"

print("=== Security Testing ===")

# Test 1: Security Headers
print("\n[1] Testing Security Headers...")
resp = requests.get(f"{BASE_URL}/auth/login") # Using a dummy GET just to check headers
headers = resp.headers
print(f"X-Frame-Options: {headers.get('X-Frame-Options')}")
print(f"Strict-Transport-Security: {headers.get('Strict-Transport-Security')}")
print(f"Content-Security-Policy: {headers.get('Content-Security-Policy')}")
if headers.get('X-Frame-Options') == 'DENY' and 'default-src' in headers.get('Content-Security-Policy', ''):
    print("SUCCESS: Security headers are present.")
else:
    print("FAILED: Missing expected security headers.")

# Test 2: Password Policy (Weak Password)
print("\n[2] Testing Password Policy (Weak Password)...")
weak_payload = {
    "companyName": "Test Company",
    "companyCode": COMPANY_CODE,
    "brId": "BR123456",
    "contactPersonName": "John Doe",
    "email": "test@test.com",
    "contactNumber": "0771234567",
    "address": "123 Test St",
    "password": "weak" # Violates 8 chars, uppercase, digit, special char
}
try:
    resp = requests.post(f"{BASE_URL}/companies/register", data=weak_payload)
    if resp.status_code == 400:
        print(f"SUCCESS: Registration blocked for weak password. Response: {resp.text}")
    else:
        print(f"FAILED: Registration allowed with weak password. Status: {resp.status_code}")
except Exception as e:
    print(f"Error: {e}")

# Note: The company registration requires a file upload. 
# It's easier to test lockout via an existing user or mock one up. Let's just create a normal request with files.
