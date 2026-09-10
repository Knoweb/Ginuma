import React, { useState, useEffect } from "react";
import {
  FaEye,
  FaEyeSlash,
  FaBuilding,
  FaMapMarkerAlt,
  FaUserLock,
  FaCheckCircle,
  FaArrowRight,
  FaArrowLeft,
  FaSpinner,
  FaShieldAlt,
  FaLock
} from "react-icons/fa";
import axios from "axios";
import { apiUrl } from "../../utils/api";
import api from "../../utils/api";
import { useNavigate } from "react-router-dom";

const COMPANY_CATEGORIES = [
  { value: "EDUCATION_AND_EDTECH", label: "Education and EdTech" },
  { value: "FINANCE", label: "Finance" },
  { value: "CREATIVE_AND_DESIGN", label: "Creative and Design" },
  { value: "REAL_ESTATE_AND_PROPERTY_MANAGEMENT", label: "Real Estate and Property Management" },
  { value: "CONSTRUCTION_AND_ENGINEERING", label: "Construction and Engineering" },
  { value: "HOSPITALITY_AND_TOURISM", label: "Hospitality and Tourism" },
  { value: "IT_AND_TECHNOLOGY", label: "IT and Technology" },
  { value: "MARKETING_AND_E_COMMERCE", label: "Marketing and E-Commerce" },
  { value: "MANUFACTURING_AND_LOGISTICS", label: "Manufacturing and Logistics" },
  { value: "HEALTHCARE_AND_LIFE_SCIENCES", label: "Healthcare and Life Sciences" },
  { value: "PROFESSIONAL_SERVICES", label: "Professional Services" }
];

const Register = () => {
  const navigate = useNavigate();
  const [currentStep, setCurrentStep] = useState(1);
  const [currencies, setCurrencies] = useState([]);
  const [loadingCurrencies, setLoadingCurrencies] = useState(true);
  const [currencyError, setCurrencyError] = useState(null);
  const [countries, setCountries] = useState([]);
  const [loadingCountries, setLoadingCountries] = useState(true);
  const [countryError, setCountryError] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState(null);
  const [registrationSuccess, setRegistrationSuccess] = useState(false);

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [isVatRegistered, setIsVatRegistered] = useState(false);
  const [logoPreview, setLogoPreview] = useState(null);

  const [formData, setFormData] = useState({
    companyName: "",
    companyLogo: null,
    companyCategory: "",
    registrationNo: "",
    tinNo: "",
    vatNo: "",
    phoneNo: "",
    mobileNo: "",
    registeredAddress: "",
    factoryAddress: "",
    countryId: "",
    currencyId: "",
    email: "",
    website: "",
    password: "",
    confirmPassword: "",
  });
  const [errors, setErrors] = useState({});

  // Password strength calculation
  const calculatePasswordStrength = (pass) => {
    if (!pass) return { score: 0, label: "", color: "bg-gray-200" };
    let score = 0;
    if (pass.length >= 8) score++;
    if (/[A-Z]/.test(pass)) score++;
    if (/[0-9]/.test(pass)) score++;
    if (/[^A-Za-z0-9]/.test(pass)) score++;

    if (score <= 1) return { score: 25, label: "Weak", color: "bg-rose-500", text: "text-rose-600" };
    if (score === 2 || score === 3) return { score: 65, label: "Medium", color: "bg-amber-500", text: "text-amber-600" };
    return { score: 100, label: "Strong", color: "bg-emerald-500", text: "text-emerald-600" };
  };

  const passwordStrength = calculatePasswordStrength(formData.password);

  const validateStep = (step) => {
    const newErrors = {};
    if (step === 1) {
      if (!formData.companyName.trim()) newErrors.companyName = "Company Name is required";
      if (!formData.companyCategory) newErrors.companyCategory = "Company Category is required";
      if (!formData.phoneNo.trim()) newErrors.phoneNo = "Phone Number is required";
    } else if (step === 2) {
      if (!formData.registeredAddress.trim()) newErrors.registeredAddress = "Registered Address is required";
      if (!formData.countryId) newErrors.country = "Country is required";
      if (!formData.currencyId) newErrors.currency = "Currency is required";
      if (isVatRegistered && !formData.vatNo.trim()) newErrors.vatNo = "VAT Number is required";
    } else if (step === 3) {
      if (!formData.email.trim()) {
        newErrors.email = "Email is required";
      } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
        newErrors.email = "Please enter a valid email address";
      }
      if (!formData.password) {
        newErrors.password = "Password is required";
      } else if (formData.password.length < 8) {
        newErrors.password = "Password must be at least 8 characters long";
      }
      if (!formData.confirmPassword) {
        newErrors.confirmPassword = "Please confirm your password";
      } else if (formData.confirmPassword !== formData.password) {
        newErrors.confirmPassword = "Passwords do not match";
      }
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleNext = () => {
    if (validateStep(currentStep)) {
      setCurrentStep((prev) => Math.min(prev + 1, 3));
    }
  };

  const handlePrev = () => {
    setCurrentStep((prev) => Math.max(prev - 1, 1));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitError(null);

    if (!validateStep(1) || !validateStep(2) || !validateStep(3)) {
      return;
    }

    setIsSubmitting(true);

    try {
      const formDataToSend = new FormData();
      const companyObject = {
        companyName: formData.companyName,
        companyCategory: formData.companyCategory,
        phoneNo: formData.phoneNo,
        companyRegisteredAddress: formData.registeredAddress,
        countryId: parseInt(formData.countryId),
        currencyId: parseInt(formData.currencyId),
        email: formData.email,
        password: formData.password,
        dateJoined: new Date().toISOString().split("T")[0],
        isVatRegistered: isVatRegistered,
        companyRegNo: formData.registrationNo || null,
        tinNo: formData.tinNo || null,
        vatNo: isVatRegistered ? formData.vatNo : null,
        mobileNo: formData.mobileNo || null,
        companyFactoryAddress: formData.factoryAddress || null,
        websiteUrl: formData.website || null,
      };

      formDataToSend.append(
        "company",
        new Blob([JSON.stringify(companyObject)], { type: "application/json" })
      );

      formDataToSend.append("companyName", formData.companyName);
      formDataToSend.append("companyCategory", formData.companyCategory);
      formDataToSend.append("phoneNo", formData.phoneNo);
      formDataToSend.append("companyRegisteredAddress", formData.registeredAddress);
      formDataToSend.append("countryId", formData.countryId);
      formDataToSend.append("currencyId", formData.currencyId);
      formDataToSend.append("email", formData.email);
      formDataToSend.append("password", formData.password);
      formDataToSend.append("dateJoined", new Date().toISOString().split("T")[0]);
      formDataToSend.append("isVatRegistered", isVatRegistered);

      if (formData.registrationNo) formDataToSend.append("companyRegNo", formData.registrationNo);
      if (formData.tinNo) formDataToSend.append("tinNo", formData.tinNo);
      if (isVatRegistered && formData.vatNo) formDataToSend.append("vatNo", formData.vatNo);
      if (formData.mobileNo) formDataToSend.append("mobileNo", formData.mobileNo);
      if (formData.factoryAddress) formDataToSend.append("companyFactoryAddress", formData.factoryAddress);
      if (formData.website) formDataToSend.append("websiteUrl", formData.website);

      if (formData.companyLogo) {
        formDataToSend.append("companyLogo", formData.companyLogo);
      }

      await axios.post(`${apiUrl}/api/companies`, formDataToSend);

      setRegistrationSuccess(true);
    } catch (error) {
      console.error("Registration error:", error);
      const serverMsg = error.response?.data?.error || error.response?.data?.message || (typeof error.response?.data === "string" ? error.response.data : null);
      setSubmitError(serverMsg || "Registration failed. Please check your information and try again.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleChange = (e) => {
    const { name, value, files } = e.target;
    if (files && files[0]) {
      setFormData((prev) => ({ ...prev, [name]: files[0] }));
      setLogoPreview(URL.createObjectURL(files[0]));
    } else {
      setFormData((prev) => ({ ...prev, [name]: value }));
    }
    if (errors[name]) {
      setErrors((prev) => ({ ...prev, [name]: null }));
    }
  };

  const handleVatRegistrationChange = (e) => {
    const isRegistered = e.target.value === "yes";
    setIsVatRegistered(isRegistered);
    if (!isRegistered) {
      setFormData((prev) => ({ ...prev, vatNo: "" }));
    }
  };

  const handleCountryChange = (e) => {
    const selectedCountryId = e.target.value;
    const selectedCountry = countries.find(c => c.id.toString() === selectedCountryId);

    setFormData((prev) => {
      let newCurrencyId = prev.currencyId;
      if (selectedCountry) {
          if (selectedCountry.defaultCurrencyId) {
              newCurrencyId = selectedCountry.defaultCurrencyId.toString();
          } else {
              const fallbackMap = {
                  "Sri Lanka": "LKR",
                  "United States": "USD",
                  "United Kingdom": "GBP",
                  "Australia": "AUD"
              };
              const fallbackCode = fallbackMap[selectedCountry.name];
              if (fallbackCode) {
                  const fallbackCurrency = currencies.find(c => c.code === fallbackCode);
                  if (fallbackCurrency) {
                      newCurrencyId = fallbackCurrency.id.toString();
                  }
              }
          }
      }
      return {
          ...prev,
          countryId: selectedCountryId,
          currencyId: newCurrencyId
      };
    });
  };

  useEffect(() => {
    const fetchCountries = async () => {
      try {
        setLoadingCountries(true);
        setCountryError(null);
        const response = await api.get("/api/countries");
        let countriesData = [];
        if (Array.isArray(response)) countriesData = response;
        else if (Array.isArray(response.data)) countriesData = response.data;
        else if (response?.data?.data && Array.isArray(response.data.data)) countriesData = response.data.data;

        setCountries(countriesData);
      } catch (error) {
        console.error("Country fetch error:", error);
        setCountryError(error.message);
        setCountries([
          { id: 1, name: "United States" },
          { id: 2, name: "Sri Lanka" },
          { id: 3, name: "United Kingdom" },
        ]);
      } finally {
        setLoadingCountries(false);
      }
    };
    fetchCountries();
  }, []);

  useEffect(() => {
    const fetchCurrencies = async () => {
      try {
        setLoadingCurrencies(true);
        setCurrencyError(null);
        const response = await api.get("/api/currencies");
        let currenciesData = [];
        if (Array.isArray(response)) currenciesData = response;
        else if (Array.isArray(response.data)) currenciesData = response.data;
        else if (response?.data?.data && Array.isArray(response.data.data)) currenciesData = response.data.data;

        setCurrencies(currenciesData);
      } catch (error) {
        console.error("Currency fetch error:", error);
        setCurrencyError(error.message);
        setCurrencies([
          { id: 1, code: "USD", name: "US Dollar" },
          { id: 2, code: "LKR", name: "Sri Lankan Rupee" },
          { id: 3, code: "GBP", name: "British Pound" },
        ]);
      } finally {
        setLoadingCurrencies(false);
      }
    };
    fetchCurrencies();
  }, []);

  if (registrationSuccess) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-slate-50 p-4">
        <div className="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 text-center space-y-6 border border-slate-100">
          <div className="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto text-4xl shadow-inner">
            ✉️
          </div>
          <div className="space-y-2">
            <h2 className="text-2xl font-black text-slate-900">Check Your Email</h2>
            <p className="text-slate-600 text-sm leading-relaxed">
              We have sent a verification link to <strong className="text-slate-900">{formData.email}</strong>. Please check your inbox and verify your email to activate your company account.
            </p>
          </div>
          <div className="bg-blue-50/70 border border-blue-150 rounded-2xl p-4 text-xs text-blue-800 text-left space-y-1">
            <p className="font-bold flex items-center gap-1.5">
              <span>💡</span> Tip for users:
            </p>
            <p>If you don't see the email within 2 minutes, check your Spam or Junk folder.</p>
          </div>
          <button
            onClick={() => navigate("/login")}
            className="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/25 transition duration-200 cursor-pointer"
          >
            Proceed to Sign In
          </button>
        </div>
      </div>
    );
  }

  const steps = [
    { number: 1, title: "Company Profile", icon: FaBuilding },
    { number: 2, title: "Location & Tax", icon: FaMapMarkerAlt },
    { number: 3, title: "Admin Credentials", icon: FaUserLock },
  ];

  return (
    <div className="flex min-h-screen items-center justify-center bg-slate-50 p-4 py-8">
      <div className="w-full max-w-3xl bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        
        {/* Header */}
        <div className="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 p-8 text-white text-center relative">
          <img src="/ginum_logo.png" alt="Ginum Logo" className="h-10 mx-auto mb-3 invert brightness-200" />
          <h1 className="text-2xl font-bold tracking-tight">Create Your Enterprise Account</h1>
          <p className="text-slate-400 text-xs mt-1">Setup your organization profile and administrative account</p>

          {/* Stepper Bar */}
          <div className="grid grid-cols-3 gap-2 mt-8 max-w-lg mx-auto">
            {steps.map((step) => {
              const Icon = step.icon;
              const isActive = currentStep === step.number;
              const isCompleted = currentStep > step.number;
              return (
                <div
                  key={step.number}
                  className={`flex flex-col items-center p-2.5 rounded-2xl transition ${
                    isActive
                      ? "bg-blue-600 text-white shadow-lg shadow-blue-600/30 font-bold"
                      : isCompleted
                      ? "bg-slate-800/80 text-emerald-400 font-semibold"
                      : "bg-slate-800/40 text-slate-500"
                  }`}
                >
                  <div className="flex items-center gap-1.5 text-xs">
                    {isCompleted ? <FaCheckCircle /> : <Icon />}
                    <span>Step {step.number}</span>
                  </div>
                  <span className="text-[11px] truncate max-w-[100px] mt-0.5 opacity-90">{step.title}</span>
                </div>
              );
            })}
          </div>
        </div>

        {/* Submit Error */}
        {submitError && (
          <div className="mx-8 mt-6 p-4 text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-2xl">
            {submitError}
          </div>
        )}

        {/* Form Form Body */}
        <form onSubmit={handleSubmit} className="p-8 space-y-6">

          {/* STEP 1: Company Profile */}
          {currentStep === 1 && (
            <div className="space-y-5 animate-fadeIn">
              <div className="border-b border-slate-100 pb-3">
                <h3 className="text-base font-bold text-slate-850 flex items-center gap-2">
                  <FaBuilding className="text-blue-600" /> Step 1: Organization Details
                </h3>
                <p className="text-xs text-slate-400 mt-0.5">Enter core identity and contact details for your business</p>
              </div>

              {/* Company Logo & Name */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-5 items-center">
                <div className="flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:bg-slate-100/60 transition cursor-pointer text-center relative">
                  {logoPreview ? (
                    <img src={logoPreview} alt="Logo Preview" className="h-16 w-16 object-contain mb-1 rounded-xl" />
                  ) : (
                    <div className="text-slate-400 text-2xl mb-1">🏢</div>
                  )}
                  <span className="text-xs font-bold text-slate-600">Company Logo</span>
                  <span className="text-[10px] text-slate-400">PNG, JPG up to 5MB</span>
                  <input
                    type="file"
                    name="companyLogo"
                    accept="image/*"
                    onChange={handleChange}
                    className="absolute inset-0 opacity-0 cursor-pointer"
                  />
                </div>

                <div className="md:col-span-2 space-y-4">
                  <div>
                    <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                      Company Name <span className="text-rose-500">*</span>
                    </label>
                    <input
                      type="text"
                      name="companyName"
                      value={formData.companyName}
                      onChange={handleChange}
                      placeholder="e.g. Acme Global Logistics Ltd."
                      className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                    />
                    {errors.companyName && <p className="text-rose-500 text-xs mt-1">{errors.companyName}</p>}
                  </div>

                  <div>
                    <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                      Industry Category <span className="text-rose-500">*</span>
                    </label>
                    <select
                      name="companyCategory"
                      value={formData.companyCategory}
                      onChange={handleChange}
                      className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm bg-white transition"
                    >
                      <option value="">Select Category</option>
                      {COMPANY_CATEGORIES.map((cat) => (
                        <option key={cat.value} value={cat.value}>{cat.label}</option>
                      ))}
                    </select>
                    {errors.companyCategory && <p className="text-rose-500 text-xs mt-1">{errors.companyCategory}</p>}
                  </div>
                </div>
              </div>

              {/* Phone & Mobile */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                    Primary Phone Number <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="phoneNo"
                    value={formData.phoneNo}
                    onChange={handleChange}
                    placeholder="+94 11 234 5678"
                    className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                  />
                  {errors.phoneNo && <p className="text-rose-500 text-xs mt-1">{errors.phoneNo}</p>}
                </div>
                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">Mobile Hotline (Optional)</label>
                  <input
                    type="text"
                    name="mobileNo"
                    value={formData.mobileNo}
                    onChange={handleChange}
                    placeholder="+94 77 123 4567"
                    className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                  />
                </div>
              </div>

              {/* Registration No & Website */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">Business Registration No. (BRN)</label>
                  <input
                    type="text"
                    name="registrationNo"
                    value={formData.registrationNo}
                    onChange={handleChange}
                    placeholder="PV 12345"
                    className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                  />
                </div>
                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">Website URL</label>
                  <input
                    type="text"
                    name="website"
                    value={formData.website}
                    onChange={handleChange}
                    placeholder="https://www.acme.com"
                    className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                  />
                </div>
              </div>
            </div>
          )}

          {/* STEP 2: Location & Tax Details */}
          {currentStep === 2 && (
            <div className="space-y-5 animate-fadeIn">
              <div className="border-b border-slate-100 pb-3">
                <h3 className="text-base font-bold text-slate-850 flex items-center gap-2">
                  <FaMapMarkerAlt className="text-blue-600" /> Step 2: Addresses, Country & Tax Info
                </h3>
                <p className="text-xs text-slate-400 mt-0.5">Specify geographical region, currency formatting, and tax numbers</p>
              </div>

              {/* Registered Address */}
              <div>
                <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                  Registered Address <span className="text-rose-500">*</span>
                </label>
                <input
                  type="text"
                  name="registeredAddress"
                  value={formData.registeredAddress}
                  onChange={handleChange}
                  placeholder="Street name, City, State/Province"
                  className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                />
                {errors.registeredAddress && <p className="text-rose-500 text-xs mt-1">{errors.registeredAddress}</p>}
              </div>

              {/* Factory Address */}
              <div>
                <label className="block text-xs uppercase font-bold text-slate-600 mb-1">Factory / Operation Address (Optional)</label>
                <input
                  type="text"
                  name="factoryAddress"
                  value={formData.factoryAddress}
                  onChange={handleChange}
                  placeholder="Factory facility address if different from registered office"
                  className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                />
              </div>

              {/* Country & Currency Select */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                    Operating Country <span className="text-rose-500">*</span>
                  </label>
                  {loadingCountries ? (
                    <div className="px-4 py-2.5 border rounded-xl bg-slate-50 animate-pulse text-xs text-slate-400">
                      Loading countries list...
                    </div>
                  ) : (
                    <select
                      name="countryId"
                      value={formData.countryId}
                      onChange={handleCountryChange}
                      className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm bg-white transition"
                    >
                      <option value="">Select Country</option>
                      {countries.map((c) => (
                        <option key={c.id} value={c.id}>{c.name}</option>
                      ))}
                    </select>
                  )}
                  {errors.country && <p className="text-rose-500 text-xs mt-1">{errors.country}</p>}
                </div>

                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                    Primary Currency <span className="text-rose-500">*</span>
                  </label>
                  {loadingCurrencies ? (
                    <div className="px-4 py-2.5 border rounded-xl bg-slate-50 animate-pulse text-xs text-slate-400">
                      Loading currencies list...
                    </div>
                  ) : (
                    <select
                      name="currencyId"
                      value={formData.currencyId}
                      onChange={handleChange}
                      className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm bg-white transition"
                    >
                      <option value="">Select Currency</option>
                      {currencies.map((curr) => (
                        <option key={curr.id} value={curr.id}>{curr.name} ({curr.code})</option>
                      ))}
                    </select>
                  )}
                  {errors.currency && <p className="text-rose-500 text-xs mt-1">{errors.currency}</p>}
                </div>
              </div>

              {/* Tax Numbers */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">TIN Number</label>
                  <input
                    type="text"
                    name="tinNo"
                    value={formData.tinNo}
                    onChange={handleChange}
                    placeholder="e.g. 10029384"
                    className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                  />
                </div>

                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                    VAT Registered? <span className="text-rose-500">*</span>
                  </label>
                  <div className="flex gap-4 mt-2">
                    <label className="flex items-center gap-1.5 text-xs font-semibold cursor-pointer">
                      <input
                        type="radio"
                        name="vatRegistered"
                        value="yes"
                        checked={isVatRegistered}
                        onChange={handleVatRegistrationChange}
                        className="text-blue-600"
                      />
                      <span>Yes</span>
                    </label>
                    <label className="flex items-center gap-1.5 text-xs font-semibold cursor-pointer">
                      <input
                        type="radio"
                        name="vatRegistered"
                        value="no"
                        checked={!isVatRegistered}
                        onChange={handleVatRegistrationChange}
                        className="text-blue-600"
                      />
                      <span>No</span>
                    </label>
                  </div>
                </div>

                {isVatRegistered && (
                  <div>
                    <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                      VAT Number <span className="text-rose-500">*</span>
                    </label>
                    <input
                      type="text"
                      name="vatNo"
                      value={formData.vatNo}
                      onChange={handleChange}
                      placeholder="e.g. VAT987654"
                      className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                    />
                    {errors.vatNo && <p className="text-rose-500 text-xs mt-1">{errors.vatNo}</p>}
                  </div>
                )}
              </div>
            </div>
          )}

          {/* STEP 3: Admin Credentials & Password */}
          {currentStep === 3 && (
            <div className="space-y-5 animate-fadeIn">
              <div className="border-b border-slate-100 pb-3">
                <h3 className="text-base font-bold text-slate-850 flex items-center gap-2">
                  <FaUserLock className="text-blue-600" /> Step 3: Administrator Credentials
                </h3>
                <p className="text-xs text-slate-400 mt-0.5">Set up the master admin email and password for logging into Ginuma ERP</p>
              </div>

              {/* Admin Email */}
              <div>
                <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                  Master Admin Email Address <span className="text-rose-500">*</span>
                </label>
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="admin@yourcompany.com"
                  className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm transition"
                />
                {errors.email && <p className="text-rose-500 text-xs mt-1">{errors.email}</p>}
                <p className="text-[11px] text-slate-400 mt-1">Verification link will be sent to this email address.</p>
              </div>

              {/* Password Fields */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                    Password <span className="text-rose-500">*</span>
                  </label>
                  <div className="relative">
                    <input
                      type={showPassword ? "text" : "password"}
                      name="password"
                      value={formData.password}
                      onChange={handleChange}
                      placeholder="Min 8 characters"
                      className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm pr-10 transition"
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600"
                    >
                      {showPassword ? <FaEyeSlash /> : <FaEye />}
                    </button>
                  </div>
                  {errors.password && <p className="text-rose-500 text-xs mt-1">{errors.password}</p>}
                </div>

                <div>
                  <label className="block text-xs uppercase font-bold text-slate-600 mb-1">
                    Confirm Password <span className="text-rose-500">*</span>
                  </label>
                  <div className="relative">
                    <input
                      type={showConfirmPassword ? "text" : "password"}
                      name="confirmPassword"
                      value={formData.confirmPassword}
                      onChange={handleChange}
                      placeholder="Re-enter password"
                      className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none text-sm pr-10 transition"
                    />
                    <button
                      type="button"
                      onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                      className="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600"
                    >
                      {showConfirmPassword ? <FaEyeSlash /> : <FaEye />}
                    </button>
                  </div>
                  {errors.confirmPassword && <p className="text-rose-500 text-xs mt-1">{errors.confirmPassword}</p>}
                </div>
              </div>

              {/* Password Strength Meter */}
              {formData.password && (
                <div className="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
                  <div className="flex items-center justify-between text-xs">
                    <span className="font-bold text-slate-600 flex items-center gap-1">
                      <FaShieldAlt className="text-blue-600" /> Password Strength:
                    </span>
                    <span className={`font-black ${passwordStrength.text}`}>
                      {passwordStrength.label}
                    </span>
                  </div>
                  <div className="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div
                      className={`h-full transition-all duration-300 ${passwordStrength.color}`}
                      style={{ width: `${passwordStrength.score}%` }}
                    ></div>
                  </div>
                </div>
              )}

            </div>
          )}

          {/* Buttons Navigation */}
          <div className="flex items-center justify-between pt-6 border-t border-slate-100">
            {currentStep > 1 ? (
              <button
                type="button"
                onClick={handlePrev}
                className="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-2 cursor-pointer"
              >
                <FaArrowLeft /> Back
              </button>
            ) : (
              <div></div>
            )}

            {currentStep < 3 ? (
              <button
                type="button"
                onClick={handleNext}
                className="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-2 cursor-pointer"
              >
                Next Step <FaArrowRight />
              </button>
            ) : (
              <button
                type="submit"
                disabled={isSubmitting}
                className="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white rounded-xl font-bold text-xs shadow-lg shadow-emerald-600/25 transition flex items-center gap-2 cursor-pointer"
              >
                {isSubmitting ? (
                  <>
                    <FaSpinner className="animate-spin text-sm" /> Registering Company...
                  </>
                ) : (
                  <>
                    Complete Registration <FaCheckCircle />
                  </>
                )}
              </button>
            )}
          </div>

        </form>

        {/* Footer Link */}
        <div className="bg-slate-50 border-t border-slate-100 p-4 text-center text-xs text-slate-500">
          Already registered?{" "}
          <a href="/login" className="text-blue-600 font-bold hover:underline">
            Sign in to your account
          </a>
        </div>

      </div>
    </div>
  );
};

export default Register;
