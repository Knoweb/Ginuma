import os
import re

directories = ['src/components', 'src/pages']
# We'll specifically look for the headers that match the exact structure we saw.
# <div className="flex (flex-col sm:flex-row |justify-between).*?border-b.*?>
#   <div>
#     <h1 className=".*text-3xl|gl-heading.*> (icon)? Title </h1>
#     <p> subtitle </p>
#   </div>
#   <button> action </button>
# </div>

# Actually, it's easier to manually do the 10 most important ones that were in my original filesToCheck list.

files_to_check = [
"src/components/Employee/AddEmployeeForm.jsx",
"src/components/account/AddAccountForm.jsx",
"src/components/department/AddDepartmentForm.jsx",
"src/components/projects/NewProjectForm.jsx",
"src/components/users/AddUserForm.jsx",
"src/components/bank/ReceiveMoney.jsx",
"src/components/bank/SpendMoney.jsx",
"src/components/transactions/GeneralJournalTransaction.jsx",
"src/pages/SettingsPage/SettingsPage.jsx"
]

for file_path in files_to_check:
    if os.path.exists(file_path):
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            # replace the button in SettingsPage
            
            # replace the button in AddUserForm
            
            # Since doing this purely by script is hard, we can just replace 'gl-btn-primary' with 'gl-btn-secondary' for Cancel/Back buttons.
            content = re.sub(r'className="([^"]*)gl-btn-primary([^"]*)"([^>]*)>\s*(?:<[^>]+>\s*)?(?:Cancel|Back|Cancel & Exit|Back to [a-zA-Z]+)\b', 
                 r'className="\1gl-btn-secondary\2"\3> \g<0>', content)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
