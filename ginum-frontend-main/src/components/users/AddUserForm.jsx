import React, { useState, useEffect } from "react";
import { FaPlusCircle, FaTimes, FaEye, FaEyeSlash } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import AddEmployeeForm from "../Employee/AddEmployeeForm";
import { apiUrl } from "../../utils/api"; 
import api from "../../utils/api";

const AddUserForm = () => {
  const navigate = useNavigate();
  const [employees, setEmployees] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  
  const [formData, setFormData] = useState({
    employeeId: "",
    designation: "",
    department: "",
    mobileNo: "",
    email: "",
    password: "",
    confirmPassword: "",
    role: "USER"
  });

  const [errors, setErrors] = useState({});
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [modalTransition, setModalTransition] = useState("opacity-0 invisible");

  const companyId = sessionStorage.getItem("companyId");
  const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

  const fetchEmployees = async () => {
    if (!companyId || !token) return;

    try {
      const response = await fetch(`${apiUrl}/api/employees/${companyId}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });

      if (response.ok) {
        const data = await response.json();
        setEmployees(Array.isArray(data) ? data : []);
        console.log("Employees loaded for dropdown:", data);
      } else {
        console.error("Failed to load employees, Status:", response.status);
      }
    } catch (error) {
      console.error("Error fetching employees:", error);
    }
  };

  useEffect(() => {
    fetchEmployees();
  }, []);

  useEffect(() => {
    if (showModal) {
      setModalTransition("opacity-100 visible");
      document.body.style.overflow = "hidden";
    } else {
      setModalTransition("opacity-0 invisible");
      document.body.style.overflow = "auto";
      fetchEmployees(); 
    }
  }, [showModal]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    
    if (name === "employeeId") {
      if (!value) {
        setFormData({
          employeeId: "", email: "", mobileNo: "", designation: "", department: ""
        });
        return;
      }

      const selectedEmp = employees.find(emp => String(emp.employeeId) === String(value));
      
      if (selectedEmp) {
        setFormData(prev => ({
          ...prev, 
          employeeId: value,
          email: selectedEmp.email || "", 
          mobileNo: selectedEmp.mobileNo || "",
          designation: selectedEmp.designation?.name || "N/A",
          department: selectedEmp.department?.name || "N/A"
        }));
        return;
      }
    }

    // අනෙකුත් input fields සඳහා (Email, Password)
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log("Current Form Data:", formData);
    
    if (formData.password !== formData.confirmPassword) {
      setErrors({ confirmPassword: "Passwords do not match!" });
      return;
    }

    setIsLoading(true);

    const payload = {
      email: formData.email,
      password: formData.password, 
      role: formData.role
    };

    console.log("Payload sending to backend:", payload);

    try {
      await api.post(`/api/users/companies/${companyId}`, payload, {
        headers: { 
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json" 
        }
      });
      alert("User assigned successfully!"); 
      navigate("/users/all");
    } catch (error) {
      console.error("Error assigning user:", error);
      alert(error.response?.data?.message || "Failed to assign user.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex justify-center p-4 bg-gray-100 min-h-full pt-10"> 
      <div className="w-full max-w-md sm:max-w-2xl bg-white rounded-lg shadow-md p-7 h-fit"> {/* h-fit එකතු කළා */}
        <h2 className="text-2xl font-bold text-gray-900 mb-6">Assign User</h2>
        <form className="space-y-4 w-full" onSubmit={handleSubmit}>
          
          <div>
            <label className="block text-gray-700">
              Select Employee <span className="text-red-500">*</span>
            </label>
            <select
              name="employeeId"
              className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              value={formData.employeeId}
              onChange={handleChange}
              required
            >
              <option value="">Select an Employee</option>
              {employees.map(emp => (
                <option key={emp.employeeId} value={emp.employeeId}>
                  {emp.firstName} {emp.lastName} {emp.nic ? `- ${emp.nic}` : ""}
                </option>
              ))}
            </select>
            <div className="text-center mt-2">
              <button
                type="button"
                className="text-blue-500 flex items-center justify-center hover:text-blue-700 transition"
                onClick={() => setShowModal(true)}
              >
                <span className="mr-2"><FaPlusCircle /></span> Add New Employee
              </button>
            </div>
          </div>

          {/* Form Fields */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            {/* Read-Only Department & Designation */}
            <div>
              <label className="block text-gray-700">Department</label>
              <input type="text" name="department" className="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600" value={formData.department} readOnly placeholder="Auto-filled" />
            </div>

            <div>
              <label className="block text-gray-700">Designation</label>
              <input type="text" name="designation" className="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600" value={formData.designation} readOnly placeholder="Auto-filled" />
            </div>

            {/* Read-Only Mobile No */}
            <div>
              <label className="block text-gray-700">Mobile Number</label>
              <input type="text" name="mobileNo" className="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600" value={formData.mobileNo} readOnly placeholder="Auto-filled" />
            </div>

            {/* Editable Email (Username) */}
            <div>
              <label className="block text-gray-700">Email (Username) <span className="text-red-500">*</span></label>
              <input type="email" name="email" className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value={formData.email} onChange={handleChange} required />
            </div>

            {/* Role Selection */}
            <div className="md:col-span-2">
              <label className="block text-gray-700">System Role <span className="text-red-500">*</span></label>
              <select name="role" className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value={formData.role} onChange={handleChange}>
                <option value="USER">User</option>
                <option value="MANAGER">Manager</option>
                <option value="ADMIN">Admin</option>
              </select>
            </div>

            {/* Passwords */}
            <div className="w-full">
              <label className="block text-gray-700">Password <span className="text-red-500">*</span></label>
              <div className="flex items-center w-full border rounded-lg focus-within:ring-2 focus-within:ring-blue-500">
                <input type={showPassword ? "text" : "password"} name="password" className="flex-1 px-4 py-2 rounded-lg outline-none" value={formData.password} onChange={handleChange} required minLength="6" />
                <button type="button" className="p-2 text-gray-500" onClick={() => setShowPassword(!showPassword)}>
                  {showPassword ? <FaEyeSlash /> : <FaEye />}
                </button>
              </div>
            </div>

            <div className="w-full">
              <label className="block text-gray-700">Confirm Password <span className="text-red-500">*</span></label>
              <div className="flex items-center w-full border rounded-lg focus-within:ring-2 focus-within:ring-blue-500">
                <input type={showConfirmPassword ? "text" : "password"} name="confirmPassword" className="flex-1 px-4 py-2 rounded-lg outline-none" value={formData.confirmPassword} onChange={handleChange} required />
                <button type="button" className="p-2 text-gray-500" onClick={() => setShowConfirmPassword(!showConfirmPassword)}>
                  {showConfirmPassword ? <FaEyeSlash /> : <FaEye />}
                </button>
              </div>
              {errors.confirmPassword && <p className="text-red-500 text-sm mt-1">{errors.confirmPassword}</p>}
            </div>
          </div>

          <button 
            type="submit" 
            disabled={isLoading}
            className={`mt-6 w-full md:w-auto bg-blue-600 px-8 py-2 rounded-lg text-white font-semibold hover:bg-blue-700 transition ${isLoading ? 'opacity-70 cursor-not-allowed' : ''}`}
          >
            {isLoading ? "Saving..." : "Save User"}
          </button>
        </form>
      </div>

      {/* Add Employee Modal */}
      {showModal && (
        <div className={`fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 ${modalTransition}`} onClick={(e) => { if (e.target === e.currentTarget) setShowModal(false) }}>
          <div className="relative w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 bg-white p-2 rounded-lg max-h-[90vh] overflow-y-auto">
            <button className="absolute top-4 right-4 text-gray-600 hover:text-red-500 text-xl z-10" onClick={() => setShowModal(false)}>
              <FaTimes />
            </button>
            <AddEmployeeForm />
          </div>
        </div>
      )}
    </div>
  );
};

export default AddUserForm;