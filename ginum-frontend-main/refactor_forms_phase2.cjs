const fs = require('fs');
const path = require('path');

const srcDir = path.join(__dirname, 'src', 'components');

function refactorFile(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');
  let originalContent = content;

  // Replace inputClass and labelClass
  content = content.replace(/const inputClass = ".*?";/gs, 'const inputClass = "gl-input";');
  content = content.replace(/const labelClass = ".*?";/gs, 'const labelClass = "gl-label";');
  
  // Replace card wrappers
  content = content.replace(/className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden"/g, 'className="gl-card mb-6"');
  content = content.replace(/className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"/g, 'className="gl-card mb-6"');
  content = content.replace(/className="bg-white shadow-md rounded-2xl p-6"/g, 'className="gl-card p-6 mb-6"');
  content = content.replace(/className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"/g, 'className="gl-card mb-6"');

  // Replace section headers
  content = content.replace(/className="font-bold text-gray-800 text-sm flex items-center gap-1\.5"/g, 'className="gl-card-title flex items-center gap-1.5"');
  content = content.replace(/className="font-bold text-slate-800 text-sm flex items-center gap-1\.5"/g, 'className="gl-card-title flex items-center gap-1.5"');
  content = content.replace(/className="font-semibold text-slate-800 text-sm mb-4"/g, 'className="gl-card-title mb-4"');

  // Replace Buttons
  content = content.replace(/className="px-8 py-2\.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-sm shadow-blue-500\/20 disabled:bg-blue-300 disabled:cursor-not-allowed cursor-pointer"/g, 'className="gl-btn gl-btn-primary flex items-center gap-2"');
  content = content.replace(/className="px-6 py-2\.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold text-sm transition-colors cursor-pointer"/g, 'className="gl-btn gl-btn-secondary"');
  content = content.replace(/className="px-6 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition-colors disabled:opacity-50"/g, 'className="gl-btn gl-btn-secondary"');
  content = content.replace(/className="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors disabled:opacity-50 flex items-center gap-2"/g, 'className="gl-btn gl-btn-primary flex items-center gap-2"');
  content = content.replace(/className="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 font-medium"/g, 'className="gl-btn gl-btn-primary flex items-center gap-2"');
  
  if (content !== originalContent) {
    fs.writeFileSync(filePath, content);
    console.log(`Refactored forms/buttons in: ${filePath}`);
  }
}

function traverseDir(dir) {
  fs.readdirSync(dir).forEach(file => {
    let fullPath = path.join(dir, file);
    if (fs.lstatSync(fullPath).isDirectory()) {
      traverseDir(fullPath);
    } else if (fullPath.endsWith('.jsx')) {
      refactorFile(fullPath);
    }
  });
}

traverseDir(srcDir);
