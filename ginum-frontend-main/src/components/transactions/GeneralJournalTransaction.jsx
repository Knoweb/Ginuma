import React, { useState, useEffect } from "react";
import { MdOutlineCancel, MdAddCircleOutline } from "react-icons/md";
import { FaTimes } from "react-icons/fa";
import { useNavigate } from "react-router-dom"; // Add this line
import AddAccountForm from "../account/AddAccountForm";
import NewProjectForm from "../projects/NewProjectForm";
import api from "../../utils/api";

const CreateGeneralJournalTransaction = () => {
  const navigate = useNavigate();

  const [rows, setRows] = useState([
    {
      account: "",
      debit: "",
      credit: "",
      job: "",
      quantity: "",
      description: "",
      project: "", // Make sure this matches your row structure
    },
  ]);
  const [showAccountModal, setShowAccountModal] = useState(false);
  const [showProjectModal, setShowProjectModal] = useState(false);
  const [referenceNumber, setReferenceNumber] = useState("");
  const [transactionDate, setTransactionDate] = useState("2025-03-14"); // Added state for Date
  const [transactionDescription, setTransactionDescription] = useState(""); // Added state for Description
  const [modalTransition, setModalTransition] = useState("opacity-0 invisible");
  
  // Accounts states
  const [accounts, setAccounts] = useState([]);
  const [accountsError, setAccountsError] = useState(null);
  const [isLoadingAccounts, setIsLoadingAccounts] = useState(true);

  // Projects states (New)
  const [projects, setProjects] = useState([]);
  const [projectsError, setProjectsError] = useState(null);
  const [isLoadingProjects, setIsLoadingProjects] = useState(true);

  const [totalDebit, setTotalDebit] = useState(0);
  const [totalCredit, setTotalCredit] = useState(0);
  const [tax, setTax] = useState(0);
  const [outOfBalance, setOutOfBalance] = useState(0);

  // Recalculate totals when rows change
  useEffect(() => {
    let debit = 0;
    let credit = 0;

    rows.forEach((row) => {
      const debitValue = parseFloat(row.debit);
      const creditValue = parseFloat(row.credit);

      if (!isNaN(debitValue)) debit += debitValue;
      if (!isNaN(creditValue)) credit += creditValue;
    });

    const calculatedTax = 0; // Tax logic
    const difference = Math.abs(debit - credit);

    setTotalDebit(debit);
    setTotalCredit(credit);
    setTax(calculatedTax);
    setOutOfBalance(difference);
  }, [rows]);

  useEffect(() => {
    if (showAccountModal || showProjectModal) {
      setModalTransition("opacity-100 visible");
    } else {
      setModalTransition("opacity-0 invisible");
    }
  }, [showAccountModal, showProjectModal]);

  const handleModalClick = (e, setModal) => {
    if (e.target === e.currentTarget) {
      setModal(false);
    }
  };

  // Fetch Accounts from API
  useEffect(() => {
    const fetchAccounts = async () => {
      try {
        setIsLoadingAccounts(true);
        setAccountsError(null);

        const companyId = sessionStorage.getItem("companyId");
        if (!companyId) throw new Error("Company ID not found");

        const response = await api.get(`/api/companies/${companyId}/accounts`);
        let accountsData = response.data || response;

        if (response?.data?.data && Array.isArray(response.data.data)) {
          accountsData = response.data.data;
        }

        if (!Array.isArray(accountsData)) throw new Error("Invalid format");

        const formattedAccounts = accountsData.map((account) => ({
          id: account.id,
          name: `${account.accountCode} - ${account.accountName}`,
          accountType: account.accountType,
          currentBalance: account.currentBalance,
          accountCode: account.accountCode,
        }));

        setAccounts(formattedAccounts);
      } catch (error) {
        setAccountsError(error.message);
        setAccounts([]);
      } finally {
        setIsLoadingAccounts(false);
      }
    };

    fetchAccounts();
  }, []);

  // Fetch Projects from API
  useEffect(() => {
    const fetchProjects = async () => {
      try {
        setIsLoadingProjects(true);
        setProjectsError(null);

        const companyId = sessionStorage.getItem("companyId");
        const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

        if (!companyId || !token) throw new Error("Credentials not found");

        const response = await api.get(`/api/companies/${companyId}/projects`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        let projectsData = response.data || response;

        if (projectsData?.data && Array.isArray(projectsData.data)) {
            projectsData = projectsData.data;
        }

        if (!Array.isArray(projectsData)) throw new Error("Invalid format");

        console.log("Fetched Projects:", projectsData); 
        setProjects(projectsData);
      } catch (error) {
        console.error("Error fetching projects:", error);
        setProjectsError("Failed to load projects.");
        setProjects([]);
      } finally {
        setIsLoadingProjects(false);
      }
    };

    fetchProjects();
  }, []);

  const handleRowChange = (index, field, value) => {
    const updatedRows = [...rows];

    if (field === "debit" && value.trim() !== "") {
      updatedRows[index].credit = "";
    } else if (field === "credit" && value.trim() !== "") {
      updatedRows[index].debit = "";
    }

    updatedRows[index][field] = value;

    if (index === rows.length - 1 && value.trim() !== "") {
      updatedRows.push({
        account: "",
        debit: "",
        credit: "",
        job: "",
        quantity: "",
        description: "",
        project: "",
      });
    }

    setRows(updatedRows);
  };

  const removeRow = (index) => {
    const updatedRows = rows.filter((_, i) => i !== index);
    setRows(updatedRows);
  };

  const handleSaveTransaction = async () => {
    const companyId = sessionStorage.getItem("companyId");
    const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

    const validEntries = rows.filter(row => row.account !== "" && (row.debit !== "" || row.credit !== ""));
    
    if (validEntries.length === 0) {
      alert("Please add at least one valid entry with an account and amount.");
      return;
    }

    const transactionData = {
        referenceNumber,
        date: transactionDate,
        description: transactionDescription,
        entries: validEntries,
        totalDebit,
        totalCredit
    };

    console.log("Payload going to Backend:", transactionData);

    try {
      const response = await api.post(`/api/transactions/companies/${companyId}`, transactionData, {
        headers: { 
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json' 
        }
      });

      console.log("Backend Response:", response);
      alert("Transaction saved successfully!");
      
      navigate("/transactions/all");
      
    } catch (error) {
      console.error("Save failed (Full Error):", error);
      
      if (error.response) {
          alert(`Failed to save: ${error.response.data?.message || error.response.statusText || 'Server Error'}`);
      } else if (error.request) {
          alert("Failed to connect to the server. Check your network or CORS policy.");
      } else {
          alert("An unexpected error occurred.");
      }
    }
  };

  return (
    <div className="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-4 sm:p-6 my-4 sm:mt-6">
      <h2 className="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">
        Create Transactions
      </h2>

      {/* Reference Number */}
      <div className="mb-6">
        <label className="block text-gray-700 font-medium">
          Reference Number <span className="text-red-500">*</span>
        </label>
        <input
          type="text"
          value={referenceNumber}
          onChange={(e) => setReferenceNumber(e.target.value)}
          className=" px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
          placeholder="Enter Reference Number"
          required
        />
      </div>

      {/* Date and Description */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
          <label className="block text-gray-700 font-medium">
            Date <span className="text-red-500">*</span>
          </label>
          <input
            type="date"
            value={transactionDate}
            onChange={(e) => setTransactionDate(e.target.value)}
            className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
          />
        </div>
        <div>
          <label className="block text-gray-700 font-medium">
            Description of Transaction <span className="text-red-500">*</span>
          </label>
          <textarea
            value={transactionDescription}
            onChange={(e) => setTransactionDescription(e.target.value)}
            className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            rows={2}
            placeholder="Description"
          ></textarea>
        </div>
      </div>

      {/* Table for Debit and Credit Entries */}
      <div className="mb-6 overflow-x-auto">
        <table className="w-full rounded-lg">
          <thead>
            <tr className="bg-gray-100 text-gray-700 text-sm">
              <th className="p-2">
                Account <span className="text-red-500">*</span>
                <button
                  onClick={() => setShowAccountModal(true)}
                  className="ml-1 text-blue-600 hover:text-blue-700"
                >
                  <MdAddCircleOutline className="h-5 w-5 inline" />
                </button>
              </th>
              <th className="p-2">Debit ($) <span className="text-red-500">*</span></th>
              <th className="p-2">Credit ($) <span className="text-red-500">*</span></th>
              <th className="p-2">Quantity</th>
              <th className="p-2">Description</th>
              <th className="p-2">
                Project
                <button
                  onClick={() => setShowProjectModal(true)}
                  className="ml-1 text-blue-600 hover:text-blue-700"
                >
                  <MdAddCircleOutline className="h-5 w-5 inline" />
                </button>
              </th>
              <th className="p-2"></th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row, index) => (
              <tr key={index}>
                <td className="p-2">
                  <select
                    value={row.account}
                    onChange={(e) => handleRowChange(index, "account", e.target.value)}
                    className="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    disabled={isLoadingAccounts}
                  >
                    <option value="">Select Account</option>
                    {isLoadingAccounts ? (
                      <option value="">Loading accounts...</option>
                    ) : accountsError ? (
                      <option value="">Error loading accounts</option>
                    ) : accounts.length === 0 ? (
                      <option value="">No accounts available</option>
                    ) : (
                      accounts.map((account) => (
                        <option key={account.id} value={account.id}>
                          {account.name}
                        </option>
                      ))
                    )}
                  </select>
                </td>
                <td className="p-2">
                  <input
                    type="number"
                    className={`w-full px-3 py-2 border rounded-lg focus:ring-2 text-sm sm:text-base ${
                      row.credit.trim() !== "" ? "bg-gray-100 cursor-not-allowed" : "focus:ring-blue-500"
                    }`}
                    placeholder="Debit"
                    value={row.debit}
                    onChange={(e) => handleRowChange(index, "debit", e.target.value)}
                    disabled={row.credit.trim() !== ""}
                  />
                </td>
                <td className="p-2">
                  <input
                    type="number"
                    className={`w-full px-3 py-2 border rounded-lg focus:ring-2 text-sm sm:text-base ${
                      row.debit.trim() !== "" ? "bg-gray-100 cursor-not-allowed" : "focus:ring-blue-500"
                    }`}
                    placeholder="Credit"
                    value={row.credit}
                    onChange={(e) => handleRowChange(index, "credit", e.target.value)}
                    disabled={row.debit.trim() !== ""}
                  />
                </td>
                <td className="p-2">
                  <input
                    type="number"
                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    placeholder="Quantity"
                    value={row.quantity}
                    onChange={(e) => handleRowChange(index, "quantity", e.target.value)}
                  />
                </td>
                <td className="p-2">
                  <input
                    type="text"
                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    placeholder="Description"
                    value={row.description}
                    onChange={(e) => handleRowChange(index, "description", e.target.value)}
                  />
                </td>
                <td className="p-2">
                  <select
                    value={row.project}
                    onChange={(e) => handleRowChange(index, "project", e.target.value)}
                    className="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base w-32"
                    disabled={isLoadingProjects}
                  >
                    <option value="">Select project</option>
                    {isLoadingProjects ? (
                      <option value="" disabled>Loading...</option>
                    ) : projectsError ? (
                      <option value="" disabled>Error</option>
                    ) : projects.length === 0 ? (
                      <option value="" disabled>No projects</option>
                    ) : (
                      projects.map((project) => (
                        <option key={project.id} value={project.id}>
                          {project.name}
                        </option>
                      ))
                    )}
                  </select>
                </td>
                <td className="p-2">
                  {index !== rows.length - 1 && (
                    <button
                      onClick={() => removeRow(index)}
                      className="text-red-500 hover:text-red-700"
                    >
                      <MdOutlineCancel className="h-5 w-5" />
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Totals Section */}
      <div className="mb-6">
        <p className="text-sm font-medium text-gray-700">Total debit: ${totalDebit.toFixed(2)}</p>
        <p className="text-sm font-medium text-gray-700">Total credit: ${totalCredit.toFixed(2)}</p>
        <p className="text-sm font-medium text-gray-700">Tax: ${tax.toFixed(2)}</p>
        <p className={`text-sm font-medium ${outOfBalance !== 0 ? "text-red-600" : "text-green-600"}`}>
          Out of balance: ${outOfBalance.toFixed(2)}
        </p>
      </div>

      {/* Save Button */}
      <div className="flex justify-end space-x-2">
        {outOfBalance > 0 && (
          <p className="text-red-600 font-medium mt-2 mr-4">
            Your entry is out of balance. Ensure total debit equals total credit.
          </p>
        )}
        <button
          disabled={outOfBalance > 0 || totalDebit === 0}
          onClick={handleSaveTransaction}
          className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition-colors"
        >
          Save Transaction
        </button>
      </div>

      {/* Modals */}
      {showAccountModal && (
        <div
          className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-500 ${modalTransition}`}
          onClick={(e) => handleModalClick(e, setShowAccountModal)}
        >
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 p-2 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              className="absolute top-2 right-2 text-black-600 text-xl"
              onClick={() => setShowAccountModal(false)}
            >
              <FaTimes />
            </button>
            <AddAccountForm />
          </div>
        </div>
      )}

      {showProjectModal && (
        <div
          className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-500 ${modalTransition}`}
          onClick={(e) => handleModalClick(e, setShowProjectModal)}
        >
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 p-2 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              className="absolute top-2 right-2 text-black-600 text-xl"
              onClick={() => setShowProjectModal(false)}
            >
              <FaTimes />
            </button>
            <NewProjectForm />
          </div>
        </div>
      )}
    </div>
  );
};

export default CreateGeneralJournalTransaction;