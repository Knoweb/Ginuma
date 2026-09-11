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

  // Additional Labels
  content = content.replace(/className="block text-sm font-semibold text-gray-700 mb-1\.5"/g, 'className="gl-label"');
  content = content.replace(/className="block text-sm font-semibold text-gray-700"/g, 'className="gl-label"');

  // Additional Inputs
  content = content.replace(/className="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none transition-all cursor-pointer"/g, 'className="gl-input cursor-pointer"');
  content = content.replace(/className="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 outline-none transition-all"/g, 'className="gl-input"');
  content = content.replace(/className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none transition-all"/g, 'className="gl-input"');
  content = content.replace(/className="w-full p-2 border border-gray-300 rounded bg-gray-50 focus:outline-none text-right font-medium text-gray-600 cursor-not-allowed"/g, 'className="gl-input bg-slate-50 text-right cursor-not-allowed text-slate-500"');
  content = content.replace(/className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none transition-all text-right font-medium"/g, 'className="gl-input text-right"');

  // Cards & Layouts in forms
  content = content.replace(/className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4"/g, 'className="gl-card p-6 space-y-4"');
  content = content.replace(/className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex flex-col"/g, 'className="gl-card p-6 flex flex-col"');
  content = content.replace(/className="p-6 bg-gray-50 min-h-screen space-y-6 max-w-full overflow-x-hidden"/g, 'className="p-6 lg:p-8 flex flex-col gap-8 max-w-full overflow-x-hidden"');
  content = content.replace(/className="text-3xl font-bold text-gray-800 flex items-center gap-2"/g, 'className="gl-heading flex items-center gap-2"');

  // Buttons
  content = content.replace(/className="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 font-semibold flex items-center gap-1\.5 transition-colors cursor-pointer"/g, 'className="gl-btn gl-btn-secondary"');
  content = content.replace(/className="px-6 py-2\.5 rounded-lg text-sm font-bold transition-all cursor-pointer bg-white text-blue-600 shadow-sm"/g, 'className="gl-btn gl-btn-secondary text-indigo-600 bg-white"');
  content = content.replace(/className="px-6 py-2\.5 rounded-lg text-sm font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900"/g, 'className="gl-btn text-slate-500 hover:text-slate-800"');
  content = content.replace(/className="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold flex items-center gap-2 transition-all shadow-md hover:shadow-lg disabled:opacity-70"/g, 'className="gl-btn gl-btn-primary px-8"');

  if (content !== original) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated Forms (pass 2) in ${filePath}`);
  }
}

for (const dir of dirs) {
  processDir(dir);
}
console.log('Forms refactor 2 done!');
