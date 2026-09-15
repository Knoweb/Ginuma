import os

def replace_in_file(filepath, search_text, replace_text):
    if not os.path.exists(filepath):
        print(f"File not found: {filepath}")
        return
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    if search_text not in content:
        print(f"Search text not found in {filepath}")
        return
        
    content = content.replace(search_text, replace_text)
    
    # Add imports if not present
    if "import PageHeader" not in content:
        lines = content.split('\n')
        last_import = 0
        for i, line in enumerate(lines):
            if line.startswith("import "):
                last_import = i
                
        lines.insert(last_import + 1, 'import PageHeader from "../../components/common/PageHeader";')
        
        # also need to import the icon if we use one from react-icons/fi
        # But we'll just let the replacements handle their own icons if needed.
        content = '\n'.join(lines)
        
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated {filepath}")

# SettingsPage
replace_in_file(
    "src/pages/SettingsPage/SettingsPage.jsx",
    """      {/* Title */}
      <div>
        <h1 className="gl-heading flex items-center gap-2">
          <FaCog className="text-blue-600 text-2xl" />
          Account Settings
        </h1>
        <p className="text-sm text-gray-500 mt-1.5">
          Configure system formats, update credentials, and manage your account preference
        </p>
      </div>""",
    """      <PageHeader
        title="Account Settings"
        subtitle="Configure system formats, update credentials, and manage your account preference"
        icon={FaCog}
      />"""
)

# DepartmentsList
replace_in_file(
    "src/components/department/DepartmentsList.jsx",
    """        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
          <h1 className="gl-heading">
            Organization Setup
          </h1>

          <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <div className="relative flex-grow">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <FiSearch className="text-gray-400" />
              </div>

              <input
                type="text"
                placeholder={`Search ${activeTab}...`}
                className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>

            <button
              type="button"
              className="bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors whitespace-nowrap"
              onClick={fetchData}
            >
              <FiRefreshCw /> Refresh
            </button>

            <button
              type="button"
              className="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors whitespace-nowrap"
              onClick={handleAddClick}
            >
              <FiPlus />
              Add {activeTab === "departments" ? "Department" : "Designation"}
            </button>
          </div>
        </div>""",
    """        <PageHeader
          title="Organization Setup"
          subtitle="Manage company departments and structural hierarchy"
          icon={FiGrid}
          actions={
            <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
              <div className="relative flex-grow">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <FiSearch className="text-gray-400" />
                </div>
                <input
                  type="text"
                  placeholder={`Search ${activeTab}...`}
                  className="gl-input pl-10 w-full"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </div>
              <button type="button" onClick={handleAddClick} className="gl-btn gl-btn-primary">
                <FiPlus className="mr-1.5" />
                Add {activeTab === "departments" ? "Department" : "Designation"}
              </button>
              <button type="button" onClick={fetchData} className="gl-btn gl-btn-secondary" title="Refresh Data">
                <FiRefreshCw className={loading ? "animate-spin mr-1.5" : "mr-1.5"} /> Refresh
              </button>
            </div>
          }
        />"""
)

# We need to make sure FiGrid is imported for DepartmentsList
d_path = "src/components/department/DepartmentsList.jsx"
if os.path.exists(d_path):
    with open(d_path, 'r', encoding='utf-8') as f:
        d_content = f.read()
    if "FiGrid" not in d_content:
        d_content = d_content.replace("FiSearch,", "FiGrid, FiSearch,")
    with open(d_path, 'w', encoding='utf-8') as f:
        f.write(d_content)
