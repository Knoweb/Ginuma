import React, { useState, useEffect } from "react";
import { MdOutlineCancel, MdAddCircleOutline } from "react-icons/md";
import { FaTimes } from "react-icons/fa";
import AddAccountForm from "../account/AddAccountForm";
import NewProjectForm from "../projects/NewProjectForm";
import api from "../../utils/api";

const CreateSaleOrder = () => {
  const [isServiceMode, setIsServiceMode] = useState(false);
  const [rows, setRows] = useState([
    {
      itemId: "",
      description: "",
      account: "",
      quantity: "",
      unitPrice: "",
      discount: "",
      amount: "",
    },
  ]);
  const [selectedCustomer, setSelectedCustomer] = useState("");
  const [showAccountModal, setShowAccountModal] = useState(false);
  const [showProjectModal, setShowProjectModal] = useState(false);
  const [modalTransition, setModalTransition] = useState("opacity-0 invisible");
  
  // Accounts States
  const [accounts, setAccounts] = useState([]);
  const [accountsError, setAccountsError] = useState(null);
  const [isLoadingAccounts, setIsLoadingAccounts] = useState(true);

  // Customers States (New)
  const [customers, setCustomers] = useState([]);
  const [customersError, setCustomersError] = useState(null);
  const [isLoadingCustomers, setIsLoadingCustomers] = useState(true);

  // Financial States
  const [subtotal, setSubtotal] = useState(0);
  const [freight, setFreight] = useState(0);
  const [tax, setTax] = useState(0);
  const [total, setTotal] = useState(0);
  const [amountPaid, setAmountPaid] = useState(0);
  const [balanceDue, setBalanceDue] = useState(0);
  const [dueDate, setDueDate] = useState("");

  // Calculate totals when relevant values change
  useEffect(() => {
    const newSubtotal = rows.reduce((sum, row) => {
      return sum + (parseFloat(row.amount) || 0);
    }, 0);
    setSubtotal(newSubtotal);

    const newTax = newSubtotal * 0; // 0.1 == 10% tax - adjust as needed
    setTax(newTax);

    const newTotal = newSubtotal + (parseFloat(freight) || 0) + newTax;
    setTotal(newTotal);

    const newBalanceDue = Math.max(newTotal - (parseFloat(amountPaid) || 0), 0);
    setBalanceDue(newBalanceDue);
  }, [rows, freight, amountPaid]);

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

  // Fetch accounts from API
  useEffect(() => {
    const fetchAccounts = async () => {
      try {
        setIsLoadingAccounts(true);
        setAccountsError(null);

        const companyId = sessionStorage.getItem("companyId");
        if (!companyId) {
          throw new Error("Company ID not found in session storage");
        }

        const response = await api.get(`/api/companies/${companyId}/accounts`);
        let accountsData = response.data;

        if (Array.isArray(response)) {
          accountsData = response;
        } else if (Array.isArray(response.data)) {
          accountsData = response.data;
        } else if (response?.data?.data && Array.isArray(response.data.data)) {
          accountsData = response.data.data;
        }

        if (!Array.isArray(accountsData)) {
          throw new Error("Invalid accounts data format");
        }

        const formattedAccounts = accountsData.map((account) => ({
          id: account.id,
          name: `${account.accountCode} - ${account.accountName}`,
          accountType: account.accountType,
          currentBalance: account.currentBalance,
          accountCode: account.accountCode,
        }));

        setAccounts(formattedAccounts);
      } catch (error) {
        console.error("Error fetching accounts:", error);
        setAccountsError(error.message);
        setAccounts([]);
      } finally {
        setIsLoadingAccounts(false);
      }
    };

    fetchAccounts();
  }, []);

  // Fetch customers from API
  useEffect(() => {
    const fetchCustomers = async () => {
      try {
        setIsLoadingCustomers(true);
        setCustomersError(null);

        const companyId = sessionStorage.getItem("companyId");
        const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

        if (!companyId || !token) {
          throw new Error("Authentication credentials not found");
        }

        const response = await api.get(`/api/customers/companies/${companyId}`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        let customersData = response.data;

        if (Array.isArray(response)) {
          customersData = response;
        } else if (Array.isArray(response.data)) {
          customersData = response.data;
        } else if (response?.data?.data && Array.isArray(response.data.data)) {
          customersData = response.data.data;
        }

        if (!Array.isArray(customersData)) {
          throw new Error("Invalid customers data format");
        }

        setCustomers(customersData);
      } catch (error) {
        console.error("Error fetching customers:", error);
        setCustomersError("Failed to load customers.");
        setCustomers([]);
      } finally {
        setIsLoadingCustomers(false);
      }
    };

    fetchCustomers();
  }, []);

  const projects = [
    { id: "1", name: "Project A" },
    { id: "2", name: "Project B" },
    { id: "3", name: "Project C" },
  ];

  const items = [
    { id: "1", name: "Item A" },
    { id: "2", name: "Item B" },
    { id: "3", name: "Item C" },
  ];

  const handleRowChange = (index, field, value) => {
    const updatedRows = [...rows];
    updatedRows[index][field] = value;

    if (
      !isServiceMode &&
      (field === "quantity" || field === "unitPrice" || field === "discount")
    ) {
      const quantity = parseFloat(updatedRows[index].quantity) || 0;
      const unitPrice = parseFloat(updatedRows[index].unitPrice) || 0;
      const discount = parseFloat(updatedRows[index].discount) || 0;

      const discountedAmount = unitPrice * (1 - discount / 100);
      updatedRows[index].amount = (quantity * discountedAmount).toFixed(2);
    }

    if (index === rows.length - 1 && value.trim() !== "") {
      updatedRows.push({
        itemId: "",
        description: "",
        account: "",
        quantity: "",
        unitPrice: "",
        discount: "",
        amount: "",
      });
    }

    setRows(updatedRows);
  };

  const removeRow = (index) => {
    const updatedRows = rows.filter((_, i) => i !== index);
    setRows(updatedRows);
  };

  return (
    <div className="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-4 sm:p-6 my-4 sm:mt-6">
      <h2 className="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">
        Create Sale Order
      </h2>

      {/* Customer and Sale Order Details */}
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
              <option value="" disabled>Loading customers...</option>
            ) : customersError ? (
              <option value="" disabled>{customersError}</option>
            ) : customers.length === 0 ? (
              <option value="" disabled>No customers found</option>
            ) : (
              customers.map((customer, index) => (
                <option key={customer.id || index} value={customer.id || customer.email}>
                  {customer.customerName}
                </option>
              ))
            )}
          </select>
        </div>
        <div>
          <label className="block text-gray-700 font-medium">
            Sale Order Number <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            placeholder="00000001"
          />
        </div>
      </div>

      {/* Sale Order Date and Due Date */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
          <label className="block text-gray-700 font-medium">
            Order Date <span className="text-red-500">*</span>
          </label>
          <input
            type="date"
            className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            defaultValue="2025-02-27"
          />
        </div>
      </div>

      {/* Items/Services Mode */}
      <div className="flex space-x-4 mb-6">
        <label className="flex items-center">
          <input
            type="radio"
            name="mode"
            value="item"
            checked={!isServiceMode}
            onChange={() => setIsServiceMode(false)}
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
            onChange={() => setIsServiceMode(true)}
            className="form-radio h-4 w-4 text-blue-600"
          />
          <span className="ml-2 text-gray-700">Services</span>
        </label>
      </div>

      {/* Items/Services Table */}
      <div className="mb-6 overflow-x-auto">
        <table className="w-full rounded-lg">
          <thead>
            <tr className="bg-gray-100 text-gray-700 text-sm">
              {!isServiceMode && (
                <th className="p-2">
                  Item ID <span className="text-red-500">*</span>
                  <button className="text-blue-600 hover:text-blue-700 ml-1">
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
                  onClick={() => setShowAccountModal(true)}
                  className="ml-1 text-blue-600 hover:text-blue-700"
                >
                  <MdAddCircleOutline className="h-5 w-5 inline" />
                </button>
              </th>
              {!isServiceMode && (
                <>
                  <th className="">
                    No of Units <span className="text-red-500">*</span>
                  </th>
                  <th className="p-2">
                    Unit Price <span className="text-red-500">*</span>
                  </th>
                  <th className="p-2">Discount (%)</th>
                </>
              )}
              <th className="p-2">
                Amount ($) <span className="text-red-500">*</span>
              </th>
              <th className="p-2">
                Project
                <button
                  onClick={() => setShowProjectModal(true)}
                  className="ml-1 text-blue-600 hover:text-blue-700"
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
                      className=" px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    >
                      <option value="">Select Item</option>
                      {items.map((item) => (
                        <option key={item.id} value={item.id}>
                          {item.name}
                        </option>
                      ))}
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
                    value={row.account}
                    onChange={(e) =>
                      handleRowChange(index, "account", e.target.value)
                    }
                    className="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                    disabled={isLoadingAccounts}
                  >
                    <option value="">Select Account</option>
                    {isLoadingAccounts ? (
                      <option value="">Loading accounts...</option>
                    ) : accountsError ? (
                      <option value="">Error loading accounts</option>
                    ) : accounts.length === 0 ? (
                      <option value="">No accounts available</option>
                    ) : (
                      accounts.map((account) => (
                        <option key={account.id} value={account.id}>
                          {account.name}
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
                        placeholder="(%)"
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
                    placeholder="Amount ($)"
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
                    value={row.project}
                    onChange={(e) =>
                      handleRowChange(index, "project", e.target.value)
                    }
                    className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                  >
                    <option value="">Select project</option>
                    {projects.map((project) => (
                      <option key={project.id} value={project.id}>
                        {project.name}
                      </option>
                    ))}
                  </select>
                </td>
                <td className="p-2">
                  {index !== rows.length - 1 && (
                    <button
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

      {/* Notes Section */}
      <div className="mb-6">
        <label className="block text-gray-700 font-medium">Notes</label>
        <textarea
          className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
          rows={3}
          placeholder="Notes"
        ></textarea>
      </div>

      {/* Financial Details */}
      <div className="flex flex-col items-end gap-4 mb-6">
        <div className="w-full md:w-1/2 flex justify-between items-center">
          <span className="text-gray-700 font-medium">Subtotal:</span>
          <span className="text-gray-900">${subtotal.toFixed(2)}</span>
        </div>
        <div className="w-full md:w-1/2 flex justify-between items-center">
          <label className="text-gray-700 font-medium">Freight ($):</label>
          <input
            type="number"
            className="w-1/2 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
            placeholder="0.00"
            value={freight}
            onChange={(e) => setFreight(e.target.value)}
            min="0"
            step="0.01"
          />
        </div>
        <div className="w-full md:w-1/2 flex justify-between items-center">
          <span className="text-gray-700 font-medium">Tax:</span>
          <span className="text-gray-900">${tax.toFixed(2)}</span>
        </div>
        <div className="w-full md:w-1/2 flex justify-between items-center">
          <span className="text-gray-700 font-medium">Total:</span>
          <span className="text-gray-900">${total.toFixed(2)}</span>
        </div>
        <div className="w-full md:w-1/2 flex justify-between items-center">
          <label className="text-gray-700 font-medium">Amount Paid ($):</label>
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
        <div className="w-full md:w-1/2 flex justify-between items-center">
          <span className="text-gray-700 font-medium">Balance Due:</span>
          <span className="text-gray-900">${balanceDue.toFixed(2)}</span>
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

      {/* Save and Cancel Buttons */}
      <div className="flex justify-end space-x-2">
        <button className="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 text-sm sm:text-base">
          Save
        </button>
      </div>

      {/* Modals */}
      {showAccountModal && (
        <div
          className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-500 ${modalTransition}`}
          onClick={(e) => handleModalClick(e, setShowAccountModal)}
        >
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3  p-2 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              className="absolute top-2 right-2 text-black-600 text-xl"
              onClick={() => setShowAccountModal(false)}
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
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3  p-2 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              className="absolute top-2 right-2 text-black-600 text-xl"
              onClick={() => setShowProjectModal(false)}
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