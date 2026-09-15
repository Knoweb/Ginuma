const fs = require('fs');
const path = require('path');

const filesToCheck = [
"src/components/account/AllAccounts.jsx",
"src/components/bank/BankReconsilation.jsx",
"src/components/bank/ReceiveMoney.jsx",
"src/components/bank/SpendMoney.jsx",
"src/components/customer/AgedReceivables.jsx",
"src/components/customer/AllSales.jsx",
"src/components/customer/CustomersList.jsx",
"src/components/department/DepartmentsList.jsx",
"src/components/depreciation/Depreciation.jsx",
"src/components/Inventory/InventoryDashboard.jsx",
"src/components/projects/AllProject.jsx",
"src/components/projects/NewProjectForm.jsx",
"src/components/reports/BalacenSheet.jsx",
"src/components/reports/Cashflow.jsx",
"src/components/reports/GeneralLedger.jsx",
"src/components/reports/IncomeStatement.jsx",
"src/components/reports/TrialBalance.jsx",
"src/components/requests/RequestsPage.jsx",
"src/components/super-admin/AllCompanies.jsx",
"src/components/super-admin/CompanyRequests.jsx",
"src/components/super-admin/Dashboard.jsx",
"src/components/supplier/AgedPayables.jsx",
"src/components/supplier/AllPurchases.jsx",
"src/components/supplier/SuppliersList.jsx",
"src/components/transactions/AllTranactions.jsx",
"src/components/transactions/GeneralJournalTransaction.jsx",
"src/components/users/AddUserForm.jsx",
"src/components/users/AllUsers.jsx",
"src/pages/CompanyProfile/CompanyProfile.jsx",
"src/pages/SettingsPage/SettingsPage.jsx",
"src/components/Employee/AddEmployeeForm.jsx",
"src/components/Employee/AllEmployeePage.jsx",
"src/components/Employee/EditEmployeeForm.jsx",
"src/components/account/AddAccountForm.jsx",
"src/components/department/AddDepartmentForm.jsx"
];

for (let file of filesToCheck) {
  let p = path.join(__dirname, file);
  if (!fs.existsSync(p)) continue;
  let content = fs.readFileSync(p, 'utf8');

  if (!content.includes('PageHeader')) {
    console.log(file + ' does not import PageHeader. Might need update.');
  }
}
