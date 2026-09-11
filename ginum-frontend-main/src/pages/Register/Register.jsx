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
  FaCloudUploadAlt,
} from "react-icons/fa";
import axios from "axios";
import { apiUrl } from "../../utils/api";
import api from "../../utils/api";
import { useNavigate } from "react-router-dom";
import "./Register.css";

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

  const calculatePasswordStrength = (pass) => {
    if (!pass) return { score: 0, label: "", color: "transparent" };
    let score = 0;
    if (pass.length >= 8) score++;
    if (/[A-Z]/.test(pass)) score++;
    if (/[0-9]/.test(pass)) score++;
    if (/[^A-Za-z0-9]/.test(pass)) score++;

    if (score <= 1) return { score: 25, label: "Weak", color: "#f43f5e", text: "#e11d48" };
    if (score === 2 || score === 3) return { score: 65, label: "Medium", color: "#f59e0b", text: "#d97706" };
    return { score: 100, label: "Strong", color: "#10b981", text: "#059669" };
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
      <div className="reg-layout" style={{ justifyContent: 'center', alignItems: 'center' }}>
        <div className="reg-form-card" style={{ maxWidth: '480px', textAlign: 'center', margin: '2rem' }}>
          <div style={{ width: '80px', height: '80px', background: '#e0e7ff', color: '#4f46e5', borderRadius: '24px', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '2.5rem', margin: '0 auto 1.5rem', boxShadow: 'inset 0 2px 10px rgba(0,0,0,0.05)' }}>
            ✉️
          </div>
          <h2 style={{ fontSize: '1.75rem', fontWeight: 800, marginBottom: '0.5rem' }}>Check Your Email</h2>
          <p style={{ fontSize: '0.9rem', color: '#475569', lineHeight: 1.6, marginBottom: '1.5rem' }}>
            We have sent a verification link to <strong style={{ color: '#0f172a' }}>{formData.email}</strong>. Please check your inbox and verify your email to activate your company account.
          </p>
          <div style={{ background: '#f8fafc', border: '1px solid #e2e8f0', borderRadius: '12px', padding: '1rem', fontSize: '0.8rem', color: '#64748b', textAlign: 'left', marginBottom: '2rem' }}>
            <p style={{ fontWeight: 700, color: '#475569', marginBottom: '0.25rem' }}>💡 Tip for users:</p>
            <p>If you don't see the email within 2 minutes, check your Spam or Junk folder.</p>
          </div>
          <button onClick={() => navigate("/login")} className="reg-btn-primary" style={{ width: '100%' }}>
            Proceed to Sign In
          </button>
        </div>
      </div>
    );
  }

  const steps = [
    { number: 1, title: "Organization Profile", desc: "Identity and contact details", icon: FaBuilding },
    { number: 2, title: "Location & Tax", desc: "Regional formatting and tax config", icon: FaMapMarkerAlt },
    { number: 3, title: "Admin Access", desc: "Master user credentials", icon: FaUserLock },
  ];

  return (
    <div className="reg-layout">
      {/* LEFT SIDEBAR (STEPPER & BRAND) */}
      <div className="reg-sidebar">
        <img src="/ginum_logo.png" alt="Ginum" className="reg-logo" style={{ alignSelf: 'flex-start' }} />
        <h1 style={{ fontSize: '1.85rem', fontWeight: 800, lineHeight: 1.2, marginBottom: '0.75rem', letterSpacing: '-0.02em' }}>
          Create Your Enterprise Account
        </h1>
        <p style={{ fontSize: '0.875rem', color: '#64748b', lineHeight: 1.5 }}>
          Set up your organization profile and administrative access to get started with Ginum.
        </p>

        <div className="reg-steps">
          {steps.map((step) => {
            const isActive = currentStep === step.number;
            const isCompleted = currentStep > step.number;
            let statusClass = "";
            if (isActive) statusClass = "active";
            if (isCompleted) statusClass = "completed";

            return (
              <div key={step.number} className={`reg-step-item ${statusClass}`}>
                <div className="reg-step-icon">
                  {isCompleted ? <FaCheckCircle /> : <step.icon />}
                </div>
                <div className="reg-step-content">
                  <div className="reg-step-title">Step {step.number}: {step.title}</div>
                  <div className="reg-step-desc">{step.desc}</div>
                </div>
              </div>
            );
          })}
        </div>
      </div>

      {/* RIGHT MAIN AREA (FORM) */}
      <div className="reg-main">
        <div className="reg-content">
          
          {submitError && (
            <div style={{ padding: '1rem', background: '#fef2f2', border: '1px solid #fecdd3', borderRadius: '12px', color: '#e11d48', fontSize: '0.875rem', marginBottom: '2rem' }}>
              {submitError}
            </div>
          )}

          <form onSubmit={handleSubmit} className="reg-form-card">
            {/* STEP 1 */}
            {currentStep === 1 && (
              <div style={{ animation: 'slideUpFade 0.4s ease forwards' }}>
                <div style={{ borderBottom: '1px solid #e2e8f0', paddingBottom: '1.5rem', marginBottom: '2rem' }}>
                  <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: '#0f172a' }}>Organization Details</h3>
                  <p style={{ fontSize: '0.875rem', color: '#64748b', marginTop: '0.25rem' }}>Enter core identity and contact details for your business.</p>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 2.5fr', gap: '2rem', marginBottom: '1.5rem' }}>
                  {/* Logo Upload Dropzone */}
                  <div>
                    <label className="reg-label">Company Logo</label>
                    <div className="reg-dropzone">
                      {logoPreview ? (
                        <img src={logoPreview} alt="Preview" style={{ maxWidth: '100%', maxHeight: '80px', borderRadius: '8px', marginBottom: '0.5rem' }} />
                      ) : (
                        <FaCloudUploadAlt style={{ fontSize: '2.5rem', color: '#94a3b8', marginBottom: '0.75rem' }} />
                      )}
                      <span style={{ fontSize: '0.85rem', fontWeight: 600, color: '#475569' }}>
                        {logoPreview ? 'Change Logo' : 'Upload Logo'}
                      </span>
                      <span style={{ fontSize: '0.7rem', color: '#94a3b8', marginTop: '0.25rem' }}>PNG, JPG up to 5MB</span>
                      <input
                        type="file"
                        name="companyLogo"
                        accept="image/*"
                        onChange={handleChange}
                        style={{ position: 'absolute', inset: 0, opacity: 0, cursor: 'pointer' }}
                      />
                    </div>
                  </div>

                  {/* Name & Category */}
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '1.5rem' }}>
                    <div>
                      <label className="reg-label">Company Name <span style={{ color: '#ef4444' }}>*</span></label>
                      <input
                        type="text"
                        name="companyName"
                        value={formData.companyName}
                        onChange={handleChange}
                        placeholder="e.g. Acme Global Logistics Ltd."
                        className="reg-input"
                      />
                      {errors.companyName && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.companyName}</p>}
                    </div>

                    <div>
                      <label className="reg-label">Industry Category <span style={{ color: '#ef4444' }}>*</span></label>
                      <select name="companyCategory" value={formData.companyCategory} onChange={handleChange} className="reg-input" style={{ cursor: 'pointer' }}>
                        <option value="">Select Category</option>
                        {COMPANY_CATEGORIES.map((cat) => (
                          <option key={cat.value} value={cat.value}>{cat.label}</option>
                        ))}
                      </select>
                      {errors.companyCategory && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.companyCategory}</p>}
                    </div>
                  </div>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem', marginBottom: '1.5rem' }}>
                  <div>
                    <label className="reg-label">Primary Phone Number <span style={{ color: '#ef4444' }}>*</span></label>
                    <input type="text" name="phoneNo" value={formData.phoneNo} onChange={handleChange} placeholder="+1 234 567 8900" className="reg-input" />
                    {errors.phoneNo && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.phoneNo}</p>}
                  </div>
                  <div>
                    <label className="reg-label">Mobile Hotline (Optional)</label>
                    <input type="text" name="mobileNo" value={formData.mobileNo} onChange={handleChange} placeholder="+1 987 654 3210" className="reg-input" />
                  </div>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem' }}>
                  <div>
                    <label className="reg-label">Business Reg No. (BRN)</label>
                    <input type="text" name="registrationNo" value={formData.registrationNo} onChange={handleChange} placeholder="PV 12345" className="reg-input" />
                  </div>
                  <div>
                    <label className="reg-label">Website URL</label>
                    <input type="text" name="website" value={formData.website} onChange={handleChange} placeholder="https://www.acme.com" className="reg-input" />
                  </div>
                </div>
              </div>
            )}

            {/* STEP 2 */}
            {currentStep === 2 && (
              <div style={{ animation: 'slideUpFade 0.4s ease forwards' }}>
                <div style={{ borderBottom: '1px solid #e2e8f0', paddingBottom: '1.5rem', marginBottom: '2rem' }}>
                  <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: '#0f172a' }}>Location & Tax Details</h3>
                  <p style={{ fontSize: '0.875rem', color: '#64748b', marginTop: '0.25rem' }}>Specify geographical region, currency formatting, and tax numbers.</p>
                </div>

                <div style={{ marginBottom: '1.5rem' }}>
                  <label className="reg-label">Registered Address <span style={{ color: '#ef4444' }}>*</span></label>
                  <input type="text" name="registeredAddress" value={formData.registeredAddress} onChange={handleChange} placeholder="Street name, City, State/Province" className="reg-input" />
                  {errors.registeredAddress && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.registeredAddress}</p>}
                </div>

                <div style={{ marginBottom: '1.5rem' }}>
                  <label className="reg-label">Factory / Operation Address (Optional)</label>
                  <input type="text" name="factoryAddress" value={formData.factoryAddress} onChange={handleChange} placeholder="Factory facility address if different from registered office" className="reg-input" />
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem', marginBottom: '1.5rem' }}>
                  <div>
                    <label className="reg-label">Operating Country <span style={{ color: '#ef4444' }}>*</span></label>
                    {loadingCountries ? (
                      <div className="reg-input" style={{ display: 'flex', alignItems: 'center', color: '#94a3b8' }}>Loading...</div>
                    ) : (
                      <select name="countryId" value={formData.countryId} onChange={handleCountryChange} className="reg-input" style={{ cursor: 'pointer' }}>
                        <option value="">Select Country</option>
                        {countries.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                      </select>
                    )}
                    {errors.country && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.country}</p>}
                  </div>
                  <div>
                    <label className="reg-label">Primary Currency <span style={{ color: '#ef4444' }}>*</span></label>
                    {loadingCurrencies ? (
                      <div className="reg-input" style={{ display: 'flex', alignItems: 'center', color: '#94a3b8' }}>Loading...</div>
                    ) : (
                      <select name="currencyId" value={formData.currencyId} onChange={handleChange} className="reg-input" style={{ cursor: 'pointer' }}>
                        <option value="">Select Currency</option>
                        {currencies.map(curr => <option key={curr.id} value={curr.id}>{curr.name} ({curr.code})</option>)}
                      </select>
                    )}
                    {errors.currency && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.currency}</p>}
                  </div>
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '1.5rem', alignItems: 'end' }}>
                  <div>
                    <label className="reg-label">TIN Number</label>
                    <input type="text" name="tinNo" value={formData.tinNo} onChange={handleChange} placeholder="e.g. 10029384" className="reg-input" />
                  </div>
                  <div>
                    <label className="reg-label">VAT Registered? <span style={{ color: '#ef4444' }}>*</span></label>
                    <div style={{ display: 'flex', gap: '1.5rem', height: '52px', alignItems: 'center', padding: '0 1rem', border: '1px solid #e2e8f0', borderRadius: '12px', background: '#f8fafc' }}>
                      <label style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', cursor: 'pointer', fontSize: '0.9rem', fontWeight: 600, color: '#475569' }}>
                        <input type="radio" name="vatRegistered" value="yes" checked={isVatRegistered} onChange={handleVatRegistrationChange} style={{ accentColor: '#4f46e5', transform: 'scale(1.2)' }} />
                        Yes
                      </label>
                      <label style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', cursor: 'pointer', fontSize: '0.9rem', fontWeight: 600, color: '#475569' }}>
                        <input type="radio" name="vatRegistered" value="no" checked={!isVatRegistered} onChange={handleVatRegistrationChange} style={{ accentColor: '#4f46e5', transform: 'scale(1.2)' }} />
                        No
                      </label>
                    </div>
                  </div>
                  {isVatRegistered && (
                    <div style={{ animation: 'slideUpFade 0.2s ease forwards' }}>
                      <label className="reg-label">VAT Number <span style={{ color: '#ef4444' }}>*</span></label>
                      <input type="text" name="vatNo" value={formData.vatNo} onChange={handleChange} placeholder="e.g. VAT987654" className="reg-input" />
                      {errors.vatNo && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.vatNo}</p>}
                    </div>
                  )}
                </div>
              </div>
            )}

            {/* STEP 3 */}
            {currentStep === 3 && (
              <div style={{ animation: 'slideUpFade 0.4s ease forwards' }}>
                <div style={{ borderBottom: '1px solid #e2e8f0', paddingBottom: '1.5rem', marginBottom: '2rem' }}>
                  <h3 style={{ fontSize: '1.25rem', fontWeight: 800, color: '#0f172a' }}>Administrator Credentials</h3>
                  <p style={{ fontSize: '0.875rem', color: '#64748b', marginTop: '0.25rem' }}>Set up the master admin email and password for logging into Ginuma ERP.</p>
                </div>

                <div style={{ marginBottom: '1.5rem' }}>
                  <label className="reg-label">Master Admin Email Address <span style={{ color: '#ef4444' }}>*</span></label>
                  <input type="email" name="email" value={formData.email} onChange={handleChange} placeholder="admin@yourcompany.com" className="reg-input" />
                  <p style={{ fontSize: '0.75rem', color: '#94a3b8', marginTop: '0.5rem' }}>A verification link will be sent to this email address to activate your account.</p>
                  {errors.email && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.email}</p>}
                </div>

                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem', marginBottom: '1.5rem' }}>
                  <div>
                    <label className="reg-label">Password <span style={{ color: '#ef4444' }}>*</span></label>
                    <div style={{ position: 'relative' }}>
                      <input type={showPassword ? "text" : "password"} name="password" value={formData.password} onChange={handleChange} placeholder="Min 8 characters" className="reg-input" style={{ paddingRight: '2.5rem' }} />
                      <button type="button" onClick={() => setShowPassword(!showPassword)} style={{ position: 'absolute', right: '1rem', top: '50%', transform: 'translateY(-50%)', background: 'none', border: 'none', color: '#94a3b8', cursor: 'pointer', padding: 0 }}>
                        {showPassword ? <FaEyeSlash /> : <FaEye />}
                      </button>
                    </div>
                    {errors.password && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.password}</p>}
                  </div>
                  <div>
                    <label className="reg-label">Confirm Password <span style={{ color: '#ef4444' }}>*</span></label>
                    <div style={{ position: 'relative' }}>
                      <input type={showConfirmPassword ? "text" : "password"} name="confirmPassword" value={formData.confirmPassword} onChange={handleChange} placeholder="Re-enter password" className="reg-input" style={{ paddingRight: '2.5rem' }} />
                      <button type="button" onClick={() => setShowConfirmPassword(!showConfirmPassword)} style={{ position: 'absolute', right: '1rem', top: '50%', transform: 'translateY(-50%)', background: 'none', border: 'none', color: '#94a3b8', cursor: 'pointer', padding: 0 }}>
                        {showConfirmPassword ? <FaEyeSlash /> : <FaEye />}
                      </button>
                    </div>
                    {errors.confirmPassword && <p style={{ color: '#ef4444', fontSize: '0.75rem', marginTop: '0.25rem' }}>{errors.confirmPassword}</p>}
                  </div>
                </div>

                {formData.password && (
                  <div style={{ background: '#f8fafc', border: '1px solid #e2e8f0', borderRadius: '12px', padding: '1rem' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.75rem' }}>
                      <span style={{ fontSize: '0.75rem', fontWeight: 700, color: '#475569', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                        <FaShieldAlt style={{ color: '#4f46e5' }} /> Password Strength
                      </span>
                      <span style={{ fontSize: '0.75rem', fontWeight: 800, color: passwordStrength.text }}>{passwordStrength.label}</span>
                    </div>
                    <div style={{ height: '6px', background: '#e2e8f0', borderRadius: '999px', overflow: 'hidden' }}>
                      <div style={{ height: '100%', background: passwordStrength.color, width: `${passwordStrength.score}%`, transition: 'all 0.3s ease' }}></div>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* Navigation Buttons */}
            <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: '2.5rem', paddingTop: '2rem', borderTop: '1px solid #e2e8f0' }}>
              {currentStep > 1 ? (
                <button type="button" onClick={handlePrev} className="reg-btn-secondary">
                  <FaArrowLeft /> Back
                </button>
              ) : <div></div>}

              {currentStep < 3 ? (
                <button type="button" onClick={handleNext} className="reg-btn-primary">
                  Next Step <FaArrowRight />
                </button>
              ) : (
                <button type="submit" disabled={isSubmitting} className="reg-btn-primary success">
                  {isSubmitting ? <><FaSpinner className="fa-spin" /> Registering...</> : <>Complete Registration <FaCheckCircle /></>}
                </button>
              )}
            </div>

          </form>

          {/* Footer Link */}
          <div style={{ textAlign: 'center', marginTop: '2rem', fontSize: '0.875rem', color: '#64748b' }}>
            Already registered? <a href="/login" style={{ color: '#4f46e5', fontWeight: 600, textDecoration: 'none' }}>Sign in to your account</a>
          </div>

        </div>
      </div>
    </div>
  );
};

export default Register;
