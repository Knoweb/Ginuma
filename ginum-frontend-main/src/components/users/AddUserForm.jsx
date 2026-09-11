import React, { useState, useEffect } from "react";
import { FaPlusCircle, FaTimes, FaEye, FaEyeSlash, FaSpinner } from "react-icons/fa";
import { FiUserPlus, FiShield, FiUser, FiMail, FiPhone, FiBriefcase, FiLock, FiCheckCircle, FiKey } from "react-icons/fi";
import { useNavigate } from "react-router-dom";
import AddEmployeeForm from "../Employee/AddEmployeeForm";
import { apiUrl } from "../../utils/api";
import api from "../../utils/api";
import Alert from "../Alert/Alert";

const inputClass =
  "w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 bg-gray-50 transition-all";

const readOnlyClass =
  "w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-100 text-gray-500 cursor-default";

const labelClass = "block text-sm font-semibold text-gray-700 mb-1.5";

const DEFAULT_ROLES = [
  { roleName: "COMPANY_ADMIN", description: "Full Administrative Control", otpRequired: false },
  { roleName: "ACCOUNTANT", description: "Financials, General Ledger & Reports", otpRequired: false },
  { roleName: "SALES", description: "Invoices, Customers & Quotes", otpRequired: false },
  { roleName: "VIEWER", description: "Read-Only Dashboard & Reports", otpRequired: false },
];

const AddUserForm = () => {
  const navigate = useNavigate();
  const [employees, setEmployees] = useState([]);
  const [availableRoles, setAvailableRoles] = useState(DEFAULT_ROLES);
  const [isLoading, setIsLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);

  const [formData, setFormData] = useState({
    employeeId: "",
    name: "",
    designation: "",
    department: "",
    mobileNo: "",
    email: "",
    password: "",
    confirmPassword: "",
    role: "COMPANY_ADMIN",
  });

  const [errors, setErrors] = useState({});
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const companyId = sessionStorage.getItem("companyId");
  const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

  const fetchData = async () => {
    if (!companyId || !token) return;
    try {
      const [empRes, rolesRes] = await Promise.all([
        fetch(`${apiUrl}/api/employees/${companyId}`, {
          headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" },
        }),
        fetch(`${apiUrl}/api/roles?companyId=${companyId}`, {
          headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" },
        }),
      ]);

      if (empRes.ok) {
        const empData = await empRes.json();
        setEmployees(Array.isArray(empData) ? empData : []);
      }

      if (rolesRes.ok) {
        const rolesData = await rolesRes.json();
        if (Array.isArray(rolesData) && rolesData.length > 0) {
          setAvailableRoles(rolesData);
          setFormData((prev) => ({ ...prev, role: rolesData[0].roleName }));
        }
      }
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  useEffect(() => {
    if (showModal) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "auto";
      fetchData();
    }
  }, [showModal]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (name === "employeeId") {
      if (!value) {
        setFormData((prev) => ({
          ...prev,
          employeeId: "",
          name: "",
          email: "",
          mobileNo: "",
          designation: "",
          department: "",
        }));
        return;
      }
      const selectedEmp = employees.find((emp) => String(emp.employeeId) === String(value));
      if (selectedEmp) {
        setFormData((prev) => ({
          ...prev,
          employeeId: value,
          name: `${selectedEmp.firstName || ""} ${selectedEmp.lastName || ""}`.trim(),
          email: selectedEmp.email || "",
          mobileNo: selectedEmp.mobileNo || "",
          designation: selectedEmp.designation?.name || "N/A",
          department: selectedEmp.department?.name || "N/A",
        }));
        return;
      }
    }
    setFormData((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) setErrors((prev) => ({ ...prev, [name]: "" }));
  };

  const validate = () => {
    const e = {};
    if (!formData.email.trim()) e.email = "Email is required";
    else if (!/^\S+@\S+\.\S+$/.test(formData.email)) e.email = "Email is invalid";
    if (!formData.password) e.password = "Password is required";
    else if (formData.password.length < 6) e.password = "Password must be at least 6 characters";
    if (!formData.confirmPassword) e.confirmPassword = "Please confirm your password";
    else if (formData.password !== formData.confirmPassword) e.confirmPassword = "Passwords do not match";
    if (!formData.role) e.role = "Please select a role";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validate()) return;

    setIsLoading(true);
    try {
      await api.post(
        `/api/users/companies/${companyId}`,
        {
          email: formData.email,
          password: formData.password,
          role: formData.role,
          name: formData.name,
        },
        { headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json" } }
      );
      Alert.success("User assigned role & login credentials successfully!");
      navigate("/users/all");
    } catch (error) {
      console.error("Error assigning user:", error);
      Alert.error(error.response?.data?.error || error.response?.data?.message || "Failed to assign user.");
    } finally {
      setIsLoading(false);
    }
  };

  const selectedEmployee = employees.find((emp) => String(emp.employeeId) === String(formData.employeeId));

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      {/* Header */}
      <div className="flex items-center gap-4 mb-8 max-w-3xl mx-auto">
        <div className="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
          <FiUserPlus className="text-blue-600 text-xl" />
        </div>
        <div>
          <h1 className="gl-heading">Add & Assign User Role</h1>
          <p className="text-sm text-gray-500 mt-0.5">
            Create user login credentials and assign company roles with optional OTP security
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6 max-w-3xl mx-auto">
        {/* Step 1: User / Employee Details */}
        <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
            <span className="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">1</span>
            <h3 className="font-bold text-gray-800 text-sm flex items-center gap-1.5">
              <FiUser className="text-blue-500" /> User Profile Information
            </h3>
          </div>
          <div className="p-6 space-y-4">
            <div>
              <label className={labelClass}>
                Select Existing Employee <span className="text-gray-400 font-normal">(Optional)</span>
              </label>
              <select
                name="employeeId"
                value={formData.employeeId}
                onChange={handleChange}
                className={`${inputClass} cursor-pointer`}
              >
                <option value="">-- Direct User / Custom Employee --</option>
                {employees.map((emp) => (
                  <option key={emp.employeeId} value={emp.employeeId}>
                    {emp.firstName} {emp.lastName} {emp.email ? `(${emp.email})` : ""}
                  </option>
                ))}
              </select>
              <button
                type="button"
                onClick={() => setShowModal(true)}
                className="text-blue-600 hover:text-blue-800 text-xs font-semibold flex items-center gap-1.5 mt-2 cursor-pointer"
              >
                <FaPlusCircle /> Register New Employee
              </button>
            </div>

            {/* User Full Name */}
            <div>
              <label className={labelClass}>
                Full Name <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="name"
                value={formData.name}
                onChange={handleChange}
                className={inputClass}
                placeholder="e.g. John Doe"
                required
              />
            </div>

            {/* Auto-filled Employee Details */}
            {selectedEmployee && (
              <div className="mt-3 bg-blue-50/40 border border-blue-100 rounded-xl p-4">
                <p className="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">Employee Details (Auto-filled)</p>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div>
                    <label className="block text-xs font-semibold text-gray-500 mb-1 flex items-center gap-1"><FiBriefcase className="text-gray-400" /> Department</label>
                    <input type="text" value={formData.department} readOnly className={readOnlyClass} />
                  </div>
                  <div>
                    <label className="block text-xs font-semibold text-gray-500 mb-1 flex items-center gap-1"><FiBriefcase className="text-gray-400" /> Designation</label>
                    <input type="text" value={formData.designation} readOnly className={readOnlyClass} />
                  </div>
                  <div>
                    <label className="block text-xs font-semibold text-gray-500 mb-1 flex items-center gap-1"><FiPhone className="text-gray-400" /> Mobile No.</label>
                    <input type="text" value={formData.mobileNo} readOnly className={readOnlyClass} />
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Step 2: Login Credentials */}
        <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
            <span className="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">2</span>
            <h3 className="font-bold text-gray-800 text-sm flex items-center gap-1.5">
              <FiLock className="text-blue-500" /> Account Login Credentials
            </h3>
          </div>
          <div className="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            {/* Email */}
            <div className="sm:col-span-2">
              <label className={labelClass}>
                Username / Email Address <span className="text-red-500">*</span>
              </label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                className={`${inputClass} ${errors.email ? "border-red-400" : ""}`}
                placeholder="user@company.com"
                required
              />
              {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
            </div>

            {/* Password */}
            <div>
              <label className={labelClass}>
                Password <span className="text-red-500">*</span>
              </label>
              <div className={`flex items-center border ${errors.password ? "border-red-400" : "border-gray-300"} rounded-xl focus-within:ring-2 focus-within:ring-blue-200 focus-within:border-blue-500 bg-gray-50 transition-all`}>
                <input
                  type={showPassword ? "text" : "password"}
                  name="password"
                  value={formData.password}
                  onChange={handleChange}
                  className="flex-1 px-3 py-2.5 text-sm bg-transparent outline-none rounded-xl"
                  placeholder="Min. 6 characters"
                  required
                  minLength="6"
                />
                <button
                  type="button"
                  className="p-2.5 text-gray-400 hover:text-gray-600 cursor-pointer"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? <FaEyeSlash /> : <FaEye />}
                </button>
              </div>
              {errors.password && <p className="text-red-500 text-xs mt-1">{errors.password}</p>}
            </div>

            {/* Confirm Password */}
            <div>
              <label className={labelClass}>
                Confirm Password <span className="text-red-500">*</span>
              </label>
              <div className={`flex items-center border ${errors.confirmPassword ? "border-red-400" : "border-gray-300"} rounded-xl focus-within:ring-2 focus-within:ring-blue-200 focus-within:border-blue-500 bg-gray-50 transition-all`}>
                <input
                  type={showConfirmPassword ? "text" : "password"}
                  name="confirmPassword"
                  value={formData.confirmPassword}
                  onChange={handleChange}
                  className="flex-1 px-3 py-2.5 text-sm bg-transparent outline-none rounded-xl"
                  placeholder="Repeat password"
                  required
                />
                <button
                  type="button"
                  className="p-2.5 text-gray-400 hover:text-gray-600 cursor-pointer"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                >
                  {showConfirmPassword ? <FaEyeSlash /> : <FaEye />}
                </button>
              </div>
              {errors.confirmPassword && (
                <p className="text-red-500 text-xs mt-1">{errors.confirmPassword}</p>
              )}
            </div>
          </div>
        </div>

        {/* Step 3: Role Selection */}
        <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
          <div className="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-2 justify-between">
            <div className="flex items-center gap-2">
              <span className="w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">3</span>
              <h3 className="font-bold text-gray-800 text-sm flex items-center gap-1.5">
                <FiShield className="text-blue-500" /> Select System Role
              </h3>
            </div>
            <button
              type="button"
              onClick={() => navigate("/settings")}
              className="text-xs text-blue-600 hover:underline font-semibold flex items-center gap-1 cursor-pointer"
            >
              Manage Roles & OTP Settings &rarr;
            </button>
          </div>
          <div className="p-6">
            <label className={labelClass}>
              Assign Company Role <span className="text-red-500">*</span>
            </label>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
              {availableRoles.map((r) => {
                const isSelected = formData.role === r.roleName;
                return (
                  <label
                    key={r.roleName}
                    className={`flex items-start justify-between p-4 border-2 rounded-2xl cursor-pointer transition-all ${
                      isSelected
                        ? "border-blue-500 bg-blue-50/70 shadow-sm"
                        : "border-gray-200 hover:border-blue-200 hover:bg-gray-50"
                    }`}
                  >
                    <div className="flex items-start gap-3">
                      <input
                        type="radio"
                        name="role"
                        value={r.roleName}
                        checked={isSelected}
                        onChange={handleChange}
                        className="mt-1 text-blue-600 focus:ring-blue-500"
                      />
                      <div>
                        <div className="flex items-center gap-2">
                          <span className={`text-sm font-bold ${isSelected ? "text-blue-800" : "text-gray-800"}`}>
                            {r.roleName}
                          </span>
                          {r.otpRequired && (
                            <span className="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full flex items-center gap-1">
                              <FiKey className="text-amber-600" /> OTP Required
                            </span>
                          )}
                        </div>
                        <p className="text-xs text-gray-500 mt-0.5">
                          {r.description || "System Access Role"}
                        </p>
                      </div>
                    </div>
                    {isSelected && <FiCheckCircle className="text-blue-600 text-lg flex-shrink-0" />}
                  </label>
                );
              })}
            </div>
            {errors.role && <p className="text-red-500 text-xs mt-2">{errors.role}</p>}
          </div>
        </div>

        {/* Actions */}
        <div className="flex justify-end gap-3">
          <button
            type="button"
            onClick={() => navigate("/users/all")}
            disabled={isLoading}
            className="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold text-sm transition-colors cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="submit"
            disabled={isLoading}
            className="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-sm shadow-blue-500/20 disabled:bg-blue-300 disabled:cursor-not-allowed cursor-pointer"
          >
            {isLoading ? (
              <><FaSpinner className="animate-spin" /> Assigning User Role...</>
            ) : (
              "Save User & Assign Role"
            )}
          </button>
        </div>
      </form>

      {/* Add Employee Modal */}
      {showModal && (
        <div
          className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
          onClick={(e) => { if (e.target === e.currentTarget) setShowModal(false); }}
        >
          <div className="relative w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-4">
            <button
              type="button"
              className="absolute top-4 right-4 text-gray-600 hover:text-red-500 text-xl z-10 cursor-pointer"
              onClick={() => setShowModal(false)}
            >
              <FaTimes />
            </button>
            <div className="p-2">
              <AddEmployeeForm onClose={() => setShowModal(false)} />
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AddUserForm;