import os
import re

def update_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content
    
    # Let's replace the wrapper for search inputs
    # From:
    # <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6">
    #   <div className="relative w-full md:max-w-md">
    # To:
    # <div className="list-toolbar mb-6">
    #   <div className="list-toolbar-search relative">
    
    new_content = new_content.replace(
        '<div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6">\n        <div className="relative w-full md:max-w-md">',
        '<div className="list-toolbar mb-6">\n        <div className="list-toolbar-search relative">'
    )
    new_content = new_content.replace(
        '<div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">\n          <div className="relative w-full md:max-w-md">',
        '<div className="list-toolbar">\n          <div className="list-toolbar-search relative">'
    )
    new_content = new_content.replace(
        '<div className="bg-white rounded-t-xl shadow-sm border-b border-gray-100 p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">\n        <div className="flex flex-col gap-2 md:flex-row md:items-center md:w-auto md:flex-none flex-1">\n          <div className="relative w-full md:max-w-md">',
        '<div className="list-toolbar bg-white rounded-t-xl shadow-sm border-b border-gray-100 p-4">\n        <div className="list-toolbar-search relative">'
    )
    new_content = new_content.replace(
        '<div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6">\n        <div className="relative w-full md:max-w-md">',
        '<div className="list-toolbar bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">\n        <div className="list-toolbar-search relative">'
    )

    # Wrap buttons in <div className="list-toolbar-actions">
    # Match the button and replace it
    # We will look for: <button ... className="... w-full md:w-auto md:flex-none" ...> ... </button>
    
    def replacer(match):
        full_button = match.group(0)
        # Remove w-full md:w-auto md:flex-none
        modified_button = full_button.replace(' w-full md:w-auto md:flex-none', '')
        # Wrap it in list-toolbar-actions
        # Check if the next tag is </select> inside AllUsers.jsx or RequestsPage.jsx
        return f'<div className="list-toolbar-actions">\n{modified_button}\n          </div>'
        
    # BUT we need to handle cases where there are other elements in the right side like the select dropdown in AllUsers.jsx
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated wrappers in {filepath}")

def main():
    target_files = [
        "src/components/Employee/AllEmployeePage.jsx",
        "src/components/customer/CustomersList.jsx",
        "src/components/projects/AllProject.jsx",
        "src/components/users/AllUsers.jsx",
        "src/components/account/AllAccounts.jsx",
        "src/components/Inventory/InventoryDashboard.jsx",
        "src/components/supplier/AllPurchases.jsx",
        "src/components/customer/AllSales.jsx",
        "src/components/requests/RequestsPage.jsx",
        "src/components/department/DepartmentsList.jsx"
    ]
    for filepath in target_files:
        if os.path.exists(filepath):
            update_file(filepath)

if __name__ == '__main__':
    main()
