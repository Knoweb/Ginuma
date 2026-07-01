import React, { useState, useEffect } from "react";
import { FiPrinter, FiDownload, FiRefreshCw } from "react-icons/fi";
import { apiUrl } from "../../utils/api";

const BalanceSheet = () => {
  const [reportData, setReportData] = useState({
    assets: [],
    liabilities: [],
    equity: [],
    totalAssets: 0,
    totalLiabilities: 0,
    totalEquity: 0
  });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const fetchBalanceSheet = async () => {
    try {
      setLoading(true);
      setError("");

      const companyId = sessionStorage.getItem("companyId");
      const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

      if (!companyId || !token) {
        setError("Authentication missing. Please re-login.");
        return;
      }

      // ඔබගේ සැබෑ Database එකේ ඇති සියලුම ගිණුම් ලබා ගැනීම
      const response = await fetch(`${apiUrl}/api/companies/${companyId}/accounts`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        throw new Error("Failed to load Accounts data from database.");
      }

      const data = await response.json();
      const accountsList = Array.isArray(data) ? data : (data.data || []);

      // ගිණුම් වර්ග කිරීම සඳහා Arrays
      const assets = [];
      const liabilities = [];
      const equity = [];

      let totalAssets = 0;
      let totalLiabilities = 0;
      let totalEquity = 0;

      // ලබාගත් ගිණුම් ඒවායේ වර්ගය (accountType) අනුව වෙන් කිරීම
      accountsList.forEach(account => {
        const type = (account.accountType || "").toUpperCase();
        const balance = Number(account.currentBalance || 0);

        // Assets (වත්කම්)
        if (type.includes("ASSET") || type.includes("BANK") || type.includes("CASH") || type.includes("RECEIVABLE")) {
          assets.push({ accountName: account.accountName, balance });
          totalAssets += balance;
        } 
        // Liabilities (බැඳීම්)
        else if (type.includes("LIABILITY") || type.includes("PAYABLE") || type.includes("LOAN") || type.includes("CREDIT")) {
          liabilities.push({ accountName: account.accountName, balance });
          totalLiabilities += balance;
        } 
        // Equity (හිමිකම්)
        else if (type.includes("EQUITY") || type.includes("CAPITAL") || type.includes("RETAINED")) {
          equity.push({ accountName: account.accountName, balance });
          totalEquity += balance;
        }
        // ආදායම් සහ වියදම් (INCOME / EXPENSE) Balance Sheet එකට එන්නේ නැත.
      });

      // State එක අලුත් කිරීම (Mock data නැත)
      setReportData({
        assets,
        liabilities,
        equity,
        totalAssets,
        totalLiabilities,
        totalEquity
      });

    } catch (err) {
      console.error("Error fetching balance sheet:", err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchBalanceSheet();
  }, []);

  const formatCurrency = (amount) => {
    return Number(amount || 0).toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
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
        <button onClick={fetchBalanceSheet} className="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
          <FiRefreshCw /> Retry
        </button>
      </div>
    );
  }

  const totalLiabilitiesAndEquity = reportData.totalLiabilities + reportData.totalEquity;

  return (
    <div className="container mx-auto px-4 py-8 max-w-5xl">
      <div className="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
          <h1 className="text-3xl font-bold text-gray-800">Balance Sheet</h1>
          <p className="text-gray-500 mt-1">As of {new Date().toLocaleDateString()}</p>
        </div>
        <div className="flex gap-3 mt-4 md:mt-0">
          <button className="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 border">
            <FiPrinter /> Print
          </button>
          <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <FiDownload /> Export PDF
          </button>
        </div>
      </div>

      <div className="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <div className="p-6 sm:p-10">
          
          {/* ASSETS SECTION */}
          <div className="mb-8">
            <h2 className="text-xl font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4 uppercase">Assets</h2>
            <table className="w-full">
              <tbody>
                {reportData.assets.length > 0 ? (
                  reportData.assets.map((item, index) => (
                    <tr key={index} className="hover:bg-gray-50">
                      <td className="py-2 text-gray-700">{item.accountName}</td>
                      <td className="py-2 text-right text-gray-900 font-medium">${formatCurrency(item.balance)}</td>
                    </tr>
                  ))
                ) : (
                  <tr><td colSpan="2" className="py-2 text-gray-400 italic">No asset accounts found.</td></tr>
                )}
              </tbody>
              <tfoot>
                <tr className="border-t border-gray-300 font-bold text-lg">
                  <td className="py-3 text-gray-800">Total Assets</td>
                  <td className="py-3 text-right text-blue-700">${formatCurrency(reportData.totalAssets)}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          {/* LIABILITIES SECTION */}
          <div className="mb-8">
            <h2 className="text-xl font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4 uppercase">Liabilities</h2>
            <table className="w-full">
              <tbody>
                {reportData.liabilities.length > 0 ? (
                  reportData.liabilities.map((item, index) => (
                    <tr key={index} className="hover:bg-gray-50">
                      <td className="py-2 text-gray-700">{item.accountName}</td>
                      <td className="py-2 text-right text-gray-900 font-medium">${formatCurrency(item.balance)}</td>
                    </tr>
                  ))
                ) : (
                  <tr><td colSpan="2" className="py-2 text-gray-400 italic">No liability accounts found.</td></tr>
                )}
              </tbody>
              <tfoot>
                <tr className="border-t border-gray-200 font-semibold">
                  <td className="py-3 text-gray-800">Total Liabilities</td>
                  <td className="py-3 text-right text-gray-900">${formatCurrency(reportData.totalLiabilities)}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          {/* EQUITY SECTION */}
          <div className="mb-6">
            <h2 className="text-xl font-bold text-gray-800 border-b-2 border-gray-300 pb-2 mb-4 uppercase">Equity</h2>
            <table className="w-full">
              <tbody>
                {reportData.equity.length > 0 ? (
                  reportData.equity.map((item, index) => (
                    <tr key={index} className="hover:bg-gray-50">
                      <td className="py-2 text-gray-700">{item.accountName}</td>
                      <td className="py-2 text-right text-gray-900 font-medium">${formatCurrency(item.balance)}</td>
                    </tr>
                  ))
                ) : (
                  <tr><td colSpan="2" className="py-2 text-gray-400 italic">No equity accounts found.</td></tr>
                )}
              </tbody>
              <tfoot>
                <tr className="border-t border-gray-200 font-semibold">
                  <td className="py-3 text-gray-800">Total Equity</td>
                  <td className="py-3 text-right text-gray-900">${formatCurrency(reportData.totalEquity)}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          {/* TOTAL LIABILITIES & EQUITY */}
          <div className="mt-8 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <table className="w-full">
              <tbody>
                <tr className="font-bold text-lg">
                  <td className="text-gray-800 uppercase">Total Liabilities and Equity</td>
                  <td className="text-right text-blue-700">${formatCurrency(totalLiabilitiesAndEquity)}</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  );
};

export default BalanceSheet;