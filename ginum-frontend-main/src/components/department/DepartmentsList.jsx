import React, { useState, useEffect } from "react";
import { FiEdit, FiTrash2, FiPlus, FiSearch, FiHash } from "react-icons/fi";
import {apiUrl} from "../../utils/api";

const DepartmentsList = () => {
  const [departments, setDepartments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [isAddModalOpen, setIsAddModalOpen] = useState(false);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [currentDepartment, setCurrentDepartment] = useState(null);
  const [newDepartment, setNewDepartment] = useState({
    name: "",
    code: "",
  });

  // Fetch departments from API
  useEffect(() => {
    const fetchDepartments = async () => {
      try {
        const companyId = sessionStorage.getItem("companyId");
        const token = sessionStorage.getItem("auth_token");

        if (!companyId || !token) {
          throw new Error("Missing company ID or authentication token");
        }

        const response = await fetch(
          `${apiUrl}/api/${companyId}/departments`,
          {
            method: "GET",
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          }
        );

        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        setDepartments(Array.isArray(data) ? data : []);
      } catch (err) {
        setError(err.message);
        // Fallback to dummy data if API fails
        setDepartments([
          { id: 1, name: "Information Technology", code: "IT-001" },
          { id: 2, name: "Human Resources", code: "HR-002" },
          { id: 3, name: "Finance", code: "FIN-003" },
        ]);
      } finally {
        setLoading(false);
      }
    };

    fetchDepartments();
  }, []);

  const filteredDepartments = departments.filter(dept => {
    const searchLower = searchTerm.toLowerCase();
    return (
      dept.name.toLowerCase().includes(searchLower) ||
      dept.code.toLowerCase().includes(searchLower)
    );
  });

  const handleAddDepartment = () => {
    if (newDepartment.name && newDepartment.code) {
      const newId = Math.max(0, ...departments.map(d => d.id)) + 1;
      setDepartments([
        ...departments,
        {
          id: newId,
          name: newDepartment.name,
          code: newDepartment.code,
        },
      ]);
      setNewDepartment({ name: "", code: "" });
      setIsAddModalOpen(false);
    }
  };

  const handleEdit = (department) => {
    setCurrentDepartment(department);
    setIsEditModalOpen(true);
  };

  const handleUpdateDepartment = () => {
    if (currentDepartment.name && currentDepartment.code) {
      setDepartments(
        departments.map(dept =>
          dept.id === currentDepartment.id ? currentDepartment : dept
        )
      );
      setIsEditModalOpen(false);
    }
  };

  const handleDelete = (departmentId) => {
    if (window.confirm("Are you sure you want to delete this department?")) {
      setDepartments(departments.filter(dept => dept.id !== departmentId));
    }
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
      <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mx-4 mt-6">
        <p className="font-bold">Warning</p>
        <p>{error} Using dummy data for demonstration.</p>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 className="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Departments</h1>
        <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
          <div className="relative flex-grow">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <FiSearch className="text-gray-400" />
            </div>
            <input
              type="text"
              placeholder="Search departments..."
              className="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
          <button 
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors"
            onClick={() => setIsAddModalOpen(true)}
          >
            <FiPlus /> Add Department
          </button>
        </div>
      </div>

      {filteredDepartments.length === 0 ? (
        <div className="bg-white rounded-lg shadow p-8 text-center">
          <p className="text-gray-600 text-lg">
            {departments.length === 0 ? "No departments found." : "No matching departments found."}
          </p>
          <button 
            className="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center gap-2 transition-colors"
            onClick={() => setIsAddModalOpen(true)}
          >
            <FiPlus /> Add New Department
          </button>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Department Name
                  </th>
                  <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Department Code
                  </th>
                  <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {filteredDepartments.map((dept) => (
                  <tr key={dept.id} className="hover:bg-gray-50 transition-colors">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm font-medium text-gray-900">
                        {dept.name}
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900 flex items-center">
                        {/* <FiHash className="mr-2" size={14} /> */}
                        {dept.code}
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div className="flex justify-end space-x-2">
                        <button
                          onClick={() => handleEdit(dept)}
                          className="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition-colors"
                          title="Edit"
                        >
                          <FiEdit size={18} />
                        </button>
                        <button
                          onClick={() => handleDelete(dept.id)}
                          className="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition-colors"
                          title="Delete"
                        >
                          <FiTrash2 size={18} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Add Department Modal */}
      {isAddModalOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div className="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div className="p-6">
              <h2 className="text-xl font-bold mb-4">Add New Department</h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Department Name</label>
                  <input
                    type="text"
                    className="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value={newDepartment.name}
                    onChange={(e) => setNewDepartment({...newDepartment, name: e.target.value})}
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Department Code</label>
                  <input
                    type="text"
                    className="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value={newDepartment.code}
                    onChange={(e) => setNewDepartment({...newDepartment, code: e.target.value})}
                  />
                </div>
              </div>
              <div className="mt-6 flex justify-end space-x-3">
                <button
                  onClick={() => setIsAddModalOpen(false)}
                  className="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
                >
                  Cancel
                </button>
                <button
                  onClick={handleAddDepartment}
                  className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Add Department
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Edit Department Modal */}
      {isEditModalOpen && currentDepartment && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div className="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div className="p-6">
              <h2 className="text-xl font-bold mb-4">Edit Department</h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Department Name</label>
                  <input
                    type="text"
                    className="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value={currentDepartment.name}
                    onChange={(e) => setCurrentDepartment({...currentDepartment, name: e.target.value})}
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Department Code</label>
                  <input
                    type="text"
                    className="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value={currentDepartment.code}
                    onChange={(e) => setCurrentDepartment({...currentDepartment, code: e.target.value})}
                  />
                </div>
              </div>
              <div className="mt-6 flex justify-end space-x-3">
                <button
                  onClick={() => setIsEditModalOpen(false)}
                  className="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100 transition-colors"
                >
                  Cancel
                </button>
                <button
                  onClick={handleUpdateDepartment}
                  className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Update Department
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default DepartmentsList;