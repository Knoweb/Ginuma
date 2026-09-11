import React from 'react';
import { useNavigate } from 'react-router-dom';

const PageHeader = ({ title, subtitle, icon: Icon, actions, onBack, backText = "Back" }) => {
  const navigate = useNavigate();

  return (
    <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
      <div className="flex items-center gap-3">
        {onBack && (
          <button
            onClick={typeof onBack === 'function' ? onBack : () => navigate(onBack)}
            className="flex items-center justify-center w-10 h-10 rounded-full hover:bg-slate-200/50 text-slate-500 transition-colors"
            title={backText}
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
          </button>
        )}
        {Icon && (
          <div className="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100/50 shadow-sm shrink-0">
            <Icon size={24} />
          </div>
        )}
        <div>
          <h1 className="gl-heading">{title}</h1>
          {subtitle && <p className="gl-subheading mt-1">{subtitle}</p>}
        </div>
      </div>
      
      {actions && (
        <div className="flex flex-wrap items-center gap-3 shrink-0">
          {actions}
        </div>
      )}
    </div>
  );
};

export default PageHeader;
