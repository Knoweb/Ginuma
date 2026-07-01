import React, { useState, useEffect } from "react";
import { MdSearch, MdAccountBalanceWallet, MdRefresh } from "react-icons/md";
import { apiUrl } from "../../utils/api";

function BankReconsilation() {
  const [transactions, setTransactions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");

  useEffect(() => {
    fetchTransactions();
  }, []);

  const fetchTransactions = async () => {
    setLoading(true);
    try {
      const companyId = sessionStorage.getItem("companyId");
      const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");
      
      const response = await fetch(`${apiUrl}/api/transactions/companies/${companyId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      
      if (response.ok) {
        const data = await response.json();
        
        console.log("Data from Backend:", data); 
        
        setTransactions(Array.isArray(data) ? data : []);
      } else {
        console.error("Failed to fetch transactions");
      }
    } catch (error) {
      console.error("Error connecting to API:", error);
    } finally {
      setLoading(false);
    }
  };

  const filteredTransactions = transactions.filter(t => 
    (t.description || "").toLowerCase().includes(searchTerm.toLowerCase()) ||
    (t.referenceNumber || "").toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="max-w-7xl mx-auto p-4 sm:p-6 mt-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2">
            <MdAccountBalanceWallet className="text-blue-600" />
            Bank Reconciliation
          </h1>
          <p className="text-gray-500 text-sm mt-1">Review and reconcile your recent bank transactions.</p>
        </div>
        
        <div className="flex items-center gap-3 w-full sm:w-auto">
          <div className="relative w-full sm:w-64">
            <MdSearch className="absolute left-3 top-3 text-gray-400 text-xl" />
            <input 
              type="text" 
              placeholder="Search transactions..." 
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
            />
          </div>
          <button 
            onClick={fetchTransactions}
            className="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors border border-gray-300"
            title="Refresh Data"
          >
            <MdRefresh className={`text-xl ${loading ? "animate-spin" : ""}`} />
          </button>
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs tracking-wider">
                <th className="px-6 py-4 font-semibold">Date</th>
                <th className="px-6 py-4 font-semibold">Reference</th>
                <th className="px-6 py-4 font-semibold">Description</th>
                <th className="px-6 py-4 font-semibold">Type</th>
                <th className="px-6 py-4 font-semibold text-right">Amount ($)</th>
              </tr>
            </thead>
            
            <tbody className="divide-y divide-gray-100">
              {loading ? (
                <tr>
                  <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                    <div className="flex flex-col items-center justify-center">
                      <div className="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                      <p>Loading transactions...</p>
                    </div>
                  </td>
                </tr>
              ) : filteredTransactions.length > 0 ? (
                filteredTransactions.map((t) => {
                  const transactionType = t.transactionType || t.type || "UNKNOWN";
                  const rawAmount = transactionType === "SPEND" 
                    ? (t.totalCredit || t.totalDebit || 0) 
                    : (t.totalDebit || t.totalCredit || 0);
                  const formattedAmount = parseFloat(rawAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                  return (
                    <tr key={t.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {t.date || "-"}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {t.referenceNumber || "-"}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        {t.description || "No description"}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`px-3 py-1 rounded-full text-xs font-medium ${
                          transactionType === "RECEIVE" ? "bg-green-100 text-green-800" : 
                          transactionType === "SPEND" ? "bg-red-100 text-red-800" : 
                          "bg-gray-100 text-gray-800"
                        }`}>
                          {transactionType}
                        </span>
                      </td>
                      <td className={`px-6 py-4 whitespace-nowrap text-sm font-bold text-right ${
                        transactionType === "RECEIVE" ? "text-green-600" : 
                        transactionType === "SPEND" ? "text-red-600" : 
                        "text-gray-900"
                      }`}>
                        {transactionType === "SPEND" ? "-" : "+"} {formattedAmount}
                      </td>
                    </tr>
                  );
                })
              ) : (
                <tr>
                  <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                    <div className="flex flex-col items-center justify-center">
                      <MdAccountBalanceWallet className="text-4xl text-gray-300 mb-2" />
                      <p className="text-lg font-medium text-gray-600">No transactions found</p>
                    </div>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
        
        {!loading && filteredTransactions.length > 0 && (
          <div className="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-600">
            <span>Showing {filteredTransactions.length} transactions</span>
            <span className="font-semibold text-gray-800">
              Total Balance Impact: ${filteredTransactions.reduce((acc, t) => {
                const transactionType = t.transactionType || t.type || "UNKNOWN";
                const amount = parseFloat(t.totalDebit || t.totalCredit || 0);
                return acc + (transactionType === "SPEND" ? -amount : amount);
              }, 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
            </span>
          </div>
        )}
      </div>
    </div>
  );
}

export default BankReconsilation;