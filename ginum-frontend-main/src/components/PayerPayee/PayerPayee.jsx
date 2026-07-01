import React, { useState } from "react";
import { FaUser, FaBuilding, FaUserTie } from "react-icons/fa";
import AddCustomerForm from "../customer/AddCustomer"; // ඔබගේ path එක නිවැරදිදැයි බලන්න
import AddSupplierForm from "../supplier/AddSupplierForm";
import AddEmployeeForm from "../Employee/AddEmployeeForm";

const PayerPayee = ({ onClose }) => {
  const [activeTab, setActiveTab] = useState("customer");

  const handleTabChange = (tab) => {
    setActiveTab(tab);
  };

  return (
    <div className="bg-white rounded-lg shadow-xl w-full overflow-hidden">
      <div className="flex justify-between items-center border-b p-4 bg-gray-50">
        <h2 className="text-xl font-semibold">Create New Payer/Payee</h2>
      </div>

      <div className="border-b">
        <div className="flex">
          <button
            type="button"
            onClick={() => handleTabChange("customer")}
            className={`flex-1 py-3 px-4 text-center font-medium ${activeTab === "customer" ? "text-blue-600 border-b-2 border-blue-600" : "text-gray-500 hover:text-gray-700"}`}
          >
            <FaUser className="inline mr-2" /> Customer
          </button>
          <button
            type="button"
            onClick={() => handleTabChange("supplier")}
            className={`flex-1 py-3 px-4 text-center font-medium ${activeTab === "supplier" ? "text-blue-600 border-b-2 border-blue-600" : "text-gray-500 hover:text-gray-700"}`}
          >
            <FaBuilding className="inline mr-2" /> Supplier
          </button>
          <button
            type="button"
            onClick={() => handleTabChange("employee")}
            className={`flex-1 py-3 px-4 text-center font-medium ${activeTab === "employee" ? "text-blue-600 border-b-2 border-blue-600" : "text-gray-500 hover:text-gray-700"}`}
          >
            <FaUserTie className="inline mr-2" /> Employee
          </button>
        </div>
      </div>

      {/* මෙතැනදී කිසිදු <form> එකක් භාවිතා නොකර අදාළ Component එක පමණක් කැඳවන්න */}
      <div className="p-2 max-h-[70vh] overflow-y-auto">
        {activeTab === "customer" && <AddCustomerForm onClose={onClose} />}
        {activeTab === "supplier" && <AddSupplierForm onClose={onClose} />}
        {activeTab === "employee" && <AddEmployeeForm onClose={onClose} />}
      </div>
    </div>
  );
};

export default PayerPayee;