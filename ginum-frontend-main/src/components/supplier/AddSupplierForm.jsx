import { useState } from "react";
import { apiUrl } from "../../utils/api"; 
import Alert from "../../components/Alert/Alert";
import { FaSpinner } from "react-icons/fa";

export default function AddSupplierForm({ onClose }) {
  const [formData, setFormData] = useState({
    supplier_name: "",
    email: "",
    mobile: "",
    address: "",
    supplier_type: "",
    item_category: "",
    tin_no: "",
    vat: "",
    tax: "inclusive",
    swift_no: "",
    currency: "USD",
    discount: "",
    br_document: null,
  });

  const [errors, setErrors] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false); 

  const itemCategories = ["CLOTHING", "AUTOMOBILE", "FURNITURE", "FOOD", "ELECTRONICS"];

  const handleChange = (e) => {
    const { name, value, type, files } = e.target;
    setFormData({ ...formData, [name]: type === "file" ? files[0] : value });
    if (errors[name]) {
      setErrors({ ...errors, [name]: "" });
    }
  };

  const validateForm = () => {
    const newErrors = {};

    if (!formData.supplier_name.trim()) newErrors.supplier_name = "Supplier Name is required";
    if (formData.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = "Invalid email address";
    }
    
    // FIX: Mobile number is now explicitly required and strictly checked for 10 digits
    if (!formData.mobile.trim()) {
      newErrors.mobile = "Mobile No is required";
    } else if (!/^\d{10}$/.test(formData.mobile)) {
      newErrors.mobile = "Mobile number must be exactly 10 digits (e.g., 0712345678)";
    }

    if (!formData.supplier_type) newErrors.supplier_type = "Supplier Type is required";
    if (!formData.item_category.trim()) newErrors.item_category = "Item Category is required";
    if (!formData.address.trim()) newErrors.address = "Address is required"; // Added address validation
    if (formData.tin_no && !/^\d+$/.test(formData.tin_no)) newErrors.tin_no = "TIN No must be numeric";
    if (formData.vat && !/^\d+$/.test(formData.vat)) newErrors.vat = "VAT must be numeric";
    if (formData.swift_no && !/^[A-Za-z0-9]+$/.test(formData.swift_no)) {
      newErrors.swift_no = "SWIFT No must be alphanumeric";
    }
    if (formData.discount && isNaN(formData.discount)) {
      newErrors.discount = "Discount must be a number";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validateForm()) return;

    const companyId = sessionStorage.getItem("companyId");
    const token = sessionStorage.getItem("auth_token");

    if (!companyId || !token) {
      Alert.error("Company session expired. Please re-login.");
      return;
    }

    setIsSubmitting(true);

    try {
      const supplierDetails = {
        supplierName: formData.supplier_name,
        email: formData.email,
        mobileNo: formData.mobile,
        address: formData.address,
        supplierType: formData.supplier_type,
        itemCategory: formData.item_category,
        tinNo: formData.tin_no,
        vat: formData.vat,
        tax: formData.tax.toUpperCase(), 
        swiftNo: formData.swift_no,
        currencyId: 1, 
        discountPercentage: formData.discount ? parseFloat(formData.discount) : 0,
        companyId: parseInt(companyId) 
      };

      const formDataToSend = new FormData();

      formDataToSend.append(
        "supplier",
        new Blob([JSON.stringify(supplierDetails)], { type: "application/json" })
      );

      if (formData.br_document) {
        formDataToSend.append("file", formData.br_document);
      }

      const response = await fetch(`${apiUrl}/api/suppliers/${companyId}`, {
        method: "POST",
        headers: {
          "Authorization": `Bearer ${token}`,
        },
        body: formDataToSend,
      });

      if (!response.ok) {
        const errText = await response.text();
        throw new Error(errText || `HTTP error! status: ${response.status}`);
      }

      Alert.success("Supplier added successfully!");

      if (!response.ok) {
        const errText = await response.text();
        throw new Error(errText || `HTTP error! status: ${response.status}`);
      }

      Alert.success("Supplier added successfully!");
      
      setFormData({
        supplier_name: "", email: "", mobile: "", address: "", supplier_type: "",
        item_category: "", tin_no: "", vat: "", tax: "inclusive", swift_no: "",
        currency: "USD", discount: "", br_document: null,
      });

      if (onClose) {
        onClose();
      }

    } catch (error) {
      console.error("Error adding supplier:", error);
      Alert.error("Failed to add supplier. " + error.message);
    } finally {
      setIsSubmitting(false);
    }
  };
      

  return (
    <div className="flex items-center justify-center p-4">
      <div className="w-full max-w-2xl bg-white rounded-lg shadow-md p-7 flex flex-col">
        <h2 className="text-2xl font-bold text-gray-900 mb-6">Add Supplier</h2>
        <form onSubmit={handleSubmit} className="space-y-4 w-full">
          <div className="flex flex-wrap -mx-2">
            
            {/* Supplier Name */}
            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">
                Supplier Name <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="supplier_name"
                value={formData.supplier_name}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.supplier_name && <p className="text-red-500 text-sm">{errors.supplier_name}</p>}
            </div>

            {/* Email */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">Email</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.email && <p className="text-red-500 text-sm">{errors.email}</p>}
            </div>

            {/* Mobile No - Updated to be Required */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">
                Mobile No <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="mobile"
                value={formData.mobile}
                onChange={handleChange}
                maxLength="10"
                placeholder="07XXXXXXXX"
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.mobile && <p className="text-red-500 text-sm">{errors.mobile}</p>}
            </div>

            {/* Address */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">
                Address <span className="text-red-500">*</span>
              </label>
              <textarea
                name="address"
                value={formData.address}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                rows="2"
              />
              {errors.address && <p className="text-red-500 text-sm">{errors.address}</p>}
            </div>

            {/* Supplier Type - Fixed className */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">
                Supplier Type <span className="text-red-500">*</span>
              </label>
              <select
                name="supplier_type"
                value={formData.supplier_type}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
              >
                <option value="">Select Type</option>
                <option value="MANUFACTURER">Manufacturer</option>
                <option value="SUPPLIER">Supplier</option>
                <option value="RETAILER">Retailer</option>
              </select>
              {errors.supplier_type && <p className="text-red-500 text-sm">{errors.supplier_type}</p>}
            </div>

            {/* Item Category - Fixed className */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">
                Item Category <span className="text-red-500">*</span>
              </label>
              <select
                name="item_category"
                value={formData.item_category}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
              >
                <option value="">Select Category</option>
                {itemCategories.map((cat) => (
                  <option key={cat} value={cat}>{cat}</option>
                ))}
              </select>
              {errors.item_category && <p className="text-red-500 text-sm">{errors.item_category}</p>}
            </div>

            {/* TIN No */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">TIN No</label>
              <input
                type="text"
                name="tin_no"
                value={formData.tin_no}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.tin_no && <p className="text-red-500 text-sm">{errors.tin_no}</p>}
            </div>

            {/* VAT */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">VAT</label>
              <input
                type="text"
                name="vat"
                value={formData.vat}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.vat && <p className="text-red-500 text-sm">{errors.vat}</p>}
            </div>

            {/* Tax */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700 mb-2">
                Tax <span className="text-red-500">*</span>
              </label>
              <div className="flex gap-4">
                <label className="flex items-center">
                  <input type="radio" name="tax" value="inclusive" checked={formData.tax === "inclusive"} onChange={handleChange} className="mr-2"/> Inclusive
                </label>
                <label className="flex items-center">
                  <input type="radio" name="tax" value="exclusive" checked={formData.tax === "exclusive"} onChange={handleChange} className="mr-2"/> Exclusive
                </label>
              </div>
            </div>

            {/* SWIFT No */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">SWIFT No</label>
              <input
                type="text"
                name="swift_no"
                value={formData.swift_no}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.swift_no && <p className="text-red-500 text-sm">{errors.swift_no}</p>}
            </div>

            {/* Currency */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">
                Currency <span className="text-red-500">*</span>
              </label>
              <select
                name="currency"
                value={formData.currency}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
              >
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="LKR">LKR</option>
              </select>
            </div>

            {/* Discount */}
            <div className="w-full md:w-1/2 px-2 mb-4">
              <label className="block text-gray-700">Discount (%) </label>
              <input
                type="number"
                name="discount"
                value={formData.discount}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                step="0.01"
              />
              {errors.discount && <p className="text-red-500 text-sm">{errors.discount}</p>}
            </div>

            {/* BR Document */}
            <div className="w-full px-2 mb-4">
              <label className="block text-gray-700">Business Registration (BR)</label>
              <input
                type="file"
                name="br_document"
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div className="flex justify-end pt-4 border-t mt-4">
            <button
              type="submit"
              className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:bg-blue-300 flex items-center justify-center font-medium transition-colors"
              disabled={isSubmitting}
            >
              {isSubmitting ? (
                <>
                  <FaSpinner className="animate-spin mr-2" />
                  Saving...
                </>
              ) : (
                "Save Supplier"
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}