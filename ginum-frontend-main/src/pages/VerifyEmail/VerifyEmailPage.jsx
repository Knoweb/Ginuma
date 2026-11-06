import React, { useEffect, useState } from "react";
import { useSearchParams, Link } from "react-router-dom";
import axios from "axios";
import { apiUrl } from "../../utils/api";
import { FaCheckCircle, FaExclamationTriangle, FaEnvelope, FaSpinner, FaArrowRight } from "react-icons/fa";
import BrandLogo from "../../components/common/BrandLogo";

const VerifyEmailPage = () => {
  const [searchParams] = useSearchParams();
  const token = searchParams.get("token");

  const [status, setStatus] = useState("verifying"); // 'verifying', 'success', 'error'
  const [message, setMessage] = useState("");
  const [resending, setResending] = useState(false);
  const [resendEmail, setResendEmail] = useState("");
  const [resendMessage, setResendMessage] = useState("");

  useEffect(() => {
    if (!token) {
      setStatus("error");
      setMessage("No verification token found in URL. Please check the link from your email.");
      return;
    }

    const verifyToken = async () => {
      try {
        const response = await axios.get(`${apiUrl}/api/auth/verify-email?token=${token}`);
        setStatus("success");
        setMessage(response.data.message || "Your email address has been successfully verified!");
      } catch (err) {
        setStatus("error");
        setMessage(
          err.response?.data?.error || "Invalid or expired verification link. Please request a new verification link."
        );
      }
    };

    verifyToken();
  }, [token]);

  const handleResend = async (e) => {
    e.preventDefault();
    if (!resendEmail) return;

    setResending(true);
    setResendMessage("");
    try {
      const response = await axios.post(`${apiUrl}/api/auth/resend-verification`, {
        email: resendEmail,
      });
      setResendMessage(response.data.message || "Verification email sent!");
    } catch (err) {
      setResendMessage(err.response?.data?.error || "Failed to resend verification email. Please check the email address.");
    } finally {
      setResending(false);
    }
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-[#0c1a2e] via-[#1A335A] to-[#0c1a2e] p-4 text-white">
      <div className="w-full max-w-lg rounded-2xl bg-white/10 p-8 backdrop-blur-xl border border-white/20 shadow-2xl transition-all duration-300">
        
        {/* Logo */}
        <div className="flex justify-center mb-6">
          <BrandLogo variant="white" size="lg" alt="GINUM" />
        </div>

        {/* Verifying State */}
        {status === "verifying" && (
          <div className="text-center py-8 space-y-4">
            <FaSpinner className="animate-spin text-5xl text-blue-400 mx-auto" />
            <h2 className="text-2xl font-bold">Verifying your email...</h2>
            <p className="text-slate-300 text-sm">Please wait while we confirm your email address.</p>
          </div>
        )}

        {/* Success State */}
        {status === "success" && (
          <div className="text-center py-6 space-y-6">
            <div className="w-20 h-20 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto border border-emerald-500/40 animate-bounce">
              <FaCheckCircle className="text-5xl" />
            </div>

            <div className="space-y-2">
              <h2 className="text-2xl font-bold text-emerald-300">Email Verified!</h2>
              <p className="text-slate-200 text-sm">{message}</p>
            </div>

            <div className="pt-4">
              <Link
                to="/login"
                className="inline-flex items-center justify-center gap-2 w-full py-3 px-6 bg-[#90C8EC] hover:bg-[#a8d7f7] text-[#1A335A] font-bold rounded-xl shadow-lg hover:shadow-cyan-500/20 transition-all duration-200"
              >
                Go to Sign In <FaArrowRight />
              </Link>
            </div>
          </div>
        )}

        {/* Error State */}
        {status === "error" && (
          <div className="text-center py-6 space-y-6">
            <div className="w-20 h-20 bg-rose-500/20 text-rose-400 rounded-full flex items-center justify-center mx-auto border border-rose-500/40">
              <FaExclamationTriangle className="text-5xl" />
            </div>

            <div className="space-y-2">
              <h2 className="text-2xl font-bold text-rose-300">Verification Failed</h2>
              <p className="text-slate-300 text-sm">{message}</p>
            </div>

            {/* Resend Form */}
            <div className="bg-white/5 rounded-xl p-5 border border-white/10 text-left space-y-3">
              <h3 className="text-sm font-semibold text-slate-200 flex items-center gap-2">
                <FaEnvelope className="text-[#90C8EC]" /> Resend Verification Email
              </h3>
              
              {resendMessage && (
                <div className="p-3 text-xs rounded-lg bg-blue-500/20 border border-blue-500/40 text-blue-200">
                  {resendMessage}
                </div>
              )}

              <form onSubmit={handleResend} className="space-y-3">
                <input
                  type="email"
                  required
                  placeholder="Enter your registered email"
                  value={resendEmail}
                  onChange={(e) => setResendEmail(e.target.value)}
                  className="w-full px-4 py-2.5 rounded-lg bg-slate-950/60 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:border-[#90C8EC] text-sm"
                />
                <button
                  type="submit"
                  disabled={resending}
                  className="w-full py-2.5 px-4 bg-[#90C8EC] hover:bg-[#a8d7f7] disabled:opacity-50 text-[#1A335A] text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
                >
                  {resending ? <FaSpinner className="animate-spin" /> : "Send Link"}
                </button>
              </form>
            </div>

            <div className="pt-2">
              <Link to="/login" className="text-sm text-[#90C8EC] hover:underline">
                Back to Sign In
              </Link>
            </div>
          </div>
        )}

      </div>
    </div>
  );
};

export default VerifyEmailPage;
