import React, { useState } from "react";
import usePageTitle from "../../hooks/usePageTitle";
import { FaEye, FaEyeSlash, FaShieldAlt, FaArrowRight } from "react-icons/fa";
import axios from "axios";
import { apiUrl } from "../../utils/api";
import Alert from "../../components/Alert/Alert";
import "./Login.css";

/* ────────────────────────────────────────────
   DECORATIVE FINANCIAL DASHBOARD (right panel)
──────────────────────────────────────────── */

/** Mini sparkline SVG */
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

/** Minimal SVG donut */
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

/** Mini bar chart */
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

/** Full financial dashboard composition */
const FinanceDashboard = () => (
  <div className="gl-dashboard" aria-hidden="true">

    {/* ── Revenue Card ── */}
    <div className="gl-fcard gl-card-revenue">
      <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#6366f1,#8b5cf6)" }} />
      <div className="gl-card-label">Total Revenue</div>
      <div className="gl-card-value">$284,920</div>
      <span className="gl-badge gl-badge-up">↑ 12.8%</span>
      <Sparkline
        values={[140,165,155,185,170,200,190,215,205,230,220,248]}
        color="#6366f1"
        height={40}
      />
    </div>

    {/* ── Expenses Card ── */}
    <div className="gl-fcard gl-card-expenses">
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

    {/* ── Cash Flow Card ── */}
    <div className="gl-fcard gl-card-cashflow">
      <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#22d3ee,#6366f1)" }} />
      <div style={{ display:"flex", justifyContent:"space-between", alignItems:"flex-start" }}>
        <div>
          <div className="gl-card-label">Cash Flow</div>
          <div className="gl-card-value-sm">+$188,490</div>
        </div>
        <span className="gl-badge gl-badge-up">↑ 8.4%</span>
      </div>
      <BarChart
        bars={[55, 70, 48, 82, 65, 90, 75, 88, 78, 95, 80, 100]}
        color="#22d3ee"
      />
      <div style={{ display:"flex", justifyContent:"space-between", marginTop:"6px" }}>
        {["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"].map(m => (
          <div key={m} style={{ fontSize:"0.55rem", color:"rgba(0,0,0,0.4)" }}>{m}</div>
        ))}
      </div>
    </div>

    {/* ── Invoice Status Card ── */}
    <div className="gl-fcard gl-card-invoices">
      <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#10b981,#22d3ee)" }} />
      <div className="gl-card-label">Invoice Status</div>
      <div style={{ display:"flex", flexDirection:"column", gap:"0.5rem", marginTop:"0.375rem" }}>
        {[
          { label:"Paid",       count:24, color:"#10b981", pct: 68 },
          { label:"Pending",    count: 7, color:"#f59e0b", pct: 20 },
          { label:"Overdue",    count: 3, color:"#f43f5e", pct: 12 },
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

    {/* ── Recent Transactions ── */}
    <div className="gl-fcard gl-card-transactions">
      <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#8b5cf6,#6366f1)" }} />
      <div className="gl-card-label" style={{ marginBottom:"0.5rem" }}>Recent Transactions</div>
      {[
        { icon:"🛒", name:"Shopify Revenue",    date:"Today",     amount:"+$4,820", up: true  },
        { icon:"🏢", name:"Office Lease Q4",    date:"Yesterday", amount:"-$2,100", up: false },
        { icon:"📦", name:"Inventory Restock",  date:"Sep 8",     amount:"-$890",   up: false },
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

    {/* ── Account Balance Card ── */}
    <div className="gl-fcard gl-card-balance">
      <div className="gl-fcard-accent" style={{ background: "linear-gradient(90deg,#f59e0b,#f97316)" }} />
      <div className="gl-card-label">Net Profit</div>
      <div className="gl-card-value-sm">$52,194</div>
      <span className="gl-badge gl-badge-up">↑ 21.4%</span>
      <Sparkline
        values={[28,32,29,38,35,42,40,47,44,50,48,52]}
        color="#f59e0b"
        height={30}
      />
    </div>

  </div>
);

/* ────────────────────────────────────────────
   MAIN LOGIN COMPONENT
──────────────────────────────────────────── */
const Login = () => {
  usePageTitle("Login — Ginum");

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

      sessionStorage.setItem("permissions", JSON.stringify(response.permissions ?? []));

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
      let msg = "Invalid email or password. Please try again.";
      if (err.response?.data) {
        const d = err.response.data;
        if (typeof d === "string") {
          msg = d.includes("<html") || d.includes("502 Bad Gateway")
            ? "Server is initializing. Please retry in a moment."
            : d;
        } else {
          msg = d.error || d.message || msg;
        }
      }
      setEmailUnverified(!!(err.response?.data?.emailUnverified));
      setError(msg);
      Alert.error(msg);
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

  /* ── render ── */
  return (
    <div className="gl-root">

      {/* Background */}
      <div className="gl-bg">
        <div className="gl-orb gl-orb-1" />
        <div className="gl-orb gl-orb-2" />
        <div className="gl-orb gl-orb-3" />
      </div>

      {/* ══ LEFT PANEL — LOGIN FORM ══ */}
      <aside className="gl-left">

        {/* Logo */}
        <div className="gl-logo-wrap">
          <img src="ginum_logo.png" alt="Ginum logo" className="gl-logo" />
          <span className="gl-platform-label">Financial Intelligence</span>
        </div>

        {/* Step: OTP */}
        {otpRequired ? (
          <>
            <div className="gl-mfa-box">
              <div className="gl-mfa-icon">🔐</div>
              <div className="gl-mfa-title">Security Verification</div>
              <p className="gl-mfa-desc">
                A 6-digit code was sent to <strong style={{ color:"#4f46e5" }}>{email}</strong>
              </p>
            </div>

            {error && (
              <div className="gl-error" role="alert">
                <svg className="gl-error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {error}
              </div>
            )}

            <form onSubmit={handleSubmit}>
              <label htmlFor="loginOtp" className="gl-label">6-Digit OTP</label>
              <input
                id="loginOtp"
                type="text"
                required
                inputMode="numeric"
                value={loginOtp}
                onChange={e => setLoginOtp(e.target.value.replace(/\D/g, ""))}
                className="gl-otp-input"
                placeholder="• • • • • •"
                maxLength={6}
                aria-label="One-time password"
              />
              <button type="submit" className="gl-btn">
                Verify &amp; Sign In
                <FaArrowRight className="gl-btn-icon" size={13} />
              </button>
              <button type="button" onClick={resetMfa} className="gl-cancel-btn">Cancel</button>
            </form>
          </>
        ) : mfaRequired ? (
          <>
            {/* Step: MFA */}
            <div className="gl-mfa-box">
              <div className="gl-mfa-icon">🔑</div>
              <div className="gl-mfa-title">Two-Factor Authentication</div>
              <p className="gl-mfa-desc">Open Google Authenticator and enter your 6-digit code.</p>
            </div>

            {error && (
              <div className="gl-error" role="alert">
                <svg className="gl-error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {error}
              </div>
            )}

            <form onSubmit={handleSubmit}>
              <label htmlFor="mfaCode" className="gl-label">Authenticator Code</label>
              <input
                id="mfaCode"
                type="text"
                required
                inputMode="numeric"
                value={mfaCode}
                onChange={e => setMfaCode(e.target.value)}
                className="gl-otp-input"
                placeholder="• • • • • •"
                maxLength={6}
                aria-label="Authenticator code"
              />
              <button type="submit" className="gl-btn">
                Verify Code
                <FaArrowRight className="gl-btn-icon" size={13} />
              </button>
              <button type="button" onClick={resetMfa} className="gl-cancel-btn">Cancel</button>
            </form>
          </>
        ) : (
          /* ══ Step: Main login form ══ */
          <>
            <h1 className="gl-heading">Welcome back</h1>
            <p className="gl-subheading">Sign in to continue managing your financial workspace.</p>

            {error && (
              <div className="gl-error" role="alert">
                <svg className="gl-error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <span>{error}</span>
                  {emailUnverified && (
                    <div style={{ marginTop:"0.5rem" }}>
                      <button
                        type="button"
                        onClick={handleResendVerification}
                        disabled={isResending}
                        style={{
                          background:"rgba(239,68,68,0.18)",
                          border:"1px solid rgba(239,68,68,0.35)",
                          borderRadius:"6px",
                          padding:"0.2rem 0.65rem",
                          color:"#fca5a5",
                          fontSize:"0.75rem",
                          fontWeight:600,
                          cursor:"pointer",
                        }}
                      >
                        {isResending ? "Sending…" : "Resend Verification Email"}
                      </button>
                      {resendStatus && (
                        <p style={{ marginTop:"0.375rem", fontSize:"0.75rem", color:"#fca5a5" }}>{resendStatus}</p>
                      )}
                    </div>
                  )}
                </div>
              </div>
            )}

            <form onSubmit={handleSubmit} noValidate>
              {/* Email */}
              <div className="gl-field">
                <label htmlFor="email" className="gl-label">Email address</label>
                <div className="gl-input-wrap">
                  <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autoComplete="email"
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    className="gl-input"
                    placeholder="name@company.com"
                    aria-required="true"
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
                    className="gl-input gl-input-pr"
                    placeholder="Enter your password"
                    aria-required="true"
                  />
                  <button
                    type="button"
                    className="gl-eye-btn"
                    onClick={() => setShowPassword(v => !v)}
                    aria-label={showPassword ? "Hide password" : "Show password"}
                  >
                    {showPassword ? <FaEyeSlash size={15} /> : <FaEye size={15} />}
                  </button>
                </div>
              </div>

              {/* Remember / Forgot */}
              <div className="gl-options-row">
                <label className="gl-remember">
                  <input type="checkbox" id="remember-me" name="remember-me" />
                  Remember me
                </label>
                <a href="#" className="gl-forgot">Forgot password?</a>
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

      {/* ══ RIGHT PANEL — FINANCIAL DASHBOARD ══ */}
      <main className="gl-right" aria-hidden="true">
        <FinanceDashboard />
      </main>

    </div>
  );
};

export default Login;
