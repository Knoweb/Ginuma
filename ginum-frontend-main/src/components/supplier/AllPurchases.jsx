import React, { useEffect, useState } from "react";
import {
  FaSearch,
  FaSyncAlt,
  FaEye,
  FaTimes,
  FaFileInvoiceDollar,
} from "react-icons/fa";

import { apiUrl as API_BASE_URL } from "../../utils/api";

function AllPurchases() {
  const [purchaseOrders, setPurchaseOrders] = useState([]);
  const [filteredPurchaseOrders, setFilteredPurchaseOrders] = useState([]);

  const [searchTerm, setSearchTerm] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const [selectedOrder, setSelectedOrder] = useState(null);
  const [showViewModal, setShowViewModal] = useState(false);

  const getCompanyId = () => sessionStorage.getItem("companyId");
  const getToken = () => sessionStorage.getItem("auth_token");

  const getAuthHeaders = () => {
    const token = getToken();

    return {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    };
  };

  const checkAuth = () => {
    const companyId = getCompanyId();
    const token = getToken();

    if (!companyId || !token) {
      throw new Error("Missing company ID or auth token. Please login again.");
    }

    return companyId;
  };

  const extractArray = (data) => {
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.data)) return data.data;
    if (Array.isArray(data?.content)) return data.content;
    if (Array.isArray(data?.purchaseOrders)) return data.purchaseOrders;
    return [];
  };

  const fetchPurchaseOrders = async () => {
    try {
      setLoading(true);
      setError("");

      const companyId = checkAuth();

      const response = await fetch(
        `${API_BASE_URL}/api/${companyId}/purchase-orders`,
        {
          method: "GET",
          headers: getAuthHeaders(),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error("Purchase orders fetch error:", errorText);
        throw new Error(errorText || `HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      console.log("Purchase Orders API Response:", data);

      const list = extractArray(data);

      setPurchaseOrders(list);
      setFilteredPurchaseOrders(list);
    } catch (err) {
      console.error("Failed to fetch purchase orders:", err);
      setError("Failed to fetch purchase orders. " + err.message);
      setPurchaseOrders([]);
      setFilteredPurchaseOrders([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchPurchaseOrders();
  }, []);

  useEffect(() => {
    const term = searchTerm.toLowerCase();

    const filtered = purchaseOrders.filter((order) => {
      const poNumber = order.purchaseOrderNumber || order.poNumber || "";
      const supplierName = order.supplierName || "";
      const supplierInvoiceNumber = order.supplierInvoiceNumber || "";
      const purchaseType = order.purchaseType || "";

      return (
        String(poNumber).toLowerCase().includes(term) ||
        String(supplierName).toLowerCase().includes(term) ||
        String(supplierInvoiceNumber).toLowerCase().includes(term) ||
        String(purchaseType).toLowerCase().includes(term) ||
        String(order.total || "").includes(term) ||
        String(order.balanceDue || "").includes(term)
      );
    });

    setFilteredPurchaseOrders(filtered);
  }, [searchTerm, purchaseOrders]);

  const formatAmount = (amount) => {
    const value = Number(amount || 0);

    return value.toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  };

  const formatDate = (date) => {
    if (!date) return "-";
    return date;
  };

  const formatText = (text) => {
    if (!text) return "-";

    return text
      .toString()
      .replace(/_/g, " ")
      .toLowerCase()
      .replace(/\b\w/g, (char) => char.toUpperCase());
  };

  const getPurchaseTypeLabel = (type) => {
    if (type === "GOODS") return "Items";
    if (type === "SERVICES") return "Services";
    return formatText(type);
  };

  const getPaymentStatus = (order) => {
    const total = Number(order.total || 0);
    const paid = Number(order.amountPaid || 0);
    const balance = Number(order.balanceDue || 0);

    if (total > 0 && balance === 0) {
      return {
        label: "Paid",
        className: "bg-green-100 text-green-700",
      };
    }

    if (paid > 0 && balance > 0) {
      return {
        label: "Partial",
        className: "bg-yellow-100 text-yellow-700",
      };
    }

    return {
      label: "Unpaid",
      className: "bg-red-100 text-red-700",
    };
  };

  const openViewModal = (order) => {
    setSelectedOrder(order);
    setShowViewModal(true);
  };

  const closeViewModal = () => {
    setSelectedOrder(null);
    setShowViewModal(false);
  };

  const getOrderNumber = (order) => {
    return order.purchaseOrderNumber || order.poNumber || "-";
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

        <button
          type="button"
          onClick={fetchPurchaseOrders}
          className="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2"
        >
          <FaSyncAlt /> Retry
        </button>
      </div>
    );
  }

  return (
    <>
      <div className="p-6 lg:p-8 flex flex-col gap-6">
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
          <div>
            <h1 className="gl-heading">
              All Purchase Orders
            </h1>
            <p className="gl-subheading mt-1">
              View and manage company purchase orders
            </p>
          </div>

          <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto mt-4 md:mt-0">
            <div className="relative flex-grow">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <FaSearch className="text-gray-400" />
              </div>

              <input
                type="text"
                placeholder="Search purchase orders..."
                className="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>

            <button
              type="button"
              onClick={fetchPurchaseOrders}
              className="gl-btn gl-btn-secondary"
            >
              <FaSyncAlt /> Refresh
            </button>
          </div>
        </div>

        {filteredPurchaseOrders.length === 0 ? (
          <div className="gl-card p-12 text-center flex flex-col items-center justify-center min-h-[300px]">
            <div className="h-16 w-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500 mb-4 shadow-sm border border-indigo-100/50 mx-auto">
              <FaFileInvoiceDollar size={30} />
            </div>

            <p className="text-gray-600 text-lg">
              {purchaseOrders.length === 0
                ? "No purchase orders found."
                : "No matching purchase orders found."}
            </p>
          </div>
        ) : (
          <div className="gl-table-container">
            <div className="overflow-x-auto">
              <table className="gl-table">
                <thead>
                  <tr>
                    <th>
                      PO Number
                    </th>

                    <th>
                      Supplier
                    </th>

                    <th>
                      Supplier Invoice
                    </th>

                    <th>
                      Issue Date
                    </th>

                    <th>
                      Type
                    </th>

                    <th className="text-right">
                      Total
                    </th>

                    <th className="text-right">
                      Paid
                    </th>

                    <th className="text-right">
                      Balance
                    </th>

                    <th className="text-center">
                      Status
                    </th>

                    <th className="text-right">
                      Actions
                    </th>
                  </tr>
                </thead>

                <tbody>
                  {filteredPurchaseOrders.map((order) => {
                    const status = getPaymentStatus(order);

                    return (
                      <tr
                        key={order.id}
                        
                      >
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-semibold text-gray-900">
                            {getOrderNumber(order)}
                          </div>
                          <div className="text-xs text-gray-500">
                            ID: {order.id}
                          </div>
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {order.supplierName || "-"}
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {order.supplierInvoiceNumber || "-"}
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {formatDate(order.issueDate)}
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {getPurchaseTypeLabel(order.purchaseType)}
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                          Rs. {formatAmount(order.total)}
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                          Rs. {formatAmount(order.amountPaid)}
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                          Rs. {formatAmount(order.balanceDue)}
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-center">
                          <span
                            className={`inline-block px-2 py-1 text-xs rounded-full font-semibold ${status.className}`}
                          >
                            {status.label}
                          </span>
                        </td>

                        <td className="px-6 py-4 whitespace-nowrap text-right">
                          <button
                            type="button"
                            onClick={() => openViewModal(order)}
                            className="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg inline-flex items-center gap-2"
                          >
                            <FaEye /> View
                          </button>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>

            <div className="bg-gray-50 px-6 py-3 text-sm text-gray-600">
              Showing {filteredPurchaseOrders.length} of{" "}
              {purchaseOrders.length} purchase orders
            </div>
          </div>
        )}
      </div>

      {showViewModal && selectedOrder && (
        <div className="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg w-full max-w-5xl p-6 relative max-h-[90vh] overflow-y-auto">
            <button
              type="button"
              onClick={closeViewModal}
              className="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
            >
              <FaTimes />
            </button>

            <h2 className="section-title mb-4">
              Purchase Order Details
            </h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div>
                <p className="gl-label">PO Number</p>
                <p className="font-semibold">{getOrderNumber(selectedOrder)}</p>
              </div>

              <div>
                <p className="gl-label">Supplier</p>
                <p className="font-semibold">
                  {selectedOrder.supplierName || "-"}
                </p>
              </div>

              <div>
                <p className="gl-label">Supplier Invoice Number</p>
                <p className="font-semibold">
                  {selectedOrder.supplierInvoiceNumber || "-"}
                </p>
              </div>

              <div>
                <p className="gl-label">Issue Date</p>
                <p className="font-semibold">
                  {formatDate(selectedOrder.issueDate)}
                </p>
              </div>

              <div>
                <p className="gl-label">Purchase Type</p>
                <p className="font-semibold">
                  {getPurchaseTypeLabel(selectedOrder.purchaseType)}
                </p>
              </div>

              <div>
                <p className="gl-label">Balance Due</p>
                <p className="font-semibold">
                  Rs. {formatAmount(selectedOrder.balanceDue)}
                </p>
              </div>
            </div>

            {selectedOrder.notes && (
              <div className="mb-6">
                <p className="gl-label">Notes</p>
                <p className="bg-gray-50 border rounded-lg p-3 text-gray-700">
                  {selectedOrder.notes}
                </p>
              </div>
            )}

            <h3 className="text-lg font-semibold text-gray-800 mb-3">
              Line Items
            </h3>

            {selectedOrder.items && selectedOrder.items.length > 0 ? (
              <div className="overflow-x-auto border rounded-lg">
                <table className="gl-table">
                  <thead>
                    <tr>
                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Item
                      </th>

                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Description
                      </th>

                      <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                        Qty
                      </th>

                      <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                        Unit Price
                      </th>

                      <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                        Discount
                      </th>

                      <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                        Amount
                      </th>

                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Account
                      </th>

                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Project ID
                      </th>
                    </tr>
                  </thead>

                  <tbody>
                    {selectedOrder.items.map((item, index) => (
                      <tr key={index}>
                        <td className="px-4 py-2 text-sm text-gray-900">
                          {item.itemName || "-"}
                        </td>

                        <td className="px-4 py-2 text-sm text-gray-900">
                          {item.description || "-"}
                        </td>

                        <td className="px-4 py-2 text-sm text-gray-900 text-right">
                          {item.quantity || 0}
                        </td>

                        <td className="px-4 py-2 text-sm text-gray-900 text-right">
                          Rs. {formatAmount(item.unitPrice)}
                        </td>

                        <td className="px-4 py-2 text-sm text-gray-900 text-right">
                          {Number(item.discountPercent || 0)}%
                        </td>

                        <td className="px-4 py-2 text-sm text-gray-900 text-right font-semibold">
                          Rs. {formatAmount(item.amount)}
                        </td>

                        <td className="px-4 py-2 text-sm text-gray-900">
                          {item.accountCode || "-"}
                        </td>

                        <td className="px-4 py-2 text-sm text-gray-900">
                          {item.projectId || "-"}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="bg-gray-50 border rounded-lg p-4 text-gray-600">
                No line items found.
              </div>
            )}

            <div className="flex justify-end mt-6">
              <div className="w-full md:w-1/2 space-y-2">
                <div className="flex justify-between">
                  <span className="text-gray-600">Subtotal:</span>
                  <span className="font-semibold">
                    Rs. {formatAmount(selectedOrder.subtotal)}
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-gray-600">Freight:</span>
                  <span className="font-semibold">
                    Rs. {formatAmount(selectedOrder.freight)}
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-gray-600">Tax:</span>
                  <span className="font-semibold">
                    Rs. {formatAmount(selectedOrder.taxAmount)}
                  </span>
                </div>

                <div className="flex justify-between border-t pt-2">
                  <span className="text-gray-800 font-semibold">Total:</span>
                  <span className="font-bold">
                    Rs. {formatAmount(selectedOrder.total)}
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-gray-600">Amount Paid:</span>
                  <span className="font-semibold">
                    Rs. {formatAmount(selectedOrder.amountPaid)}
                  </span>
                </div>

                <div className="flex justify-between border-t pt-2">
                  <span className="text-gray-800 font-semibold">
                    Balance Due:
                  </span>
                  <span className="font-bold">
                    Rs. {formatAmount(selectedOrder.balanceDue)}
                  </span>
                </div>
              </div>
            </div>

            <div className="flex justify-end mt-6">
              <button
                type="button"
                onClick={closeViewModal}
                className="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}

export default AllPurchases;