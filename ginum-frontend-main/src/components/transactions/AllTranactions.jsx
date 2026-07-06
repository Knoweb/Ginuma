import React, { useState, useEffect } from "react";
import { FiSearch, FiDownload, FiFilter, FiX } from "react-icons/fi";
import api from "../../utils/api";
import { useNavigate } from "react-router-dom";

function AllTransactions() {
  const [transactions, setTransactions] = useState([]);
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedTransaction, setSelectedTransaction] = useState(null);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [jeDetails, setJeDetails] = useState(null);
  const [loadingJe, setLoadingJe] = useState(false);

  const handleRowClick = async (tx) => {
    setSelectedTransaction(tx);
    setIsModalOpen(true);
    setJeDetails(null);

    const jeId = tx.journalEntryId || tx.jeId || tx.journalId;
    if (jeId) {
      try {
        setLoadingJe(true);
        const companyId = sessionStorage.getItem("companyId");
        const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");
        const response = await api.get(`/api/companies/${companyId}/journal-entries/${jeId}`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        setJeDetails(response);
      } catch (err) {
        console.error("Error fetching JE details:", err);
        setJeDetails(null);
      } finally {
        setLoadingJe(false);
      }
    } else {
      setJeDetails(null);
    }
  };

  const fetchTransactions = async () => {
    try {
      setLoading(true);
      setError(null);

      const companyId = sessionStorage.getItem("companyId");
      const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

      if (!companyId || !token) {
        throw new Error("Authentication required. Please log in.");
      }

      const transactions = await api.get(
        `/api/transactions/companies/${companyId}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      console.log(transactions);

      setTransactions(Array.isArray(transactions) ? transactions : []);

    } catch (err) {
      console.error("Error fetching transactions:", err);
      setError("Failed to load transactions.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTransactions();
  }, [window.location.pathname]);

  const filteredTransactions = transactions.filter(tx => {
    const query = searchQuery.toLowerCase();
    return (
      (tx.description && tx.description.toLowerCase().includes(query)) ||
      (tx.referenceNumber && tx.referenceNumber.toLowerCase().includes(query)) ||
      (tx.date && tx.date.includes(query))
    );
  });

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <div className="max-w-6xl mx-auto">
        {/* Header Section */}
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-3xl font-bold text-gray-800">All Transactions</h1>
          <button
            onClick={() => navigate("/transactions/new")}
            className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
          >
            <FiDownload /> Export
          </button>
        </div>

        {/* Error Message */}
        {error && (
          <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {error}
          </div>
        )}

        {/* Filters Section */}
        <div className="bg-white p-4 rounded-lg shadow-sm mb-6 flex gap-4">
          <div className="relative flex-grow">
            <FiSearch className="absolute left-3 top-3 text-gray-400" />
            <input
              type="text"
              placeholder="Search by Reference, Description or Date..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <button className="flex items-center gap-2 px-4 py-2 border rounded-lg hover:bg-gray-100 transition">
            <FiFilter /> Filter
          </button>
        </div>

        {/* Table Section */}
        <div className="bg-white shadow rounded-lg overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-100">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">JE Number</th>
                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                <th className="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                <th className="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {loading ? (
                <tr>
                  <td colSpan="5" className="text-center py-8">
                    <div className="flex justify-center items-center">
                      <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                      <span className="ml-2 text-gray-500">Loading transactions...</span>
                    </div>
                  </td>
                </tr>
              ) : filteredTransactions.length > 0 ? (
                filteredTransactions.map((tx, index) => (
                  <tr
                    key={index}
                    className="hover:bg-gray-50 transition-colors cursor-pointer"
                    onClick={() => handleRowClick(tx)}
                  >
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{tx.date || "-"}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">{tx.journalEntryNumber || tx.journalNumber || tx.jeNumber || tx.referenceNumber || tx.refNumber || "-"}</td>
                    <td className="px-6 py-4 text-sm text-gray-700">{tx.description || "-"}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                      ${parseFloat(tx.totalDebit || tx.amount || 0).toFixed(2)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-center">
                      <span className="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        {tx.status || 'Completed'}
                      </span>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="5" className="text-center py-8 text-gray-500">
                    No transactions found.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
          <div className="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
            <div className="flex justify-between items-center p-6 border-b">
              <h2 className="text-2xl font-bold text-gray-800">Journal Entry Details</h2>
              <button onClick={() => setIsModalOpen(false)} className="text-gray-500 hover:text-gray-700">
                <FiX size={24} />
              </button>
            </div>
            <div className="p-6 overflow-y-auto">
              {loadingJe ? (
                <div className="flex justify-center items-center py-12">
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <span className="ml-2 text-gray-500">Loading details...</span>
                </div>
              ) : jeDetails ? (
                <div>
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                      <p className="text-sm text-gray-500 font-semibold">JE Number</p>
                      <p className="text-gray-900 font-medium">{jeDetails.journalEntryNumber || jeDetails.journalNumber || jeDetails.jeNumber || jeDetails.referenceNumber || jeDetails.refNumber || "-"}</p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500 font-semibold">Date</p>
                      <p className="text-gray-900 font-medium">{jeDetails.date || "-"}</p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500 font-semibold">Status</p>
                      <p className="text-gray-900">
                        <span className="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                          {jeDetails.status || "Completed"}
                        </span>
                      </p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500 font-semibold">Description</p>
                      <p className="text-gray-900 font-medium">{jeDetails.description || "-"}</p>
                    </div>
                  </div>

                  {jeDetails.lines && jeDetails.lines.length > 0 ? (
                    <div className="bg-white border rounded-lg overflow-hidden">
                      <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                          <tr>
                            <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Account Code</th>
                            <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Account Name</th>
                            <th className="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Debit</th>
                            <th className="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Credit</th>
                          </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                          {jeDetails.lines.map((line, idx) => (
                            <tr key={idx}>
                              <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{line.accountCode || "-"}</td>
                              <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{line.accountName || "-"}</td>
                              <td className="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">${parseFloat(line.debit || 0).toFixed(2)}</td>
                              <td className="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">${parseFloat(line.credit || 0).toFixed(2)}</td>
                            </tr>
                          ))}
                        </tbody>
                        <tfoot className="bg-gray-50">
                          <tr>
                            <td colSpan="2" className="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">Total</td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">${parseFloat(jeDetails.totalDebit || 0).toFixed(2)}</td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">${parseFloat(jeDetails.totalCredit || 0).toFixed(2)}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  ) : (
                    <div className="text-center py-8 text-gray-500 border rounded-lg bg-gray-50">
                      No journal entry lines available for this transaction.
                    </div>
                  )}
                </div>
              ) : (
                <div className="text-center py-8 text-gray-500">Journal entry is not linked to this transaction.</div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default AllTransactions;