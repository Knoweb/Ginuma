import React, { useState, useEffect } from "react";
import { FaSpinner, FaArrowLeft, FaCheckCircle, FaExclamationTriangle } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import { apiUrl } from "../../utils/api";
import Alert from "../Alert/Alert";

const ReceiveMoney = () => {
  const navigate = useNavigate();

  // State variables
  const [accounts, setAccounts] = useState([]);
  const [customers, setCustomers] = useState([]);
  const [salesOrders, setSalesOrders] = useState([]);

  const [selectedCustomer, setSelectedCustomer] = useState("");
  const [selectedSoId, setSelectedSoId] = useState("");
  const [selectedBankAccountCode, setSelectedBankAccountCode] = useState("");
  const [paymentAmount, setPaymentAmount] = useState("");
  const [date, setDate] = useState(new Date().toISOString().split("T")[0]);
  const [referenceNumber, setReferenceNumber] = useState(`REF-${Math.floor(Date.now() / 1000)}`);
  const [paymentNote, setPaymentNote] = useState("");

  const [isLoading, setIsLoading] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMsg, setErrorMsg] = useState("");

  const companyId = sessionStorage.getItem("companyId");
  const token = sessionStorage.getItem("auth_token");

  // Helper functions
  const getCustomerId = (customer) => {
    return customer.id || customer.customerId || customer.customer_id || customer.customerID || "";
  };

  const getCustomerName = (customer) => {
    return customer.customerName || customer.name || customer.companyName || customer.email || "Unnamed Customer";
  };

  const getAccountLabel = (account) => {
    const code = account.accountCode || "";
    const name = account.accountName || account.name || "Unnamed Account";
    return code ? `${code} - ${name}` : name;
  };

  const getAuthHeaders = () => ({
    Authorization: `Bearer ${token}`,
    "Content-Type": "application/json",
    Accept: "application/json",
  });

  // Fetch initial data (accounts, customers, Sales Orders)
  useEffect(() => {
    const fetchData = async () => {
      if (!companyId || !token) {
        Alert.error("Missing company ID or auth token. Please login again.");
        return;
      }

      setIsLoading(true);
      setErrorMsg("");

      try {
        const [accRes, custRes, soRes] = await Promise.all([
          fetch(`${apiUrl}/api/companies/${companyId}/accounts`, { headers: getAuthHeaders() }),
          fetch(`${apiUrl}/api/customers/companies/${companyId}`, { headers: getAuthHeaders() }),
          fetch(`${apiUrl}/api/sales-orders/company/${companyId}`, { headers: getAuthHeaders() })
        ]);

        if (accRes.ok) {
          const accData = await accRes.json();
          setAccounts(Array.isArray(accData) ? accData : accData?.data || []);
        } else {
          console.error("Failed to fetch accounts");
        }

        if (custRes.ok) {
          const custData = await custRes.json();
          setCustomers(Array.isArray(custData) ? custData : custData?.data || []);
        } else {
          console.error("Failed to fetch customers");
        }

        if (soRes.ok) {
          const soData = await soRes.json();
          setSalesOrders(Array.isArray(soData) ? soData : soData?.data || soData?.salesOrders || []);
        } else {
          console.error("Failed to fetch sales orders");
        }

      } catch (err) {
        console.error("Error loading receive money data:", err);
        setErrorMsg("Failed to load layout data from server.");
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, [companyId, token]);

  // Unpaid sales orders filtered by the selected customer
  const customerSalesOrders = salesOrders.filter((so) => {
    return (
      String(so.customerId) === String(selectedCustomer) &&
      Number(so.balanceDue || 0) > 0
    );
  });

  // Selected Sales Order details
  const currentSo = salesOrders.find((so) => String(so.id) === String(selectedSoId));

  // Set default amount when selected SO changes
  useEffect(() => {
    if (currentSo) {
      setPaymentAmount(Number(currentSo.balanceDue || 0).toFixed(2));
    } else {
      setPaymentAmount("");
    }
  }, [selectedSoId, salesOrders]);

  const handleRecordPayment = async (e) => {
    e.preventDefault();

    if (!selectedBankAccountCode) {
      Alert.error("Please select a bank account.");
      return;
    }

    if (!selectedCustomer) {
      Alert.error("Please select a customer.");
      return;
    }

    if (!selectedSoId) {
      Alert.error("Please select a sales order.");
      return;
    }

    if (currentSo && String(currentSo.customerId) !== String(selectedCustomer)) {
      Alert.error("Selected sales order does not belong to the selected customer.");
      return;
    }

    const amount = parseFloat(paymentAmount);
    if (isNaN(amount) || amount <= 0) {
      Alert.error("Please enter a valid payment amount greater than zero.");
      return;
    }

    if (currentSo && amount > Number(currentSo.balanceDue)) {
      Alert.error(`Payment amount cannot exceed the balance due of Rs. ${Number(currentSo.balanceDue).toFixed(2)}.`);
      return;
    }

    setIsSubmitting(true);

    try {
      const payload = {
        amount: amount,
        paymentAccountCode: selectedBankAccountCode,
        companyId: parseInt(companyId, 10),
      };

      const response = await fetch(
        `${apiUrl}/api/sales-orders/${selectedSoId}/pay`,
        {
          method: "POST",
          headers: getAuthHeaders(),
          body: JSON.stringify(payload),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText || "Payment submission failed.");
      }

      Alert.success("Payment received successfully!");

      // Reset form fields
      setSelectedCustomer("");
      setSelectedSoId("");
      setPaymentAmount("");
      setPaymentNote("");
      setReferenceNumber(`REF-${Math.floor(Date.now() / 1000)}`);

      // Fetch fresh SOs to update status in memory
      const soRes = await fetch(`${apiUrl}/api/sales-orders/company/${companyId}`, { headers: getAuthHeaders() });
      if (soRes.ok) {
        const soData = await soRes.json();
        setSalesOrders(Array.isArray(soData) ? soData : soData?.data || soData?.salesOrders || []);
      }
    } catch (err) {
      console.error("Receipt record error:", err);
      Alert.error(err.message || "Failed to record payment.");
    } finally {
      setIsSubmitting(false);
    }
  };

  if (isLoading) {
    return (
      <div className="flex justify-center items-center h-96">
        <FaSpinner className="animate-spin text-4xl text-blue-600" />
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto p-4 sm:p-6 bg-white shadow-xl rounded-2xl mt-6 border border-gray-100">
      <div className="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
        <div>
          <h2 className="text-2xl font-extrabold text-gray-900">Receive Money Transaction</h2>
          <p className="text-sm text-gray-500 mt-1">Record a payment received against an outstanding sale order</p>
        </div>
        <button
          onClick={() => navigate("/customer/sales/all")}
          className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors"
        >
          <FaArrowLeft /> Back to Sales
        </button>
      </div>

      {errorMsg && (
        <div className="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg flex items-center gap-3">
          <FaExclamationTriangle className="text-xl flex-shrink-0" />
          <span>{errorMsg}</span>
        </div>
      )}

      <form onSubmit={handleRecordPayment} className="space-y-6">
        {/* Core details grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Bank Account */}
          <div>
            <label className="block text-gray-700 font-semibold mb-2">
              Deposit Account <span className="text-red-500">*</span>
            </label>
            <select
              value={selectedBankAccountCode}
              onChange={(e) => setSelectedBankAccountCode(e.target.value)}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-gray-50 font-medium transition-all"
              required
            >
              <option value="">Select Account</option>
              {accounts.map((acc) => (
                <option key={acc.id} value={acc.accountCode}>
                  {getAccountLabel(acc)}
                </option>
              ))}
            </select>
          </div>

          {/* Date */}
          <div>
            <label className="block text-gray-700 font-semibold mb-2">
              Receipt Date <span className="text-red-500">*</span>
            </label>
            <input
              type="date"
              value={date}
              onChange={(e) => setDate(e.target.value)}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-gray-50 font-medium transition-all"
              required
            />
          </div>

          {/* Reference Number */}
          <div>
            <label className="block text-gray-700 font-semibold mb-2">Reference Number</label>
            <input
              type="text"
              value={referenceNumber}
              onChange={(e) => setReferenceNumber(e.target.value)}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-gray-50 font-medium transition-all"
              placeholder="e.g. REF-12345"
            />
          </div>

          {/* Customer Dropdown */}
          <div>
            <label className="block text-gray-700 font-semibold mb-2">
              Customer <span className="text-red-500">*</span>
            </label>
            <select
              value={selectedCustomer}
              onChange={(e) => {
                setSelectedCustomer(e.target.value);
                setSelectedSoId(""); // Reset SO when customer changes
              }}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-gray-50 font-medium transition-all"
              required
            >
              <option value="">Select Customer</option>
              {customers.map((cust) => {
                const cId = getCustomerId(cust);
                return (
                  <option key={cId} value={cId}>
                    {getCustomerName(cust)}
                  </option>
                );
              })}
            </select>
          </div>
        </div>

        {/* SO and Payment amount details */}
        {selectedCustomer && (
          <div className="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Sales Order Dropdown */}
              <div>
                <label className="block text-blue-900 font-semibold mb-2">
                  Select Sales Order <span className="text-red-500">*</span>
                </label>
                {customerSalesOrders.length === 0 ? (
                  <div className="text-amber-700 font-medium py-3 text-sm flex items-center gap-2">
                    <FaExclamationTriangle /> No outstanding sales orders for this customer.
                  </div>
                ) : (
                  <select
                    value={selectedSoId}
                    onChange={(e) => setSelectedSoId(e.target.value)}
                    className="w-full px-4 py-3 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-white font-medium transition-all"
                    required
                  >
                    <option value="">Choose Sales Order</option>
                    {customerSalesOrders.map((so) => (
                      <option key={so.id} value={so.id}>
                        {so.soNumber || `SO #${so.id}`} (Balance: Rs. {Number(so.balanceDue).toFixed(2)})
                      </option>
                    ))}
                  </select>
                )}
              </div>

              {/* Payment Amount */}
              {selectedSoId && currentSo && (
                <div>
                  <label className="block text-blue-900 font-semibold mb-2">
                    Received Amount <span className="text-red-500">*</span>
                  </label>
                  <div className="relative">
                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 font-semibold">
                      Rs.
                    </div>
                    <input
                      type="number"
                      step="0.01"
                      min="0.01"
                      max={Number(currentSo.balanceDue)}
                      value={paymentAmount}
                      onChange={(e) => setPaymentAmount(e.target.value)}
                      className="w-full pl-12 pr-4 py-3 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-white font-semibold transition-all"
                      placeholder="0.00"
                      required
                    />
                  </div>
                  <span className="text-xs text-blue-700 mt-2 block font-medium">
                    Maximum receivable: Rs. {Number(currentSo.balanceDue).toFixed(2)}
                  </span>
                </div>
              )}
            </div>

            {/* SO Financial Snapshot Card */}
            {selectedSoId && currentSo && (
              <div className="bg-white p-4 rounded-xl border border-blue-200/60 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Invoice Amt</span>
                  <span className="text-sm font-bold text-gray-800">Rs. {Number(currentSo.total).toFixed(2)}</span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Amount Received</span>
                  <span className="text-sm font-bold text-green-600">Rs. {Number(currentSo.amountPaid || 0).toFixed(2)}</span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Balance Due</span>
                  <span className="text-sm font-bold text-red-600">Rs. {Number(currentSo.balanceDue).toFixed(2)}</span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Due Date</span>
                  <span className="text-sm font-bold text-gray-800">{currentSo.dueDate || currentSo.issueDate || "-"}</span>
                </div>
              </div>
            )}
          </div>
        )}

        {/* Transaction Description */}
        <div>
          <label className="block text-gray-700 font-semibold mb-2">Description / Notes</label>
          <textarea
            value={paymentNote}
            onChange={(e) => setPaymentNote(e.target.value)}
            className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-gray-50 font-medium transition-all"
            rows="3"
            placeholder="Describe the nature of this transaction..."
          />
        </div>

        {/* Action buttons */}
        <div className="flex justify-end gap-4 pt-4 border-t border-gray-100">
          <button
            type="button"
            onClick={() => navigate("/customer/sales/all")}
            className="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold transition-colors"
            disabled={isSubmitting}
          >
            Cancel
          </button>
          <button
            type="submit"
            className="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg hover:shadow-blue-500/20 disabled:bg-blue-400 disabled:cursor-not-allowed transition-all"
            disabled={isSubmitting || !selectedSoId}
          >
            {isSubmitting ? (
              <>
                <FaSpinner className="animate-spin" /> Recording...
              </>
            ) : (
              "Record Receipt"
            )}
          </button>
        </div>
      </form>
    </div>
  );
};

export default ReceiveMoney;