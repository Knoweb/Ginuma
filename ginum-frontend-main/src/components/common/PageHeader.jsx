import React from 'react';
import { useNavigate } from 'react-router-dom';

const PageHeader = ({ title, subtitle, icon: Icon, actions, onBack, backText = "Back" }) => {
  const navigate = useNavigate();

  return (
    <div className="flex items-start justify-between gap-6 mb-6">
      <div className="flex-1 min-w-0 flex items-start gap-4">
        {onBack && (
          <button
            onClick={typeof onBack === 'function' ? onBack : () => navigate(onBack)}
            className="flex items-center justify-center w-10 h-10 mt-1 rounded-full hover:bg-slate-200/50 text-slate-500 transition-colors shrink-0"
            title={backText}
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
          </button>
        )}
        {Icon && (
          <div className="flex items-center justify-center w-12 h-12 mt-0.5 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100/50 shadow-sm shrink-0">
            <Icon size={24} />
          </div>
        )}
        <div className="min-w-0">
          <h1 className="gl-heading truncate whitespace-normal">{title}</h1>
          {subtitle && <p className="gl-subheading mt-1 max-w-2xl">{subtitle}</p>}
        </div>
      </div>
      
      {actions && (
        <div className="flex items-center justify-end gap-2 flex-wrap flex-none w-auto">
          {actions}
        </div>
      )}
    </div>
  );
};

export default PageHeader;
