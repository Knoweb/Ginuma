import React, { useState, useEffect } from 'react';
import { apiUrl } from "../../utils/api"; 
import Alert from "../../components/Alert/Alert";
import { FaSpinner } from "react-icons/fa";

const SuppliersList = () => {
  const [suppliers, setSuppliers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchSuppliers();
  }, []);

  const fetchSuppliers = async () => {
    const companyId = sessionStorage.getItem("companyId");
    const token = sessionStorage.getItem("auth_token");

    if (!companyId || !token) {
      setError("Company session expired. Please re-login.");
      setLoading(false);
      return;
    }

    try {
      const response = await fetch(`${apiUrl}/api/suppliers/companies/${companyId}`, {
        method: "GET",
        headers: {
          "Authorization": `Bearer ${token}`,
          "Content-Type": "application/json"
        }
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      setSuppliers(data); 

    } catch (err) {
      console.error("Error fetching suppliers:", err);
      setError("Failed to load suppliers.");
      Alert.error("Failed to load suppliers.");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = (supplierEmail) => {
    Alert.info(`Delete requested for: ${supplierEmail}`);
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64 text-blue-500">
        <FaSpinner className="animate-spin text-4xl" />
        <span className="ml-3 text-lg">Loading Suppliers...</span>
      </div>
    );
  }

  return (
    <div className="p-6">
      <h2 className="text-2xl font-bold mb-4">Suppliers</h2>
      
      {error && <div className="mb-4 p-3 bg-red-100 text-red-700 rounded">{error}</div>}

      <div className="overflow-x-auto shadow rounded-lg border border-gray-200">
        <table className="min-w-full bg-white">
          <thead className="bg-gray-100 text-gray-700">
            <tr>
              <th className="py-3 px-4 text-left">Name</th>
              <th className="py-3 px-4 text-left">Category</th>
              <th className="py-3 px-4 text-left">Phone</th>
              <th className="py-3 px-4 text-left">Email</th>
              <th className="py-3 px-4 text-left">Supplier Type</th>
              <th className="py-3 px-4 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            {suppliers.map((supplier, index) => (
              <tr key={index} className="border-t hover:bg-gray-50">
                <td className="py-3 px-4 font-medium">{supplier.supplierName}</td>
                <td className="py-3 px-4">{supplier.itemCategory}</td>
                <td className="py-3 px-4">{supplier.mobileNo}</td>
                <td className="py-3 px-4">{supplier.email}</td>
                <td className="py-3 px-4">{supplier.supplierType}</td>
                <td className="py-3 px-4">
                  <button className="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 mr-2 transition-colors">
                    Edit
                  </button>
                  <button 
                    onClick={() => handleDelete(supplier.email)}
                    className="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
            {suppliers.length === 0 && !error && (
              <tr>
                <td className="py-6 px-4 text-center text-gray-500" colSpan="6">
                  No suppliers found. Click "Add Supplier" to add a new one.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default SuppliersList;