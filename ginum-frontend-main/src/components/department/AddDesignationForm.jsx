import React, { useEffect, useState } from "react";
import { FaPlusCircle, FaTimes, FaSpinner } from "react-icons/fa";
import AddDepartmentForm from "./AddDepartmentForm";
import { apiUrl } from "../../utils/api";
import Alert from "../../components/Alert/Alert";

const AddDesignationForm = ({
  onSuccess,
  onCancel,
  initialData = null,
  mode = "create",
}) => {
  const [selectedDepartment, setSelectedDepartment] = useState("");
  const [designationName, setDesignationName] = useState("");
  const [departments, setDepartments] = useState([]);

  const [errors, setErrors] = useState({});
  const [showDepartmentModal, setShowDepartmentModal] = useState(false);

  const [isLoading, setIsLoading] = useState(false);
  const [isFetchingDepartments, setIsFetchingDepartments] = useState(false);

  const getCompanyId = () => sessionStorage.getItem("companyId");
  const getToken = () => sessionStorage.getItem("auth_token");

  const getAuthHeaders = () => {
    const token = getToken();

    return {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
      "Content-Type": "application/json",
    };
  };

  const extractArray = (data) => {
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.data)) return data.data;
    if (Array.isArray(data?.content)) return data.content;
    if (Array.isArray(data?.departments)) return data.departments;
    return [];
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

  const getDesignationId = (designation) => {
    return designation?.id || designation?.designationId || "";
  };

  const fetchDepartments = async () => {
    const companyId = getCompanyId();
    const token = getToken();

    if (!companyId || !token) {
      Alert.error("Authentication required. Please log in again.");
      return;
    }

    try {
      setIsFetchingDepartments(true);

      const response = await fetch(`${apiUrl}/api/${companyId}/departments`, {
        method: "GET",
        headers: getAuthHeaders(),
      });

      const data = await extractResponse(response);

      if (!response.ok) {
        const message =
          data?.message || data?.error || data || "Failed to load departments.";
        throw new Error(message);
      }

      const list = extractArray(data);
      setDepartments(list);

      if (initialData?.departmentCode) {
        const matchedDepartment = list.find(
          (dept) => dept.code === initialData.departmentCode
        );

        if (matchedDepartment) {
          setSelectedDepartment(String(getDepartmentId(matchedDepartment)));
        }
      }
    } catch (err) {
      console.error("Departments fetch error:", err);
      Alert.error(err.message || "Failed to load departments.");
      setDepartments([]);
    } finally {
      setIsFetchingDepartments(false);
    }
  };

  useEffect(() => {
    fetchDepartments();
  }, []);

  useEffect(() => {
    if (initialData) {
      setDesignationName(initialData.name || "");

      if (initialData.departmentId) {
        setSelectedDepartment(String(initialData.departmentId));
      } else if (initialData.departmentCode) {
        setSelectedDepartment(String(initialData.departmentCode));
      }
    }
  }, [initialData]);

  const validateForm = () => {
    const validationErrors = {};

    if (!selectedDepartment) {
      validationErrors.department = "Department is required";
    }

    if (!designationName.trim()) {
      validationErrors.designationName = "Designation Name is required";
    }

    setErrors(validationErrors);

    return Object.keys(validationErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    const companyId = getCompanyId();
    const token = getToken();

    if (!companyId || !token) {
      Alert.error("Authentication required. Please log in again.");
      return;
    }

    if (!validateForm()) return;

    try {
      setIsLoading(true);

      const department = departments.find((dept) => {
        const deptId = String(getDepartmentId(dept));
        const deptCode = String(dept.code || "");
        const selected = String(selectedDepartment);
        return selected === deptId || selected === deptCode;
      });

      if (!department) {
        throw new Error("Selected department not found.");
      }

      const payload = {
        name: designationName.trim(),
        departmentCode: department.code,
      };

      const isEdit = mode === "edit" && initialData;
      const designationId = getDesignationId(initialData);

      const url = isEdit
        ? `${apiUrl}/api/${companyId}/designations/${designationId}`
        : `${apiUrl}/api/${companyId}/designations`;

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
          "Failed to save designation.";
        throw new Error(message);
      }

      const savedDesignation = data?.data || data || payload;

      Alert.success(
        isEdit
          ? "Designation updated successfully."
          : "Designation added successfully."
      );

      if (!isEdit) {
        setSelectedDepartment("");
        setDesignationName("");
      }

      setErrors({});

      if (onSuccess) {
        onSuccess(savedDesignation);
      }
    } catch (err) {
      console.error("Designation save error:", err);
      Alert.error(err.message || "Failed to save designation.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleNewDepartment = (newDepartment) => {
    const savedDepartment = newDepartment?.data || newDepartment;

    if (savedDepartment) {
      setDepartments((prev) => [...prev, savedDepartment]);

      const newDepartmentId = getDepartmentId(savedDepartment);

      if (newDepartmentId) {
        setSelectedDepartment(String(newDepartmentId));
      }
    }

    setShowDepartmentModal(false);
  };

  return (
    <div className="flex items-center justify-center p-4">
      <div className="w-full max-w-2xl bg-white rounded-lg shadow-md p-7 flex flex-col">
        <h2 className="text-2xl font-bold text-gray-900 mb-6">
          {mode === "edit" ? "Edit Designation" : "Add Designation"}
        </h2>

        <form className="space-y-4" onSubmit={handleSubmit}>
          <div className="w-full">
            <label className="block text-gray-700 mb-1">
              Department <span className="text-red-500">*</span>
            </label>

            {isFetchingDepartments ? (
              <div className="w-full px-4 py-2 border rounded-lg bg-gray-100 flex items-center gap-2">
                <FaSpinner className="animate-spin text-blue-500" />
                Loading departments...
              </div>
            ) : (
              <>
                <select
                  className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                    errors.department ? "border-red-500" : "border-gray-300"
                  }`}
                  value={selectedDepartment}
                  onChange={(e) => {
                    setSelectedDepartment(e.target.value);
                    setErrors((prev) => ({ ...prev, department: "" }));
                  }}
                  disabled={isLoading}
                >
                  <option value="">Select Department</option>

                  {departments.map((dept, index) => {
                    const optionKey = String(getDepartmentId(dept) || dept.code || index);
                    const optionValue = String(getDepartmentId(dept) || dept.code || "");

                    return (
                      <option key={optionKey} value={optionValue}>
                        {dept.name} ({dept.code})
                      </option>
                    );
                  })}
                </select>

                {errors.department && (
                  <p className="text-red-500 text-sm mt-1">
                    {errors.department}
                  </p>
                )}

                {mode !== "edit" && (
                  <div className="mt-2">
                    <button
                      type="button"
                      className="text-blue-500 flex items-center gap-2"
                      onClick={() => setShowDepartmentModal(true)}
                    >
                      <FaPlusCircle />
                      Add New Department
                    </button>
                  </div>
                )}
              </>
            )}
          </div>

          <div className="w-full">
            <label className="block text-gray-700 mb-1">
              Designation Name <span className="text-red-500">*</span>
            </label>

            <input
              type="text"
              className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                errors.designationName ? "border-red-500" : "border-gray-300"
              }`}
              value={designationName}
              onChange={(e) => {
                setDesignationName(e.target.value);
                setErrors((prev) => ({ ...prev, designationName: "" }));
              }}
              disabled={isLoading}
              placeholder="Enter designation name"
            />

            {errors.designationName && (
              <p className="text-red-500 text-sm mt-1">
                {errors.designationName}
              </p>
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
              className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 flex items-center gap-2"
              disabled={isLoading}
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

      {showDepartmentModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]">
          <div className="w-11/12 sm:w-3/4 md:w-1/2 lg:w-2/5 xl:w-1/3 rounded-lg max-h-[90vh] overflow-y-auto relative">
            <button
              type="button"
              className="absolute top-4 right-4 text-gray-600 text-xl z-10"
              onClick={() => setShowDepartmentModal(false)}
            >
              <FaTimes />
            </button>

            <AddDepartmentForm
              onSuccess={handleNewDepartment}
              onCancel={() => setShowDepartmentModal(false)}
            />
          </div>
        </div>
      )}
    </div>
  );
};

export default AddDesignationForm;