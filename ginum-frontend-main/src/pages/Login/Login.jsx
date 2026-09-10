import React, { useState } from "react";
import usePageTitle from "../../hooks/usePageTitle";
import { FaEye, FaEyeSlash } from "react-icons/fa";
import axios from "axios";
import { apiUrl } from "../../utils/api";
import Alert from "../../components/Alert/Alert";
import "./Login.css";

const Login = () => {
  usePageTitle("Login");

  const [showPassword, setShowPassword] = useState(false);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [mfaCode, setMfaCode] = useState("");
  const [mfaRequired, setMfaRequired] = useState(false);
  const [otpRequired, setOtpRequired] = useState(false);
  const [loginOtp, setLoginOtp] = useState("");
  const [error, setError] = useState("");
  const [emailUnverified, setEmailUnverified] = useState(false);
  const [resendStatus, setResendStatus] = useState("");
  const [isResending, setIsResending] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const res = await axios.post(`${apiUrl}/api/auth/login`, {
        email,
        password,
        mfaCode: mfaRequired ? mfaCode : null,
        loginOtp: otpRequired ? loginOtp : null,
      }, { withCredentials: true });

      const response = res.data;

      if (response.otpRequired) { setOtpRequired(true); setError(""); return; }
      if (response.mfaRequired) { setMfaRequired(true); setError(""); return; }
      if (!response.token || !response.role) throw new Error("Missing token or role in response");

      if (response.permissions) {
        sessionStorage.setItem("permissions", JSON.stringify(response.permissions));
      } else {
        sessionStorage.setItem("permissions", JSON.stringify([]));
      }

      if (response.role === "ROLE_SUPER_ADMIN") {
        sessionStorage.setItem("auth_token", response.token);
        sessionStorage.setItem("role", response.role);
        sessionStorage.setItem("userId", response.userId ?? "");
        Alert.success("Welcome, Super Admin!");
        setTimeout(() => { window.location.href = "/super-admin/dashboard"; }, 1500);
      } else if (response.companyId || response.role) {
        sessionStorage.setItem("auth_token", response.token);
        sessionStorage.setItem("role", response.role);
        sessionStorage.setItem("companyId", response.companyId ?? "");
        sessionStorage.setItem("userId", response.userId ?? "");
        Alert.success("Welcome!");
        setTimeout(() => { window.location.href = "/dashboard"; }, 1500);
      } else {
        Alert.error("Unknown user role. Please contact support.");
      }
    } catch (err) {
      let errorMsg = "Invalid email or password. Please try again.";
      if (err.response?.data) {
        if (typeof err.response.data === "string") {
          if (err.response.data.includes("<html") || err.response.data.includes("502 Bad Gateway")) {
            errorMsg = "Backend server is initializing. Please try again in a few seconds.";
          } else {
            errorMsg = err.response.data;
          }
        } else if (err.response.data.error) {
          errorMsg = err.response.data.error;
        } else if (err.response.data.message) {
          errorMsg = err.response.data.message;
        }
      }
      setEmailUnverified(!!(err.response?.data?.emailUnverified));
      setError(errorMsg);
      Alert.error(errorMsg);
    }
  };

  const handleResendVerification = async () => {
    if (!email) { setResendStatus("Please enter your email address above first."); return; }
    setIsResending(true);
    setResendStatus("");
    try {
      const res = await axios.post(`${apiUrl}/api/auth/resend-verification`, { email });
      setResendStatus(res.data.message || "Verification link sent! Check your inbox.");
    } catch (err) {
      setResendStatus(err.response?.data?.error || "Failed to resend verification link.");
    } finally {
      setIsResending(false);
    }
  };

  const resetMfa = () => { setMfaRequired(false); setOtpRequired(false); setMfaCode(""); setLoginOtp(""); };

  return (
    <div className="login-bg">
      {/* Animated background blobs */}
      <div className="login-blob login-blob-1" />
      <div className="login-blob login-blob-2" />
      <div className="login-blob login-blob-3" />
      <div className="login-blob login-blob-4" />
      {/* Grid overlay */}
      <div className="login-grid" />

      {/* Main card */}
      <div className="login-card">

        {/* ── Left: Form panel ── */}
        <div className="login-form-panel">
          <img src="ginum_logo.png" alt="Ginum Logo" className="login-logo" />

          {!otpRequired && !mfaRequired && (
            <div>
              <h2 className="login-title">Welcome back</h2>
              <p className="login-subtitle">Please enter your details to sign in.</p>
            </div>
          )}

          {/* Error */}
          {error && (
            <div className="login-error">
              <svg style={{width:'1.1rem',height:'1.1rem',flexShrink:0,marginTop:'1px'}} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <span>{error}</span>
                {emailUnverified && (
                  <div style={{marginTop:'0.5rem'}}>
                    <button
                      type="button"
                      onClick={handleResendVerification}
                      disabled={isResending}
                      style={{background:'rgba(239,68,68,0.2)',border:'1px solid rgba(239,68,68,0.4)',borderRadius:'0.5rem',padding:'0.25rem 0.75rem',color:'#fca5a5',fontSize:'0.8rem',fontWeight:600,cursor:'pointer'}}
                    >
                      {isResending ? "Sending..." : "Resend Verification Email"}
                    </button>
                    {resendStatus && <p style={{marginTop:'0.5rem',fontSize:'0.8rem'}}>{resendStatus}</p>}
                  </div>
                )}
              </div>
            </div>
          )}

          <form onSubmit={handleSubmit}>
            {/* ── OTP step ── */}
            {otpRequired ? (
              <div>
                <div className="login-otp-box">
                  <div className="login-otp-icon">🔐</div>
                  <h3 style={{color:'#c7d2fe',fontWeight:700,marginBottom:'0.5rem'}}>Security Verification</h3>
                  <p style={{color:'rgba(255,255,255,0.6)',fontSize:'0.85rem',lineHeight:'1.5'}}>
                    A 6-digit OTP was sent to <strong style={{color:'#a5b4fc'}}>{email}</strong>
                  </p>
                </div>
                <label className="login-label">6-Digit OTP Code</label>
                <input
                  id="loginOtp"
                  type="text"
                  required
                  value={loginOtp}
                  onChange={(e) => setLoginOtp(e.target.value.replace(/\D/g, ""))}
                  className="login-otp-input"
                  placeholder="••••••"
                  maxLength={6}
                  style={{marginBottom:'1rem'}}
                />
              </div>
            ) : !mfaRequired ? (
              /* ── Main login form ── */
              <div>
                <div style={{marginBottom:'1.25rem'}}>
                  <label htmlFor="email" className="login-label">Email</label>
                  <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="login-input"
                    placeholder="name@company.com"
                  />
                </div>

                <div>
                  <label htmlFor="password" className="login-label">Password</label>
                  <div className="login-input-wrap">
                    <input
                      id="password"
                      name="password"
                      type={showPassword ? "text" : "password"}
                      required
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      className="login-input"
                      placeholder="Enter your password"
                      style={{paddingRight:'3rem'}}
                    />
                    <button type="button" onClick={() => setShowPassword(!showPassword)} className="login-eye-btn">
                      {showPassword ? <FaEyeSlash size={16} /> : <FaEye size={16} />}
                    </button>
                  </div>
                </div>

                <div className="login-options-row">
                  <label className="login-remember">
                    <input type="checkbox" style={{accentColor:'#6366f1'}} />
                    Remember me
                  </label>
                  <a href="#" className="login-forgot">Forgot password?</a>
                </div>
              </div>
            ) : (
              /* ── MFA step ── */
              <div>
                <div className="login-otp-box" style={{marginBottom:'1.5rem'}}>
                  <h3 style={{color:'#c7d2fe',fontWeight:700,marginBottom:'0.5rem'}}>Two-Factor Authentication</h3>
                  <p style={{color:'rgba(255,255,255,0.6)',fontSize:'0.85rem',lineHeight:'1.5'}}>
                    Open Google Authenticator and enter the 6-digit code.
                  </p>
                </div>
                <label className="login-label">Authenticator Code</label>
                <input
                  id="mfaCode"
                  type="text"
                  required
                  value={mfaCode}
                  onChange={(e) => setMfaCode(e.target.value)}
                  className="login-otp-input"
                  placeholder="••••••"
                  maxLength={6}
                  style={{marginBottom:'1rem'}}
                />
              </div>
            )}

            {/* Submit */}
            <button type="submit" className="login-btn">
              {otpRequired ? "Verify OTP & Log In" : mfaRequired ? "Verify Code" : "Sign in"}
              <svg style={{width:'1rem',height:'1rem'}} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </button>

            {(mfaRequired || otpRequired) && (
              <button type="button" onClick={resetMfa} className="login-cancel-btn">Cancel</button>
            )}
          </form>

          <p className="login-signup-text">
            Don't have an account?{" "}
            <a href="/register" className="login-signup-link">Sign up now</a>
          </p>
        </div>

        {/* ── Right: Branding panel ── */}
        <div className="login-brand-panel">
          <div className="login-brand-content">
            <div className="login-brand-img-wrap">
              <img src="ginum-login.svg" alt="Workspace" className="login-brand-img" />
            </div>
            <h3 className="login-brand-title">Streamline your workflow</h3>
            <p className="login-brand-subtitle">
              Manage your enterprise seamlessly with Ginum's intelligent dashboard and automated insights.
            </p>
          </div>
        </div>

      </div>
    </div>
  );
};

export default Login;
