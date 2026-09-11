import { useState, useEffect } from "react";
import Alert from "../components/Alert/Alert";
import { Outlet, useLocation } from "react-router-dom";
import Header from "../components/Topbar/Header";
import Sidebar from "../components/Sidebar/Sidebar";
import TabHeader from "../components/TabHeader/TabHeader";

const MainLayout = () => {
  const [isSidebarVisible, setIsSidebarVisible] = useState(true);
  const location = useLocation();

  const toggleSidebar = () => setIsSidebarVisible(!isSidebarVisible);

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
    };

    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, []);

  return (
    <div className="flex h-screen w-full bg-[#f8fafc] text-slate-800 font-sans">
      {/* Sidebar */}
      <Sidebar isVisible={isSidebarVisible} />

      {/* Main Content */}
      <div
        className={`flex flex-col flex-1 h-full min-w-0 transition-all duration-300 ${
          isSidebarVisible ? "lg:ml-72 ml-0" : "md:ml-1 ml-0"
        }`}
      >
        {/* Header with Fixed Height */}
        <Header
          toggleSidebar={toggleSidebar}
          isSidebarVisible={isSidebarVisible}
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
