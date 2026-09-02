import re

with open('live_bundle.js', 'r', encoding='utf-8') as f:
    code = f.read()

# Find the start of the AppRouter component. 
# In minified React, it's often a function returning JSX (React.createElement or jsx-runtime)
# Let's just find the index of "path:\"/\",element:" and print a 500-character window around it.
match = re.search(r'path:"/",element:', code)
if match:
    start = max(0, match.start() - 250)
    end = min(len(code), match.end() + 500)
    print("Found around index", match.start())
    print("---")
    print(code[start:end])
else:
    print("Could not find path:'/',element:")
