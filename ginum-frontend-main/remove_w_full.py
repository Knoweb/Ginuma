import os
import re

directories = ['src']
# This regex looks for w-full inside the className of a <button> that contains text matching Cancel/Back/Refresh/Export
# Note: we need to be careful with jsx interpolation, but for simple strings it works.
pattern = re.compile(r'(<button[^>]*className=[\"\'][^\"\']*)\bw-full\b([^\"\']*[\"\'][^>]*>(?:<[^>]+>\s*)?(?:Cancel|Back|Refresh|Export|Save|Create)[^<]*</button>)', re.IGNORECASE | re.DOTALL)

for d in directories:
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.jsx'):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                new_content, count = pattern.subn(r'\1\2', content)
                # Cleanup double spaces from class names
                new_content = re.sub(r' +', ' ', new_content)
                if count > 0:
                    with open(path, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f'Removed w-full from button in {path}')
