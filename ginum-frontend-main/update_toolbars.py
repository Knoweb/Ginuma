import os

def update_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content
    
    # 1. Update toolbar wrapper
    new_content = new_content.replace(
        'className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"',
        'className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"'
    )
    
    # 2. Update button classes
    new_content = new_content.replace(
        'className="w-full md:w-auto bg-sky-600 hover:bg-sky-700 text-white px-5 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium transition-colors shadow-sm"',
        'className="w-full lg:w-auto bg-sky-600 hover:bg-sky-700 text-white px-5 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium transition-colors shadow-sm lg:flex-none"'
    )
    
    new_content = new_content.replace(
        'w-full md:w-auto',
        'w-full lg:w-auto lg:flex-none'
    )
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

def main():
    for root, dirs, files in os.walk('src/components'):
        for file in files:
            if file.endswith('.jsx'):
                update_file(os.path.join(root, file))

if __name__ == '__main__':
    main()
