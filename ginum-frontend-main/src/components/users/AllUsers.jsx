import React, { useState, useEffect } from "react";
import { FiSearch, FiUser, FiEdit, FiTrash2 } from "react-icons/fi";
import { apiUrl } from "../../utils/api";
import api from "../../utils/api";

function AllUsers() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const companyId = sessionStorage.getItem("companyId");
  const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      // මෙතැනදී api.get භාවිතා කරනවා නම් api එක හරහා යන්න, 
      // නැත්නම් apiUrl පාවිච්චි කරලා fetch යන්න.
      const response = await fetch(`${apiUrl}/api/users/companies/${companyId}`, {
        method: "GET",
        headers: { 
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json" 
        }
      });

      if (!response.ok) throw new Error("Failed to fetch");

      const data = await response.json();

      console.log("Users fetched successfully:", data);
      setUsers(Array.isArray(data) ? data : []);

    } catch (error) {
      console.error("Error fetching users:", error);
    } finally {
      setLoading(false);
    }
  };

  const filteredUsers = users.filter(user => 
    (user.email || "").toLowerCase().includes(searchTerm.toLowerCase()) ||
    (user.role || "").toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <div className="max-w-6xl mx-auto">
        <h1 className="text-2xl font-bold mb-6">System Users</h1>
        
        {/* Search Bar */}
        <div className="mb-6 relative">
          <FiSearch className="absolute left-3 top-3 text-gray-400" />
          <input 
            type="text" 
            placeholder="Search users..." 
            className="pl-10 pr-4 py-2 border rounded-lg w-full md:w-1/3"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>

        <div className="bg-white shadow rounded-lg overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-100">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User Email</th>
                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                <th className="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
  {loading ? (
    <tr><td colSpan="3" className="text-center py-8">Loading...</td></tr>
  ) : filteredUsers.length > 0 ? (
    filteredUsers.map((user) => (
      <tr key={user.id} className="hover:bg-gray-50">
        <td className="px-6 py-4">{user.email || "No Email"}</td>
        <td className="px-6 py-4">
          <span className="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
            {user.role || "USER"}
          </span>
        </td>
        <td className="px-6 py-4 text-right">
           <button className="text-blue-600 hover:text-blue-800 p-2"><FiEdit /></button>
           <button className="text-red-600 hover:text-red-800 p-2"><FiTrash2 /></button>
        </td>
      </tr>
    ))
  ) : (
    <tr><td colSpan="3" className="text-center py-8 text-gray-500">No users found. Please add a new user.</td></tr>
  )}
</tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

export default AllUsers;