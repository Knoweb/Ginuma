import React, { useEffect, useState } from "react";
import { apiUrl } from "../../utils/api";
import Alert from "../../components/Alert/Alert";
import { FaSpinner } from "react-icons/fa";

const AddDepartmentForm = ({
  onSuccess,
  onCancel,
  initialData = null,
  mode = "create",
}) => {
  const [formData, setFormData] = useState({
    name: "",
    code: "",
  });

  const [errors, setErrors] = useState({});
  const [isLoading, setIsLoading] = useState(false);

  const getCompanyId = () => sessionStorage.getItem("companyId");
  const getToken = () => sessionStorage.getItem("auth_token");

  useEffect(() => {
    if (initialData) {
      setFormData({
        name: initialData.name || "",
        code: initialData.code || "",
      });
    }
  }, [initialData]);

  const getAuthHeaders = () => {
    const token = getToken();

    return {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
      "Content-Type": "application/json",
    };
  };

  const extractResponse = async (response) => {
    const text = await response.text();

    if (!text) return null;

    try {
      return JSON.parse(text);
    } catch {
      return text;
    }
  };

  const getDepartmentId = (department) => {
    return department?.id || department?.departmentId || "";
  };

  const handleChange = (e) => {
    const { name, value } = e.target;

    setFormData((prev) => ({
      ...prev,
      [name]: value,
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
      newErrors.name = "Department Name is required";
    }

    if (!formData.code.trim()) {
      newErrors.code = "Department Code is required";
    }

    setErrors(newErrors);

    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!validateForm()) return;

    const companyId = getCompanyId();
    const token = getToken();

    if (!companyId || !token) {
      Alert.error("Session expired. Please login again.");
      return;
    }

    try {
      setIsLoading(true);

      const payload = {
        name: formData.name.trim(),
        code: formData.code.trim(),
      };

      const isEdit = mode === "edit" && initialData;
      const departmentId = getDepartmentId(initialData);

      const url = isEdit
        ? `${apiUrl}/api/${companyId}/departments/${departmentId}`
        : `${apiUrl}/api/${companyId}/departments`;

      const response = await fetch(url, {
        method: isEdit ? "PUT" : "POST",
        headers: getAuthHeaders(),
        body: JSON.stringify(payload),
      });

      const data = await extractResponse(response);

      if (!response.ok) {
        const message =
          data?.message ||
          data?.error ||
          data ||
          "Failed to save department.";
        throw new Error(message);
      }

      const savedDepartment = data?.data || data || payload;

      Alert.success(
        isEdit
          ? "Department updated successfully."
          : "Department added successfully."
      );

      if (!isEdit) {
        setFormData({
          name: "",
          code: "",
        });
      }

      setErrors({});

      if (onSuccess) {
        onSuccess(savedDepartment);
      }
    } catch (err) {
      console.error("Department save error:", err);
      Alert.error(err.message || "Failed to save department.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="flex items-center justify-center p-4">
      <div className="w-full max-w-2xl bg-white rounded-lg shadow-md p-7 flex flex-col">
        <h2 className="text-2xl font-bold text-gray-900 mb-6">
          {mode === "edit" ? "Edit Department" : "Add Department"}
        </h2>

        <form className="space-y-4" onSubmit={handleSubmit}>
          <div>
            <label className="block text-gray-700 mb-1">
              Department Name <span className="text-red-500">*</span>
            </label>

            <input
              type="text"
              name="name"
              className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                errors.name ? "border-red-500" : "border-gray-300"
              }`}
              value={formData.name}
              onChange={handleChange}
              disabled={isLoading}
              placeholder="Enter department name"
            />

            {errors.name && (
              <p className="text-red-500 text-sm mt-1">{errors.name}</p>
            )}
          </div>

          <div>
            <label className="block text-gray-700 mb-1">
              Department Code <span className="text-red-500">*</span>
            </label>

            <input
              type="text"
              name="code"
              className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                errors.code ? "border-red-500" : "border-gray-300"
              }`}
              value={formData.code}
              onChange={handleChange}
              disabled={isLoading}
              placeholder="Enter department code"
            />

            {errors.code && (
              <p className="text-red-500 text-sm mt-1">{errors.code}</p>
            )}
          </div>

          <div className="flex justify-end gap-3 pt-4">
            {onCancel && (
              <button
                type="button"
                onClick={onCancel}
                disabled={isLoading}
                className="px-4 py-2 rounded-lg bg-gray-500 hover:bg-gray-600 text-white disabled:bg-gray-300"
              >
                Cancel
              </button>
            )}

            <button
              type="submit"
              disabled={isLoading}
              className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 flex items-center gap-2"
            >
              {isLoading && <FaSpinner className="animate-spin" />}
              {isLoading
                ? "Saving..."
                : mode === "edit"
                ? "Update"
                : "Save"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default AddDepartmentForm;