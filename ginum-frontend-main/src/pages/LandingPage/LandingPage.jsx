import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import {
  Calculator, LineChart, ShoppingCart, Package, PieChart, Users,
  CheckCircle2, ShieldCheck, Cloud, Zap, FileText, TrendingUp, Clock,
  FolderOpen, Scale, Box, Globe, Grid, Facebook, Twitter, Linkedin, Bell, Menu, X,
  Lock, Network
} from 'lucide-react';
import './LandingPage.css';

const LandingPage = () => {
  const [scrolled, setScrolled] = useState(false);
  const [activeSection, setActiveSection] = useState('');
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);

      const sections = ['features', 'plans', 'why-us'];
      let current = '';

      // Determine active section based on scroll position
      for (const section of sections) {
        const element = document.getElementById(section);
        if (element) {
          const rect = element.getBoundingClientRect();
          // Adjust threshold for when section is considered active
          if (rect.top <= 250 && rect.bottom >= 250) {
            current = section;
          }
        }
      }
      setActiveSection(current);
    };

    window.addEventListener('scroll', handleScroll);
    // Initial check
    handleScroll();

    const observerOptions = {
      root: null,
      rootMargin: '0px',
      threshold: 0.1,
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
        }
      });
    }, observerOptions);

    const revealElements = document.querySelectorAll('.reveal');
    revealElements.forEach((el) => observer.observe(el));

    return () => {
      window.removeEventListener('scroll', handleScroll);
      revealElements.forEach((el) => observer.unobserve(el));
      observer.disconnect();
    };
  }, []);

  // Close mobile menu if clicked outside
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (mobileMenuOpen && !event.target.closest('.lp-navbar-wrapper')) {
        setMobileMenuOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [mobileMenuOpen]);

  const scrollToSection = (e, id) => {
    e.preventDefault();
    const el = document.getElementById(id);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <div className="landing-page">
      {/* 1. Navbar */}
      <div className={`lp-navbar-wrapper ${scrolled ? 'scrolled' : ''}`}>
        <nav className="lp-navbar-container">
          <Link to="/" className="lp-logo">
            <div className="lp-logo-icon">G</div>
            GINUM
          </Link>

          <div className="lp-nav-links desktop-only">
            <a
              href="#features"
              className={activeSection === 'features' ? 'active' : ''}
              onClick={(e) => scrollToSection(e, 'features')}
            >
              Features
            </a>
            <a
              href="#plans"
              className={activeSection === 'plans' ? 'active' : ''}
              onClick={(e) => scrollToSection(e, 'plans')}
            >
              Plans
            </a>
            <a
              href="#why-us"
              className={activeSection === 'why-us' ? 'active' : ''}
              onClick={(e) => scrollToSection(e, 'why-us')}
            >
              Why GINUM
            </a>
          </div>

          <div className="lp-nav-actions desktop-only">
            <Link to="/login" className="lp-login-btn">Login</Link>
          </div>

          <button className="lp-mobile-menu-btn mobile-only" onClick={() => setMobileMenuOpen(!mobileMenuOpen)}>
            {mobileMenuOpen ? <X size={24} color="white" /> : <Menu size={24} color="white" />}
          </button>
        </nav>

        {/* Mobile Menu Dropdown */}
        <div className={`lp-mobile-menu ${mobileMenuOpen ? 'open' : ''}`}>
          <a href="#features" className={activeSection === 'features' ? 'active' : ''} onClick={(e) => { scrollToSection(e, 'features'); setMobileMenuOpen(false); }}>Features</a>
          <a href="#plans" className={activeSection === 'plans' ? 'active' : ''} onClick={(e) => { scrollToSection(e, 'plans'); setMobileMenuOpen(false); }}>Plans</a>
          <a href="#why-us" className={activeSection === 'why-us' ? 'active' : ''} onClick={(e) => { scrollToSection(e, 'why-us'); setMobileMenuOpen(false); }}>Why GINUM</a>
          <Link to="/login" className="lp-login-btn-mobile" onClick={() => setMobileMenuOpen(false)}>Login</Link>
        </div>
      </div>

      {/* 2. Hero Section */}
      <section className="lp-hero reveal">
        <div className="lp-hero-bg">
          <div className="lp-hero-grid"></div>
          <div className="lp-hero-orb lp-hero-orb-1"></div>
          <div className="lp-hero-orb lp-hero-orb-2"></div>
        </div>

        <div className="lp-hero-content">
          <div className="lp-hero-badge">
            <Zap size={14} style={{ marginRight: '6px' }} /> All-in-One Business Management
          </div>
          <h1 className="lp-hero-title">
            Smart Business & Accounting Platform for <span className="lp-gradient-text">Growing Businesses</span>
          </h1>
          <p className="lp-hero-subtitle">
            Manage accounting, sales, purchases, inventory, customers, suppliers, employees, and financial reports from one secure cloud-based business platform.
          </p>

          <div className="lp-hero-buttons">
            <Link to="/login" className="lp-btn-primary">Get Started</Link>
            <a href="#features" onClick={(e) => scrollToSection(e, 'features')} className="lp-btn-secondary">Explore Features</a>
          </div>

          <div className="lp-trust-chips">
            <div className="lp-trust-chip"><Cloud size={16} color="var(--lp-primary)" /> Cloud Based</div>
            <div className="lp-trust-chip"><ShieldCheck size={16} color="var(--lp-slate-dark)" /> Secure & Reliable</div>
            <div className="lp-trust-chip"><PieChart size={16} color="var(--lp-primary)" /> Real-time Insights</div>
          </div>
        </div>

        <div className="lp-hero-visual">
          <div className="lp-floating-badge lp-badge-1">
            <LineChart size={14} /> Real-time Reports
          </div>
          <div className="lp-floating-badge lp-badge-2">
            <Package size={14} /> Inventory Sync
          </div>
          <div className="lp-floating-badge lp-badge-3">
            <TrendingUp size={14} /> Sales Growth +24.5%
          </div>
          <div className="lp-floating-badge lp-badge-4">
            Low Stock Items<br />12 Items
          </div>

          <div className="lp-dashboard-mockup">
            <div className="lp-mockup-sidebar">
              <div className="lp-mockup-logo-small">G</div>
              <Calculator className="lp-mockup-nav-icon active" />
              <LineChart className="lp-mockup-nav-icon" />
              <Package className="lp-mockup-nav-icon" />
              <PieChart className="lp-mockup-nav-icon" />
              <Users className="lp-mockup-nav-icon" />
            </div>

            <div className="lp-mockup-main">
              <div className="lp-mockup-topbar">
                <div className="lp-mockup-topbar-title">Dashboard</div>
                <div className="lp-mockup-search-container">
                  <div className="lp-mockup-search">Search anything...</div>
                  <Bell size={16} color="#64748b" />
                  <div className="lp-mockup-profile"></div>
                </div>
              </div>

              <div className="lp-mockup-kpis">
                <div className="lp-mockup-kpi-card">
                  <div className="lp-mockup-kpi-label">Revenue</div>
                  <div className="lp-mockup-kpi-value">$45,231</div>
                  <div className="lp-mockup-kpi-badge up">+12.8%</div>
                </div>
                <div className="lp-mockup-kpi-card">
                  <div className="lp-mockup-kpi-label">Expenses</div>
                  <div className="lp-mockup-kpi-value">$12,845</div>
                  <div className="lp-mockup-kpi-badge down">-2.4%</div>
                </div>
                <div className="lp-mockup-kpi-card">
                  <div className="lp-mockup-kpi-label">Orders</div>
                  <div className="lp-mockup-kpi-value">1,245</div>
                  <div className="lp-mockup-kpi-badge up">+8.1%</div>
                </div>
                <div className="lp-mockup-kpi-card">
                  <div className="lp-mockup-kpi-label">Profit</div>
                  <div className="lp-mockup-kpi-value">$32,386</div>
                  <div className="lp-mockup-kpi-badge up">+15.3%</div>
                </div>
              </div>

              <div className="lp-mockup-charts-row">
                <div className="lp-mockup-chart-box">
                  <div className="lp-mockup-chart-title">Sales Overview</div>
                  <div className="lp-mockup-bar-chart">
                    <div className="lp-mockup-bar" style={{ height: '40%' }}></div>
                    <div className="lp-mockup-bar" style={{ height: '70%' }}></div>
                    <div className="lp-mockup-bar" style={{ height: '50%' }}></div>
                    <div className="lp-mockup-bar" style={{ height: '90%' }}></div>
                    <div className="lp-mockup-bar" style={{ height: '65%' }}></div>
                    <div className="lp-mockup-bar" style={{ height: '80%' }}></div>
                    <div className="lp-mockup-bar" style={{ height: '100%' }}></div>
                  </div>
                </div>
                <div className="lp-mockup-chart-box">
                  <div className="lp-mockup-chart-title">Expenses by Category</div>
                  <div className="lp-mockup-donut-chart">
                    <div className="lp-mockup-circle"></div>
                    <div className="lp-mockup-donut-legend">
                      <div className="lp-mockup-legend-item"><div className="lp-mockup-dot" style={{ background: '#3b82f6' }}></div> Operations</div>
                      <div className="lp-mockup-legend-item"><div className="lp-mockup-dot" style={{ background: '#ec4899' }}></div> Marketing</div>
                      <div className="lp-mockup-legend-item"><div className="lp-mockup-dot" style={{ background: '#8b5cf6' }}></div> Purchases</div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="lp-mockup-cashflow">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                  <div>
                    <div className="lp-mockup-chart-title">Cash Flow</div>
                    <div style={{ display: 'flex', gap: '8px', marginTop: '4px', fontSize: '0.6rem', color: '#94a3b8', alignItems: 'center' }}>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                        <div style={{ width: '6px', height: '6px', borderRadius: '50%', background: '#6366F1' }}></div> Inflow
                      </div>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                        <div style={{ width: '6px', height: '6px', borderRadius: '50%', background: '#E2E8F0' }}></div> Outflow
                      </div>
                    </div>
                  </div>
                  <div style={{ textAlign: 'right' }}>
                    <div style={{ fontSize: '0.65rem', color: '#94a3b8' }}>This Month</div>
                    <div style={{ fontSize: '1rem', fontWeight: '700', color: 'var(--lp-slate-dark)' }}>$24,850</div>
                    <div style={{ fontSize: '0.6rem', color: '#10b981' }}>+16.5%</div>
                  </div>
                </div>
                <div className="lp-mockup-line-chart" style={{ position: 'relative', height: '80px', marginTop: '15px' }}>
                  <svg width="100%" height="100%" viewBox="0 0 300 80" preserveAspectRatio="none" style={{ overflow: 'visible' }}>
                    <defs>
                      <linearGradient id="lineGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stopColor="#6366F1" />
                        <stop offset="100%" stopColor="#A855F7" />
                      </linearGradient>
                      <linearGradient id="areaGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stopColor="#6366F1" stopOpacity="0.15" />
                        <stop offset="100%" stopColor="#6366F1" stopOpacity="0" />
                      </linearGradient>
                    </defs>

                    {/* Grid lines */}
                    <line x1="0" y1="20" x2="300" y2="20" stroke="#E2E8F0" strokeWidth="1" strokeDasharray="4 4" vectorEffect="non-scaling-stroke" />
                    <line x1="0" y1="40" x2="300" y2="40" stroke="#E2E8F0" strokeWidth="1" strokeDasharray="4 4" vectorEffect="non-scaling-stroke" />
                    <line x1="0" y1="60" x2="300" y2="60" stroke="#E2E8F0" strokeWidth="1" strokeDasharray="4 4" vectorEffect="non-scaling-stroke" />

                    {/* Area Fill */}
                    <path d="M 0,55 C 30,55 45,25 90,25 C 135,25 150,45 200,45 C 250,45 270,15 300,15 L 300,80 L 0,80 Z" fill="url(#areaGrad)" />

                    {/* Smooth Line (Bezier curves) */}
                    <path d="M 0,55 C 30,55 45,25 90,25 C 135,25 150,45 200,45 C 250,45 270,15 300,15" fill="none" stroke="url(#lineGrad)" strokeWidth="2.5" strokeLinecap="round" vectorEffect="non-scaling-stroke" />

                    {/* Data Points */}
                    <circle cx="0" cy="55" r="3.5" fill="#FFFFFF" stroke="#6366F1" strokeWidth="2" vectorEffect="non-scaling-stroke" />
                    <circle cx="90" cy="25" r="3.5" fill="#FFFFFF" stroke="#6366F1" strokeWidth="2" vectorEffect="non-scaling-stroke" />
                    <circle cx="200" cy="45" r="3.5" fill="#FFFFFF" stroke="#6366F1" strokeWidth="2" vectorEffect="non-scaling-stroke" />
                    <circle cx="300" cy="15" r="3.5" fill="#FFFFFF" stroke="#6366F1" strokeWidth="2" vectorEffect="non-scaling-stroke" />

                    {/* X-axis Labels */}
                    <text x="0" y="75" fontSize="10" fill="#94a3b8" fontFamily="sans-serif">Week 1</text>
                    <text x="85" y="75" fontSize="10" fill="#94a3b8" fontFamily="sans-serif">Week 2</text>
                    <text x="185" y="75" fontSize="10" fill="#94a3b8" fontFamily="sans-serif">Week 3</text>
                    <text x="265" y="75" fontSize="10" fill="#94a3b8" fontFamily="sans-serif">Week 4</text>
                  </svg>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>

      {/* 3. Features Section */}
      <section id="features" className="lp-section reveal">
        <div className="lp-bg-dots"></div>
        <div className="lp-section-label">Powerful Modules</div>
        <h2 className="lp-section-title">Everything your business needs in one intelligent platform</h2>
        <div className="lp-features-grid">
          <div className="lp-feature-card f-blue">
            <div className="lp-feature-icon-wrapper">
              <FileText size={24} />
            </div>
            <h3 className="lp-feature-title">Accounting & Finance</h3>
            <p className="lp-feature-desc">
              Manage chart of accounts, ledger entries, balances, and all financial transactions.
            </p>
          </div>
          <div className="lp-feature-card f-emerald">
            <div className="lp-feature-icon-wrapper">
              <TrendingUp size={24} />
            </div>
            <h3 className="lp-feature-title">Sales Management</h3>
            <p className="lp-feature-desc">
              Create and track sales orders, manage customers, credit sales, cash sales, and receivables.
            </p>
          </div>
          <div className="lp-feature-card f-orange">
            <div className="lp-feature-icon-wrapper">
              <ShoppingCart size={24} />
            </div>
            <h3 className="lp-feature-title">Purchase Management</h3>
            <p className="lp-feature-desc">
              Manage suppliers, purchase orders, payments, credit purchases, and aged payables.
            </p>
          </div>
          <div className="lp-feature-card f-purple">
            <div className="lp-feature-icon-wrapper">
              <Package size={24} />
            </div>
            <h3 className="lp-feature-title">Inventory Management</h3>
            <p className="lp-feature-desc">
              Track inventory, item prices, stock levels, reorder levels, and inventory value in real-time.
            </p>
          </div>
          <div className="lp-feature-card f-cyan">
            <div className="lp-feature-icon-wrapper">
              <Clock size={24} />
            </div>
            <h3 className="lp-feature-title">Financial Reports</h3>
            <p className="lp-feature-desc">
              Generate detailed financial reports including balance sheet, P&L, cash flow, and more.
            </p>
          </div>
          <div className="lp-feature-card f-pink">
            <div className="lp-feature-icon-wrapper">
              <Users size={24} />
            </div>
            <h3 className="lp-feature-title">HR & User Management</h3>
            <p className="lp-feature-desc">
              Manage employees, departments, roles, users, and secure company access.
            </p>
          </div>
        </div>
      </section>

      {/* 4. Plans Section */}
      <section id="plans" className="lp-section reveal">
        <div className="lp-bg-dots"></div>
        <div className="lp-section-label">Flexible Versions</div>
        <h2 className="lp-section-title">Choose the right version for your business</h2>
        <div className="lp-plans-grid">

          <div className="lp-plan-card">
            <div className="lp-plan-header">
              <h3 className="lp-plan-title">Basic</h3>
              <p className="lp-plan-desc">Perfect for small businesses getting started with daily accounting and business records.</p>
            </div>
            <ul className="lp-plan-features">
              <li><CheckCircle2 size={16} /> Dashboard overview</li>
              <li><CheckCircle2 size={16} /> Chart of accounts</li>
              <li><CheckCircle2 size={16} /> Customer & supplier management</li>
              <li><CheckCircle2 size={16} /> Sales & purchase records</li>
              <li><CheckCircle2 size={16} /> Basic inventory</li>
              <li><CheckCircle2 size={16} /> Trial Balance & Ledger</li>
              <li><CheckCircle2 size={16} /> Balance Sheet & Income Statement</li>
              <li><CheckCircle2 size={16} /> Single company access</li>
            </ul>
            <Link to="/login" className="lp-plan-btn">Start with Basic</Link>
          </div>

          <div className="lp-plan-card recommended">
            <div className="lp-recommended-badge">Recommended</div>
            <div className="lp-plan-header">
              <h3 className="lp-plan-title">Professional</h3>
              <p className="lp-plan-desc">Ideal for growing businesses that need advanced financial visibility and better operational control.</p>
            </div>
            <ul className="lp-plan-features">
              <li><CheckCircle2 size={16} /> Everything in Basic</li>
              <li><CheckCircle2 size={16} /> Aged receivables & payables</li>
              <li><CheckCircle2 size={16} /> Fixed asset tracking</li>
              <li><CheckCircle2 size={16} /> Depreciation management</li>
              <li><CheckCircle2 size={16} /> Manufacturing cost tracking</li>
              <li><CheckCircle2 size={16} /> Inventory valuation</li>
              <li><CheckCircle2 size={16} /> Report printing</li>
              <li><CheckCircle2 size={16} /> Role-based access</li>
              <li><CheckCircle2 size={16} /> Advanced dashboard analytics</li>
            </ul>
            <Link to="/login" className="lp-plan-btn">Choose Professional</Link>
          </div>

          <div className="lp-plan-card">
            <div className="lp-plan-header">
              <h3 className="lp-plan-title">Enterprise</h3>
              <p className="lp-plan-desc">For larger organizations with advanced operations and custom business requirements.</p>
            </div>
            <ul className="lp-plan-features">
              <li><CheckCircle2 size={16} /> Everything in Professional</li>
              <li><CheckCircle2 size={16} /> Multi-branch management</li>
              <li><CheckCircle2 size={16} /> Multi-company support</li>
              <li><CheckCircle2 size={16} /> Approval workflows</li>
              <li><CheckCircle2 size={16} /> GRN management</li>
              <li><CheckCircle2 size={16} /> PDF & Excel export</li>
              <li><CheckCircle2 size={16} /> Bank reconciliation</li>
              <li><CheckCircle2 size={16} /> Audit logs & alerts</li>
              <li><CheckCircle2 size={16} /> API integrations</li>
              <li><CheckCircle2 size={16} /> Custom modules</li>
            </ul>
            <Link to="/login" className="lp-plan-btn">Contact Us</Link>
          </div>

        </div>
      </section>

      {/* 5. Why GINUM Section */}
      <section id="why-us" className="lp-why-section reveal">
        <div className="lp-why-container">
          <div className="lp-why-text">
            <h2>Why businesses choose GINUM</h2>
            <p>Experience a unified platform that brings all your business operations together with real-time data, accurate reporting, and secure access whenever you need it.</p>
          </div>
          <div className="lp-why-grid">
            <div className="lp-why-card" style={{ animationDelay: '0.1s' }}>
              <CheckCircle2 className="lp-why-card-icon" size={28} color="#10b981" />
              <p>Simple and<br />easy to use</p>
            </div>
            <div className="lp-why-card" style={{ animationDelay: '0.5s' }}>
              <ShieldCheck className="lp-why-card-icon" size={28} color="#10b981" />
              <p>Secure company-<br />based login</p>
            </div>
            <div className="lp-why-card" style={{ animationDelay: '0.3s' }}>
              <FolderOpen className="lp-why-card-icon" size={28} color="#3b82f6" />
              <p>Centralized<br />business data</p>
            </div>
            <div className="lp-why-card" style={{ animationDelay: '0.7s' }}>
              <Scale className="lp-why-card-icon" size={28} color="#f97316" />
              <p>Real-time account<br />balances</p>
            </div>
            <div className="lp-why-card" style={{ animationDelay: '0.2s' }}>
              <FileText className="lp-why-card-icon" size={28} color="#f97316" />
              <p>Accurate financial<br />reports</p>
            </div>
            <div className="lp-why-card" style={{ animationDelay: '0.6s' }}>
              <Box className="lp-why-card-icon" size={28} color="#ec4899" />
              <p>Inventory connected<br />with accounting</p>
            </div>
            <div className="lp-why-card">
              <Globe className="lp-why-card-icon" size={28} color="#8b5cf6" />
              <p>Web-based access<br />from anywhere</p>
            </div>
            <div className="lp-why-card">
              <Grid className="lp-why-card-icon" size={28} color="#f43f5e" />
              <p>Expandable for future<br />business features</p>
            </div>
          </div>
        </div>
      </section>

      {/* 6. Final CTA Section */}
      <section className="lp-cta reveal">
        <div className="lp-cta-blob lp-cta-blob-1"></div>
        <div className="lp-cta-blob lp-cta-blob-2"></div>
        <div className="lp-cta-container">
          <div className="lp-cta-content">
            <h2>Ready to manage your<br />business smarter?</h2>
            <p>
              Start using GINUM to simplify accounting, operations,<br />inventory, and financial reporting.
            </p>
            <div className="lp-cta-buttons">
              <Link to="/login" className="lp-cta-btn-primary">Get Started</Link>
              <a href="#footer" onClick={(e) => scrollToSection(e, 'footer')} className="lp-cta-btn-secondary">Contact Us</a>
            </div>
          </div>
          <div className="lp-cta-visual">
            <div className="lp-ag-cluster">
              <div className="lp-ag-sphere lp-ag-sphere-1"></div>
              <div className="lp-ag-sphere lp-ag-sphere-2"></div>

              <div className="lp-ag-card lp-ag-card-1">
                <Lock size={32} color="#22d3ee" className="lp-ag-icon-glow-cyan" />
              </div>

              <div className="lp-ag-card lp-ag-card-2">
                <div className="lp-ag-barchart">
                  <div className="lp-ag-bar" style={{ height: '40%' }}></div>
                  <div className="lp-ag-bar" style={{ height: '70%' }}></div>
                  <div className="lp-ag-bar" style={{ height: '50%' }}></div>
                  <div className="lp-ag-bar" style={{ height: '90%' }}></div>
                </div>
              </div>

              <div className="lp-ag-card lp-ag-card-3">
                <Network size={28} color="#a855f7" className="lp-ag-icon-glow-purple" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 7. Footer */}
      <footer id="footer" className="lp-footer">
        <div className="lp-footer-content">
          <div className="lp-footer-brand">
            <Link to="/" className="lp-footer-logo">
              <div className="lp-footer-logo-icon">G</div>
              GINUM
            </Link>
            <p>Smart Business & Accounting Platform for Growing Businesses.</p>
          </div>
          <div className="lp-footer-col">
            <h4>Product</h4>
            <a href="#features" onClick={(e) => scrollToSection(e, 'features')}>Features</a>
            <a href="#plans" onClick={(e) => scrollToSection(e, 'plans')}>Plans</a>
            <a href="#why-us" onClick={(e) => scrollToSection(e, 'why-us')}>Why GINUM</a>
          </div>
          <div className="lp-footer-col">
            <h4>Support</h4>
            <Link to="/login">Login</Link>
            <a href="mailto:contact@ginum.com">Contact</a>
          </div>
          <div className="lp-footer-col" style={{ position: 'relative' }}>
            <div className="lp-footer-ag-decor">
              <div className="lp-footer-ag-orb lp-footer-ag-orb-1"></div>
              <div className="lp-footer-ag-orb lp-footer-ag-orb-2"></div>
              <div className="lp-footer-ag-orb lp-footer-ag-orb-3"></div>
            </div>
          </div>
        </div>
        <div className="lp-footer-bottom">
          &copy; {new Date().getFullYear()} GINUM. All rights reserved.
        </div>
      </footer>
    </div>
  );
};

export default LandingPage;
