import React, { useState, useEffect } from "react";
import { FaSpinner, FaArrowLeft, FaCheckCircle, FaExclamationTriangle } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import { apiUrl } from "../../utils/api";
import Alert from "../Alert/Alert";

const SpendMoney = () => {
  const navigate = useNavigate();
  
  // State variables
  const [accounts, setAccounts] = useState([]);
  const [suppliers, setSuppliers] = useState([]);
  const [purchaseOrders, setPurchaseOrders] = useState([]);
  
  const [selectedSupplier, setSelectedSupplier] = useState("");
  const [selectedPoId, setSelectedPoId] = useState("");
  const [selectedBankAccountCode, setSelectedBankAccountCode] = useState("");
  const [paymentAmount, setPaymentAmount] = useState("");
  const [date, setDate] = useState(new Date().toISOString().split("T")[0]);
  const [referenceNumber, setReferenceNumber] = useState(`REF-${Math.floor(Date.now() / 1000)}`);
  const [paymentNote, setPaymentNote] = useState("");

  const [isLoading, setIsLoading] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [message, setMessage] = useState("");
  const [errorMsg, setErrorMsg] = useState("");

  const companyId = sessionStorage.getItem("companyId");
  const token = sessionStorage.getItem("auth_token");

  // Helper functions
  const getSupplierId = (supplier) => {
    return supplier.id || supplier.supplierId || supplier.supplier_id || supplier.supplierID || "";
  };

  const getSupplierName = (supplier) => {
    return supplier.supplierName || supplier.name || supplier.companyName || supplier.email || "Unnamed Supplier";
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

  // Fetch initial data (accounts, suppliers, POs)
  useEffect(() => {
    const fetchData = async () => {
      if (!companyId || !token) {
        Alert.error("Missing company ID or auth token. Please login again.");
        return;
      }

      setIsLoading(true);
      setErrorMsg("");

      try {
        const [accRes, supRes, poRes] = await Promise.all([
          fetch(`${apiUrl}/api/companies/${companyId}/accounts`, { headers: getAuthHeaders() }),
          fetch(`${apiUrl}/api/suppliers/companies/${companyId}/active`, { headers: getAuthHeaders() }),
          fetch(`${apiUrl}/api/${companyId}/purchase-orders`, { headers: getAuthHeaders() })
        ]);

        if (accRes.ok) {
          const accData = await accRes.json();
          setAccounts(Array.isArray(accData) ? accData : accData?.data || []);
        } else {
          console.error("Failed to fetch accounts");
        }

        if (supRes.ok) {
          const supData = await supRes.json();
          setSuppliers(Array.isArray(supData) ? supData : supData?.data || []);
        } else {
          console.error("Failed to fetch suppliers");
        }

        if (poRes.ok) {
          const poData = await poRes.json();
          setPurchaseOrders(Array.isArray(poData) ? poData : poData?.data || poData?.purchaseOrders || []);
        } else {
          console.error("Failed to fetch purchase orders");
        }

      } catch (err) {
        console.error("Error loading data:", err);
        setErrorMsg("Failed to load layout data from server.");
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, [companyId, token]);

  // Unpaid purchase orders filtered by the selected supplier
  const supplierPurchaseOrders = purchaseOrders.filter((po) => {
    return (
      String(po.supplierId) === String(selectedSupplier) &&
      Number(po.balanceDue || 0) > 0
    );
  });

  // Selected Purchase Order details
  const currentPo = purchaseOrders.find((po) => String(po.id) === String(selectedPoId));

  // Set default amount when selected PO changes
  useEffect(() => {
    if (currentPo) {
      setPaymentAmount(Number(currentPo.balanceDue || 0).toFixed(2));
    } else {
      setPaymentAmount("");
    }
  }, [selectedPoId, purchaseOrders]);

  const handleRecordPayment = async (e) => {
    e.preventDefault();

    if (!selectedBankAccountCode) {
      Alert.error("Please select a bank account.");
      return;
    }

    if (!selectedSupplier) {
      Alert.error("Please select a supplier.");
      return;
    }

    if (!selectedPoId) {
      Alert.error("Please select a purchase order.");
      return;
    }

    const amount = parseFloat(paymentAmount);
    if (isNaN(amount) || amount <= 0) {
      Alert.error("Please enter a valid payment amount greater than zero.");
      return;
    }

    if (currentPo && amount > Number(currentPo.balanceDue)) {
      Alert.error(`Payment amount cannot exceed the balance due of Rs. ${Number(currentPo.balanceDue).toFixed(2)}.`);
      return;
    }

    setIsSubmitting(true);
    setMessage("");

    try {
      const payload = {
        amount: amount,
        paymentAccountCode: selectedBankAccountCode,
        paymentNote: paymentNote.trim() || `Payment against PO #${currentPo.purchaseOrderNumber || currentPo.poNumber || selectedPoId}`,
        companyId: parseInt(companyId, 10),
      };

      const response = await fetch(
        `${apiUrl}/api/${companyId}/purchase-orders/${selectedPoId}/pay`,
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

      Alert.success("Payment recorded successfully!");
      
      // Reset form fields
      setSelectedSupplier("");
      setSelectedPoId("");
      setPaymentAmount("");
      setPaymentNote("");
      setReferenceNumber(`REF-${Math.floor(Date.now() / 1000)}`);
      
      // Fetch fresh POs to update status in memory
      const poRes = await fetch(`${apiUrl}/api/${companyId}/purchase-orders`, { headers: getAuthHeaders() });
      if (poRes.ok) {
        const poData = await poRes.json();
        setPurchaseOrders(Array.isArray(poData) ? poData : poData?.data || poData?.purchaseOrders || []);
      }
    } catch (err) {
      console.error("Payment error:", err);
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
          <h2 className="text-2xl font-extrabold text-gray-900">Spend Money Transaction</h2>
          <p className="text-sm text-gray-500 mt-1">Record a payment against an outstanding purchase order</p>
        </div>
        <button
          onClick={() => navigate("/supplier/purchase/all")}
          className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors"
        >
          <FaArrowLeft /> Back to POs
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
              Payment Account <span className="text-red-500">*</span>
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
              Payment Date <span className="text-red-500">*</span>
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

          {/* Supplier Dropdown */}
          <div>
            <label className="block text-gray-700 font-semibold mb-2">
              Supplier <span className="text-red-500">*</span>
            </label>
            <select
              value={selectedSupplier}
              onChange={(e) => {
                setSelectedSupplier(e.target.value);
                setSelectedPoId(""); // Reset PO when supplier changes
              }}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-gray-50 font-medium transition-all"
              required
            >
              <option value="">Select Supplier</option>
              {suppliers.map((sup) => {
                const sId = getSupplierId(sup);
                return (
                  <option key={sId} value={sId}>
                    {getSupplierName(sup)}
                  </option>
                );
              })}
            </select>
          </div>
        </div>

        {/* PO and Payment amount details */}
        {selectedSupplier && (
          <div className="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Purchase Order Dropdown */}
              <div>
                <label className="block text-blue-900 font-semibold mb-2">
                  Select Purchase Order <span className="text-red-500">*</span>
                </label>
                {supplierPurchaseOrders.length === 0 ? (
                  <div className="text-amber-700 font-medium py-3 text-sm flex items-center gap-2">
                    <FaExclamationTriangle /> No outstanding purchase orders for this supplier.
                  </div>
                ) : (
                  <select
                    value={selectedPoId}
                    onChange={(e) => setSelectedPoId(e.target.value)}
                    className="w-full px-4 py-3 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-white font-medium transition-all"
                    required
                  >
                    <option value="">Choose Purchase Order</option>
                    {supplierPurchaseOrders.map((po) => (
                      <option key={po.id} value={po.id}>
                        {po.purchaseOrderNumber || po.poNumber || `PO #${po.id}`} (Balance: Rs. {Number(po.balanceDue).toFixed(2)})
                      </option>
                    ))}
                  </select>
                )}
              </div>

              {/* Payment Amount */}
              {selectedPoId && currentPo && (
                <div>
                  <label className="block text-blue-900 font-semibold mb-2">
                    Payment Amount <span className="text-red-500">*</span>
                  </label>
                  <div className="relative">
                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 font-semibold">
                      Rs.
                    </div>
                    <input
                      type="number"
                      step="0.01"
                      min="0.01"
                      max={Number(currentPo.balanceDue)}
                      value={paymentAmount}
                      onChange={(e) => setPaymentAmount(e.target.value)}
                      className="w-full pl-12 pr-4 py-3 border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-900 bg-white font-semibold transition-all"
                      placeholder="0.00"
                      required
                    />
                  </div>
                  <span className="text-xs text-blue-700 mt-2 block font-medium">
                    Maximum payable: Rs. {Number(currentPo.balanceDue).toFixed(2)}
                  </span>
                </div>
              )}
            </div>

            {/* PO Financial Snapshot Card */}
            {selectedPoId && currentPo && (
              <div className="bg-white p-4 rounded-xl border border-blue-200/60 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total PO Amount</span>
                  <span className="text-sm font-bold text-gray-800">Rs. {Number(currentPo.total).toFixed(2)}</span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Amount Paid</span>
                  <span className="text-sm font-bold text-green-600">Rs. {Number(currentPo.amountPaid || 0).toFixed(2)}</span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Balance Due</span>
                  <span className="text-sm font-bold text-red-600">Rs. {Number(currentPo.balanceDue).toFixed(2)}</span>
                </div>
                <div>
                  <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Promise Date</span>
                  <span className="text-sm font-bold text-gray-800">{currentPo.promiseDate || currentPo.dueDate || "-"}</span>
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
            placeholder="Describe the nature of this payment..."
          />
        </div>

        {/* Action buttons */}
        <div className="flex justify-end gap-4 pt-4 border-t border-gray-100">
          <button
            type="button"
            onClick={() => navigate("/supplier/purchase/all")}
            className="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold transition-colors"
            disabled={isSubmitting}
          >
            Cancel
          </button>
          <button
            type="submit"
            className="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg hover:shadow-blue-500/20 disabled:bg-blue-400 disabled:cursor-not-allowed transition-all"
            disabled={isSubmitting || !selectedPoId}
          >
            {isSubmitting ? (
              <>
                <FaSpinner className="animate-spin" /> Recording...
              </>
            ) : (
              "Record Payment"
            )}
          </button>
        </div>
      </form>
    </div>
  );
};

export default SpendMoney;