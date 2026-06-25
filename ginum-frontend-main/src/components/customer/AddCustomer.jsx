import { useState } from "react";

export default function AddCustomerForm() {
  const [formData, setFormData] = useState({
    name: "",
    phone_no: "",
    email: "",
    nic_no: "",
    customer_type: "",
    tin_no: "",
    vat: "",
    br_document: null,
    swift_no: "",
    billing_address: "",
    delivery_address: "",
    currencyId: "1",
    discount: "",
    tax: "INCLUSIVE",
  });

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const [fileInputKey, setFileInputKey] = useState(Date.now());

  const getAuthToken = () => {
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

  const handleChange = (e) => {
    const { name, value, type, files } = e.target;

    setFormData((prev) => ({
      ...prev,
      [name]: type === "file" ? files[0] : value,
    }));

    if (errors[name]) {
      setErrors((prev) => ({
        ...prev,
        [name]: "",
      }));
    }
  };

  const validateForm = () => {
    const newErrors = {};

    if (!formData.name.trim()) {
      newErrors.name = "Name is required";
    }

    if (!formData.phone_no.trim()) {
      newErrors.phone_no = "Phone No is required";
    } else if (!/^\+?[0-9\s-]{10,}$/.test(formData.phone_no)) {
      newErrors.phone_no = "Invalid phone number format";
    }

    if (
      formData.email &&
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)
    ) {
      newErrors.email = "Invalid email address";
    }

    if (
      formData.nic_no &&
      !/^[0-9]{9}[vVxX]?$|^[0-9]{12}$/.test(formData.nic_no)
    ) {
      newErrors.nic_no = "Invalid NIC No";
    }

    if (!formData.customer_type) {
      newErrors.customer_type = "Customer Type is required";
    }

    if (formData.tin_no && !/^\d+$/.test(formData.tin_no)) {
      newErrors.tin_no = "TIN No must be numeric";
    }

    if (formData.vat && !/^\d+$/.test(formData.vat)) {
      newErrors.vat = "VAT must be numeric";
    }

    if (!formData.delivery_address.trim()) {
      newErrors.delivery_address = "Delivery Address is required";
    }

    if (!formData.billing_address.trim()) {
      newErrors.billing_address = "Billing Address is required";
    }

    if (formData.swift_no && !/^[A-Za-z0-9]+$/.test(formData.swift_no)) {
      newErrors.swift_no = "SWIFT No must be alphanumeric";
    }

    if (!formData.currencyId) {
      newErrors.currencyId = "Currency is required";
    }

    if (formData.discount !== "" && isNaN(Number(formData.discount))) {
      newErrors.discount = "Discount must be a number";
    } else if (
      formData.discount !== "" &&
      (Number(formData.discount) < 0 || Number(formData.discount) > 100)
    ) {
      newErrors.discount = "Discount must be between 0 and 100";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const resetForm = () => {
    setFormData({
      name: "",
      phone_no: "",
      email: "",
      nic_no: "",
      customer_type: "",
      tin_no: "",
      vat: "",
      br_document: null,
      swift_no: "",
      billing_address: "",
      delivery_address: "",
      currencyId: "1",
      discount: "",
      tax: "INCLUSIVE",
    });

    setErrors({});
    setFileInputKey(Date.now());
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const isValid = validateForm();
    if (!isValid) return;

    const token = getAuthToken();
    const companyId = getCompanyId();

    console.log("Token:", token);
    console.log("Company ID:", companyId);

    if (!token) {
      alert("Login token not found. Please login again.");
      return;
    }

    if (!companyId) {
      alert("Company ID not found. Please login again.");
      return;
    }

    const customerPayload = {
      name: formData.name,
      phoneNo: formData.phone_no,
      email: formData.email,
      nicNo: formData.nic_no,
      customerType: formData.customer_type,
      vat: formData.vat,
      tinNo: formData.tin_no,
      deliveryAddress: formData.delivery_address,
      tax: formData.tax,
      billingAddress: formData.billing_address,
      swiftNo: formData.swift_no,
      currencyId: Number(formData.currencyId),
      discountPercentage:
        formData.discount !== "" ? Number(formData.discount) : 0,
      companyId: Number(companyId),
    };

    console.log("Customer Payload:", customerPayload);

    const multipartData = new FormData();

    multipartData.append(
      "customer",
      new Blob([JSON.stringify(customerPayload)], {
        type: "application/json",
      })
    );

    if (formData.br_document) {
      multipartData.append("businessRegistration", formData.br_document);
    }

    try {
      setLoading(true);

      const response = await fetch("http://localhost:8081/api/customers", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
        },
        body: multipartData,
      });

      if (!response.ok) {
        const errorText = await response.text();
        console.error("Backend error:", errorText);
        alert("Customer save failed. Check console.");
        return;
      }

      const savedCustomer = await response.json();
      console.log("Customer saved:", savedCustomer);
      alert("Customer saved successfully!");

      resetForm();
    } catch (error) {
      console.error("Cannot connect to backend:", error);
      alert("Cannot connect to backend. Check backend server or CORS.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex items-center justify-center p-4">
      <div className="w-full max-w-2xl bg-white rounded-lg shadow-md p-7 flex flex-col">
        <h2 className="text-2xl font-bold text-gray-900 mb-6">Add Customer</h2>

        <form onSubmit={handleSubmit} className="space-y-4 w-full">
          <div className="flex flex-wrap -mx-2">
            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">
                Name <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="name"
                value={formData.name}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.name && (
                <p className="text-red-500 text-sm">{errors.name}</p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">
                Phone No <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="phone_no"
                value={formData.phone_no}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.phone_no && (
                <p className="text-red-500 text-sm">{errors.phone_no}</p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">Email</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.email && (
                <p className="text-red-500 text-sm">{errors.email}</p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">NIC No</label>
              <input
                type="text"
                name="nic_no"
                value={formData.nic_no}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.nic_no && (
                <p className="text-red-500 text-sm">{errors.nic_no}</p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">
                Customer Type <span className="text-red-500">*</span>
              </label>
              <select
                name="customer_type"
                value={formData.customer_type}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Select Type *</option>
                <option value="INDIVIDUAL">Individual</option>
                <option value="CORPORATE">Corporate</option>
                <option value="GOVERNMENT">Government</option>
              </select>
              {errors.customer_type && (
                <p className="text-red-500 text-sm">
                  {errors.customer_type}
                </p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">TIN No</label>
              <input
                type="text"
                name="tin_no"
                value={formData.tin_no}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.tin_no && (
                <p className="text-red-500 text-sm">{errors.tin_no}</p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">VAT</label>
              <input
                type="text"
                name="vat"
                value={formData.vat}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.vat && (
                <p className="text-red-500 text-sm">{errors.vat}</p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">
                Tax <span className="text-red-500">*</span>
              </label>

              <div className="flex gap-4 mt-2">
                <label>
                  <input
                    type="radio"
                    name="tax"
                    value="INCLUSIVE"
                    checked={formData.tax === "INCLUSIVE"}
                    onChange={handleChange}
                  />
                  <span className="ml-1">Inclusive</span>
                </label>

                <label>
                  <input
                    type="radio"
                    name="tax"
                    value="EXCLUSIVE"
                    checked={formData.tax === "EXCLUSIVE"}
                    onChange={handleChange}
                  />
                  <span className="ml-1">Exclusive</span>
                </label>
              </div>
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">
                Delivery Address <span className="text-red-500">*</span>
              </label>
              <textarea
                name="delivery_address"
                value={formData.delivery_address}
                rows="4"
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.delivery_address && (
                <p className="text-red-500 text-sm">
                  {errors.delivery_address}
                </p>
              )}
            </div>

            <div className="w-full md:w-1/2 px-2">
              <label className="block text-gray-700">
                Billing Address <span className="text-red-500">*</span>
              </label>
              <textarea
                name="billing_address"
                value={formData.billing_address}
                onChange={handleChange}
                rows="4"
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.billing_address && (
                <p className="text-red-500 text-sm">
                  {errors.billing_address}
                </p>
              )}
            </div>

            <div className="w-full md:w-1/3 px-2">
              <label className="block text-gray-700">SWIFT No</label>
              <input
                type="text"
                name="swift_no"
                value={formData.swift_no}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
              {errors.swift_no && (
                <p className="text-red-500 text-sm">{errors.swift_no}</p>
              )}
            </div>

            <div className="w-full md:w-1/3 px-2">
              <label className="block text-gray-700">
                Currency <span className="text-red-500">*</span>
              </label>
              <select
                name="currencyId"
                value={formData.currencyId}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="1">USD</option>
                <option value="2">EUR</option>
                <option value="3">LKR</option>
              </select>
              {errors.currencyId && (
                <p className="text-red-500 text-sm">{errors.currencyId}</p>
              )}
            </div>

            <div className="w-full md:w-1/3 px-2">
              <label className="block text-gray-700">Discount (%)</label>
              <input
                type="number"
                name="discount"
                value={formData.discount}
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                step="0.01"
                min="0"
                max="100"
              />
              {errors.discount && (
                <p className="text-red-500 text-sm">{errors.discount}</p>
              )}
            </div>

            <div className="w-full px-2">
              <label className="block text-gray-700">
                Business Registration BR
              </label>
              <input
                key={fileInputKey}
                type="file"
                name="br_document"
                onChange={handleChange}
                className="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
          >
            {loading ? "Saving..." : "Save"}
          </button>
        </form>
      </div>
    </div>
  );
}