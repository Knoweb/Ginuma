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
      if (err.response && err.response.data && err.response.data.error) {
        errorMsg = err.response.data.error;
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
    <div className="flex min-h-screen items-center justify-center bg-gray-100 p-4">
      {/* Main container for the login form and image */}
      <div className="flex w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-lg md:flex-row flex-col">
        {/* Left Section (Form) */}
        <div className="w-full p-6 md:w-1/2 md:p-10">
          {/* Ginum logo */}
          <div className="flex justify-center mb-6">
            <img
              src="ginum_logo.png"
              alt="Ginum Logo"
              className="h-16 w-76" // Adjust the size as needed
            />
          </div>

          {/* Login heading and sign-up link */}
          <div className="text-center">
            <h2 className="text-2xl font-bold text-gray-900 mb-2">
              Sign in to your account
            </h2>
            <p className="mt-2 text-sm text-gray-500">
              Don't have an account?{" "}
              <a href="/register" className="text-blue-600 hover:underline">
                Sign up now
              </a>
            </p>
          </div>

          {/* Error message display */}
          {error && (
            <div className="mt-4 text-center text-red-500 text-sm bg-red-50 p-3 rounded-lg border border-red-200">
              <p>{error}</p>
              {emailUnverified && (
                <div className="mt-2 text-xs">
                  <button
                    type="button"
                    onClick={handleResendVerification}
                    disabled={isResending}
                    className="font-semibold text-blue-600 hover:underline disabled:opacity-50"
                  >
                    {isResending ? "Sending link..." : "Resend Verification Email"}
                  </button>
                  {resendStatus && (
                    <p className="mt-1 text-slate-700 font-medium">{resendStatus}</p>
                  )}
                </div>
              )}
            </div>
          )}

          {/* Login Form */}
          <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
            {otpRequired ? (
              <div className="space-y-4">
                <div className="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                  <span className="text-2xl">🔐</span>
                  <h3 className="text-sm font-bold text-blue-900 mt-1">Role Security Verification</h3>
                  <p className="text-xs text-blue-700 mt-1">
                    An OTP code has been sent to <strong>{email}</strong>. Enter the 6-digit code below to log in.
                  </p>
                </div>
                <div>
                  <label htmlFor="loginOtp" className="block text-sm font-medium text-gray-700 text-center">
                    6-Digit OTP Code
                  </label>
                  <input
                    id="loginOtp"
                    name="loginOtp"
                    type="text"
                    required
                    value={loginOtp}
                    onChange={(e) => setLoginOtp(e.target.value.replace(/\D/g, ""))}
                    className="mt-2 block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-xl text-center tracking-[0.4em] font-mono"
                    placeholder="000000"
                    maxLength={6}
                  />
                </div>
              </div>
            ) : !mfaRequired ? (
              <>
                <div className="space-y-4">
                  {/* Email Input Field */}
                  <div>
                    <label
                      htmlFor="email"
                      className="block text-sm font-medium text-gray-700"
                    >
                      Email address
                    </label>
                    <input
                      id="email"
                      name="email"
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm"
                      placeholder="Enter your email"
                    />
                  </div>

                  {/* Password Input Field */}
                  <div>
                    <label
                      htmlFor="password"
                      className="block text-sm font-medium text-gray-700"
                    >
                      Password
                    </label>
                    <div className="relative mt-1">
                      <input
                        id="password"
                        name="password"
                        type={showPassword ? "text" : "password"}
                        required
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        className="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm pr-10"
                        placeholder="Enter your password"
                      />
                      {/* Show/Hide password toggle button */}
                      <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
                      >
                        {showPassword ? <FaEyeSlash /> : <FaEye />}
                      </button>
                    </div>
                  </div>
                </div>

                {/* Remember me and Forgot password options */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center">
                    <input
                      id="remember-me"
                      name="remember-me"
                      type="checkbox"
                      className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label
                      htmlFor="remember-me"
                      className="ml-2 block text-sm text-gray-900"
                    >
                      Remember me
                    </label>
                  </div>
                  <div className="text-sm">
                    <a
                      href="#"
                      className="font-medium text-blue-600 hover:text-blue-500"
                    >
                      Forgot your password?
                    </a>
                  </div>
                </div>
              </>
            ) : (
              <div className="space-y-4">
                <div>
                  <label
                    htmlFor="mfaCode"
                    className="block text-sm font-medium text-gray-700"
                  >
                    Authenticator Code
                  </label>
                  <input
                    id="mfaCode"
                    name="mfaCode"
                    type="text"
                    required
                    value={mfaCode}
                    onChange={(e) => setMfaCode(e.target.value)}
                    className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm text-center tracking-widest text-lg"
                    placeholder="000000"
                    maxLength={6}
                  />
                  <p className="mt-2 text-xs text-gray-500 text-center">
                    Open Google Authenticator and enter the 6-digit code.
                  </p>
                </div>
              </div>
            )}

            {/* Submit Button */}
            <div>
              <button
                type="submit"
                className="group relative flex w-full justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                {otpRequired ? "Verify OTP & Log In" : mfaRequired ? "Verify Code" : "Sign in"}
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
                  className="mt-3 group relative flex w-full justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                >
                  Cancel
                </button>
              )}
            </div>
          </form>
        </div>

        {/* Right Section (Image) - Hidden on small screens */}
        <div className="hidden w-1/2 md:block">
          <img
            src="ginum-login.svg"
            alt="Workspace"
            className="h-full w-full object-cover"
          />
        </div>
      </div>
    </div>
  );
};

export default Login;
