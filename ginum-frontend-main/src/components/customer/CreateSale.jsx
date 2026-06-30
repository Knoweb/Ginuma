import React, { useState, useEffect } from "react";
import { MdOutlineCancel, MdAddCircleOutline } from "react-icons/md";
import { FaTimes } from "react-icons/fa";
import AddAccountForm from "../account/AddAccountForm";
import NewProjectForm from "../projects/NewProjectForm";

const API_BASE_URL = "http://localhost:8081";

const emptyRow = {
  itemId: "",
  description: "",
  accountCode: "",
  quantity: "",
  unitPrice: "",
  discount: "",
  amount: "",
  projectId: "",
};

const CreateSaleOrder = () => {
  const [isServiceMode, setIsServiceMode] = useState(false);

  const [rows, setRows] = useState([{ ...emptyRow }]);

  const [selectedCustomer, setSelectedCustomer] = useState("");
  const [saleOrderNumber, setSaleOrderNumber] = useState("");
  const [orderDate, setOrderDate] = useState(
    new Date().toISOString().split("T")[0]
  );
  const [dueDate, setDueDate] = useState("");
  const [notes, setNotes] = useState("");

  const [customers, setCustomers] = useState([]);
  const [accounts, setAccounts] = useState([]);
  const [projects, setProjects] = useState([]);
  const [items, setItems] = useState([]);

  const [isLoadingCustomers, setIsLoadingCustomers] = useState(false);
  const [isLoadingAccounts, setIsLoadingAccounts] = useState(false);
  const [isLoadingProjects, setIsLoadingProjects] = useState(false);
  const [isLoadingItems, setIsLoadingItems] = useState(false);

  const [customersError, setCustomersError] = useState("");
  const [accountsError, setAccountsError] = useState("");
  const [projectsError, setProjectsError] = useState("");
  const [itemsError, setItemsError] = useState("");

  const [showAccountModal, setShowAccountModal] = useState(false);
  const [showProjectModal, setShowProjectModal] = useState(false);
  const [modalTransition, setModalTransition] = useState("opacity-0 invisible");

  const [subtotal, setSubtotal] = useState(0);
  const [amountPaid, setAmountPaid] = useState("");
  const [balanceDue, setBalanceDue] = useState(0);
  const [paymentAccountCode, setPaymentAccountCode] = useState("");

  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState("");

  const getCompanyId = () => sessionStorage.getItem("companyId");
  const getToken = () => sessionStorage.getItem("auth_token");

  const getAuthHeaders = () => {
    const token = getToken();

    return {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    };
  };

  const getJsonHeaders = () => {
    const token = getToken();

    return {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
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

  const getCustomerId = (customer) => customer.customerId || customer.id;

  const getProjectId = (project) => project.id || project.projectId;

  const getItemId = (item) => item.itemId || item.id;

  const getAccountLabel = (account) => {
    const code = account.accountCode || "";
    const name = account.accountName || account.name || "Unnamed Account";

    return code ? `${code} - ${name}` : name;
  };

  useEffect(() => {
    const newSubtotal = rows.reduce((sum, row) => {
      return sum + (Number(row.amount) || 0);
    }, 0);

    const newBalanceDue = Math.max(
      newSubtotal - (Number(amountPaid) || 0),
      0
    );

    setSubtotal(newSubtotal);
    setBalanceDue(newBalanceDue);
  }, [rows, amountPaid]);

  useEffect(() => {
    if (showAccountModal || showProjectModal) {
      setModalTransition("opacity-100 visible");
    } else {
      setModalTransition("opacity-0 invisible");
    }
  }, [showAccountModal, showProjectModal]);

  const handleModalClick = (e, setModal) => {
    if (e.target === e.currentTarget) {
      setModal(false);
    }
  };

  const fetchCustomers = async () => {
    try {
      setIsLoadingCustomers(true);
      setCustomersError("");

      const companyId = checkAuth();

      const response = await fetch(
        `${API_BASE_URL}/api/customers/companies/${companyId}`,
        {
          method: "GET",
          headers: getAuthHeaders(),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText || "Failed to load customers.");
      }

      const data = await response.json();
      setCustomers(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error("Error fetching customers:", error);
      setCustomersError("Failed to load customers.");
      setCustomers([]);
    } finally {
      setIsLoadingCustomers(false);
    }
  };

  const fetchAccounts = async () => {
    try {
      setIsLoadingAccounts(true);
      setAccountsError("");

      const companyId = checkAuth();

      const response = await fetch(
        `${API_BASE_URL}/api/companies/${companyId}/accounts`,
        {
          method: "GET",
          headers: getAuthHeaders(),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText || "Failed to load accounts.");
      }

      const data = await response.json();
      setAccounts(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error("Error fetching accounts:", error);
      setAccountsError("Failed to load accounts.");
      setAccounts([]);
    } finally {
      setIsLoadingAccounts(false);
    }
  };

  const fetchProjects = async () => {
    try {
      setIsLoadingProjects(true);
      setProjectsError("");

      const companyId = checkAuth();

      const response = await fetch(
        `${API_BASE_URL}/api/companies/${companyId}/projects`,
        {
          method: "GET",
          headers: getAuthHeaders(),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText || "Failed to load projects.");
      }

      const data = await response.json();
      setProjects(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error("Error fetching projects:", error);
      setProjectsError("Failed to load projects.");
      setProjects([]);
    } finally {
      setIsLoadingProjects(false);
    }
  };

  const fetchItems = async () => {
    try {
      setIsLoadingItems(true);
      setItemsError("");

      const companyId = checkAuth();

      const response = await fetch(
        `${API_BASE_URL}/api/companies/${companyId}/items`,
        {
          method: "GET",
          headers: getAuthHeaders(),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText || "Failed to load items.");
      }

      const data = await response.json();
      setItems(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error("Error fetching items:", error);
      setItemsError("Failed to load items.");
      setItems([]);
    } finally {
      setIsLoadingItems(false);
    }
  };

  useEffect(() => {
    fetchCustomers();
    fetchAccounts();
    fetchProjects();
    fetchItems();
  }, []);

  const calculateRowAmount = (row) => {
    if (isServiceMode) {
      return Number(row.amount || 0).toFixed(2);
    }

    const quantity = Number(row.quantity) || 0;
    const unitPrice = Number(row.unitPrice) || 0;
    const discount = Number(row.discount) || 0;

    const discountedPrice = unitPrice * (1 - discount / 100);
    return (quantity * discountedPrice).toFixed(2);
  };

  const shouldAddNewRow = (row) => {
    if (isServiceMode) {
      return row.description || row.accountCode || row.amount || row.projectId;
    }

    return (
      row.itemId ||
      row.description ||
      row.accountCode ||
      row.quantity ||
      row.unitPrice ||
      row.discount ||
      row.amount ||
      row.projectId
    );
  };

  const handleRowChange = (index, field, value) => {
    const updatedRows = [...rows];

    let updatedRow = {
      ...updatedRows[index],
      [field]: value,
    };

    if (field === "itemId") {
      const selectedItem = items.find(
        (item) => String(getItemId(item)) === String(value)
      );

      if (selectedItem) {
        updatedRow = {
          ...updatedRow,
          itemId: getItemId(selectedItem),
          description: selectedItem.description || selectedItem.name || "",
          unitPrice: selectedItem.unitPrice || "",
        };
      }
    }

    if (
      !isServiceMode &&
      (field === "itemId" ||
        field === "quantity" ||
        field === "unitPrice" ||
        field === "discount")
    ) {
      updatedRow.amount = calculateRowAmount(updatedRow);
    }

    updatedRows[index] = updatedRow;

    if (index === rows.length - 1 && shouldAddNewRow(updatedRow)) {
      updatedRows.push({ ...emptyRow });
    }

    setRows(updatedRows);
  };

  const removeRow = (index) => {
    const updatedRows = rows.filter((_, i) => i !== index);

    if (updatedRows.length === 0) {
      updatedRows.push({ ...emptyRow });
    }

    setRows(updatedRows);
  };

  const getValidRows = () => {
    return rows.filter((row) => {
      if (isServiceMode) {
        return (
          row.description.trim() &&
          row.accountCode &&
          Number(row.amount) > 0
        );
      }

      return (
        row.itemId &&
        row.accountCode &&
        Number(row.quantity) > 0 &&
        Number(row.unitPrice) > 0 &&
        Number(row.amount) > 0
      );
    });
  };

  const validateSaleOrder = () => {
    if (!selectedCustomer) {
      setMessage("Please select a customer.");
      return false;
    }

    if (!saleOrderNumber.trim()) {
      setMessage("Please enter sale order number.");
      return false;
    }

    if (!orderDate) {
      setMessage("Please select order date.");
      return false;
    }

    const validRows = getValidRows();

    if (validRows.length === 0) {
      setMessage("Please add at least one valid item or service row.");
      return false;
    }

    if (balanceDue > 0 && !dueDate) {
      setMessage("Please select due date.");
      return false;
    }

    if (Number(amountPaid || 0) > 0 && !paymentAccountCode) {
      setMessage("Please select payment account.");
      return false;
    }

    setMessage("");
    return true;
  };

  const buildPayload = () => {
    const companyId = checkAuth();
    const validRows = getValidRows();

    return {
      customerId: Number(selectedCustomer),
      soNumber: saleOrderNumber.trim(),
      issueDate: orderDate,
      dueDate: balanceDue > 0 ? dueDate : null,
      notes: notes.trim(),
      amountPaid: Number(amountPaid || 0),
      salesType: isServiceMode ? "SERVICES" : "GOODS",
      paymentAccountCode:
        Number(amountPaid || 0) > 0 ? paymentAccountCode : null,
      companyId: Number(companyId),

      items: validRows.map((row) => ({
        itemId: isServiceMode ? null : Number(row.itemId),
        description: row.description.trim(),
        accountCode: row.accountCode,
        quantity: isServiceMode ? 1 : Number(row.quantity),
        unitPrice: isServiceMode
          ? Number(row.amount)
          : Number(row.unitPrice),
        discountPercent: Number(row.discount || 0),
        projectId: row.projectId ? Number(row.projectId) : null,
        itemType: isServiceMode ? "SERVICE" : "GOODS",
      })),
    };
  };

  const resetForm = () => {
    setRows([{ ...emptyRow }]);
    setSelectedCustomer("");
    setSaleOrderNumber("");
    setOrderDate(new Date().toISOString().split("T")[0]);
    setDueDate("");
    setNotes("");
    setAmountPaid("");
    setPaymentAccountCode("");
    setMessage("");
  };

  const handleSaveSaleOrder = async () => {
    try {
      if (!validateSaleOrder()) return;

      setSaving(true);
      setMessage("");

      const companyId = checkAuth();
      const payload = buildPayload();

      console.log("Sales Order Payload:", payload);

      const response = await fetch(
        `${API_BASE_URL}/api/sales-orders/company/${companyId}`,
        {
          method: "POST",
          headers: getJsonHeaders(),
          body: JSON.stringify(payload),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error("Sales order save error:", errorText);
        throw new Error(errorText || "Sales order save failed.");
      }

      setMessage("Sales order saved successfully!");
      resetForm();
    } catch (error) {
      console.error("Cannot save sale order:", error);
      setMessage(error.message || "Sales order save failed.");
    } finally {
      setSaving(false);
    }
  };

  const closeAccountModal = () => {
    setShowAccountModal(false);
    fetchAccounts();
  };

  const closeProjectModal = () => {
    setShowProjectModal(false);
    fetchProjects();
  };

  return (
    <div className="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-4 sm:p-6 my-4 sm:mt-6">
      <h2 className="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">
        Create Sale Order
      </h2>

      {message && (
        <div
          className={`mb-4 px-4 py-3 rounded-lg ${
            message.includes("successfully")
              ? "bg-green-100 text-green-700 border border-green-300"
              : "bg-red-100 text-red-700 border border-red-300"
          }`}
        >
          {message}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
          <label className="block text-gray-700 font-medium">
            Customer <span className="text-red-500">*</span>
          </label>

          <select
            value={selectedCustomer}
            onChange={(e) => setSelectedCustomer(e.target.value)}
            className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            disabled={isLoadingCustomers}
          >
            <option value="">Select a customer</option>

            {isLoadingCustomers ? (
              <option value="" disabled>
                Loading customers...
              </option>
            ) : customersError ? (
              <option value="" disabled>
                {customersError}
              </option>
            ) : customers.length === 0 ? (
              <option value="" disabled>
                No customers found
              </option>
            ) : (
              customers.map((customer, index) => {
                const customerId = getCustomerId(customer);

                return (
                  <option key={customerId || index} value={customerId}>
                    {customer.customerName || customer.name || "Unnamed Customer"}
                  </option>
                );
              })
            )}
          </select>
        </div>

        <div>
          <label className="block text-gray-700 font-medium">
            Sale Order Number <span className="text-red-500">*</span>
          </label>

          <input
            type="text"
            value={saleOrderNumber}
            onChange={(e) => setSaleOrderNumber(e.target.value)}
            className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            placeholder="SO-000001"
          />
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
          <label className="block text-gray-700 font-medium">
            Order Date <span className="text-red-500">*</span>
          </label>

          <input
            type="date"
            value={orderDate}
            onChange={(e) => setOrderDate(e.target.value)}
            className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
          />
        </div>
      </div>

      <div className="flex space-x-4 mb-6">
        <label className="flex items-center">
          <input
            type="radio"
            name="mode"
            value="item"
            checked={!isServiceMode}
            onChange={() => {
              setIsServiceMode(false);
              setRows([{ ...emptyRow }]);
            }}
            className="form-radio h-4 w-4 text-blue-600"
          />
          <span className="ml-2 text-gray-700">Items</span>
        </label>

        <label className="flex items-center">
          <input
            type="radio"
            name="mode"
            value="service"
            checked={isServiceMode}
            onChange={() => {
              setIsServiceMode(true);
              setRows([{ ...emptyRow }]);
            }}
            className="form-radio h-4 w-4 text-blue-600"
          />
          <span className="ml-2 text-gray-700">Services</span>
        </label>
      </div>

      <div className="mb-6 overflow-x-auto">
        <table className="w-full rounded-lg">
          <thead>
            <tr className="bg-gray-100 text-gray-700 text-sm">
              {!isServiceMode && (
                <th className="p-2">
                  Item <span className="text-red-500">*</span>
                  <button
                    type="button"
                    onClick={fetchItems}
                    className="text-blue-600 hover:text-blue-700 ml-1"
                    title="Refresh items"
                  >
                    <MdAddCircleOutline className="h-5 w-5 inline" />
                  </button>
                </th>
              )}

              <th className="p-2">
                Description <span className="text-red-500">*</span>
              </th>

              <th className="p-2">
                Account <span className="text-red-500">*</span>
                <button
                  type="button"
                  onClick={() => setShowAccountModal(true)}
                  className="ml-1 text-blue-600 hover:text-blue-700"
                  title="Add account"
                >
                  <MdAddCircleOutline className="h-5 w-5 inline" />
                </button>
              </th>

              {!isServiceMode && (
                <>
                  <th className="p-2">
                    No of Units <span className="text-red-500">*</span>
                  </th>

                  <th className="p-2">
                    Unit Price <span className="text-red-500">*</span>
                  </th>

                  <th className="p-2">Discount (%)</th>
                </>
              )}

              <th className="p-2">
                Amount (Rs.) <span className="text-red-500">*</span>
              </th>

              <th className="p-2">
                Project
                <button
                  type="button"
                  onClick={() => setShowProjectModal(true)}
                  className="ml-1 text-blue-600 hover:text-blue-700"
                  title="Add project"
                >
                  <MdAddCircleOutline className="h-5 w-5 inline" />
                </button>
              </th>

              <th className="p-2"></th>
            </tr>
          </thead>

          <tbody>
            {rows.map((row, index) => (
              <tr key={index}>
                {!isServiceMode && (
                  <td className="p-2">
                    <select
                      value={row.itemId}
                      onChange={(e) =>
                        handleRowChange(index, "itemId", e.target.value)
                      }
                      className="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                      disabled={isLoadingItems}
                    >
                      <option value="">Select Item</option>

                      {isLoadingItems ? (
                        <option value="" disabled>
                          Loading items...
                        </option>
                      ) : itemsError ? (
                        <option value="" disabled>
                          {itemsError}
                        </option>
                      ) : items.length === 0 ? (
                        <option value="" disabled>
                          No items available
                        </option>
                      ) : (
                        items.map((item) => {
                          const itemId = getItemId(item);

                          return (
                            <option key={itemId} value={itemId}>
                              {item.itemCode
                                ? `${item.itemCode} - ${item.name}`
                                : item.name}
                            </option>
                          );
                        })
                      )}
                    </select>
                  </td>
                )}

                <td className="p-2">
                  <input
                    type="text"
                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    placeholder="Description"
                    value={row.description}
                    onChange={(e) =>
                      handleRowChange(index, "description", e.target.value)
                    }
                  />
                </td>

                <td className="p-2">
                  <select
                    value={row.accountCode}
                    onChange={(e) =>
                      handleRowChange(index, "accountCode", e.target.value)
                    }
                    className="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    disabled={isLoadingAccounts}
                  >
                    <option value="">Select Account</option>

                    {isLoadingAccounts ? (
                      <option value="" disabled>
                        Loading accounts...
                      </option>
                    ) : accountsError ? (
                      <option value="" disabled>
                        {accountsError}
                      </option>
                    ) : accounts.length === 0 ? (
                      <option value="" disabled>
                        No accounts available
                      </option>
                    ) : (
                      accounts.map((account, accountIndex) => (
                        <option
                          key={account.id || account.accountCode || accountIndex}
                          value={account.accountCode}
                        >
                          {getAccountLabel(account)}
                        </option>
                      ))
                    )}
                  </select>
                </td>

                {!isServiceMode && (
                  <>
                    <td className="p-2">
                      <input
                        type="number"
                        className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                        value={row.quantity}
                        onChange={(e) =>
                          handleRowChange(index, "quantity", e.target.value)
                        }
                        min="0"
                        step="1"
                      />
                    </td>

                    <td className="p-2">
                      <input
                        type="number"
                        className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                        value={row.unitPrice}
                        onChange={(e) =>
                          handleRowChange(index, "unitPrice", e.target.value)
                        }
                        min="0"
                        step="0.01"
                      />
                    </td>

                    <td className="p-2">
                      <input
                        type="number"
                        className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                        placeholder="%"
                        value={row.discount}
                        onChange={(e) =>
                          handleRowChange(index, "discount", e.target.value)
                        }
                        min="0"
                        max="100"
                        step="1"
                      />
                    </td>
                  </>
                )}

                <td className="p-2">
                  <input
                    type="number"
                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    placeholder="Amount"
                    value={row.amount}
                    onChange={(e) =>
                      handleRowChange(index, "amount", e.target.value)
                    }
                    readOnly={!isServiceMode}
                    min="0"
                    step="0.01"
                  />
                </td>

                <td className="p-2">
                  <select
                    value={row.projectId}
                    onChange={(e) =>
                      handleRowChange(index, "projectId", e.target.value)
                    }
                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    disabled={isLoadingProjects}
                  >
                    <option value="">Select project</option>

                    {isLoadingProjects ? (
                      <option value="" disabled>
                        Loading projects...
                      </option>
                    ) : projectsError ? (
                      <option value="" disabled>
                        {projectsError}
                      </option>
                    ) : projects.length === 0 ? (
                      <option value="" disabled>
                        No projects available
                      </option>
                    ) : (
                      projects.map((project, projectIndex) => {
                        const projectId = getProjectId(project);

                        return (
                          <option key={projectId || projectIndex} value={projectId}>
                            {project.code
                              ? `${project.code} - ${project.name}`
                              : project.name}
                          </option>
                        );
                      })
                    )}
                  </select>
                </td>

                <td className="p-2">
                  {index !== rows.length - 1 && (
                    <button
                      type="button"
                      onClick={() => removeRow(index)}
                      className="text-red-500 hover:text-red-700"
                    >
                      <MdOutlineCancel className="h-5 w-5" />
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="mb-6">
        <label className="block text-gray-700 font-medium">Notes</label>

        <textarea
          className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
          rows={3}
          placeholder="Notes"
          value={notes}
          onChange={(e) => setNotes(e.target.value)}
        />
      </div>

      <div className="flex flex-col items-end gap-4 mb-6">
        <div className="w-full md:w-1/2 flex justify-between items-center">
          <span className="text-gray-700 font-medium">Subtotal:</span>
          <span className="text-gray-900">Rs. {subtotal.toFixed(2)}</span>
        </div>

        <div className="w-full md:w-1/2 flex justify-between items-center">
          <span className="text-gray-700 font-medium">Total:</span>
          <span className="text-gray-900">Rs. {subtotal.toFixed(2)}</span>
        </div>

        <div className="w-full md:w-1/2 flex justify-between items-center">
          <label className="text-gray-700 font-medium">Amount Paid:</label>

          <input
            type="number"
            className="w-1/2 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            placeholder="0.00"
            value={amountPaid}
            onChange={(e) => setAmountPaid(e.target.value)}
            min="0"
            step="0.01"
          />
        </div>

        {Number(amountPaid || 0) > 0 && (
          <div className="w-full md:w-1/2 flex justify-between items-center">
            <label className="text-gray-700 font-medium">
              Payment Account <span className="text-red-500">*</span>
            </label>

            <select
              value={paymentAccountCode}
              onChange={(e) => setPaymentAccountCode(e.target.value)}
              className="w-1/2 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            >
              <option value="">Select Account</option>

              {accounts.map((account, index) => (
                <option
                  key={account.id || account.accountCode || index}
                  value={account.accountCode}
                >
                  {getAccountLabel(account)}
                </option>
              ))}
            </select>
          </div>
        )}

        <div className="w-full md:w-1/2 flex justify-between items-center">
          <span className="text-gray-700 font-medium">Balance Due:</span>
          <span className="text-gray-900">Rs. {balanceDue.toFixed(2)}</span>
        </div>

        {balanceDue > 0 && (
          <div className="w-full md:w-1/2 flex justify-between items-center">
            <label className="block text-gray-700 font-medium">
              Due Date <span className="text-red-500">*</span>
            </label>

            <input
              type="date"
              className="w-1/2 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
              value={dueDate}
              onChange={(e) => setDueDate(e.target.value)}
              required
            />
          </div>
        )}
      </div>

      <div className="flex justify-end space-x-2">
        <button
          type="button"
          onClick={handleSaveSaleOrder}
          disabled={saving}
          className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm sm:text-base disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          {saving ? "Saving..." : "Save"}
        </button>
      </div>

      {showAccountModal && (
        <div
          className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-500 ${modalTransition}`}
          onClick={(e) => handleModalClick(e, setShowAccountModal)}
        >
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 p-2 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              type="button"
              className="absolute top-2 right-2 text-black-600 text-xl"
              onClick={closeAccountModal}
            >
              <FaTimes />
            </button>

            <AddAccountForm />
          </div>
        </div>
      )}

      {showProjectModal && (
        <div
          className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-500 ${modalTransition}`}
          onClick={(e) => handleModalClick(e, setShowProjectModal)}
        >
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 p-2 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              type="button"
              className="absolute top-2 right-2 text-black-600 text-xl"
              onClick={closeProjectModal}
            >
              <FaTimes />
            </button>

            <NewProjectForm />
          </div>
        </div>
      )}
    </div>
  );
};

export default CreateSaleOrder;