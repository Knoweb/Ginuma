import os
import re

def update_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content

    # Standardize classes for gl-btn
    # Match: className="gl-btn gl-btn-secondary" or className="gl-btn gl-btn-primary" or similar
    # But ONLY if it doesn't already have lg:w-auto or w-full
    
    def replacer(match):
        cls_content = match.group(1)
        if 'lg:w-auto' in cls_content:
            return f'className="{cls_content}"' # already done
        
        # Remove any existing w-auto, flex-none, w-full to prevent duplicates
        cls_content = re.sub(r'\b(w-auto|w-full|md:w-auto|flex-none|min-w-fit|lg:flex-none)\b', '', cls_content)
        # cleanup double spaces
        cls_content = re.sub(r'\s+', ' ', cls_content).strip()
        
        cls_content += " w-full lg:w-auto lg:flex-none"
        
        return f'className="{cls_content}"'

    new_content = re.sub(r'className="(gl-btn\s+[^"]+)"', replacer, new_content)
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

def main():
    for root, dirs, files in os.walk('src/components'):
        for file in files:
            if file.endswith('.jsx'):
                update_file(os.path.join(root, file))
    
    # Also process pages dir
    for root, dirs, files in os.walk('src/pages'):
        for file in files:
            if file.endswith('.jsx'):
                update_file(os.path.join(root, file))

if __name__ == '__main__':
    main()
