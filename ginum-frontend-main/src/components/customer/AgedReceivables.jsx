import React, { useState, useEffect } from 'react';
import { apiUrl } from "../../utils/api";
import { FaSpinner } from "react-icons/fa";

export default function AgedReceivables() {
  const [activeTab, setActiveTab] = useState('summary');
  const [searchQuery, setSearchQuery] = useState('');
  const [dateRange, setDateRange] = useState('');
  const [page, setPage] = useState(1);
  const itemsPerPage = 5;

  const [customers, setCustomers] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchReceivablesData();
  }, []);

  const fetchReceivablesData = async () => {
    try {
      setIsLoading(true);
      const companyId = sessionStorage.getItem("companyId");
      const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

      if (!companyId || !token) {
        throw new Error("Authentication credentials not found. Please log in again.");
      }

      const response = await fetch(`${apiUrl}/api/customers/companies/${companyId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });

      if (!response.ok) {
        throw new Error("Failed to fetch customer data from server");
      }

      const data = await response.json();
      setCustomers(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error("Fetch Error:", err);
      setError("Failed to load data.");
    } finally {
      setIsLoading(false);
    }
  };

  const summaryData = customers.map(c => ({
    customer: c.customerName || 'Unknown Customer',
    notDueYet: '$0.00',
    age1: '$0.00',
    age2: '$0.00',
    age3: '$0.00',
    total: '$0.00'
  }));

  const detailData = customers.map(c => ({
    customer: c.customerName || 'Unknown Customer',
    invoice: 'N/A', 
    invoiceDate: new Date().toISOString().split('T')[0], 
    dueDate: new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0], 
    notDueYet: '$0.00',
    age1: '$0.00',
    age2: '$0.00',
    age3: '$0.00',
    total: '$0.00',
    balance: '$0.00'
  }));

  const filterData = (data) => {
    let filtered = data;
    if (searchQuery) {
      filtered = filtered.filter(row => 
        row.customer && row.customer.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }
    // Date Filtering (Optional for now as dummy dates are used)
    // ...
    return filtered;
  };

  const currentDataList = activeTab === 'summary' ? summaryData : detailData;
  const filteredList = filterData(currentDataList);
  const paginatedData = filteredList.slice((page - 1) * itemsPerPage, page * itemsPerPage);
  const totalPages = Math.ceil(filteredList.length / itemsPerPage) || 1;

  // Handlers
  const handleExport = () => console.log("Exporting data...");
  const handleSearch = (e) => { setSearchQuery(e.target.value); setPage(1); };
  const handleDateRangeChange = (e) => { setDateRange(e.target.value); setPage(1); };
  const handlePaginationChange = (newPage) => setPage(newPage);

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Aged Receivables</h1>

      {/* Error Message */}
      {error && (
        <div className="p-3 bg-red-100 text-red-700 rounded-md">
          {error}
        </div>
      )}

      {/* Filters and Buttons */}
      <div className="flex space-x-4 items-center mb-4">
        <input
          type="text"
          placeholder="Search by Customer"
          value={searchQuery}
          onChange={handleSearch}
          className="px-4 py-2 border rounded-md shadow-sm w-1/3"
        />
        <select
          value={dateRange}
          onChange={handleDateRangeChange}
          className="px-4 py-2 border rounded-md shadow-sm"
        >
          <option value="">All Dates</option>
          <option value="last30">Last 30 Days</option>
          <option value="thisMonth">This Month</option>
          <option value="lastMonth">Last Month</option>
        </select>

        <button
          onClick={handleExport}
          className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
        >
          Export
        </button>

        <div className="flex space-x-2 ml-auto">
          <button className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">Create Invoice</button>
          <button className="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">Add Payment</button>
        </div>
      </div>

      {/* Tab Navigation */}
      <div className="flex space-x-4 border-b">
        <button
          className={`px-4 py-2 text-sm font-medium ${activeTab === 'summary' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-500'}`}
          onClick={() => { setActiveTab('summary'); setPage(1); }}
        >
          Summary
        </button>
        <button
          className={`px-4 py-2 text-sm font-medium ${activeTab === 'detail' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-500'}`}
          onClick={() => { setActiveTab('detail'); setPage(1); }}
        >
          Detail
        </button>
      </div>

      {/* Loading State or Tables */}
      {isLoading ? (
        <div className="flex justify-center items-center py-10">
          <FaSpinner className="animate-spin text-blue-500 text-3xl mr-3" />
          <span className="text-gray-600 text-lg">Loading Receivables Data...</span>
        </div>
      ) : (
        <div className="mt-6 bg-white shadow rounded-lg overflow-x-auto">
          {activeTab === 'summary' ? (
            /* ================= SUMMARY TABLE ================= */
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-100">
                <tr>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Customer</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Not Due Yet</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">1–30</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">31–60</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">61–90+</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Total</th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {paginatedData.length > 0 ? (
                  paginatedData.map((row, index) => (
                    <tr key={index} className="hover:bg-gray-50 transition-colors">
                      <td className="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{row.customer}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.notDueYet}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age1}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age2}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age3}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">{row.total}</td>
                    </tr>
                  ))
                ) : (
                  <tr><td colSpan="6" className="px-4 py-6 text-center text-gray-500">No customer data available.</td></tr>
                )}
              </tbody>
            </table>
          ) : (
            /* ================= DETAIL TABLE ================= */
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-100">
                <tr>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Customer</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Invoice</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Invoice Date</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Due Date</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Not Due Yet</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">1–30</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">31–60</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">61–90+</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Total</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Balance</th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {paginatedData.length > 0 ? (
                  paginatedData.map((row, index) => (
                    <tr key={index} className="hover:bg-gray-50 transition-colors">
                      <td className="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{row.customer}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.invoice}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.invoiceDate}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.dueDate}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.notDueYet}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age1}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age2}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age3}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.total}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">{row.balance}</td>
                    </tr>
                  ))
                ) : (
                  <tr><td colSpan="10" className="px-4 py-6 text-center text-gray-500">No customer detail data available.</td></tr>
                )}
              </tbody>
            </table>
          )}
        </div>
      )}

      {/* Pagination */}
      {!isLoading && customers.length > 0 && (
        <div className="mt-4 flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
          <div className="text-sm text-gray-600 font-medium">
            Page {page} of {totalPages}
          </div>
          <div className="flex space-x-2">
            <button
              onClick={() => handlePaginationChange(page - 1)}
              disabled={page === 1}
              className={`px-4 py-2 rounded-md font-medium transition-colors ${
                page === 1 
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                  : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
              }`}
            >
              Previous
            </button>
            <button
              onClick={() => handlePaginationChange(page + 1)}
              disabled={page >= totalPages}
              className={`px-4 py-2 rounded-md font-medium transition-colors ${
                page >= totalPages 
                  ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                  : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
              }`}
            >
              Next
            </button>
          </div>
        </div>
      )}
    </div>
  );
}