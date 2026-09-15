import os
import re

def update_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content

    # The user wants list toolbars to be:
    # <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    #   <div className="w-full md:max-w-md">
    #     search input
    #   </div>
    #   <button className="w-full md:w-auto md:flex-none">
    #     + Add...
    #   </button>
    # </div>
    
    # 1. Revert lg:flex-row back to md:flex-row in the toolbar wrapper
    new_content = new_content.replace(
        'className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"',
        'className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"'
    )
    new_content = new_content.replace(
        'className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-6"',
        'className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6"'
    )
    new_content = new_content.replace(
        'className="bg-white rounded-t-xl shadow-sm border-b border-gray-100 p-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"',
        'className="bg-white rounded-t-xl shadow-sm border-b border-gray-100 p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between"'
    )
    new_content = new_content.replace(
        'className="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between mb-6"',
        'className="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6"'
    )
    
    # 2. Update search input wrapper from 'relative flex-1 min-w-0' to 'relative w-full md:max-w-md'
    # For AllUsers.jsx:
    new_content = new_content.replace(
        '<div className="relative flex-1 min-w-0">\n          <FiSearch',
        '<div className="relative w-full md:max-w-md">\n          <FiSearch'
    )
    # For other list pages:
    new_content = new_content.replace(
        '<div className="relative flex-1 min-w-0">\n            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">',
        '<div className="relative w-full md:max-w-md">\n            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">'
    )
    new_content = new_content.replace(
        '<div className="relative flex-1 min-w-0">\n            <FaSearch className="absolute',
        '<div className="relative w-full md:max-w-md">\n            <FaSearch className="absolute'
    )
    new_content = new_content.replace(
        '<div className="relative flex-1 min-w-0">\n          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">',
        '<div className="relative w-full md:max-w-md">\n          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">'
    )

    # For RequestsPage.jsx
    new_content = new_content.replace(
        '<div className="flex flex-col gap-2 lg:flex-row lg:items-center lg:w-auto lg:flex-none flex-1">',
        '<div className="flex flex-col gap-2 md:flex-row md:items-center md:w-auto md:flex-none flex-1">'
    )

    # 3. Update buttons from lg:w-auto lg:flex-none to md:w-auto md:flex-none
    new_content = new_content.replace('w-full lg:w-auto lg:flex-none', 'w-full md:w-auto md:flex-none')
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

def main():
    target_files = [
        "src/components/supplier/SuppliersList.jsx",
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
