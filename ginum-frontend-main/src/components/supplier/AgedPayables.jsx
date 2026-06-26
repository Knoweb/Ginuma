import React, { useState, useEffect } from 'react';
import { apiUrl } from "../../utils/api"; // API URL එක import කිරීම
import { FaSpinner } from "react-icons/fa"; // Loading spinner එක සඳහා

export default function AgedPayables() {
  const [activeTab, setActiveTab] = useState('summary');
  const [searchQuery, setSearchQuery] = useState('');
  const [dateRange, setDateRange] = useState('');
  const [page, setPage] = useState(1);
  const itemsPerPage = 5; // පිටුවකට පෙන්වන ප්‍රමාණය 5ක් කළා

  // Backend එකෙන් එන Suppliers ලා Save කරගන්න
  const [suppliers, setSuppliers] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  // API එකෙන් Suppliers ලාව Fetch කිරීම
  useEffect(() => {
    const fetchSuppliers = async () => {
      try {
        setIsLoading(true);
        const companyId = sessionStorage.getItem("companyId");
        const token = sessionStorage.getItem("auth_token");

        if (!companyId || !token) {
          throw new Error("Session expired. Please log in again.");
        }

        const response = await fetch(`${apiUrl}/api/suppliers/companies/${companyId}`, {
          method: "GET",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json"
          }
        });

        if (!response.ok) {
          throw new Error("Failed to fetch suppliers");
        }

        const data = await response.json();
        setSuppliers(data);
      } catch (err) {
        console.error("Error:", err);
        setError(err.message);
      } finally {
        setIsLoading(false);
      }
    };

    fetchSuppliers();
  }, []);

  // Backend එකෙන් එන Suppliers ලාව Table එකට ගැලපෙන විදිහට Map කිරීම (දැනට amounts $0.00 යි)
  const dynamicSummaryData = suppliers.map(s => ({
    supplier: s.supplierName,
    notDueYet: '$0.00',
    age1: '$0.00',
    age2: '$0.00',
    age3: '$0.00',
    total: '$0.00',
    invoiceDate: new Date().toISOString().split('T')[0], 
  }));

  const dynamicDetailData = suppliers.map(s => ({
    supplier: s.supplierName,
    invoice: 'N/A', // තාම Invoices නැති නිසා
    invoiceDate: new Date().toISOString().split('T')[0],
    dueDate: new Date().toISOString().split('T')[0],
    notDueYet: '$0.00',
    age1: '$0.00',
    age2: '$0.00',
    age3: '$0.00',
    total: '$0.00',
    balance: '$0.00',
  }));

  // Filter function based on search query and date range
  const filterData = (data) => {
    let filteredData = data;

    // Search by supplier
    if (searchQuery) {
      filteredData = filteredData.filter((row) =>
        row.supplier.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    // Date range filtering
    if (dateRange) {
      const today = new Date();
      filteredData = filteredData.filter((row) => {
        const invoiceDate = new Date(row.invoiceDate);
        switch (dateRange) {
          case 'last30':
            const last30Days = new Date(today.setDate(today.getDate() - 30));
            return invoiceDate >= last30Days;
          case 'thisMonth':
            return invoiceDate.getMonth() === today.getMonth() && invoiceDate.getFullYear() === today.getFullYear();
          case 'lastMonth':
            const lastMonth = new Date(today.setMonth(today.getMonth() - 1));
            return (
              invoiceDate.getMonth() === lastMonth.getMonth() && invoiceDate.getFullYear() === lastMonth.getFullYear()
            );
          default:
            return true;
        }
      });
    }

    return filteredData;
  };

  const handleExport = () => {
    console.log('Exporting data...');
  };

  const handleSearch = (e) => {
    setSearchQuery(e.target.value);
    setPage(1); // Search කරන විට මුල් පිටුවට යාමට
  };

  const handleDateRangeChange = (e) => {
    setDateRange(e.target.value);
    setPage(1);
  };

  const handlePaginationChange = (newPage) => {
    setPage(newPage);
  };

  // Paginate the filtered data
  const currentDataList = activeTab === 'summary' ? dynamicSummaryData : dynamicDetailData;
  const filteredList = filterData(currentDataList);
  const paginatedData = filteredList.slice((page - 1) * itemsPerPage, page * itemsPerPage);

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Aged Payables</h1>

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
          placeholder="Search by Supplier"
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
          className="px-4 py-2 bg-blue-600 text-white rounded-md"
        >
          Export
        </button>

        <div className="flex space-x-2 ml-auto">
          <button className="px-4 py-2 bg-green-600 text-white rounded-md">Create Bill</button>
          <button className="px-4 py-2 bg-gray-600 text-white rounded-md">Add Payment</button>
        </div>
      </div>

      {/* Tab Navigation */}
      <div className="flex space-x-4 border-b">
        <button
          className={`px-4 py-2 text-sm font-medium ${activeTab === 'summary' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'}`}
          onClick={() => { setActiveTab('summary'); setPage(1); }}
        >
          Summary
        </button>
        <button
          className={`px-4 py-2 text-sm font-medium ${activeTab === 'detail' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'}`}
          onClick={() => { setActiveTab('detail'); setPage(1); }}
        >
          Detail
        </button>
      </div>

      {/* Loading State */}
      {isLoading ? (
        <div className="flex justify-center items-center py-10">
          <FaSpinner className="animate-spin text-blue-500 text-3xl mr-3" />
          <span className="text-gray-600 text-lg">Loading Payables...</span>
        </div>
      ) : (
        /* Tab Content */
        activeTab === 'summary' ? (
          <div className="mt-6 bg-white shadow rounded-lg overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-100">
                <tr>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Supplier</th>
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
                    <tr key={index} className="hover:bg-gray-50">
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.supplier}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.notDueYet}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age1}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age2}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age3}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-semibold">{row.total}</td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="6" className="px-4 py-6 text-center text-gray-500">No data available. Add suppliers first.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="mt-6 bg-white shadow rounded-lg overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-100">
                <tr>
                  <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600">Supplier</th>
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
                    <tr key={index} className="hover:bg-gray-50">
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.supplier}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.invoice}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.invoiceDate}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.dueDate}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.notDueYet}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age1}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age2}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.age3}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{row.total}</td>
                      <td className="px-4 py-3 whitespace-nowrap text-sm text-gray-700 font-semibold">{row.balance}</td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="10" className="px-4 py-6 text-center text-gray-500">No data available. Add suppliers first.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        )
      )}

      {/* Pagination */}
      {!isLoading && filteredList.length > 0 && (
        <div className="mt-4 flex justify-between items-center">
          <div className="text-sm text-gray-600">Page {page} of {Math.ceil(filteredList.length / itemsPerPage)}</div>
          <div className="flex space-x-2">
            <button
              onClick={() => handlePaginationChange(page - 1)}
              disabled={page === 1}
              className={`px-4 py-2 rounded-md ${page === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-300 text-gray-700 hover:bg-gray-400'}`}
            >
              Previous
            </button>
            <button
              onClick={() => handlePaginationChange(page + 1)}
              disabled={page * itemsPerPage >= filteredList.length}
              className={`px-4 py-2 rounded-md ${page * itemsPerPage >= filteredList.length ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-gray-300 text-gray-700 hover:bg-gray-400'}`}
            >
              Next
            </button>
          </div>
        </div>
      )}
    </div>
  );
}