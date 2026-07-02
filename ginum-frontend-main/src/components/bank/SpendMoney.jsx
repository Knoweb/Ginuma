import React, { useState, useEffect } from "react";
import {
  FaSpinner,
  FaArrowLeft,
  FaExclamationTriangle,
  FaMoneyBillWave,
  FaBuilding,
  FaUniversity,
} from "react-icons/fa";
import {
  FiCreditCard,
  FiCalendar,
  FiHash,
  FiFileText,
  FiCheckCircle,
  FiAlertCircle,
  FiArrowRight,
  FiDollarSign,
} from "react-icons/fi";
import { useNavigate } from "react-router-dom";
import { apiUrl } from "../../utils/api";
import Alert from "../Alert/Alert";

const SpendMoney = () => {
  const navigate = useNavigate();

  const [accounts, setAccounts] = useState([]);
  const [suppliers, setSuppliers] = useState([]);
  const [purchaseOrders, setPurchaseOrders] = useState([]);

  const [selectedSupplier, setSelectedSupplier] = useState("");
  const [selectedPoId, setSelectedPoId] = useState("");
  const [selectedBankAccountCode, setSelectedBankAccountCode] = useState("");
  const [paymentAmount, setPaymentAmount] = useState("");
  const [date, setDate] = useState(new Date().toISOString().split("T")[0]);
  const [referenceNumber, setReferenceNumber] = useState(
    `REF-${Math.floor(Date.now() / 1000)}`
  );
  const [paymentNote, setPaymentNote] = useState("");

  const [isLoading, setIsLoading] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMsg, setErrorMsg] = useState("");

  const companyId = sessionStorage.getItem("companyId");
  const token = sessionStorage.getItem("auth_token");

  const getSupplierId = (supplier) =>
    supplier.id ||
    supplier.supplierId ||
    supplier.supplier_id ||
    supplier.supplierID ||
    "";

  const getSupplierName = (supplier) =>
    supplier.supplierName ||
    supplier.name ||
    supplier.companyName ||
    supplier.email ||
    "Unnamed Supplier";

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
          fetch(`${apiUrl}/api/companies/${companyId}/accounts`, {
            headers: getAuthHeaders(),
          }),
          fetch(`${apiUrl}/api/suppliers/companies/${companyId}/active`, {
            headers: getAuthHeaders(),
          }),
          fetch(`${apiUrl}/api/${companyId}/purchase-orders`, {
            headers: getAuthHeaders(),
          }),
        ]);

        if (accRes.ok) {
          const accData = await accRes.json();
          setAccounts(Array.isArray(accData) ? accData : accData?.data || []);
        }
        if (supRes.ok) {
          const supData = await supRes.json();
          setSuppliers(Array.isArray(supData) ? supData : supData?.data || []);
        }
        if (poRes.ok) {
          const poData = await poRes.json();
          setPurchaseOrders(
            Array.isArray(poData)
              ? poData
              : poData?.data || poData?.purchaseOrders || []
          );
        }
      } catch (err) {
        console.error("Error loading data:", err);
        setErrorMsg("Failed to load data from server. Please refresh.");
      } finally {
        setIsLoading(false);
      }
    };

    fetchData();
  }, [companyId, token]);

  const supplierPurchaseOrders = purchaseOrders.filter(
    (po) =>
      String(po.supplierId) === String(selectedSupplier) &&
      Number(po.balanceDue || 0) > 0
  );

  const currentPo = purchaseOrders.find(
    (po) => String(po.id) === String(selectedPoId)
  );

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
      Alert.error("Please select a payment account.");
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
      Alert.error(
        `Amount cannot exceed the balance due of Rs. ${Number(currentPo.balanceDue).toFixed(2)}.`
      );
      return;
    }

    setIsSubmitting(true);

    try {
      const payload = {
        amount,
        paymentAccountCode: selectedBankAccountCode,
        paymentNote:
          paymentNote.trim() ||
          `Payment against PO #${currentPo.purchaseOrderNumber || currentPo.poNumber || selectedPoId}`,
        companyId: parseInt(companyId, 10),
      };

      const response = await fetch(
        `${apiUrl}/api/${companyId}/purchase-orders/${selectedPoId}/pay`,
        { method: "POST", headers: getAuthHeaders(), body: JSON.stringify(payload) }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText || "Payment submission failed.");
      }

      Alert.success("Payment recorded successfully!");

      setSelectedSupplier("");
      setSelectedPoId("");
      setPaymentAmount("");
      setPaymentNote("");
      setReferenceNumber(`REF-${Math.floor(Date.now() / 1000)}`);

      const poRes = await fetch(
        `${apiUrl}/api/${companyId}/purchase-orders`,
        { headers: getAuthHeaders() }
      );
      if (poRes.ok) {
        const poData = await poRes.json();
        setPurchaseOrders(
          Array.isArray(poData)
            ? poData
            : poData?.data || poData?.purchaseOrders || []
        );
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
      <div className="flex flex-col justify-center items-center h-96 gap-4">
        <FaSpinner className="animate-spin text-4xl text-blue-600" />
        <p className="text-gray-500 font-medium">Loading transaction data...</p>
      </div>
    );
  }

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div className="flex items-start gap-4">
          <div className="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center flex-shrink-0">
            <FaMoneyBillWave className="text-red-600 text-xl" />
          </div>
          <div>
            <h2 className="text-2xl font-bold text-gray-900 mb-2">
              Spend Money
            </h2>
            <p className="text-sm text-gray-500 mt-0.5">
              Record an outgoing payment against a supplier purchase order
            </p>
          </div>
        </div>
        <div className="flex items-center gap-2">
          <button
            onClick={() => navigate("/bank/receive-money")}
            className="px-4 py-2 text-sm border border-gray-300 rounded-xl text-gray-600 hover:bg-gray-100 font-semibold transition-colors flex items-center gap-1.5 cursor-pointer"
          >
            Switch to Receive Money <FiArrowRight />
          </button>
          <button
            onClick={() => navigate("/supplier/purchase/all")}
            className="px-4 py-2 text-sm border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 font-semibold transition-colors flex items-center gap-1.5 cursor-pointer"
          >
            <FaArrowLeft /> Back to POs
          </button>
        </div>
      </div>

      {/* Error Banner */}
      {errorMsg && (
        <div className="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl flex items-center gap-3">
          <FaExclamationTriangle className="text-xl flex-shrink-0" />
          <span className="font-medium">{errorMsg}</span>
        </div>
      )}

      <form onSubmit={handleRecordPayment}>
        <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
          {/* Left Column - Main Form */}
          <div className="xl:col-span-2 space-y-5">

            {/* Step 1: Transaction Info */}
            <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
              <div className="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center gap-2">
                <span className="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                  1
                </span>
                <h3 className="font-bold text-gray-800 text-sm">
                  Transaction Details
                </h3>
              </div>
              <div className="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                {/* Payment Account */}
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                    <FiCreditCard className="text-blue-500" />
                    Payment Account <span className="text-red-500">*</span>
                  </label>
                  <select
                    value={selectedBankAccountCode}
                    onChange={(e) => setSelectedBankAccountCode(e.target.value)}
                    className="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 bg-gray-50 transition-all cursor-pointer"
                    required
                  >
                    <option value="">Select Account</option>
                    {accounts.map((acc) => (
                      <option key={acc.id || acc.accountCode} value={acc.accountCode}>
                        {getAccountLabel(acc)}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Payment Date */}
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                    <FiCalendar className="text-blue-500" />
                    Payment Date <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="date"
                    value={date}
                    onChange={(e) => setDate(e.target.value)}
                    className="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 bg-gray-50 transition-all"
                    required
                  />
                </div>

                {/* Reference Number */}
                <div className="sm:col-span-2">
                  <label className="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                    <FiHash className="text-blue-500" />
                    Reference Number
                  </label>
                  <input
                    type="text"
                    value={referenceNumber}
                    onChange={(e) => setReferenceNumber(e.target.value)}
                    className="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 bg-gray-50 transition-all"
                    placeholder="e.g. REF-12345"
                  />
                </div>
              </div>
            </div>

            {/* Step 2: Supplier & PO */}
            <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
              <div className="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center gap-2">
                <span className="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                  2
                </span>
                <h3 className="font-bold text-gray-800 text-sm">
                  Supplier & Purchase Order
                </h3>
              </div>
              <div className="p-6 space-y-5">
                {/* Supplier */}
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                    <FaBuilding className="text-blue-500" />
                    Supplier <span className="text-red-500">*</span>
                  </label>
                  <select
                    value={selectedSupplier}
                    onChange={(e) => {
                      setSelectedSupplier(e.target.value);
                      setSelectedPoId("");
                    }}
                    className="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 bg-gray-50 transition-all cursor-pointer"
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

                {/* Purchase Order (shown only after supplier selected) */}
                {selectedSupplier && (
                  <div className="pt-1">
                    <label className="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                      <FiFileText className="text-blue-500" />
                      Purchase Order <span className="text-red-500">*</span>
                    </label>
                    {supplierPurchaseOrders.length === 0 ? (
                      <div className="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm font-medium">
                        <FaExclamationTriangle />
                        No outstanding purchase orders found for this supplier.
                      </div>
                    ) : (
                      <select
                        value={selectedPoId}
                        onChange={(e) => setSelectedPoId(e.target.value)}
                        className="w-full px-3 py-2.5 border border-blue-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 bg-blue-50/30 transition-all cursor-pointer"
                        required
                      >
                        <option value="">Choose Purchase Order</option>
                        {supplierPurchaseOrders.map((po) => (
                          <option key={po.id} value={po.id}>
                            {po.purchaseOrderNumber || po.poNumber || `PO #${po.id}`} — Balance: Rs.{" "}
                            {Number(po.balanceDue).toFixed(2)}
                          </option>
                        ))}
                      </select>
                    )}
                  </div>
                )}
              </div>
            </div>

            {/* Step 3: Payment Amount (shown only after PO selected) */}
            {selectedPoId && currentPo && (
              <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div className="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center gap-2">
                  <span className="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                    3
                  </span>
                  <h3 className="font-bold text-gray-800 text-sm">
                    Payment Amount
                  </h3>
                </div>
                <div className="p-6 space-y-4">
                  <div>
                    <label className="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                      <FiDollarSign className="text-blue-500" />
                      Amount to Pay <span className="text-red-500">*</span>
                    </label>
                    <div className="relative">
                      <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 font-bold text-sm">
                        Rs.
                      </div>
                      <input
                        type="number"
                        step="0.01"
                        min="0.01"
                        max={Number(currentPo.balanceDue)}
                        value={paymentAmount}
                        onChange={(e) => setPaymentAmount(e.target.value)}
                        className="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 font-bold text-gray-900 transition-all"
                        placeholder="0.00"
                        required
                      />
                    </div>
                    <p className="text-xs text-blue-700 mt-1.5 font-medium">
                      Maximum payable: Rs. {Number(currentPo.balanceDue).toFixed(2)}
                    </p>
                  </div>
                </div>
              </div>
            )}

            {/* Notes */}
            <div className="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
              <label className="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                <FiFileText className="text-blue-500" />
                Description / Notes
              </label>
              <textarea
                value={paymentNote}
                onChange={(e) => setPaymentNote(e.target.value)}
                className="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 bg-gray-50 transition-all resize-none"
                rows="3"
                placeholder="Describe the nature of this payment transaction..."
              />
            </div>
          </div>

          {/* Right Column - Summary */}
          <div className="space-y-5">
            {/* PO Financial Summary Card */}
            {selectedPoId && currentPo ? (
              <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div className="px-5 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white">
                  <p className="text-xs font-semibold uppercase tracking-wider opacity-80">
                    Purchase Order Summary
                  </p>
                  <p className="text-lg font-bold mt-0.5">
                    {currentPo.purchaseOrderNumber || currentPo.poNumber || `PO #${currentPo.id}`}
                  </p>
                </div>
                <div className="p-5 space-y-4">
                  <div className="flex justify-between items-center text-sm">
                    <span className="text-gray-500">Total Amount</span>
                    <span className="font-bold text-gray-800">
                      Rs. {Number(currentPo.total || 0).toFixed(2)}
                    </span>
                  </div>
                  <div className="flex justify-between items-center text-sm">
                    <span className="text-gray-500">Amount Paid</span>
                    <span className="font-bold text-green-600">
                      Rs. {Number(currentPo.amountPaid || 0).toFixed(2)}
                    </span>
                  </div>
                  <div className="flex justify-between items-center text-sm border-t border-gray-100 pt-4">
                    <span className="text-gray-700 font-semibold">Balance Due</span>
                    <span className="font-extrabold text-red-600 text-base">
                      Rs. {Number(currentPo.balanceDue || 0).toFixed(2)}
                    </span>
                  </div>
                  {(currentPo.promiseDate || currentPo.dueDate) && (
                    <div className="flex justify-between items-center text-sm border-t border-gray-100 pt-3">
                      <span className="text-gray-500">Due Date</span>
                      <span className="font-semibold text-gray-700">
                        {currentPo.promiseDate || currentPo.dueDate}
                      </span>
                    </div>
                  )}
                </div>
              </div>
            ) : (
              <div className="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center">
                <div className="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                  <FiFileText className="text-gray-400 text-2xl" />
                </div>
                <p className="text-sm text-gray-500 font-medium">
                  Select a supplier and purchase order to see the financial summary
                </p>
              </div>
            )}

            {/* Payment Amount Confirmation */}
            {selectedPoId && currentPo && paymentAmount && (
              <div className="bg-red-50 border border-red-100 rounded-2xl p-5">
                <p className="text-xs font-semibold text-red-500 uppercase tracking-wider mb-1">
                  You are paying
                </p>
                <p className="text-3xl font-extrabold text-red-700">
                  Rs. {Number(paymentAmount || 0).toFixed(2)}
                </p>
                <p className="text-xs text-red-500 mt-1">
                  to {getSupplierName(suppliers.find((s) => String(getSupplierId(s)) === String(selectedSupplier)) || {})}
                </p>
              </div>
            )}

            {/* Action Buttons */}
            <div className="space-y-3">
              <button
                type="submit"
                className="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-red-500/20 disabled:bg-red-300 disabled:cursor-not-allowed transition-all cursor-pointer text-sm"
                disabled={isSubmitting || !selectedPoId}
              >
                {isSubmitting ? (
                  <>
                    <FaSpinner className="animate-spin" /> Recording Payment...
                  </>
                ) : (
                  <>
                    <FaMoneyBillWave /> Record Payment
                  </>
                )}
              </button>
              <button
                type="button"
                onClick={() => navigate("/supplier/purchase/all")}
                className="w-full py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors cursor-pointer text-sm"
                disabled={isSubmitting}
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  );
};

export default SpendMoney;