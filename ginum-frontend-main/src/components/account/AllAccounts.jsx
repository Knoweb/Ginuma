import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import {
  FiPlus,
  FiSearch,
  FiEdit,
  FiTrash2,
  FiRefreshCw,
  FiCreditCard,
  FiDollarSign,
  FiTag,
  FiLayers,
  FiHash,
} from "react-icons/fi";

const AllAccounts = () => {
  const navigate = useNavigate();

  const [accounts, setAccounts] = useState([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const fetchAccounts = async () => {
    try {
      setLoading(true);
      setError("");

      const companyId = sessionStorage.getItem("companyId");
      const token = sessionStorage.getItem("auth_token");

      console.log("Company ID:", companyId);
      console.log("Token:", token);

      if (!companyId || !token) {
        setError("Missing company ID or auth token. Please login again.");
        return;
      }

      const response = await fetch(
        `http://localhost:8081/api/companies/${companyId}/accounts`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error("Backend error:", errorText);
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();

      console.log("Accounts API Response:", data);

      setAccounts(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error("Failed to fetch accounts:", err);
      setError("Failed to fetch accounts. " + err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAccounts();
  }, []);

  const filteredAccounts = accounts.filter((account) => {
    const searchLower = searchTerm.toLowerCase();

    return (
      (account.accountName || "").toLowerCase().includes(searchLower) ||
      (account.subAccountName || "").toLowerCase().includes(searchLower) ||
      (account.accountType || "").toLowerCase().includes(searchLower) ||
      (account.accountCode || "").toLowerCase().includes(searchLower) ||
      String(account.currentBalance || "").includes(searchLower)
    );
  });

  const formatAccountType = (type) => {
    if (!type) return "-";

    return type
      .toString()
      .replace(/_/g, " ")
      .toLowerCase()
      .replace(/\b\w/g, (char) => char.toUpperCase());
  };

  const formatAmount = (amount) => {
    const value = Number(amount || 0);

    return value.toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  };

  const handleEdit = (account) => {
    console.log("Edit account:", account);
    alert("Edit function is not created yet.");
  };

  const handleDelete = (account) => {
    console.log("Delete account:", account);
    alert("Delete function is not created yet.");
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-4 mt-6">
        <p className="font-bold">Error</p>
        <p>{error}</p>

        <button
          type="button"
          onClick={fetchAccounts}
          className="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 transition-colors"
        >
          <FiRefreshCw /> Retry
        </button>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 className="text-3xl font-bold text-gray-800 mb-4 md:mb-0">
          Accounts
        </h1>

        <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
          <div className="relative flex-grow">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <FiSearch className="text-gray-400" />
            </div>

            <input
              type="text"
              placeholder="Search accounts..."
              className="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>

          <button
            type="button"
            onClick={() => navigate("/account/new")}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors"
          >
            <FiPlus /> Add Account
          </button>
        </div>
      </div>

      {filteredAccounts.length === 0 ? (
        <div className="bg-white rounded-lg shadow p-8 text-center">
          <div className="mx-auto h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-4">
            <FiCreditCard size={30} />
          </div>

          <p className="text-gray-600 text-lg">
            {accounts.length === 0
              ? "No accounts found."
              : "No matching accounts found."}
          </p>

          <button
            type="button"
            onClick={() => navigate("/account/new")}
            className="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center gap-2 transition-colors"
          >
            <FiPlus /> Add New Account
          </button>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Account
                  </th>

                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Account Code
                  </th>

                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>

                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                    Sub Account
                  </th>

                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Balance
                  </th>

                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>

              <tbody className="bg-white divide-y divide-gray-200">
                {filteredAccounts.map((account) => (
                  <tr
                    key={account.id}
                    className="hover:bg-gray-50 transition-colors"
                  >
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                          <FiCreditCard size={20} />
                        </div>

                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">
                            {account.accountName || "-"}
                          </div>

                          <div className="text-sm text-gray-500 flex items-center">
                            <FiTag className="mr-1" size={14} />
                            ID: {account.id || "-"}
                          </div>
                        </div>
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900 flex items-center">
                        <FiHash className="mr-2" size={14} />
                        {account.accountCode || "-"}
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900 flex items-center">
                        <FiLayers className="mr-2" size={14} />
                        {formatAccountType(account.accountType)}
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                      <div className="text-sm text-gray-900">
                        {account.subAccountName || "-"}
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap text-right">
                      <div className="text-sm font-semibold text-gray-900 flex items-center justify-end">
                        <FiDollarSign className="mr-1" size={14} />
                        {formatAmount(account.currentBalance)}
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div className="flex justify-end space-x-2">
                        <button
                          type="button"
                          onClick={() => handleEdit(account)}
                          className="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition-colors"
                          title="Edit"
                        >
                          <FiEdit size={18} />
                        </button>

                        <button
                          type="button"
                          onClick={() => handleDelete(account)}
                          className="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition-colors"
                          title="Delete"
                        >
                          <FiTrash2 size={18} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="bg-gray-50 px-6 py-3 text-sm text-gray-600">
            Showing {filteredAccounts.length} of {accounts.length} accounts
          </div>
        </div>
      )}
    </div>
  );
};

export default AllAccounts;