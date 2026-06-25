import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import {
  FiEdit,
  FiTrash2,
  FiPlus,
  FiSearch,
  FiUser,
  FiPhone,
  FiMail,
  FiHome,
  FiTag,
  FiRefreshCw,
} from "react-icons/fi";
import { apiUrl } from "../../utils/api";

const CustomersList = () => {
  const navigate = useNavigate();

  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [searchTerm, setSearchTerm] = useState("");

  const getToken = () => {
    return (
      sessionStorage.getItem("auth_token") ||
      localStorage.getItem("auth_token") ||
      sessionStorage.getItem("token") ||
      localStorage.getItem("token")
    );
  };

  const getCompanyId = () => {
    return (
      sessionStorage.getItem("companyId") ||
      localStorage.getItem("companyId")
    );
  };

  const fetchCustomers = async () => {
    try {
      setLoading(true);
      setError("");

      const companyId = getCompanyId();
      const token = getToken();

      if (!companyId || !token) {
        setError("Missing company ID or auth token. Please login again.");
        setLoading(false);
        return;
      }

      const response = await fetch(
        `${apiUrl}/api/customers/companies/${companyId}`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (!response.ok) {
        const errorText = await response.text();
        console.error("Backend error:", errorText);
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      console.log("Customers:", data);

      setCustomers(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error("Failed to fetch customers:", err);
      setError("Failed to fetch customers. " + err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchCustomers();
  }, []);

  const filteredCustomers = customers.filter((customer) => {
    const searchLower = searchTerm.toLowerCase();

    return (
      (customer.customerName || "").toLowerCase().includes(searchLower) ||
      (customer.email || "").toLowerCase().includes(searchLower) ||
      (customer.mobileNo || "").toLowerCase().includes(searchLower) ||
      (customer.address || "").toLowerCase().includes(searchLower) ||
      (customer.customerType || "").toLowerCase().includes(searchLower) ||
      (customer.tax || "").toLowerCase().includes(searchLower)
    );
  });

  const getInitials = (name) => {
    if (!name) return "C";

    const parts = name.trim().split(" ");

    if (parts.length === 1) {
      return parts[0].charAt(0).toUpperCase();
    }

    return (
      parts[0].charAt(0).toUpperCase() +
      parts[parts.length - 1].charAt(0).toUpperCase()
    );
  };

  const formatEnumText = (value) => {
    if (!value) return "-";

    return value
      .toString()
      .toLowerCase()
      .replace("_", " ")
      .replace(/\b\w/g, (char) => char.toUpperCase());
  };

  const handleEdit = (customer) => {
    console.log("Edit customer:", customer);

    // Later, when backend sends customerId/id, use:
    // navigate(`/customers/edit/${customer.customerId}`);
    alert("Edit function needs customer ID from backend.");
  };

  const handleDelete = (customer) => {
    if (window.confirm("Are you sure you want to delete this customer?")) {
      console.log("Delete customer:", customer);

      // Later, create DELETE backend endpoint and call it here.
      alert("Delete function needs backend delete API.");
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
      <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-4 mt-6">
        <p className="font-bold">Error</p>
        <p>{error}</p>

        <button
          type="button"
          onClick={fetchCustomers}
          className="mt-4 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 transition-colors"
        >
          <FiRefreshCw /> Retry
        </button>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h1 className="text-3xl font-bold text-gray-800 mb-4 md:mb-0">
          Customers
        </h1>

        <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
          <div className="relative flex-grow">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <FiSearch className="text-gray-400" />
            </div>

            <input
              type="text"
              placeholder="Search customers..."
              className="pl-10 pr-4 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>

          <button
            type="button"
            onClick={() => navigate("/customers/add")}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors"
          >
            <FiPlus /> Add Customer
          </button>
        </div>
      </div>

      {filteredCustomers.length === 0 ? (
        <div className="bg-white rounded-lg shadow p-8 text-center">
          <div className="mx-auto h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-4">
            <FiUser size={30} />
          </div>

          <p className="text-gray-600 text-lg">
            {customers.length === 0
              ? "No customers found."
              : "No matching customers found."}
          </p>

          <button
            type="button"
            onClick={() => navigate("/customers/add")}
            className="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-flex items-center gap-2 transition-colors"
          >
            <FiPlus /> Add New Customer
          </button>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th
                    scope="col"
                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Customer
                  </th>

                  <th
                    scope="col"
                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell"
                  >
                    Contact Info
                  </th>

                  <th
                    scope="col"
                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Address
                  </th>

                  <th
                    scope="col"
                    className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell"
                  >
                    Type / Tax
                  </th>

                  <th
                    scope="col"
                    className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Actions
                  </th>
                </tr>
              </thead>

              <tbody className="bg-white divide-y divide-gray-200">
                {filteredCustomers.map((customer, index) => (
                  <tr
                    key={index}
                    className="hover:bg-gray-50 transition-colors"
                  >
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold uppercase">
                          {getInitials(customer.customerName)}
                        </div>

                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">
                            {customer.customerName || "-"}
                          </div>

                          <div className="text-sm text-gray-500 flex items-center">
                            <FiTag className="mr-1" size={14} />
                            {formatEnumText(customer.customerType)}
                          </div>
                        </div>
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                      <div className="text-sm text-gray-900 flex items-center">
                        <FiPhone className="mr-2" size={14} />
                        {customer.mobileNo || "-"}
                      </div>

                      <div className="text-sm text-gray-500 flex items-center">
                        <FiMail className="mr-2" size={14} />
                        <span
                          className="truncate max-w-xs"
                          title={customer.email}
                        >
                          {customer.email || "-"}
                        </span>
                      </div>
                    </td>

                    <td className="px-6 py-4">
                      <div className="text-sm text-gray-900 flex items-center">
                        <FiHome className="mr-2 flex-shrink-0" size={14} />
                        <span
                          className="truncate max-w-sm"
                          title={customer.address}
                        >
                          {customer.address || "-"}
                        </span>
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                      <div className="text-sm text-gray-900">
                        {formatEnumText(customer.customerType)}
                      </div>

                      <div className="text-sm text-gray-500">
                        Tax: {formatEnumText(customer.tax)}
                      </div>
                    </td>

                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div className="flex justify-end space-x-2">
                        <button
                          type="button"
                          onClick={() => handleEdit(customer)}
                          className="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition-colors"
                          title="Edit"
                        >
                          <FiEdit size={18} />
                        </button>

                        <button
                          type="button"
                          onClick={() => handleDelete(customer)}
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

          <div className="bg-gray-50 px-6 py-3 text-sm text-gray-600">
            Showing {filteredCustomers.length} of {customers.length} customers
          </div>
        </div>
      )}
    </div>
  );
};

export default CustomersList;