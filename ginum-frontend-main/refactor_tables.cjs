const fs = require('fs');
const path = require('path');

const dirs = [
  path.join(__dirname, 'src', 'components'),
  path.join(__dirname, 'src', 'pages')
];

function processDir(dir) {
  if (!fs.existsSync(dir)) return;
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const fullPath = path.join(dir, file);
    if (fs.statSync(fullPath).isDirectory()) {
      processDir(fullPath);
    } else if (fullPath.endsWith('.jsx')) {
      processFile(fullPath);
    }
  }
}

function processFile(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');
  let original = content;

  // Layouts & Headings
  content = content.replace(/className="container mx-auto px-4 py-8"/g, 'className="p-6 lg:p-8 flex flex-col gap-6"');
  content = content.replace(/className="text-3xl font-bold text-gray-800"/g, 'className="gl-heading"');
  content = content.replace(/className="text-2xl font-bold text-gray-800"/g, 'className="gl-heading"');
  content = content.replace(/className="text-gray-500 mt-1"/g, 'className="gl-subheading mt-1"');
  content = content.replace(/className="text-gray-600 mt-1"/g, 'className="gl-subheading mt-1"');
  
  // Cards and Panels
  content = content.replace(/className="bg-white rounded-lg shadow p-6"/g, 'className="gl-card p-6"');
  content = content.replace(/className="bg-white rounded-lg shadow-md p-6"/g, 'className="gl-card p-6"');
  content = content.replace(/className="bg-white rounded-lg shadow-sm p-6"/g, 'className="gl-card p-6"');
  content = content.replace(/className="bg-white rounded-lg shadow p-4"/g, 'className="gl-card p-6"');
  
  // Empty states
  content = content.replace(/className="bg-white rounded-lg shadow p-8 text-center"/g, 'className="gl-card p-12 text-center flex flex-col items-center justify-center min-h-[300px]"');
  content = content.replace(/className="mx-auto h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-4"/g, 'className="h-16 w-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500 mb-4 shadow-sm border border-indigo-100/50 mx-auto"');
  
  // Tables containers
  content = content.replace(/className="bg-white rounded-lg shadow overflow-hidden"/g, 'className="gl-table-container"');
  content = content.replace(/className="bg-white shadow-md rounded-lg overflow-hidden"/g, 'className="gl-table-container"');
  content = content.replace(/className="min-w-full divide-y divide-gray-200"/g, 'className="gl-table"');
  content = content.replace(/className="w-full text-left border-collapse"/g, 'className="gl-table"');
  
  // Table head & body
  content = content.replace(/<thead className="bg-gray-50( text-gray-700)?"(?:[^>]*)>/g, '<thead>');
  content = content.replace(/<tbody className="bg-white divide-y divide-gray-200"/g, '<tbody');
  
  // Table rows
  content = content.replace(/className="hover:bg-gray-50 transition-colors"/g, '');
  content = content.replace(/className="border-b hover:bg-gray-50"/g, '');

  // Table headers (remove complex th classes)
  content = content.replace(/<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">/g, '<th>');
  content = content.replace(/<th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">/g, '<th className="text-center">');
  content = content.replace(/<th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">/g, '<th className="text-right">');
  content = content.replace(/<th className="p-3 border-b">/g, '<th>');

  // Search input and sync buttons (header area)
  content = content.replace(/className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2"/g, 'className="gl-btn gl-btn-secondary"');
  content = content.replace(/className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"/g, 'className="gl-btn gl-btn-secondary"');
  content = content.replace(/className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors"/g, 'className="gl-btn gl-btn-secondary"');

  if (content !== original) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated ${filePath}`);
  }
}

for (const dir of dirs) {
  processDir(dir);
}
console.log('Done!');
