import React, { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";
import { apiUrl } from "../../utils/api";
import {
  FaSpinner,
  FaSearch,
  FaSyncAlt,
  FaFileExport,
  FaPlus,
  FaMoneyBillWave,
} from "react-icons/fa";

import {
  exportAgedPayablesToCSV,
  goToCreateBill,
  goToAddPayment,
} from "./agedPayablesActions";

export default function AgedPayables() {
  const navigate = useNavigate();

  const [activeTab, setActiveTab] = useState("summary");
  const [searchQuery, setSearchQuery] = useState("");
  const [dateRange, setDateRange] = useState("");
  const [page, setPage] = useState(1);

  const [purchaseOrders, setPurchaseOrders] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState("");

  const itemsPerPage = 5;

  const getCompanyId = () => sessionStorage.getItem("companyId");
  const getToken = () => sessionStorage.getItem("auth_token");

  const getAuthHeaders = () => ({
    Authorization: `Bearer ${getToken()}`,
    Accept: "application/json",
    "Content-Type": "application/json",
  });

  const extractArray = (data) => {
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.data)) return data.data;
    if (Array.isArray(data?.content)) return data.content;
    if (Array.isArray(data?.purchaseOrders)) return data.purchaseOrders;
    return [];
  };

  const formatAmount = (amount) => {
    const value = Number(amount || 0);

    return `Rs. ${value.toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })}`;
  };

  const formatDate = (date) => {
    if (!date) return "-";
    return date;
  };

  const getDaysOverdue = (dueDate) => {
    if (!dueDate) return 0;

    const today = new Date();
    const due = new Date(dueDate);

    today.setHours(0, 0, 0, 0);
    due.setHours(0, 0, 0, 0);

    const diffTime = today.getTime() - due.getTime();
    return Math.floor(diffTime / (1000 * 60 * 60 * 24));
  };

  const getBucketValues = (balanceDue, dueDate) => {
    const balance = Number(balanceDue || 0);
    const daysOverdue = getDaysOverdue(dueDate);

    const buckets = {
      notDueYet: 0,
      age1: 0,
      age2: 0,
      age3: 0,
    };

    if (daysOverdue <= 0) {
      buckets.notDueYet = balance;
    } else if (daysOverdue >= 1 && daysOverdue <= 30) {
      buckets.age1 = balance;
    } else if (daysOverdue >= 31 && daysOverdue <= 60) {
      buckets.age2 = balance;
    } else {
      buckets.age3 = balance;
    }

    return buckets;
  };

  const fetchPurchaseOrders = async () => {
    try {
      setIsLoading(true);
      setError("");

      const companyId = getCompanyId();
      const token = getToken();

      if (!companyId || !token) {
        throw new Error("Session expired. Please log in again.");
      }

      const response = await fetch(
        `${apiUrl}/api/${companyId}/purchase-orders`,
        {
          method: "GET",
          headers: getAuthHeaders(),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(
          errorText ||
            `Failed to fetch purchase orders. Status: ${response.status}`
        );
      }

      const data = await response.json();
      const list = extractArray(data);

      console.log("Purchase Orders for Aged Payables:", list);

      setPurchaseOrders(list);
    } catch (err) {
      console.error("Aged Payables Error:", err);
      setError(err.message || "Failed to load aged payables.");
      setPurchaseOrders([]);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchPurchaseOrders();
  }, []);

  const detailData = useMemo(() => {
    return purchaseOrders
      .filter((order) => Number(order.balanceDue || 0) > 0)
      .map((order) => {
        const balance = Number(order.balanceDue || 0);
        const dueDate = order.dueDate || order.issueDate;
        const buckets = getBucketValues(balance, dueDate);

        return {
          id: order.id,
          supplier: order.supplierName || "-",
          invoice:
            order.supplierInvoiceNumber ||
            order.purchaseOrderNumber ||
            order.poNumber ||
            "-",
          invoiceDate: order.issueDate || "-",
          dueDate: dueDate || "-",
          daysOverdue: getDaysOverdue(dueDate),
          notDueYet: buckets.notDueYet,
          age1: buckets.age1,
          age2: buckets.age2,
          age3: buckets.age3,
          total: Number(order.total || 0),
          balance,
        };
      });
  }, [purchaseOrders]);

  const summaryData = useMemo(() => {
    const supplierMap = {};

    detailData.forEach((row) => {
      if (!supplierMap[row.supplier]) {
        supplierMap[row.supplier] = {
          supplier: row.supplier,
          notDueYet: 0,
          age1: 0,
          age2: 0,
          age3: 0,
          total: 0,
        };
      }

      supplierMap[row.supplier].notDueYet += row.notDueYet;
      supplierMap[row.supplier].age1 += row.age1;
      supplierMap[row.supplier].age2 += row.age2;
      supplierMap[row.supplier].age3 += row.age3;
      supplierMap[row.supplier].total += row.balance;
    });

    return Object.values(supplierMap);
  }, [detailData]);

  const filterData = (data) => {
    let filteredData = [...data];

    if (searchQuery.trim()) {
      const term = searchQuery.toLowerCase();

      filteredData = filteredData.filter((row) => {
        return (
          String(row.supplier || "").toLowerCase().includes(term) ||
          String(row.invoice || "").toLowerCase().includes(term)
        );
      });
    }

    if (dateRange && activeTab === "detail") {
      const today = new Date();

      filteredData = filteredData.filter((row) => {
        const invoiceDate = new Date(row.invoiceDate);

        if (dateRange === "last30") {
          const last30 = new Date();
          last30.setDate(today.getDate() - 30);
          return invoiceDate >= last30;
        }

        if (dateRange === "thisMonth") {
          return (
            invoiceDate.getMonth() === today.getMonth() &&
            invoiceDate.getFullYear() === today.getFullYear()
          );
        }

        if (dateRange === "lastMonth") {
          const lastMonth = new Date(
            today.getFullYear(),
            today.getMonth() - 1,
            1
          );

          return (
            invoiceDate.getMonth() === lastMonth.getMonth() &&
            invoiceDate.getFullYear() === lastMonth.getFullYear()
          );
        }

        return true;
      });
    }

    return filteredData;
  };

  const currentDataList = activeTab === "summary" ? summaryData : detailData;
  const filteredList = filterData(currentDataList);

  const totalPages = Math.ceil(filteredList.length / itemsPerPage) || 1;

  const paginatedData = filteredList.slice(
    (page - 1) * itemsPerPage,
    page * itemsPerPage
  );

  const handleSearch = (e) => {
    setSearchQuery(e.target.value);
    setPage(1);
  };

  const handleDateRangeChange = (e) => {
    setDateRange(e.target.value);
    setPage(1);
  };

  const handleExport = () => {
    exportAgedPayablesToCSV(filteredList, activeTab);
  };

  const handleCreateBill = () => {
    goToCreateBill(navigate);
  };

  const handleAddPayment = () => {
    goToAddPayment(navigate);
  };

  const totalOutstanding = summaryData.reduce(
    (sum, row) => sum + Number(row.total || 0),
    0
  );

  const notDueTotal = summaryData.reduce(
    (sum, row) => sum + Number(row.notDueYet || 0),
    0
  );

  const age1Total = summaryData.reduce(
    (sum, row) => sum + Number(row.age1 || 0),
    0
  );

  const age2Total = summaryData.reduce(
    (sum, row) => sum + Number(row.age2 || 0),
    0
  );

  const age3Total = summaryData.reduce(
    (sum, row) => sum + Number(row.age3 || 0),
    0
  );

  return (
    <div className="p-6 bg-gray-50 min-h-screen space-y-6">
      <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Aged Payables</h1>
          <p className="text-sm text-gray-500 mt-1">
            View supplier outstanding balances by aging period
          </p>
        </div>

        <div className="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
          <button
            type="button"
            onClick={fetchPurchaseOrders}
            className="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg flex items-center justify-center gap-2 hover:bg-gray-100"
          >
            <FaSyncAlt />
            Refresh
          </button>

          <button
            type="button"
            onClick={handleExport}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-700"
          >
            <FaFileExport />
            Export
          </button>

          <button
            type="button"
            onClick={handleCreateBill}
            className="px-4 py-2 bg-green-600 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-green-700"
          >
            <FaPlus />
            Create Bill
          </button>

          <button
            type="button"
            onClick={handleAddPayment}
            className="px-4 py-2 bg-gray-700 text-white rounded-lg flex items-center justify-center gap-2 hover:bg-gray-800"
          >
            <FaMoneyBillWave />
            Add Payment
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
        <SummaryCard
          title="Total Outstanding"
          value={formatAmount(totalOutstanding)}
        />
        <SummaryCard title="Not Due Yet" value={formatAmount(notDueTotal)} />
        <SummaryCard title="1–30 Days" value={formatAmount(age1Total)} />
        <SummaryCard title="31–60 Days" value={formatAmount(age2Total)} />
        <SummaryCard title="61–90+ Days" value={formatAmount(age3Total)} />
      </div>

      {error && (
        <div className="p-3 bg-red-100 text-red-700 rounded-lg border border-red-200">
          {error}
        </div>
      )}

      <div className="bg-white rounded-xl shadow border border-gray-200 p-4">
        <div className="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
          <div className="relative w-full lg:w-80">
            <FaSearch className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />

            <input
              type="text"
              placeholder="Search by supplier or invoice..."
              value={searchQuery}
              onChange={handleSearch}
              className="w-full pl-11 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <select
            value={dateRange}
            onChange={handleDateRangeChange}
            disabled={activeTab === "summary"}
            className="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
          >
            <option value="">All Dates</option>
            <option value="last30">Last 30 Days</option>
            <option value="thisMonth">This Month</option>
            <option value="lastMonth">Last Month</option>
          </select>
        </div>

        <div className="flex gap-4 border-b mt-5">
          <button
            type="button"
            className={`px-4 py-2 text-sm font-medium ${
              activeTab === "summary"
                ? "border-b-2 border-blue-600 text-blue-600"
                : "text-gray-600"
            }`}
            onClick={() => {
              setActiveTab("summary");
              setPage(1);
            }}
          >
            Summary
          </button>

          <button
            type="button"
            className={`px-4 py-2 text-sm font-medium ${
              activeTab === "detail"
                ? "border-b-2 border-blue-600 text-blue-600"
                : "text-gray-600"
            }`}
            onClick={() => {
              setActiveTab("detail");
              setPage(1);
            }}
          >
            Detail
          </button>
        </div>
      </div>

      {isLoading ? (
        <div className="flex justify-center items-center py-16">
          <FaSpinner className="animate-spin text-blue-500 text-3xl mr-3" />
          <span className="text-gray-600 text-lg">Loading Payables...</span>
        </div>
      ) : activeTab === "summary" ? (
        <SummaryTable rows={paginatedData} formatAmount={formatAmount} />
      ) : (
        <DetailTable
          rows={paginatedData}
          formatAmount={formatAmount}
          formatDate={formatDate}
        />
      )}

      {!isLoading && filteredList.length > 0 && (
        <div className="flex justify-between items-center">
          <div className="text-sm text-gray-600">
            Page {page} of {totalPages}
          </div>

          <div className="flex gap-2">
            <button
              type="button"
              onClick={() => setPage((prev) => Math.max(prev - 1, 1))}
              disabled={page === 1}
              className="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 disabled:text-gray-400 disabled:cursor-not-allowed hover:bg-gray-300"
            >
              Previous
            </button>

            <button
              type="button"
              onClick={() => setPage((prev) => Math.min(prev + 1, totalPages))}
              disabled={page >= totalPages}
              className="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 disabled:text-gray-400 disabled:cursor-not-allowed hover:bg-gray-300"
            >
              Next
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

const SummaryCard = ({ title, value }) => {
  return (
    <div className="bg-white rounded-xl shadow border border-gray-200 p-4">
      <p className="text-xs font-semibold text-gray-500 uppercase">{title}</p>
      <p className="text-lg font-bold text-gray-900 mt-2">{value}</p>
    </div>
  );
};

const SummaryTable = ({ rows, formatAmount }) => {
  return (
    <div className="bg-white shadow rounded-xl border border-gray-200 overflow-x-auto">
      <table className="min-w-full">
        <thead className="bg-gray-50 border-b">
          <tr>
            <Th>Supplier</Th>
            <Th>Not Due Yet</Th>
            <Th>1–30</Th>
            <Th>31–60</Th>
            <Th>61–90+</Th>
            <Th>Total</Th>
          </tr>
        </thead>

        <tbody className="divide-y">
          {rows.length > 0 ? (
            rows.map((row, index) => (
              <tr key={index} className="hover:bg-gray-50">
                <Td strong>{row.supplier}</Td>
                <Td>{formatAmount(row.notDueYet)}</Td>
                <Td>{formatAmount(row.age1)}</Td>
                <Td>{formatAmount(row.age2)}</Td>
                <Td>{formatAmount(row.age3)}</Td>
                <Td strong>{formatAmount(row.total)}</Td>
              </tr>
            ))
          ) : (
            <tr>
              <td
                colSpan="6"
                className="px-4 py-8 text-center text-gray-500 text-sm"
              >
                No unpaid purchase bills found.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
};

const DetailTable = ({ rows, formatAmount, formatDate }) => {
  return (
    <div className="bg-white shadow rounded-xl border border-gray-200 overflow-x-auto">
      <table className="min-w-full">
        <thead className="bg-gray-50 border-b">
          <tr>
            <Th>Supplier</Th>
            <Th>Invoice</Th>
            <Th>Invoice Date</Th>
            <Th>Due Date</Th>
            <Th>Days Overdue</Th>
            <Th>Not Due Yet</Th>
            <Th>1–30</Th>
            <Th>31–60</Th>
            <Th>61–90+</Th>
            <Th>Total</Th>
            <Th>Balance</Th>
          </tr>
        </thead>

        <tbody className="divide-y">
          {rows.length > 0 ? (
            rows.map((row, index) => (
              <tr key={row.id || index} className="hover:bg-gray-50">
                <Td strong>{row.supplier}</Td>
                <Td>{row.invoice}</Td>
                <Td>{formatDate(row.invoiceDate)}</Td>
                <Td>{formatDate(row.dueDate)}</Td>
                <Td>{row.daysOverdue <= 0 ? "Not Due" : row.daysOverdue}</Td>
                <Td>{formatAmount(row.notDueYet)}</Td>
                <Td>{formatAmount(row.age1)}</Td>
                <Td>{formatAmount(row.age2)}</Td>
                <Td>{formatAmount(row.age3)}</Td>
                <Td>{formatAmount(row.total)}</Td>
                <Td strong>{formatAmount(row.balance)}</Td>
              </tr>
            ))
          ) : (
            <tr>
              <td
                colSpan="11"
                className="px-4 py-8 text-center text-gray-500 text-sm"
              >
                No unpaid purchase bills found.
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
};

const Th = ({ children }) => {
  return (
    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">
      {children}
    </th>
  );
};

const Td = ({ children, strong = false }) => {
  return (
    <td
      className={`px-4 py-3 whitespace-nowrap text-sm text-gray-700 ${
        strong ? "font-semibold text-gray-900" : ""
      }`}
    >
      {children}
    </td>
  );
};