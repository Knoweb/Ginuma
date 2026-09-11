import React, { useState, useEffect, useRef } from "react";
import { Line, Bar, Pie, Doughnut, Radar } from "react-chartjs-2";
import { Chart as ChartJS } from "chart.js/auto";
import { FiRefreshCw, FiPieChart } from "react-icons/fi";
import { apiUrl } from "../../utils/api";
import PageHeader from "../../components/common/PageHeader";

// Helper Functions
const getAmount = (item) => Number(item.totalAmount || item.grandTotal || item.total || item.amount || 0);
const getDate = (item) => item.orderDate || item.date || item.createdAt || item.purchaseDate || new Date().toISOString();
const getCustomerName = (item) => item.customerName || item.customer?.name || item.customer?.customerName || item.customer?.companyName || item.customerId || "Unknown Client";
const getSupplierName = (item) => item.supplierName || item.supplier?.name || item.supplier?.supplierName || item.supplier?.companyName || item.supplierId || "Unknown Supplier";

const RevealOnScroll = ({ children, className = "", delay = 0 }) => {
  const [isVisible, setIsVisible] = useState(false);
  const domRef = useRef();

  useEffect(() => {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          setIsVisible(true);
          if (domRef.current) observer.unobserve(domRef.current);
        }
      });
    }, { threshold: 0.1 });

    if (domRef.current) observer.observe(domRef.current);

    return () => {
      if (domRef.current) observer.unobserve(domRef.current);
    };
  }, []);

  return (
    <div
      ref={domRef}
      className={`transition-all duration-700 ease-out ${isVisible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-8"
        } ${className}`}
      style={{ transitionDelay: `${delay}ms` }}
    >
      {children}
    </div>
  );
};

const LazyChart = ({ children }) => {
  const [isVisible, setIsVisible] = useState(false);
  const domRef = useRef();

  useEffect(() => {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          setIsVisible(true);
          if (domRef.current) observer.unobserve(domRef.current);
        }
      });
    }, { threshold: 0.1 });

    if (domRef.current) observer.observe(domRef.current);

    return () => {
      if (domRef.current) observer.unobserve(domRef.current);
    };
  }, []);

  return (
    <div ref={domRef} className="w-full h-full flex justify-center items-center">
      {isVisible ? children : <div className="text-gray-400 animate-pulse text-sm">Loading chart...</div>}
    </div>
  );
};

// Finance Stats Component
const FinanceStats = ({ revenue, expenses, profit, prevRevenue, prevExpenses, prevProfit }) => {
  return (
    <div className="mb-6">
      <h2 className="gl-section-title mb-4">Financial Overview (Last 30 Days)</h2>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <StatCard title="Total Revenue" value={revenue} previous={prevRevenue} color="text-indigo-600" delay={0} icon="📈" />
        <StatCard title="Total Expenses" value={expenses} previous={prevExpenses} color="text-rose-500" delay={150} icon="📉" />
        <StatCard title="Net Profit" value={profit} previous={prevProfit} color="text-emerald-500" delay={300} icon="💎" />
      </div>
    </div>
  );
};

// Reusable Stat Card Component
const StatCard = ({ title, value, previous, color, delay, icon }) => {
  const previousValue = previous || 0;
  const change = previousValue === 0
    ? (value > 0 ? 100 : 0)
    : (((value - previousValue) / Math.abs(previousValue)) * 100).toFixed(2);
  const isPositive = change >= 0;

  return (
    <RevealOnScroll className="gl-card gl-card-hover p-6 flex flex-col relative" delay={delay}>
      <div className="flex justify-between items-start mb-4">
        <p className="text-slate-500 text-xs font-bold uppercase tracking-wider">{title}</p>
        <span className="text-2xl opacity-80">{icon}</span>
      </div>
      <p className={`text-3xl font-extrabold tracking-tight mb-1 ${color}`}>
        Rs. {value.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
      </p>
      <div className="flex items-center gap-2 mt-auto pt-2">
        <span
          className={`text-xs font-bold px-2 py-0.5 rounded-full ${isPositive ? "bg-emerald-100 text-emerald-700" : "bg-rose-100 text-rose-700"}`}
        >
          {isPositive ? "▲" : "▼"} {Math.abs(change)}%
        </span>
        <span className="text-slate-400 text-xs font-medium">vs last month</span>
      </div>
    </RevealOnScroll>
  );
};

const DashboardPage = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const [sales, setSales] = useState([]);
  const [purchases, setPurchases] = useState([]);
  const [accounts, setAccounts] = useState([]);
  const [customers, setCustomers] = useState([]);

  const fetchData = async () => {
    try {
      setLoading(true);
      setError(null);

      const companyId = sessionStorage.getItem("companyId");
      const token = sessionStorage.getItem("auth_token");

      if (!companyId || !token) {
        throw new Error("Missing company ID or auth token. Please login again.");
      }

      const headers = {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      };

      const [salesRes, purchasesRes, accountsRes, customersRes] = await Promise.all([
        fetch(`${apiUrl}/api/sales-orders/company/${companyId}`, { headers }).catch(() => ({ ok: false })),
        fetch(`${apiUrl}/api/${companyId}/purchase-orders`, { headers }).catch(() => ({ ok: false })),
        fetch(`${apiUrl}/api/companies/${companyId}/accounts`, { headers }).catch(() => ({ ok: false })),
        fetch(`${apiUrl}/api/customers/companies/${companyId}`, { headers }).catch(() => ({ ok: false }))
      ]);

      const salesData = salesRes.ok ? await salesRes.json() : [];
      const purchasesData = purchasesRes.ok ? await purchasesRes.json() : [];
      const accountsData = accountsRes.ok ? await accountsRes.json() : [];
      const customersData = customersRes.ok ? await customersRes.json() : [];

      setSales(Array.isArray(salesData) ? salesData : (salesData.data || []));
      setPurchases(Array.isArray(purchasesData) ? purchasesData : (purchasesData.data || []));
      setAccounts(Array.isArray(accountsData) ? accountsData : (accountsData.data || []));
      setCustomers(Array.isArray(customersData) ? customersData : (customersData.data || []));

    } catch (err) {
      console.error("Dashboard fetch error:", err);
      setError(err.message || "Failed to load dashboard data");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  if (loading) {
    return (
      <div className="flex justify-center items-center min-h-screen bg-gray-100">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="p-4 bg-gray-100 min-h-screen">
        <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-4 mt-6 rounded shadow">
          <p className="font-bold">Error Loading Dashboard</p>
          <p>{error}</p>
          <button
            onClick={fetchData}
            className="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 transition-colors"
          >
            <FiRefreshCw /> Retry
          </button>
        </div>
      </div>
    );
  }

  // --- Date Calculations ---
  const now = new Date();
  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(now.getDate() - 30);
  const sixtyDaysAgo = new Date();
  sixtyDaysAgo.setDate(now.getDate() - 60);

  // --- Metrics ---
  let currentRevenue = 0;
  let prevRevenue = 0;
  let currentExpenses = 0;
  let prevExpenses = 0;

  sales.forEach(sale => {
    const date = new Date(getDate(sale));
    const amount = getAmount(sale);
    if (date >= thirtyDaysAgo) currentRevenue += amount;
    else if (date >= sixtyDaysAgo && date < thirtyDaysAgo) prevRevenue += amount;
  });

  purchases.forEach(purchase => {
    const date = new Date(getDate(purchase));
    const amount = getAmount(purchase);
    if (date >= thirtyDaysAgo) currentExpenses += amount;
    else if (date >= sixtyDaysAgo && date < thirtyDaysAgo) prevExpenses += amount;
  });

  const currentProfit = currentRevenue - currentExpenses;
  const prevProfit = prevRevenue - prevExpenses;

  // --- Monthly Grouping ---
  const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  const monthlyRevenue = Array(12).fill(0);
  const monthlyExpenses = Array(12).fill(0);
  const monthlySalesCount = Array(12).fill(0);

  sales.forEach(sale => {
    const d = new Date(getDate(sale));
    if (d.getFullYear() === now.getFullYear()) {
      const m = d.getMonth();
      monthlyRevenue[m] += getAmount(sale);
      monthlySalesCount[m] += 1;
    }
  });

  purchases.forEach(purchase => {
    const d = new Date(getDate(purchase));
    if (d.getFullYear() === now.getFullYear()) {
      monthlyExpenses[d.getMonth()] += getAmount(purchase);
    }
  });

  const currentMonth = now.getMonth();
  // Get last 7 months for the charts
  const chartLabels = [];
  const revData = [];
  const expData = [];
  const salesCountData = [];

  for (let i = 6; i >= 0; i--) {
    let mIndex = currentMonth - i;
    if (mIndex < 0) mIndex += 12;
    chartLabels.push(monthNames[mIndex]);
    revData.push(monthlyRevenue[mIndex]);
    expData.push(monthlyExpenses[mIndex]);
    salesCountData.push(monthlySalesCount[mIndex]);
  }

  const chartData = {
    labels: chartLabels,
    datasets: [
      {
        label: "Revenue (Rs.)",
        data: revData,
        borderColor: "#4f46e5", // indigo-600
        backgroundColor: "rgba(79, 70, 229, 0.1)",
        borderWidth: 2,
        tension: 0.4,
        fill: true
      },
      {
        label: "Expenses (Rs.)",
        data: expData,
        borderColor: "#f43f5e", // rose-500
        backgroundColor: "rgba(244, 63, 94, 0.1)",
        borderWidth: 2,
        tension: 0.4,
        fill: true
      },
    ],
  };

  const barData = {
    labels: chartLabels,
    datasets: [
      {
        label: "Sales Count",
        data: salesCountData,
        backgroundColor: "#6366f1", // indigo-500
        borderColor: "#4f46e5", // indigo-600
        borderWidth: 1,
        borderRadius: 4
      },
    ],
  };

  // --- Customer / Supplier Grouping ---
  const customerTotals = {};
  sales.forEach(sale => {
    const cName = getCustomerName(sale);
    customerTotals[cName] = (customerTotals[cName] || 0) + getAmount(sale);
  });

  const sortedCustomers = Object.entries(customerTotals).sort((a, b) => b[1] - a[1]);
  const top5Customers = sortedCustomers.slice(0, 5);

  const pieData = {
    labels: top5Customers.length ? top5Customers.map(c => c[0]) : ["No Data"],
    datasets: [{
      data: top5Customers.length ? top5Customers.map(c => c[1]) : [1],
      backgroundColor: ["#4f46e5", "#0ea5e9", "#10b981", "#8b5cf6", "#f43f5e"],
      borderWidth: 0,
      hoverOffset: 4
    }],
  };

  const supplierTotals = {};
  purchases.forEach(p => {
    const sName = getSupplierName(p);
    supplierTotals[sName] = (supplierTotals[sName] || 0) + getAmount(p);
  });

  const sortedSuppliers = Object.entries(supplierTotals).sort((a, b) => b[1] - a[1]);
  const top4Suppliers = sortedSuppliers.slice(0, 4);

  const doughnutData = {
    labels: top4Suppliers.length ? top4Suppliers.map(s => s[0]) : ["No Data"],
    datasets: [{
      data: top4Suppliers.length ? top4Suppliers.map(s => s[1]) : [1],
      backgroundColor: ["#f43f5e", "#f97316", "#eab308", "#84cc16"],
      borderWidth: 0,
      hoverOffset: 4
    }],
  };

  // --- Recent Transactions ---
  const combinedTransactions = [
    ...sales.map(s => ({
      id: s.id || Math.random(),
      date: new Date(getDate(s)),
      desc: `Sale to ${getCustomerName(s)}`,
      amount: getAmount(s),
      type: "Revenue",
      color: "text-emerald-600"
    })),
    ...purchases.map(p => ({
      id: p.id || Math.random(),
      date: new Date(getDate(p)),
      desc: `Purchase from ${getSupplierName(p)}`,
      amount: getAmount(p),
      type: "Expense",
      color: "text-rose-600"
    }))
  ].sort((a, b) => b.date - a.date).slice(0, 5);

  const radarData = {
    labels: [
      "Revenue",
      "Growth",
      "Customer Satisfaction",
      "Efficiency",
      "Profitability",
    ],
    datasets: [
      {
        label: "Performance",
        data: [
          currentRevenue > 0 ? 85 : 0,
          currentRevenue >= prevRevenue ? 90 : 40,
          80, // Static baseline for customer satisfaction
          currentExpenses < currentRevenue ? 85 : 50,
          currentProfit > 0 ? 95 : 30
        ],
        backgroundColor: "rgba(99, 102, 241, 0.2)",
        borderColor: "rgba(79, 70, 229, 1)",
        pointBackgroundColor: "rgba(79, 70, 229, 1)",
        pointBorderColor: "#fff",
        pointHoverBackgroundColor: "#fff",
        pointHoverBorderColor: "rgba(79, 70, 229, 1)",
        borderWidth: 2,
      },
    ],
  };

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: {
      duration: 1900,
      easing: "easeOutQuart",
      animateRotate: true,
      animateScale: true
    }
  };

  return (
    <div className="p-6 lg:p-8 flex flex-col gap-8">

      <PageHeader
        title="Dashboard"
        subtitle="A clear snapshot of your business performance"
        icon={FiPieChart}
        actions={
          <button onClick={fetchData} className="gl-btn gl-btn-secondary">
            <FiRefreshCw className="mr-1.5" /> Refresh
          </button>
        }
      />

      <FinanceStats
        revenue={currentRevenue} prevRevenue={prevRevenue}
        expenses={currentExpenses} prevExpenses={prevExpenses}
        profit={currentProfit} prevProfit={prevProfit}
      />

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <RevealOnScroll className="gl-card p-6 flex flex-col items-center" delay={0}>
          <h3 className="gl-section-title mb-4">Revenue and Expenses Overview</h3>
          <div className="h-80 w-full max-w-md">
            {sales.length > 0 || purchases.length > 0 ? (
              <LazyChart>
                <Line data={chartData} options={chartOptions} />
              </LazyChart>
            ) : <p className="text-center text-slate-400 mt-32 text-sm">No data available</p>}
          </div>
        </RevealOnScroll>
        <RevealOnScroll className="gl-card p-6 flex flex-col items-center" delay={150}>
          <h3 className="gl-section-title mb-4">Monthly Sales Comparison</h3>
          <div className="h-80 w-full max-w-md">
            {sales.length > 0 ? (
              <LazyChart>
                <Bar data={barData} options={chartOptions} />
              </LazyChart>
            ) : <p className="text-center text-slate-400 mt-32 text-sm">No data available</p>}
          </div>
        </RevealOnScroll>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <RevealOnScroll className="gl-card p-6 flex flex-col items-center" delay={0}>
          <h3 className="gl-section-title mb-4">Top Revenue Sources</h3>
          <div className="h-80 w-full max-w-md">
            {top5Customers.length > 0 ? (
              <LazyChart>
                <Pie data={pieData} options={chartOptions} />
              </LazyChart>
            ) : <p className="text-center text-slate-400 mt-32 text-sm">No data available</p>}
          </div>
        </RevealOnScroll>
        <RevealOnScroll className="gl-card p-6 flex flex-col items-center" delay={150}>
          <h3 className="gl-section-title mb-4">Top Expenses</h3>
          <div className="h-80 w-full max-w-md">
            {top4Suppliers.length > 0 ? (
              <LazyChart>
                <Doughnut data={doughnutData} options={chartOptions} />
              </LazyChart>
            ) : <p className="text-center text-slate-400 mt-32 text-sm">No data available</p>}
          </div>
        </RevealOnScroll>
        <RevealOnScroll className="gl-card p-6 flex flex-col items-center" delay={300}>
          <h3 className="gl-section-title mb-4">Financial KPIs</h3>
          <div className="h-80 w-full max-w-md">
            <LazyChart>
              <Radar data={radarData} options={chartOptions} />
            </LazyChart>
          </div>
        </RevealOnScroll>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <RevealOnScroll className="gl-card p-6" delay={0}>
          <h3 className="gl-section-title mb-6">Recent Transactions</h3>
          <div className="gl-table-container">
            {combinedTransactions.length > 0 ? (
              <table className="gl-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th className="text-right">Amount</th>
                    <th>Category</th>
                  </tr>
                </thead>
                <tbody>
                  {combinedTransactions.map((t, idx) => (
                    <tr key={idx}>
                      <td className="text-slate-500 font-medium">{t.date.toLocaleDateString()}</td>
                      <td className="font-bold text-slate-800">{t.desc}</td>
                      <td className={`text-right font-bold tracking-tight ${t.color}`}>
                        {t.type === "Expense" ? "- " : ""}Rs. {t.amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                      </td>
                      <td>
                        <span className={t.type === 'Revenue' ? 'gl-badge gl-badge-success' : 'gl-badge gl-badge-error'}>
                          {t.type}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            ) : (
              <p className="text-center text-slate-400 py-8 text-sm">No recent transactions found.</p>
            )}
          </div>
        </RevealOnScroll>

        <RevealOnScroll className="gl-card p-6" delay={150}>
          <h3 className="gl-section-title mb-6">Top Clients</h3>
          {top5Customers.length > 0 ? (
            <ul className="flex flex-col gap-2">
              {top5Customers.map((client, idx) => (
                <li key={idx} className="py-3 px-4 flex justify-between items-center bg-slate-50 border border-slate-100 rounded-xl hover:border-indigo-100 hover:bg-indigo-50/30 transition-colors">
                  <span className="font-bold text-slate-700 flex items-center gap-3 text-sm">
                    <span className="w-6 h-6 rounded-md bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-black">{idx + 1}</span>
                    {client[0]}
                  </span>
                  <span className="font-extrabold text-slate-900 tracking-tight">Rs. {client[1].toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                </li>
              ))}
            </ul>
          ) : (
            <p className="text-center text-slate-400 py-8 text-sm">No client data available.</p>
          )}
        </RevealOnScroll>
      </div>
    </div>
  );
};

export default DashboardPage;
