import React, { useState, useEffect } from "react";
import { apiUrl } from "../../utils/api";

function BankReconsilation() {
  const [transactions, setTransactions] = useState([]);

  useEffect(() => {
    fetchTransactions();
  }, []);

  const fetchTransactions = async () => {
    const companyId = sessionStorage.getItem("companyId");
    const token = sessionStorage.getItem("auth_token");
    
    const response = await fetch(`${apiUrl}/api/transactions/companies/${companyId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    const data = await response.json();
    setTransactions(data);
  };

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold mb-4">Bank Reconciliation</h1>
      <table className="min-w-full bg-white border">
        <thead>
          <tr>
            <th className="p-2 border">Date</th>
            <th className="p-2 border">Description</th>
            <th className="p-2 border">Amount</th>
          </tr>
        </thead>
        <tbody>
          {transactions.map(t => (
            <tr key={t.id}>
              <td className="p-2 border">{t.date}</td>
              <td className="p-2 border">{t.description}</td>
              <td className="p-2 border">{t.totalAmount}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default BankReconsilation;