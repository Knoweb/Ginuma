import React, { useState, useEffect } from "react";
import { FaPlusCircle, FaTimes } from "react-icons/fa";
import AddCustomerForm from "../customer/AddCustomer";

const NewProjectForm = () => {
  const [showCustomerModal, setShowCustomerModal] = useState(false);
  const [modalTransition, setModalTransition] = useState("opacity-0 invisible");

  const [projectCode, setProjectCode] = useState("");
  const [projectName, setProjectName] = useState("");
  const [selectedCustomer, setSelectedCustomer] = useState("");
  const [startDate, setStartDate] = useState("");
  const [workingStatus, setWorkingStatus] = useState("");
  const [priority, setPriority] = useState("");

  const [customers, setCustomers] = useState([]);
  const [customerLoading, setCustomerLoading] = useState(false);
  const [customerError, setCustomerError] = useState("");
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (showCustomerModal) {
      setModalTransition("opacity-100 visible");
    } else {
      setModalTransition("opacity-0 invisible");
    }
  }, [showCustomerModal]);

  const getCompanyId = () => {
    return sessionStorage.getItem("companyId");
  };

  const getToken = () => {
    return sessionStorage.getItem("auth_token");
  };

  const fetchCustomers = async () => {
    try {
      setCustomerLoading(true);
      setCustomerError("");

      const companyId = getCompanyId();
      const token = getToken();

      console.log("Company ID:", companyId);
      console.log("Token:", token);

      if (!companyId || !token) {
        setCustomerError("Missing company ID or auth token. Please login again.");
        return;
      }

      const response = await fetch(
        `http://localhost:8081/api/customers/companies/${companyId}`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error("Customer fetch error:", errorText);
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      console.log("Customers:", data);

      setCustomers(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error("Failed to load customers:", error);
      setCustomerError("Failed to load customers.");
    } finally {
      setCustomerLoading(false);
    }
  };

  useEffect(() => {
    fetchCustomers();
  }, []);

  const closeCustomerModal = () => {
    setShowCustomerModal(false);
    fetchCustomers();
  };

  const workingStatusOptions = [
    { id: "ACTIVE", name: "Active" },
    { id: "WORKING", name: "Working" },
    { id: "COMPLETED", name: "Completed" },
    { id: "CANCELED", name: "Canceled" },
  ];

  const priorityOptions = [
    { id: "LOW", name: "Low" },
    { id: "MEDIUM", name: "Medium" },
    { id: "HIGH", name: "High" },
  ];

  const getCustomerId = (customer) => {
    return customer.customerId || customer.id;
  };

  const getCustomerName = (customer) => {
    return customer.customerName || customer.name || "-";
  };

  const resetForm = () => {
    setProjectCode("");
    setProjectName("");
    setSelectedCustomer("");
    setStartDate("");
    setWorkingStatus("");
    setPriority("");
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const companyId = getCompanyId();
    const token = getToken();

    if (!companyId || !token) {
      alert("Missing company ID or auth token. Please login again.");
      return;
    }

    if (!selectedCustomer) {
      alert("Please select a customer.");
      return;
    }

    const projectPayload = {
      projectCode: projectCode.trim(),
      projectName: projectName.trim(),
      customerId: Number(selectedCustomer),
      startDate,
      workingStatus,
      priority,
      description: "",
    };

    console.log("Project Payload:", projectPayload);

    try {
      setSaving(true);

      const response = await fetch(
        `http://localhost:8081/api/companies/${companyId}/projects`,
        {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify(projectPayload),
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error("Project save error:", errorText);
        alert("Project save failed. Check console.");
        return;
      }

      const savedProject = await response.json();
      console.log("Saved Project:", savedProject);

      alert("Project saved successfully!");
      resetForm();
    } catch (error) {
      console.error("Cannot connect to backend:", error);
      alert("Cannot connect to backend.");
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-4 sm:p-6 my-4 sm:mt-6">
      <h2 className="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">
        Create Project
      </h2>

      <form onSubmit={handleSubmit}>
        {/* Row 1 */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div>
            <label className="block text-gray-700 font-medium">
              Project Code <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              value={projectCode}
              onChange={(e) => setProjectCode(e.target.value)}
              className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
              placeholder="Enter Project Code"
              required
            />
          </div>

          <div>
            <label className="block text-gray-700 font-medium">
              Project Name <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              value={projectName}
              onChange={(e) => setProjectName(e.target.value)}
              className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
              placeholder="Enter Project Name"
              required
            />
          </div>
        </div>

        {/* Row 2 */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div>
            <label className="block text-gray-700 font-medium">
              Customer <span className="text-red-500">*</span>
            </label>

            <select
              value={selectedCustomer}
              onChange={(e) => setSelectedCustomer(e.target.value)}
              className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
              required
            >
              <option value="">
                {customerLoading ? "Loading customers..." : "Select a customer"}
              </option>

              {customers.map((customer, index) => {
                const customerId = getCustomerId(customer);

                return (
                  <option key={customerId || index} value={customerId}>
                    {getCustomerName(customer)}
                  </option>
                );
              })}
            </select>

            {customerError && (
              <p className="text-red-500 text-sm mt-1">{customerError}</p>
            )}

            <button
              type="button"
              className="text-blue-500 flex items-center justify-center mt-2"
              onClick={() => setShowCustomerModal(true)}
            >
              <span className="mr-2">
                <FaPlusCircle />
              </span>
              Add New Customer
            </button>
          </div>

          <div>
            <label className="block text-gray-700 font-medium">
              Start Date <span className="text-red-500">*</span>
            </label>
            <input
              type="date"
              value={startDate}
              onChange={(e) => setStartDate(e.target.value)}
              className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
              required
            />
          </div>
        </div>

        {/* Row 3 */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div>
            <label className="block text-gray-700 font-medium">
              Working Status <span className="text-red-500">*</span>
            </label>
            <select
              value={workingStatus}
              onChange={(e) => setWorkingStatus(e.target.value)}
              className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
              required
            >
              <option value="">Select working status</option>
              {workingStatusOptions.map((status) => (
                <option key={status.id} value={status.id}>
                  {status.name}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-gray-700 font-medium">
              Priority <span className="text-red-500">*</span>
            </label>
            <select
              value={priority}
              onChange={(e) => setPriority(e.target.value)}
              className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
              required
            >
              <option value="">Select priority</option>
              {priorityOptions.map((priorityItem) => (
                <option key={priorityItem.id} value={priorityItem.id}>
                  {priorityItem.name}
                </option>
              ))}
            </select>
          </div>
        </div>

        {/* Buttons */}
        <div className="flex justify-end space-x-2">
          <button
            type="button"
            onClick={resetForm}
            className="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm sm:text-base"
          >
            Cancel
          </button>

          <button
            type="submit"
            disabled={saving}
            onClick={() => console.log("Save button clicked")}
            className="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 text-sm sm:text-base disabled:bg-gray-400"
          >
            {saving ? "Saving..." : "Save"}
          </button>
        </div>
      </form>

      {showCustomerModal && (
        <div
          className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-500 ${modalTransition}`}
          onClick={(e) => {
            if (e.target === e.currentTarget) closeCustomerModal();
          }}
        >
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 p-2 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              type="button"
              className="absolute top-2 right-2 text-gray-600 text-xl z-10"
              onClick={closeCustomerModal}
            >
              <FaTimes />
            </button>

            <AddCustomerForm />
          </div>
        </div>
      )}
    </div>
  );
};

export default NewProjectForm;