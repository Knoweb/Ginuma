import re

with open('live_bundle.js', 'r', encoding='utf-8') as f:
    code = f.read()

# Find all occurrences of path: "/" or path: "/login" etc.
paths = set(re.findall(r'path:\s*[\'\"`]/?[a-zA-Z0-9_-]*[\'\"`]', code))
print("Paths found:", paths)
