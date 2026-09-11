import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import usePageTitle from "../../hooks/usePageTitle";
import { FaUser, FaLock, FaKey, FaArrowLeft } from "react-icons/fa";
import axios from "axios";
import { apiUrl } from "../../utils/api";
import Alert from "../../components/Alert/Alert";
import "./ForgotPassword.css";
// We reuse the showcase styles from Login.css for visual consistency
import "../Login/Login.css";

/* ────────────────────────────────────────────
   MOCK APP WINDOW (HERO) - Reused from Login
──────────────────────────────────────────── */
const Sparkline = ({ values, color, height = 40, width = 120 }) => {
  const min = Math.min(...values);
  const max = Math.max(...values);
  const range = max - min || 1;
  const padding = 4;
  
  const points = values.map((v, i) => {
    const x = (i / (values.length - 1)) * width;
    const y = height - padding - ((v - min) / range) * (height - 2 * padding);
    return `${x},${y}`;
  }).join(" ");

  return (
    <svg width={width} height={height} style={{ overflow: "visible" }}>
      <defs>
        <linearGradient id={`gradient-${color}`} x1="0" x2="0" y1="0" y2="1">
          <stop offset="0%" stopColor={color} stopOpacity="0.2" />
          <stop offset="100%" stopColor={color} stopOpacity="0" />
        </linearGradient>
      </defs>
      <polyline fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" points={points} />
      <polygon fill={`url(#gradient-${color})`} points={`0,${height} ${points} ${width},${height}`} />
    </svg>
  );
};

const MockAppWindow = () => (
  <div className="gl-mock-app">
    <div className="gl-mock-sidebar">
      <div className="gl-mock-logo">GINUM</div>
      <div className="gl-mock-nav">
        <div className="gl-mock-nav-item active"><span className="gl-skeleton-icon" /> Dashboard</div>
        <div className="gl-mock-nav-item"><span className="gl-skeleton-icon" /> Transactions</div>
        <div className="gl-mock-nav-item"><span className="gl-skeleton-icon" /> Invoices</div>
        <div className="gl-mock-nav-item"><span className="gl-skeleton-icon" /> Expenses</div>
        <div className="gl-mock-nav-item"><span className="gl-skeleton-icon" /> Reports</div>
        <div className="gl-mock-nav-item"><span className="gl-skeleton-icon" /> Accounts</div>
      </div>
    </div>
    <div className="gl-mock-main">
      <div className="gl-mock-topnav">
        <div className="gl-mock-search"><span className="gl-skeleton-icon" style={{width:"12px", height:"12px"}}/> Search...</div>
        <div className="gl-mock-profile">
          <span className="gl-skeleton-icon" style={{width:"14px", height:"14px", borderRadius:"50%"}} />
          <span className="gl-skeleton-icon" style={{width:"24px", height:"24px", borderRadius:"50%"}} />
        </div>
      </div>
      <div className="gl-mock-body">
        <div className="gl-mock-header">
          <h4>Overview</h4>
          <p>Here's what's happening with your business today.</p>
        </div>
        <div className="gl-mock-kpis">
          <div className="gl-mock-kpi">
            <span className="gl-mock-kpi-lbl">Total Revenue</span>
            <span className="gl-mock-kpi-val">$284,920</span>
            <span className="gl-badge gl-badge-up gl-mock-badge">↑ 12.8%</span>
          </div>
          <div className="gl-mock-kpi">
            <span className="gl-mock-kpi-lbl">Net Profit</span>
            <span className="gl-mock-kpi-val">$52,194</span>
            <span className="gl-badge gl-badge-up gl-mock-badge">↑ 21.4%</span>
          </div>
          <div className="gl-mock-kpi">
            <span className="gl-mock-kpi-lbl">Expenses</span>
            <span className="gl-mock-kpi-val">$96,430</span>
            <span className="gl-badge gl-badge-down gl-mock-badge">↓ 3.2%</span>
          </div>
        </div>
        <div className="gl-mock-chart-area">
           <Sparkline values={[140,165,155,185,170,200,190,215,205,230,220,248]} color="#6366f1" height={100} width={420} />
           <div style={{ position:"absolute", top:"20px", right:"20px", opacity:0.5 }}>
             <Sparkline values={[120,130,125,140,150,145,160,170,165,180,175,190]} color="#3b82f6" height={100} width={420} />
           </div>
           <div className="gl-mock-gridline" style={{bottom:"20%"}}></div>
           <div className="gl-mock-gridline" style={{bottom:"50%"}}></div>
           <div className="gl-mock-gridline" style={{bottom:"80%"}}></div>
        </div>
      </div>
    </div>
  </div>
);

const MiniDonutCard = () => (
  <div className="gl-fcard">
    <div className="gl-fcard-header">
      <span className="gl-card-label">Expenses</span>
    </div>
    <div className="gl-fcard-body" style={{ display: "flex", gap: "1rem", alignItems: "center" }}>
      <div className="gl-donut-wrap">
        <div className="gl-donut">
          <div className="gl-donut-inner"></div>
        </div>
      </div>
      <div className="gl-legend">
        <div className="gl-legend-item" style={{fontSize:"0.65rem"}}><span className="gl-dot" style={{background:"#6366f1", width:"6px", height:"6px"}} /> Payroll</div>
        <div className="gl-legend-item" style={{fontSize:"0.65rem"}}><span className="gl-dot" style={{background:"#10b981", width:"6px", height:"6px"}} /> Software</div>
        <div className="gl-legend-item" style={{fontSize:"0.65rem"}}><span className="gl-dot" style={{background:"#f59e0b", width:"6px", height:"6px"}} /> Marketing</div>
      </div>
    </div>
  </div>
);

const MiniToast = ({ icon, text, amount, color }) => (
  <div className="gl-chip" style={{ borderLeft: `3px solid ${color}` }}>
    <span className="gl-chip-icon">{icon}</span>
    <div className="gl-chip-content">
      <span className="gl-chip-lbl">{text}</span>
      <span className="gl-chip-val" style={{color: color}}>{amount}</span>
    </div>
  </div>
);

const FinanceShowcase = () => {
  const [slide, setSlide] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setSlide((s) => (s + 1) % 2);
    }, 5500);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="gl-showcase" aria-hidden="true">
      <div className={`gl-ambient-glow gl-glow-${slide}`} />

      <div className="gl-showcase-stage">
        <div className="gl-main-card">
          <MockAppWindow />
        </div>

        {/* Slide 0: Security & Access */}
        <div className={`gl-scene ${slide === 0 ? "active" : ""}`}>
          <div className="gl-float-card gl-float-tr delay-1">
            <div className="gl-fcard">
               <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#6366f1,#8b5cf6)" }} />
               <div className="gl-card-label">Security Protocol</div>
               <div className="gl-card-value-sm">Active</div>
               <span className="gl-badge gl-badge-up">Protected</span>
            </div>
          </div>
          <div className="gl-float-card gl-float-bl delay-2" style={{width: "220px"}}>
            <MiniDonutCard />
          </div>
          <div className="gl-float-widget gl-float-br delay-3">
            <MiniToast icon="🔒" text="Account Status" amount="Secured" color="#10b981" />
          </div>
        </div>

        {/* Slide 1: System Check */}
        <div className={`gl-scene ${slide === 1 ? "active" : ""}`}>
          <div className="gl-float-widget gl-float-tc delay-1">
            <MiniToast icon="🛡️" text="Verification" amount="Required" color="#f59e0b" />
          </div>
          <div className="gl-float-widget gl-float-bl delay-2">
            <MiniToast icon="✉️" text="Recovery Email" amount="Ready" color="#3b82f6" />
          </div>
        </div>
      </div>
      <div className="gl-showcase-bottom">
        <div className="gl-showcase-indicators">
          {[0, 1].map(i => (
            <button key={i} className={`gl-dot-ind ${slide === i ? "active" : ""}`} onClick={() => setSlide(i)} aria-label={`Go to slide ${i + 1}`} />
          ))}
        </div>
      </div>
    </div>
  );
};

const ForgotPassword = () => {
  usePageTitle("Forgot Password — Ginum");

  const [step, setStep] = useState(1); // 1 = Request Reset, 2 = Verify & Set New Password
  const [username, setUsername] = useState("");
  const [otp, setOtp] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [error, setError] = useState("");
  const [successMsg, setSuccessMsg] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  const handleRequestReset = async (e) => {
    e.preventDefault();
    if (!username) {
      setError("Please enter your username/email.");
      return;
    }
    setError("");
    setIsLoading(true);
    try {
      const res = await axios.post(`${apiUrl}/api/auth/forgot-password`, { email: username });
      setSuccessMsg(res.data.message || "Reset code sent.");
      setStep(2);
    } catch (err) {
      setError(err.response?.data?.error || "Failed to request password reset.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleResetPassword = async (e) => {
    e.preventDefault();
    if (!otp || !newPassword) {
      setError("Please fill in all fields.");
      return;
    }
    setError("");
    setSuccessMsg("");
    setIsLoading(true);
    try {
      const res = await axios.post(`${apiUrl}/api/auth/reset-password`, { 
        email: username,
        otp: otp,
        newPassword: newPassword
      });
      setSuccessMsg(res.data.message || "Password successfully reset!");
      // Redirect handled via the UI success state below
    } catch (err) {
      setError(err.response?.data?.error || "Failed to reset password.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="gl-root">
      {/* ══ LEFT PANEL — FORM ══ */}
      <aside className="gl-left">
        <div className="gl-logo-wrap">
          <img src="/ginum_logo.png" alt="Ginum Logo" className="gl-logo" />
          <span className="gl-platform-label">Financial Intelligence</span>
        </div>

        {successMsg && step === 2 && !error && newPassword ? (
          <div className="fp-success-state">
             <div className="fp-success-icon">✓</div>
             <h1 className="gl-heading">Password Reset!</h1>
             <p className="gl-subheading">{successMsg}</p>
             <Link to="/login" className="gl-btn gl-btn-primary" style={{marginTop: '2rem'}}>
               Return to Login
             </Link>
          </div>
        ) : step === 1 ? (
          <>
            <h1 className="gl-heading">Reset Password</h1>
            <p className="gl-subheading">Enter your username and we'll send you a recovery code.</p>

            {error && <Alert type="error" message={error} />}

            <form onSubmit={handleRequestReset} noValidate>
              <div className="gl-field">
                <label htmlFor="username" className="gl-label">USERNAME / EMAIL</label>
                <div className="gl-input-wrap">
                  <span className="gl-input-icon"><FaUser size={13} /></span>
                  <input
                    id="username"
                    name="username"
                    type="text"
                    required
                    value={username}
                    onChange={e => setUsername(e.target.value)}
                    className="gl-input gl-input-with-icon"
                    placeholder="Enter your username"
                  />
                </div>
              </div>

              <div className="gl-actions" style={{ marginTop: '2rem' }}>
                <button type="submit" className="gl-btn gl-btn-primary" disabled={isLoading}>
                  {isLoading ? "Sending..." : "Send Reset Code"}
                </button>
              </div>
            </form>
          </>
        ) : (
          <>
            <h1 className="gl-heading">Verification Code</h1>
            <p className="gl-subheading">Enter the 6-digit code sent to <b>{username}</b> and your new password.</p>

            {error && <Alert type="error" message={error} />}
            {successMsg && !error && <Alert type="success" message={successMsg} />}

            <form onSubmit={handleResetPassword} noValidate>
              <div className="gl-field">
                <label htmlFor="otp" className="gl-label">RECOVERY CODE</label>
                <div className="gl-input-wrap">
                  <span className="gl-input-icon"><FaKey size={13} /></span>
                  <input
                    id="otp"
                    name="otp"
                    type="text"
                    required
                    maxLength={6}
                    value={otp}
                    onChange={e => setOtp(e.target.value)}
                    className="gl-input gl-input-with-icon"
                    placeholder="000000"
                    style={{ letterSpacing: '0.2em', fontWeight: 'bold' }}
                  />
                </div>
              </div>

              <div className="gl-field">
                <label htmlFor="newPassword" className="gl-label">NEW PASSWORD</label>
                <div className="gl-input-wrap">
                  <span className="gl-input-icon"><FaLock size={13} /></span>
                  <input
                    id="newPassword"
                    name="newPassword"
                    type="password"
                    required
                    value={newPassword}
                    onChange={e => setNewPassword(e.target.value)}
                    className="gl-input gl-input-with-icon"
                    placeholder="••••••••"
                  />
                </div>
              </div>

              <div className="gl-actions" style={{ marginTop: '2rem' }}>
                <button type="submit" className="gl-btn gl-btn-primary" disabled={isLoading}>
                  {isLoading ? "Resetting..." : "Set New Password"}
                </button>
              </div>
            </form>
          </>
        )}

        <div className="gl-footer-links" style={{ marginTop: '3rem' }}>
          <Link to="/login" className="gl-forgot" style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            <FaArrowLeft size={12} /> Back to login
          </Link>
        </div>
      </aside>

      {/* ══ RIGHT PANEL — SHOWCASE ══ */}
      <aside className="gl-right">
        <FinanceShowcase />
      </aside>
    </div>
  );
};

export default ForgotPassword;
