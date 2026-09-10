import React, { useState } from "react";
import usePageTitle from "../../hooks/usePageTitle"; // Custom hook to set the page title
import { FaEye, FaEyeSlash } from "react-icons/fa"; // Icons for showing/hiding password
import api from "../../utils/api"; // Adjust the import path
import axios from "axios";
import { apiUrl } from "../../utils/api";
import Alert from "../../components/Alert/Alert"; 

const Login = () => {
  usePageTitle("Login"); // Set the page title to "Login" using the custom hook

  const [showPassword, setShowPassword] = useState(false);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [mfaCode, setMfaCode] = useState("");
  const [mfaRequired, setMfaRequired] = useState(false);
  const [otpRequired, setOtpRequired] = useState(false);
  const [loginOtp, setLoginOtp] = useState("");
  const [error, setError] = useState("");

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
      console.log("Login response:", response);

      if (response.otpRequired) {
        setOtpRequired(true);
        setError("");
        return;
      }

      if (response.mfaRequired) {
        setMfaRequired(true);
        setError("");
        return;
      }

      if (!response.token || !response.role) {
        throw new Error("Missing token or role in response");
      }

      // Save authentication details
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
        setTimeout(() => {
          window.location.href = "/super-admin/dashboard";
        }, 1500);
      } else if (response.companyId || response.role) {
        sessionStorage.setItem("auth_token", response.token);
        sessionStorage.setItem("role", response.role);
        sessionStorage.setItem("companyId", response.companyId ?? "");
        sessionStorage.setItem("userId", response.userId ?? "");

        Alert.success("Welcome!");
        setTimeout(() => {
          window.location.href = "/dashboard";
        }, 1500);
      } else {
        Alert.error("Unknown user role. Please contact support.");
      }
    } catch (err) {
      let errorMsg = "Invalid email or password. Please try again.";
      if (err.response && err.response.data) {
        if (typeof err.response.data === "string") {
          if (err.response.data.includes("<html") || err.response.data.includes("502 Bad Gateway")) {
            errorMsg = "Backend server is currently initializing or restarting. Please try again in a few seconds.";
          } else {
            errorMsg = err.response.data;
          }
        } else if (err.response.data.error) {
          errorMsg = err.response.data.error;
        } else if (err.response.data.message) {
          errorMsg = err.response.data.message;
        }
      }
      if (err.response && err.response.data && err.response.data.emailUnverified) {
        setEmailUnverified(true);
      } else {
        setEmailUnverified(false);
      }
      setError(errorMsg);
      Alert.error(errorMsg);
      console.error("Login error:", err);
    }
  };

  const [emailUnverified, setEmailUnverified] = useState(false);
  const [resendStatus, setResendStatus] = useState("");
  const [isResending, setIsResending] = useState(false);

  const handleResendVerification = async () => {
    if (!email) {
      setResendStatus("Please enter your email address above first.");
      return;
    }
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

  return (
    <div className="flex min-h-screen items-center justify-center bg-[#f8fafc] p-4 sm:p-8 font-sans selection:bg-blue-100 selection:text-blue-900">
      {/* Main Container */}
      <div className="flex w-full max-w-[1040px] overflow-hidden rounded-[2rem] bg-white shadow-[0_20px_50px_rgba(8,_112,_184,_0.07)] md:min-h-[600px]">
        
        {/* Left Section (Form) */}
        <div className="flex w-full flex-col justify-center p-8 sm:p-12 md:w-1/2 lg:p-16 relative">
          
          {/* Logo */}
          <div className="mb-10">
            <img
              src="ginum_logo.png"
              alt="Ginum Logo"
              className="h-10 w-auto object-contain"
            />
          </div>

          <div className="mb-8">
            <h2 className="text-3xl font-bold tracking-tight text-gray-900">
              Welcome back
            </h2>
            <p className="mt-2 text-sm text-gray-500 font-medium">
              Please enter your details to sign in.
            </p>
          </div>

          {/* Error Message */}
          {error && (
            <div className="mb-6 rounded-2xl bg-red-50/80 p-4 border border-red-100 text-sm text-red-600 animate-in fade-in slide-in-from-top-2 duration-300">
              <div className="flex items-start">
                <svg className="h-5 w-5 text-red-500 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p className="font-medium">{error}</p>
                  {emailUnverified && (
                    <div className="mt-3">
                      <button
                        type="button"
                        onClick={handleResendVerification}
                        disabled={isResending}
                        className="inline-flex items-center justify-center rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-200 disabled:opacity-50"
                      >
                        {isResending ? "Sending..." : "Resend Verification Email"}
                      </button>
                      {resendStatus && (
                        <p className="mt-2 text-xs font-medium text-red-700">{resendStatus}</p>
                      )}
                    </div>
                  )}
                </div>
              </div>
            </div>
          )}

          {/* Form */}
          <form className="space-y-5" onSubmit={handleSubmit}>
            {otpRequired ? (
              <div className="space-y-6 animate-in fade-in slide-in-from-right-4 duration-500">
                <div className="rounded-2xl bg-blue-50/50 border border-blue-100 p-6 text-center">
                  <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 mb-4">
                    <span className="text-xl">🔐</span>
                  </div>
                  <h3 className="text-base font-semibold text-blue-900">Security Verification</h3>
                  <p className="mt-2 text-sm text-blue-700/80 leading-relaxed">
                    An OTP code has been sent to <span className="font-medium text-blue-900">{email}</span>.
                  </p>
                </div>
                <div>
                  <label htmlFor="loginOtp" className="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 ml-1">
                    6-Digit OTP Code
                  </label>
                  <input
                    id="loginOtp"
                    name="loginOtp"
                    type="text"
                    required
                    value={loginOtp}
                    onChange={(e) => setLoginOtp(e.target.value.replace(/\D/g, ""))}
                    className="block w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3.5 text-center font-mono text-2xl tracking-[0.3em] text-gray-900 transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 placeholder:text-gray-300"
                    placeholder="••••••"
                    maxLength={6}
                  />
                </div>
              </div>
            ) : !mfaRequired ? (
              <div className="space-y-5 animate-in fade-in duration-500">
                <div>
                  <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1.5 ml-0.5">
                    Email
                  </label>
                  <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="block w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-gray-900 transition-all placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 sm:text-sm"
                    placeholder="name@company.com"
                  />
                </div>

                <div>
                  <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-1.5 ml-0.5">
                    Password
                  </label>
                  <div className="relative">
                    <input
                      id="password"
                      name="password"
                      type={showPassword ? "text" : "password"}
                      required
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      className="block w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 pr-12 text-gray-900 transition-all placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 sm:text-sm"
                      placeholder="Enter your password"
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none"
                    >
                      {showPassword ? <FaEyeSlash className="h-4 w-4" /> : <FaEye className="h-4 w-4" />}
                    </button>
                  </div>
                </div>

                <div className="flex items-center justify-between pt-1">
                  <div className="flex items-center">
                    <input
                      id="remember-me"
                      name="remember-me"
                      type="checkbox"
                      className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 transition-colors cursor-pointer"
                    />
                    <label htmlFor="remember-me" className="ml-2.5 block text-sm text-gray-600 cursor-pointer select-none">
                      Remember me
                    </label>
                  </div>
                  <a href="#" className="text-sm font-semibold text-blue-600 hover:text-blue-500 transition-colors">
                    Forgot password?
                  </a>
                </div>
              </div>
            ) : (
              <div className="space-y-6 animate-in fade-in slide-in-from-right-4 duration-500">
                <div className="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
                  <h3 className="text-base font-semibold text-slate-900">Two-Factor Authentication</h3>
                  <p className="mt-2 text-sm text-slate-500 leading-relaxed">
                    Open Google Authenticator and enter the 6-digit code to continue.
                  </p>
                </div>
                <div>
                  <label htmlFor="mfaCode" className="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 ml-1">
                    Authenticator Code
                  </label>
                  <input
                    id="mfaCode"
                    name="mfaCode"
                    type="text"
                    required
                    value={mfaCode}
                    onChange={(e) => setMfaCode(e.target.value)}
                    className="block w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3.5 text-center font-mono text-2xl tracking-[0.3em] text-gray-900 transition-all focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-500/10 placeholder:text-gray-300"
                    placeholder="••••••"
                    maxLength={6}
                  />
                </div>
              </div>
            )}

            <div className="pt-2">
              <button
                type="submit"
                className="group relative flex w-full justify-center items-center rounded-xl bg-gray-900 px-4 py-3.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-gray-800 hover:shadow-lg hover:shadow-gray-900/20 focus:outline-none focus:ring-4 focus:ring-gray-900/10 active:scale-[0.98]"
              >
                {otpRequired ? "Verify OTP & Log In" : mfaRequired ? "Verify Code" : "Sign in"}
                <svg className="ml-2 h-4 w-4 opacity-70 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </button>
              
              {(mfaRequired || otpRequired) && (
                <button
                  type="button"
                  onClick={() => {
                    setMfaRequired(false);
                    setOtpRequired(false);
                    setMfaCode("");
                    setLoginOtp("");
                  }}
                  className="mt-4 flex w-full justify-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition-all hover:bg-gray-50 hover:text-gray-900 focus:outline-none"
                >
                  Cancel
                </button>
              )}
            </div>
          </form>

          {/* Sign Up Link */}
          <p className="mt-8 text-center text-sm text-gray-500">
            Don't have an account?{" "}
            <a href="/register" className="font-semibold text-blue-600 hover:text-blue-500 transition-colors">
              Sign up now
            </a>
          </p>
        </div>

        {/* Right Section (Image/Branding) */}
        <div className="hidden md:flex md:w-1/2 relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-900 overflow-hidden items-center justify-center p-12">
          {/* Decorative circles */}
          <div className="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10 blur-3xl"></div>
          <div className="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-blue-400/20 blur-3xl"></div>
          
          <div className="relative z-10 w-full max-w-md flex flex-col items-center">
            <div className="w-full aspect-square relative flex items-center justify-center mb-8">
              {/* Glassmorphic backdrop for the image to make it pop */}
              <div className="absolute inset-0 bg-white/5 backdrop-blur-sm rounded-full transform -rotate-6 scale-105 border border-white/10"></div>
              <img
                src="ginum-login.svg"
                alt="Workspace"
                className="relative z-10 w-4/5 h-4/5 object-contain drop-shadow-2xl filter brightness-110"
              />
            </div>
            <div className="text-center text-white space-y-4">
              <h3 className="text-2xl font-bold tracking-tight">Streamline your workflow</h3>
              <p className="text-blue-100/80 text-sm leading-relaxed max-w-sm mx-auto">
                Manage your enterprise seamlessly with Ginum's intelligent dashboard and automated insights.
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>
  );
};

export default Login;
