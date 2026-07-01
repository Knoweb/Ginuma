import React, { useState, useEffect } from "react";
import { MdDelete, MdAddCircleOutline } from "react-icons/md";
import { FaTimes } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import { apiUrl } from "../../utils/api";
import PayerPayee from "../PayerPayee/PayerPayee";
import AddAccountForm from "../account/AddAccountForm";
import NewProjectForm from "../projects/NewProjectForm";

const PayeeDropdown = ({ value, onChange, onAddNew }) => {
  const [isOpen, setIsOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(false);

  const fetchCustomers = async () => {
    const companyId = sessionStorage.getItem("companyId");
    const token = sessionStorage.getItem("auth_token") || sessionStorage.getItem("token");
    if (!companyId || !token) return;
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/api/customers/companies/${companyId}`, { 
        headers: { Authorization: `Bearer ${token}` } 
      });
      const data = res.ok ? await res.json() : [];
      setCustomers(data.map(c => ({ 
        id: `CUS-${c.customerId || c.id}`, 
        name: c.customerName, 
        contact: c.mobileNo, 
        type: "Customer" 
      })));
    } catch (error) { console.error(error); } finally { setLoading(false); }
  };

  useEffect(() => { fetchCustomers(); }, []);

  const filtered = customers.filter(c => (c.name || "").toLowerCase().includes(searchTerm.toLowerCase()));
  const selectedItem = customers.find(item => item.id === value);

  return (
    <div className="relative w-full">
      <div className="flex items-center justify-between w-full px-3 py-2 border rounded-lg cursor-pointer bg-white" onClick={() => setIsOpen(!isOpen)}>
        {selectedItem ? <div>{selectedItem.name} <span className="text-xs bg-gray-200 px-1 rounded">{selectedItem.type}</span></div> : <span className="text-gray-400">{loading ? "Loading..." : "Select Customer"}</span>}
      </div>
      {isOpen && (
        <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
          <input type="text" className="w-full p-2 border-b" placeholder="Search..." onChange={(e) => setSearchTerm(e.target.value)} />
          {filtered.map(item => (
            <div key={item.id} className="px-4 py-2 cursor-pointer hover:bg-blue-50" onClick={() => { onChange(item.id); setIsOpen(false); }}>
              {item.name}
            </div>
          ))}
          <button onClick={onAddNew} className="w-full p-2 text-blue-600 hover:bg-blue-50 font-semibold border-t">+ Add New Customer</button>
        </div>
      )}
    </div>
  );
};

const ReceiveMoney = () => {
  const navigate = useNavigate();
  const [selectedPayee, setSelectedPayee] = useState(null);
  const [selectedBankAccount, setSelectedBankAccount] = useState("");
  const [accounts, setAccounts] = useState([]);
  const [projects, setProjects] = useState([]);
  const [rows, setRows] = useState([{ account: "", amount: "0.00", quantity: "0", description: "", project: "" }]);
  const [date, setDate] = useState(new Date().toISOString().split("T")[0]);
  const [referenceNumber, setReferenceNumber] = useState("REF-001");
  const [description, setDescription] = useState("");
  
  const [showContactModal, setShowContactModal] = useState(false);
  const [showAccountModal, setShowAccountModal] = useState(false);
  const [showProjectModal, setShowProjectModal] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      const companyId = sessionStorage.getItem("companyId");
      const token = sessionStorage.getItem("auth_token");
      if (!companyId || !token) return;
      const [accRes, projRes] = await Promise.all([
        fetch(`${apiUrl}/api/companies/${companyId}/accounts`, { headers: { Authorization: `Bearer ${token}` } }),
        fetch(`${apiUrl}/api/companies/${companyId}/projects`, { headers: { Authorization: `Bearer ${token}` } })
      ]);
      if(accRes.ok) setAccounts(await accRes.json());
      if(projRes.ok) setProjects(await projRes.json());
    };
    fetchData();
  }, []);

  const handleRowChange = (index, field, value) => {
    const newRows = [...rows];
    newRows[index][field] = value;
    if (index === rows.length - 1 && value !== "") newRows.push({ account: "", amount: "0.00", quantity: "0", description: "", project: "" });
    setRows(newRows);
  };

  const handleRecord = async () => {
    const payload = {
      companyId: parseInt(sessionStorage.getItem("companyId")),
      accountId: parseInt(selectedBankAccount),
      payeeId: selectedPayee,
      transactionType: "RECEIVE",
      referenceNumber,
      description,
      date,
      totalAmount: rows.reduce((sum, r) => sum + parseFloat(r.amount || 0), 0),
      rows: rows.filter(r => r.account !== "").map(r => ({ 
        accountId: parseInt(r.account), 
        amount: parseFloat(r.amount),
        quantity: parseInt(r.quantity),
        description: r.description,
        projectId: r.project ? parseInt(r.project) : null 
      }))
    };
    try {
      const res = await fetch(`${apiUrl}/api/transactions`, {
        method: "POST",
        headers: { "Authorization": `Bearer ${sessionStorage.getItem("auth_token")}`, "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
      if (res.ok) { alert("Recorded successfully!"); navigate("/bank-reconciliation"); }
    } catch (err) { alert("Error recording transaction!"); }
  };

  // Modal වැසීම සඳහා Function එක
  const handleModalClick = (e, setModal) => {
    if (e.target === e.currentTarget) setModal(false);
  };

  return (
    <div className="max-w-5xl mx-auto p-6 bg-white shadow rounded-lg mt-6">
      <h2 className="text-2xl font-bold mb-6">Receive Money Transaction</h2>
      
      <div className="grid grid-cols-2 gap-4 mb-4">
          <div className="flex items-center gap-2">
            <PayeeDropdown value={selectedPayee} onChange={setSelectedPayee} onAddNew={() => setShowContactModal(true)} />
            <button onClick={() => setShowContactModal(true)}><MdAddCircleOutline className="text-2xl text-blue-600"/></button>
          </div>
          <select className="p-2 border rounded" value={selectedBankAccount} onChange={(e) => setSelectedBankAccount(e.target.value)}>
            <option value="">Select Bank Account</option>
            {accounts.map(acc => <option key={acc.id} value={acc.id}>{acc.accountCode} - {acc.accountName}</option>)}
          </select>
          <input type="text" className="p-2 border rounded" placeholder="Reference Number" value={referenceNumber} onChange={(e) => setReferenceNumber(e.target.value)} />
          <input type="date" className="p-2 border rounded" value={date} onChange={(e) => setDate(e.target.value)} />
      </div>

      <textarea className="w-full p-2 border rounded mb-4" placeholder="Transaction Description..." value={description} onChange={(e) => setDescription(e.target.value)} />

      <table className="w-full border-collapse mb-4">
        <thead><tr className="bg-gray-100"><th className="p-2 border">Account</th><th className="p-2 border">Amount</th><th className="p-2 border">Qty</th><th className="p-2 border">Description</th><th className="p-2 border">Project</th><th className="p-2 border"></th></tr></thead>
        <tbody>
          {rows.map((row, index) => (
            <tr key={index}>
              <td className="p-2 border"><select className="w-full" value={row.account} onChange={(e) => handleRowChange(index, "account", e.target.value)}><option value="">Select</option>{accounts.map(a => <option key={a.id} value={a.id}>{a.accountName}</option>)}</select></td>
              <td className="p-2 border"><input type="number" className="w-20" value={row.amount} onChange={(e) => handleRowChange(index, "amount", e.target.value)} /></td>
              <td className="p-2 border"><input type="number" className="w-16" value={row.quantity} onChange={(e) => handleRowChange(index, "quantity", e.target.value)} /></td>
              <td className="p-2 border"><input type="text" className="w-full" value={row.description} onChange={(e) => handleRowChange(index, "description", e.target.value)} /></td>
              <td className="p-2 border"><select className="w-full" value={row.project} onChange={(e) => handleRowChange(index, "project", e.target.value)}><option value="">Select</option>{projects.map(p => <option key={p.id} value={p.id}>{p.name}</option>)}</select></td>
              <td className="p-2 border text-center"><button onClick={() => setRows(rows.filter((_,i) => i !== index))}><MdDelete className="text-red-500 text-xl"/></button></td>
            </tr>
          ))}
        </tbody>
      </table>
      
      <button onClick={handleRecord} className="bg-green-600 text-white px-6 py-2 rounded-lg">Record Receive</button>

      {/* Styled Modal for Customer */}
      {showContactModal && (
        <div 
          className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-500" 
          onClick={(e) => handleModalClick(e, setShowContactModal)}
        >
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 p-2 rounded-lg max-h-[90vh] overflow-y-auto relative bg-white shadow-2xl">
            <button 
              className="absolute top-4 right-4 text-xl text-gray-600 hover:text-red-500 z-10" 
              onClick={() => setShowContactModal(false)}
            >
              <FaTimes />
            </button>
            <PayerPayee 
               allowedTabs={["customer"]} 
               onClose={() => {
                   setShowContactModal(false);
                   window.location.reload(); 
               }} 
            />
          </div>
        </div>
      )}
    </div>
  );
};

export default ReceiveMoney;