import { useState, useEffect } from "react";
import Alert from "../components/Alert/Alert";
import { Outlet, useLocation } from "react-router-dom";
import Header from "../components/Topbar/Header";
import Sidebar from "../components/Sidebar/Sidebar";
import TabHeader from "../components/TabHeader/TabHeader";

const MainLayout = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const location = useLocation();

  const toggleSidebar = () => setSidebarOpen(!sidebarOpen);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.target && e.target.tagName === 'INPUT' && e.target.type === 'number') {
        const key = e.key;
        // If it's a single character key (not Backspace/Arrow/Tab etc)
        if (key.length === 1) {
          // Block if it's not a digit, dot, or minus sign
          if (!/[0-9.\-]/.test(key)) {
            e.preventDefault();
            Alert.error(`"${key}" is not allowed. Please enter only numbers.`);
          }
        }
      }
      
      if (e.key === "Escape") {
        setSidebarOpen(false);
      }
    };

    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, []);

  return (
    <div className="flex h-screen w-full bg-[#f8fafc] text-slate-800 font-sans">
      {/* Backdrop for mobile */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 bg-slate-900/40 z-40 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Sidebar */}
      <Sidebar isVisible={sidebarOpen} onClose={() => setSidebarOpen(false)} />

      {/* Main Content */}
      <div
        className="flex flex-col flex-1 h-full min-w-0 transition-all duration-300 ml-0 lg:ml-[260px]"
      >
        {/* Header with Fixed Height */}
        <Header
          toggleSidebar={toggleSidebar}
          isSidebarVisible={sidebarOpen}
        />

        {/* Tab Header */}
        <TabHeader pathname={location.pathname} />

        {/* Main Content Area */}
        <main className="flex-1 bg-[#f8fafc] w-full overflow-auto mt-[4rem]">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default MainLayout;
