import React, { useState, useEffect } from "react";
import usePageTitle from "../../hooks/usePageTitle";
import { Link } from "react-router-dom";
import { FaEye, FaEyeSlash, FaShieldAlt, FaArrowRight, FaUser } from "react-icons/fa";
import axios from "axios";
import { apiUrl } from "../../utils/api";
import "./Login.css";

/* ────────────────────────────────────────────
   UI HELPERS & CHARTS
──────────────────────────────────────────── */

const InlineAlert = ({ type, message }) => {
  const isError = type === "error";
  return (
    <div style={{
      padding: "1rem",
      marginBottom: "1.5rem",
      borderRadius: "12px",
      background: isError ? "#fef2f2" : "#ecfdf5",
      border: `1px solid ${isError ? "#fee2e2" : "#d1fae5"}`,
      color: isError ? "#991b1b" : "#065f46",
      fontSize: "0.875rem",
      fontWeight: 500
    }}>
      {message}
    </div>
  );
};

const Sparkline = ({ values, color, height = 36, width = 180 }) => {
  const max = Math.max(...values);
  const min = Math.min(...values);
  const range = max - min || 1;
  const pts = values.map((v, i) => {
    const x = (i / (values.length - 1)) * width;
    const y = height - ((v - min) / range) * (height - 6) - 3;
    return `${x},${y}`;
  });
  const path = `M ${pts.join(" L ")}`;
  const fill = `M ${pts[0]} L ${pts.join(" L ")} L ${width},${height} L 0,${height} Z`;
  return (
    <svg viewBox={`0 0 ${width} ${height}`} width="100%" height={height} style={{ overflow: "visible" }}>
      <defs>
        <linearGradient id={`sg-${color.replace("#", "")}`} x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stopColor={color} stopOpacity="0.35" />
          <stop offset="100%" stopColor={color} stopOpacity="0" />
        </linearGradient>
      </defs>
      <path d={fill} fill={`url(#sg-${color.replace("#", "")})`} />
      <path d={path} fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
};

const MiniDonut = ({ segments }) => {
  const r = 22, cx = 26, cy = 26, stroke = 8;
  const circ = 2 * Math.PI * r;
  let offset = 0;
  return (
    <svg width="52" height="52" viewBox="0 0 52 52">
      {segments.map((s, i) => {
        const dash = (s.pct / 100) * circ;
        const el = (
          <circle
            key={i}
            cx={cx} cy={cy} r={r}
            fill="none"
            stroke={s.color}
            strokeWidth={stroke}
            strokeDasharray={`${dash} ${circ - dash}`}
            strokeDashoffset={-offset}
            strokeLinecap="butt"
            style={{ transform: "rotate(-90deg)", transformOrigin: "26px 26px" }}
            opacity="0.85"
          />
        );
        offset += dash;
        return el;
      })}
    </svg>
  );
};

const BarChart = ({ bars, color }) => (
  <div className="gl-bars">
    {bars.map((h, i) => (
      <div
        key={i}
        className="gl-bar"
        style={{
          height: `${h}%`,
          background: i === bars.length - 1
            ? color
            : `rgba(${hexToRgb(color)}, 0.35)`,
          borderRadius: "3px 3px 0 0",
        }}
      />
    ))}
  </div>
);

function hexToRgb(hex) {
  const m = hex.replace("#","").match(/.{2}/g);
  return m ? m.map(c => parseInt(c,16)).join(",") : "99,102,241";
}

/* ────────────────────────────────────────────
   FINANCIAL CARDS
──────────────────────────────────────────── */

const RevenueCard = () => (
  <div className="gl-fcard">
    <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#6366f1,#8b5cf6)" }} />
    <div className="gl-card-label">Total Revenue</div>
    <div className="gl-card-value">$284,920</div>
    <span className="gl-badge gl-badge-up">↑ 12.8%</span>
    <Sparkline values={[140,165,155,185,170,200,190,215,205,230,220,248]} color="#6366f1" height={40} />
  </div>
);

const ExpensesCard = () => (
  <div className="gl-fcard">
    <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#f43f5e,#ec4899)" }} />
    <div className="gl-card-label">Expenses</div>
    <div className="gl-card-value-sm">$96,430</div>
    <span className="gl-badge gl-badge-down">↓ 3.2%</span>
    <div className="gl-ring-wrap">
      <MiniDonut segments={[
        { pct: 42, color: "#6366f1" },
        { pct: 28, color: "#f43f5e" },
        { pct: 30, color: "#22d3ee" },
      ]} />
      <div>
        <div className="gl-legend-item"><span className="gl-dot" style={{background:"#6366f1"}} /> Ops</div>
        <div className="gl-legend-item"><span className="gl-dot" style={{background:"#f43f5e"}} /> Sales</div>
        <div className="gl-legend-item"><span className="gl-dot" style={{background:"#22d3ee"}} /> Other</div>
      </div>
    </div>
  </div>
);

const CashFlowCard = () => (
  <div className="gl-fcard">
    <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#22d3ee,#6366f1)" }} />
    <div style={{ display:"flex", justifyContent:"space-between", alignItems:"flex-start" }}>
      <div>
        <div className="gl-card-label">Cash Flow</div>
        <div className="gl-card-value-sm">+$188,490</div>
      </div>
      <span className="gl-badge gl-badge-up">↑ 8.4%</span>
    </div>
    <BarChart bars={[55, 70, 48, 82, 65, 90, 75, 88, 78, 95, 80, 100]} color="#22d3ee" />
    <div style={{ display:"flex", justifyContent:"space-between", marginTop:"6px" }}>
      {["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"].map(m => (
        <div key={m} style={{ fontSize:"0.55rem", color:"rgba(0,0,0,0.4)" }}>{m}</div>
      ))}
    </div>
  </div>
);

const InvoicesCard = () => (
  <div className="gl-fcard">
    <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#10b981,#22d3ee)" }} />
    <div className="gl-card-label">Invoice Status</div>
    <div style={{ display:"flex", flexDirection:"column", gap:"0.5rem", marginTop:"0.375rem" }}>
      {[
        { label:"Paid", count:24, color:"#10b981", pct: 68 },
        { label:"Pending", count: 7, color:"#f59e0b", pct: 20 },
        { label:"Overdue", count: 3, color:"#f43f5e", pct: 12 },
      ].map(row => (
        <div key={row.label}>
          <div style={{ display:"flex", justifyContent:"space-between", marginBottom:"3px" }}>
            <span style={{ fontSize:"0.72rem", color:"rgba(0,0,0,0.6)", display:"flex", alignItems:"center", gap:"5px" }}>
              <span className="gl-dot gl-dot-glow" style={{ background:row.color, color:row.color }} />
              {row.label}
            </span>
            <span style={{ fontSize:"0.72rem", fontWeight:700, color:"rgba(0,0,0,0.8)" }}>{row.count}</span>
          </div>
          <div style={{ height:"3px", background:"rgba(0,0,0,0.06)", borderRadius:"999px" }}>
            <div style={{ height:"100%", width:`${row.pct}%`, background:row.color, borderRadius:"999px", opacity:0.8 }} />
          </div>
        </div>
      ))}
    </div>
  </div>
);

const TransactionsCard = () => (
  <div className="gl-fcard">
    <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#8b5cf6,#6366f1)" }} />
    <div className="gl-card-label" style={{ marginBottom:"0.5rem" }}>Recent Transactions</div>
    {[
      { icon:"🛒", name:"Shopify Revenue", date:"Today", amount:"+$4,820", up: true },
      { icon:"🏢", name:"Office Lease Q4", date:"Yesterday", amount:"-$2,100", up: false },
      { icon:"📦", name:"Inventory Restock", date:"Sep 8", amount:"-$890", up: false },
    ].map((tx, i) => (
      <div key={i} className="gl-tx-row">
        <div className="gl-tx-icon" style={{ background: tx.up ? "rgba(16,185,129,0.15)" : "rgba(239,68,68,0.12)" }}>
          {tx.icon}
        </div>
        <div className="gl-tx-info">
          <div className="gl-tx-name">{tx.name}</div>
          <div className="gl-tx-date">{tx.date}</div>
        </div>
        <div className="gl-tx-amount" style={{ color: tx.up ? "#34d399" : "#f87171" }}>
          {tx.amount}
        </div>
      </div>
    ))}
  </div>
);

const NetProfitCard = () => (
  <div className="gl-fcard">
    <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#f59e0b,#f97316)" }} />
    <div className="gl-card-label">Net Profit</div>
    <div className="gl-card-value-sm">$52,194</div>
    <span className="gl-badge gl-badge-up">↑ 21.4%</span>
    <Sparkline values={[28,32,29,38,35,42,40,47,44,50,48,52]} color="#f59e0b" height={30} />
  </div>
);

/* ────────────────────────────────────────────
   MINI WIDGETS
──────────────────────────────────────────── */
const MiniToast = ({ icon, text, amount, color }) => (
  <div className="gl-mini-toast">
    <div className="gl-mini-icon" style={{ background: `rgba(${hexToRgb(color)}, 0.15)` }}>{icon}</div>
    <div className="gl-mini-info">
      <div className="gl-mini-text">{text}</div>
      <div className="gl-mini-amount" style={{ color }}>{amount}</div>
    </div>
  </div>
);

const MiniStatusChip = ({ label, value, color }) => (
  <div className="gl-mini-chip">
    <span className="gl-dot gl-dot-glow" style={{ background: color, color }} />
    <span className="gl-chip-lbl">{label}</span>
    <span className="gl-chip-val" style={{ color }}>{value}</span>
  </div>
);

const MiniDonutCard = () => (
  <div className="gl-fcard gl-fcard-sm">
    <div className="gl-card-label" style={{ marginBottom:"0.5rem" }}>Expense Split</div>
    <div style={{ display: "flex", alignItems: "center", gap: "0.75rem" }}>
      <div style={{ transform: "scale(0.8)", transformOrigin: "left center" }}>
        <MiniDonut segments={[
          { pct: 42, color: "#6366f1" },
          { pct: 28, color: "#f43f5e" },
          { pct: 30, color: "#22d3ee" },
        ]} />
      </div>
      <div>
        <div className="gl-legend-item" style={{fontSize:"0.65rem"}}><span className="gl-dot" style={{background:"#6366f1", width:"6px", height:"6px"}} /> Ops</div>
        <div className="gl-legend-item" style={{fontSize:"0.65rem"}}><span className="gl-dot" style={{background:"#f43f5e", width:"6px", height:"6px"}} /> Sales</div>
      </div>
    </div>
  </div>
);

/* ────────────────────────────────────────────
   MOCK APP WINDOW (HERO)
──────────────────────────────────────────── */
const MockAppWindow = () => (
  <div className="gl-mock-app">
    {/* Sidebar */}
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
    
    {/* Main Content */}
    <div className="gl-mock-main">
      {/* Topnav */}
      <div className="gl-mock-topnav">
        <div className="gl-mock-search"><span className="gl-skeleton-icon" style={{width:"12px", height:"12px"}}/> Search...</div>
        <div className="gl-mock-profile">
          <span className="gl-skeleton-icon" style={{width:"14px", height:"14px", borderRadius:"50%"}} />
          <span className="gl-skeleton-icon" style={{width:"24px", height:"24px", borderRadius:"50%"}} />
        </div>
      </div>
      
      {/* Body */}
      <div className="gl-mock-body">
        <div className="gl-mock-header">
          <h4>Overview</h4>
          <p>Here's what's happening with your business today.</p>
        </div>
        
        {/* KPIs */}
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
        
        {/* Main Chart Area */}
        <div className="gl-mock-chart-area">
           <Sparkline values={[140,165,155,185,170,200,190,215,205,230,220,248]} color="#6366f1" height={100} width={420} />
           <div style={{ position:"absolute", top:"20px", right:"20px", opacity:0.5 }}>
             <Sparkline values={[120,130,125,140,150,145,160,170,165,180,175,190]} color="#3b82f6" height={100} width={420} />
           </div>
           {/* Chart Grid Lines */}
           <div className="gl-mock-gridline" style={{bottom:"20%"}}></div>
           <div className="gl-mock-gridline" style={{bottom:"50%"}}></div>
           <div className="gl-mock-gridline" style={{bottom:"80%"}}></div>
        </div>
      </div>
    </div>
  </div>
);

/* ────────────────────────────────────────────
   ANIMATED FINANCIAL SHOWCASE
──────────────────────────────────────────── */

const FinanceShowcase = () => {
  const [slide, setSlide] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setSlide((s) => (s + 1) % 3);
    }, 5500);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="gl-showcase" aria-hidden="true">
      {/* Ambient background that subtly shifts */}
      <div className={`gl-ambient-glow gl-glow-${slide}`} />

      <div className="gl-showcase-stage">
        
        {/* Persistent Mock App Window inside stage */}
        <div className="gl-main-card">
          <MockAppWindow />
        </div>

        {/* Slide 0: Financial Overview */}
        <div className={`gl-scene ${slide === 0 ? "active" : ""}`}>
          <div className="gl-float-card gl-float-tr delay-1">
            <div className="gl-fcard">
               <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#10b981,#34d399)" }} />
               <div className="gl-card-label">Account Balance</div>
               <div className="gl-card-value-sm">$320,450</div>
               <span className="gl-badge gl-badge-up">↑ 12.6%</span>
               <Sparkline values={[40,42,41,45,43,48,50]} color="#10b981" height={24} width={200} />
            </div>
          </div>
          <div className="gl-float-card gl-float-bl delay-2" style={{width: "220px"}}>
            <MiniDonutCard />
          </div>
          <div className="gl-float-widget gl-float-tl delay-3">
            <MiniStatusChip label="System Health" value="Online" color="#10b981" />
          </div>
        </div>

        {/* Slide 1: Cash Flow & Performance */}
        <div className={`gl-scene ${slide === 1 ? "active" : ""}`}>
          <div className="gl-float-card gl-float-bl delay-1">
            <CashFlowCard />
          </div>
          <div className="gl-float-widget gl-float-tr delay-2">
            <MiniStatusChip label="Cash Runway" value="8.4 Months" color="#3b82f6" />
          </div>
          <div className="gl-float-widget gl-float-br delay-3">
            <MiniToast icon="💳" text="Payment Cleared" amount="+$12,400" color="#10b981" />
          </div>
        </div>

        {/* Slide 2: Invoices & Expenses */}
        <div className={`gl-scene ${slide === 2 ? "active" : ""}`}>
          <div className="gl-float-card gl-float-tl delay-1">
            <InvoicesCard />
          </div>
          <div className="gl-float-card gl-float-br delay-2">
             <TransactionsCard />
          </div>
          <div className="gl-float-widget gl-float-bl delay-3">
            <MiniToast icon="✉️" text="Invoice Sent" amount="INV-204" color="#f59e0b" />
          </div>
        </div>

      </div>

      <div className="gl-showcase-bottom">
        <div className="gl-showcase-indicators">
          {[0, 1, 2].map(i => (
            <button 
              key={i} 
              className={`gl-dot-ind ${slide === i ? "active" : ""}`} 
              onClick={() => setSlide(i)}
              aria-label={`Go to slide ${i + 1}`}
            />
          ))}
        </div>
        <div className="gl-showcase-text">
          <h3>Financial clarity, at a glance.</h3>
          <p>Track cash flow, expenses, invoices and business performance in real time.</p>
        </div>
      </div>
    </div>
  );
};


/* ────────────────────────────────────────────
   MAIN LOGIN COMPONENT
──────────────────────────────────────────── */
const Login = () => {
  usePageTitle("Login — Ginum");

  const [showPassword, setShowPassword] = useState(false);
  const [username, setUsername] = useState("");
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
        email: username, // Send username as email property to backend
        password,
        mfaCode: mfaRequired ? mfaCode : null,
        loginOtp: otpRequired ? loginOtp : null,
      }, { withCredentials: true });

      const response = res.data;

      if (response.otpRequired) { setOtpRequired(true); setError(""); return; }
      if (response.mfaRequired) { setMfaRequired(true); setError(""); return; }
      if (!response.token || !response.role) throw new Error("Missing token or role in response");

      sessionStorage.setItem("jwtToken", response.token);
      sessionStorage.setItem("userRole", response.role);

      window.location.href = response.role === "EMPLOYEE" ? "/pos" : "/";
    } catch (err) {
      if (err.response?.status === 403 && err.response?.data?.error === "Email not verified") {
        setEmailUnverified(true);
        setError("Your email address is not verified.");
      } else {
        setError(err.response?.data?.error || err.response?.data?.message || "Login failed");
      }
    }
  };

  const handleResendVerification = async () => {
    setIsResending(true);
    setResendStatus("");
    try {
      const res = await axios.post(`${apiUrl}/api/auth/resend-verification`, { email: username });
      setResendStatus(res.data.message || "Verification email sent.");
    } catch (err) {
      setResendStatus(err.response?.data?.error || "Failed to resend email.");
    } finally {
      setIsResending(false);
    }
  };

  return (
    <div className="gl-root">

      {/* ══ LEFT PANEL — FORM ══ */}
      <aside className="gl-left">
        {/* Brand */}
        <div className="gl-logo-wrap">
          <img src="/ginum_logo.png" alt="Ginum Logo" className="gl-logo" />
          <span className="gl-platform-label">Financial Intelligence</span>
        </div>

        {/* Dynamic Forms */}
        {otpRequired ? (
          <>
            <h1 className="gl-heading">Check your email</h1>
            <p className="gl-subheading">Enter the OTP sent to <b>{username}</b> to complete login.</p>
            {error && <Alert type="error" message={error} />}
            <form onSubmit={handleSubmit} noValidate>
              <div className="gl-field">
                <label className="gl-label">One-Time Password (OTP)</label>
                <div className="gl-input-wrap">
                  <input
                    type="text"
                    required
                    value={loginOtp}
                    onChange={e => setLoginOtp(e.target.value)}
                    className="gl-input"
                    placeholder="Enter 6-digit code"
                    maxLength={6}
                    autoFocus
                  />
                </div>
              </div>
              <button type="submit" className="gl-btn">
                Verify OTP <FaArrowRight className="gl-btn-icon" size={13} />
              </button>
              <button type="button" className="gl-cancel-btn" onClick={() => setOtpRequired(false)}>
                Cancel
              </button>
            </form>
          </>
        ) : mfaRequired ? (
          <>
            <h1 className="gl-heading">Two-Factor Auth</h1>
            <p className="gl-subheading">Enter the code from your authenticator app.</p>
            {error && <InlineAlert type="error" message={error} />}
            <form onSubmit={handleSubmit} noValidate>
              <div className="gl-field">
                <label className="gl-label">Authenticator Code</label>
                <div className="gl-input-wrap">
                  <input
                    type="text"
                    required
                    value={mfaCode}
                    onChange={e => setMfaCode(e.target.value)}
                    className="gl-input"
                    placeholder="000000"
                    maxLength={6}
                    autoFocus
                  />
                </div>
              </div>
              <button type="submit" className="gl-btn">
                Verify <FaArrowRight className="gl-btn-icon" size={13} />
              </button>
              <button type="button" className="gl-cancel-btn" onClick={() => setMfaRequired(false)}>
                Back to Login
              </button>
            </form>
          </>
        ) : (
          <>
            <h1 className="gl-heading">Welcome back</h1>
            <p className="gl-subheading">Sign in to continue managing your financial workspace.</p>
            
            {error && (
              <div style={{ marginBottom: "1.5rem" }}>
                <InlineAlert type="error" message={error} />
                {emailUnverified && (
                  <div style={{ marginTop:"0.75rem", padding:"1rem", background:"#fef2f2", borderRadius:"12px", border:"1px solid #fee2e2" }}>
                    <p style={{ margin:0, fontSize:"0.8rem", color:"#991b1b", marginBottom:"0.5rem" }}>
                      Didn't receive the email or link expired?
                    </p>
                    <button
                      type="button"
                      onClick={handleResendVerification}
                      disabled={isResending}
                      style={{
                        background:"none", border:"none", padding:0,
                        color:"#fca5a5", fontSize:"0.75rem", fontWeight:600, cursor:"pointer",
                      }}
                    >
                      {isResending ? "Sending…" : "Resend Verification Email"}
                    </button>
                    {resendStatus && <p style={{ marginTop:"0.375rem", fontSize:"0.75rem", color:"#fca5a5" }}>{resendStatus}</p>}
                  </div>
                )}
              </div>
            )}

            <form onSubmit={handleSubmit} noValidate>
              {/* Username */}
              <div className="gl-field">
                <label htmlFor="username" className="gl-label">USERNAME</label>
                <div className="gl-input-wrap">
                  <span className="gl-input-icon"><FaUser size={13} /></span>
                  <input
                    id="username"
                    name="username"
                    type="text"
                    required
                    autoComplete="username"
                    value={username}
                    onChange={e => setUsername(e.target.value)}
                    className="gl-input gl-input-with-icon"
                    placeholder="Enter your username"
                  />
                </div>
              </div>

              {/* Password */}
              <div className="gl-field">
                <label htmlFor="password" className="gl-label">Password</label>
                <div className="gl-input-wrap">
                  <input
                    id="password"
                    name="password"
                    type={showPassword ? "text" : "password"}
                    required
                    autoComplete="current-password"
                    value={password}
                    onChange={e => setPassword(e.target.value)}
                    className="gl-input"
                    placeholder="Enter your password"
                    style={{ paddingRight: "3rem" }}
                  />
                  <button
                    type="button"
                    className="gl-eye-btn"
                    onClick={() => setShowPassword(!showPassword)}
                    aria-label={showPassword ? "Hide password" : "Show password"}
                  >
                    {showPassword ? <FaEyeSlash size={16} /> : <FaEye size={16} />}
                  </button>
                </div>
              </div>

              {/* Remember / Forgot */}
              <div className="gl-form-row">
                <label className="gl-checkbox-lbl">
                  <input type="checkbox" className="gl-checkbox" />
                  Remember me
                </label>
                <Link to="/forgot-password" className="gl-forgot">Forgot password?</Link>
              </div>

              {/* CTA */}
              <button type="submit" className="gl-btn">
                Sign in
                <FaArrowRight className="gl-btn-icon" size={13} />
              </button>
            </form>

            <p className="gl-signup-row">
              Don't have an account?{" "}
              <a href="/register" className="gl-signup-link">Create an account</a>
            </p>

            {/* Status badge */}
            <div className="gl-secure-badge">
              <span className="gl-status-dot" />
              All systems operational
              <span style={{ margin:"0 0.3rem" }}>·</span>
              <FaShieldAlt size={9} />
              Enterprise-grade security
            </div>
          </>
        )}
      </aside>

      {/* ══ RIGHT PANEL — SHOWCASE ══ */}
      <main className="gl-right" aria-hidden="true">
        <FinanceShowcase />
      </main>

    </div>
  );
};

export default Login;
