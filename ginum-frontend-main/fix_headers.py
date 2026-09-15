import os
import re

directories = ['src/components', 'src/pages']

header_pattern = re.compile(
    r'<div className="flex (?:flex-col sm:flex-row )?justify-between items-(?:start|center)(?: sm:items-center)?.*?">\s*'
    r'<div>\s*'
    r'<h1 className="gl-heading.*?">\s*'
    r'(?:<([A-Za-z0-9]+)\s+className="text-blue-600"\s*/>\s*)?'
    r'([^<]+)\s*'
    r'</h1>\s*'
    r'(?:<p className="(?:text-sm )?text-gray-500 mt-1">\s*([^<]+)\s*</p>\s*)?'
    r'</div>\s*'
    r'<button\s+onClick=\{([^}]+)\}\s+className="gl-btn gl-btn-secondary"\s*>\s*'
    r'(?:<FiArrowLeft\s*(?:className="mr-2"\s*)?/>\s*)?([^<]+)\s*'
    r'</button>\s*'
    r'</div>', re.DOTALL)

for d in directories:
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.jsx'):
                path = os.path.join(root, f)
                with open(path, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                def replacer(match):
                    icon = match.group(1) or 'FiFileText'
                    title = match.group(2).strip()
                    subtitle = (match.group(3) or '').strip()
                    onclick = match.group(4).strip()
                    btn_text = match.group(5).strip()
                    
                    res = f"""<PageHeader
        title="{title}"
        subtitle="{subtitle}"
        icon={{{icon}}}
        actions={{
          <button onClick={{{onclick}}} className="gl-btn gl-btn-secondary">
            <FiArrowLeft className="mr-2" /> {btn_text}
          </button>
        }}
      />"""
                    return res
                
                new_content, count = header_pattern.subn(replacer, content)
                if count > 0:
                    # also add import if missing
                    if 'import PageHeader' not in new_content:
                        # find last import
                        import_pattern = re.compile(r'^(import .*?;?)\n(?=(?:import|\n)*)', re.MULTILINE)
                        imports = list(import_pattern.finditer(new_content))
                        if imports:
                            last_import = imports[-1]
                            # Count depth of src to generate correct relative path
                            depth = path.count(os.sep) - 1 # e.g. src/components/x.jsx -> 2
                            # but simpler: PageHeader is in src/components/common/PageHeader.jsx
                            # let's just use a relative path based on path
                            rel = os.path.relpath('src/components/common/PageHeader', os.path.dirname(path)).replace('\\', '/')
                            insert = f'\nimport PageHeader from "{rel}";\n'
                            
                            new_content = new_content[:last_import.end()] + insert + new_content[last_import.end():]
                            
                    with open(path, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                    print(f"Fixed header in {path}")
