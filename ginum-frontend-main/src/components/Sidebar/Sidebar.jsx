import { useLocation, useNavigate } from "react-router-dom";
import { navItems } from "../../config/navigation";
import { FaChevronDown, FaChevronUp } from "react-icons/fa";
import { useState, useEffect, useRef } from "react";

const Sidebar = ({ isVisible }) => {
  const location = useLocation();
  const navigate = useNavigate();
  const [expandedTabs, setExpandedTabs] = useState(() => {
    const currentMainPath = location.pathname.split("/")[1];
    return new Set([currentMainPath]);
  });

  const activeItemRef = useRef(null);

  const toggleExpanded = (tabId, hasSubItems) => {
    if (!hasSubItems) return; // Prevent expanding if there are no sub-items

    setExpandedTabs((prev) => {
      const newSet = new Set(prev);
      if (newSet.has(tabId)) {
        newSet.delete(tabId);
      } else {
        newSet.add(tabId);
      }
      return newSet;
    });
  };

  const handleNavigation = (path) => {
    navigate(path);
  };

  // Scroll to the active item when the location changes
  useEffect(() => {
    if (activeItemRef.current) {
      activeItemRef.current.scrollIntoView({
        behavior: "smooth",
        block: "center",
      });
    }
  }, [location.pathname]);

  // Filter navItems based on permissions
  const userPermissionsStr = sessionStorage.getItem("permissions");
  let userPermissions = [];
  try {
    userPermissions = userPermissionsStr ? JSON.parse(userPermissionsStr) : [];
  } catch(e) {}
  
  const hasAccess = (item) => {
    if (userPermissions.includes("*") || userPermissions.includes("ALL") || userPermissions.includes("VIEW_ONLY")) return true; 
    if (!item.permissions || item.permissions.length === 0) return true; // Items without permission requirement
    return item.permissions.some(requiredPerm => 
      userPermissions.some(userPerm => userPerm.includes(requiredPerm))
    );
  };

  const filteredNavItems = navItems.map(item => {
    if (item.sectionTitle) return item; // We'll clean up empty sections later
    
    // Check if top-level has access
    if (!hasAccess(item)) return null;

    // Filter subItems if user is VIEW_ONLY
    const isViewOnly = userPermissions.includes("VIEW_ONLY") && !userPermissions.includes("ALL") && !userPermissions.includes("*");
    
    let filteredSubItems = item.subItems;
    if (isViewOnly && item.subItems) {
      filteredSubItems = item.subItems.filter(subItem => {
        const lowerId = subItem.id.toLowerCase();
        const lowerPath = subItem.path.toLowerCase();
        // Hide anything with 'new', 'create', 'edit' for view-only users
        if (lowerId.includes("new") || lowerId.includes("create") || lowerId.includes("edit")) return false;
        if (lowerPath.includes("new") || lowerPath.includes("create") || lowerPath.includes("edit")) return false;
        return true;
      });
    }

    return { ...item, subItems: filteredSubItems };
  }).filter(Boolean);
  
  // Clean up empty section titles
  const finalNavItems = [];
  let currentSection = null;
  filteredNavItems.forEach(item => {
    if (item.sectionTitle) {
      currentSection = item;
    } else {
      if (currentSection) {
        finalNavItems.push(currentSection);
        currentSection = null;
      }
      finalNavItems.push(item);
    }
  });

  return (
    <div
      className={`bg-white border-r border-slate-200/60 min-h-screen w-[260px] fixed left-0 top-0 
            transition-transform duration-500 ${
              isVisible ? "translate-x-0" : "-translate-x-full"
            } max-h-screen overflow-y-auto z-50`}
    >
      <div className="sticky top-0 bg-white/80 backdrop-blur-md z-10 py-5 border-b border-slate-100 mb-2">
        <a href="/">
          <div className="flex items-center justify-center">
            <img src="/ginum_logo.png" alt="Ginum" className="w-101 h-12" />
          </div>
        </a>
      </div>

      <nav className="mt-6 mb-10">
        <ul className="space-y-4">
          {finalNavItems.map((item) => {
            const hasSubItems = item.subItems && item.subItems.length > 0;
            // Render section titles
            if (item.sectionTitle) {
              return (
                <h4
                  key={item.sectionTitle}
                  className="px-4 text-slate-400 font-bold text-[10px] uppercase tracking-wider mt-6 mb-2"
                >
                  {item.sectionTitle}
                </h4>
              );
            }
            return (
              <div key={item.id} className="px-3">

                <li>
                  <button
                    ref={
                      location.pathname.startsWith(item.path)
                        ? activeItemRef
                        : null
                    }
                    onClick={() => {
                      handleNavigation(item.path);
                      toggleExpanded(item.id, hasSubItems);
                    }}
                    className={`w-full flex items-center justify-between px-3 h-[42px] 
                                      rounded-[10px] text-slate-600 hover:bg-indigo-50/50 hover:text-indigo-600 transition-colors cursor-pointer ${
                                        location.pathname.startsWith(item.path)
                                          ? "bg-indigo-50 text-indigo-700 font-bold shadow-sm"
                                          : "font-medium"
                                      }`}
                  >
                    <div className="flex items-center">
                      <item.icon className="text-lg mr-4" />
                      <span className="text-sm">{item.label}</span>
                    </div>
                    {hasSubItems && (
                      <div
                        className={`transform transition-transform duration-500 ease-in-out ${
                          expandedTabs.has(item.id) ? "rotate-180" : "rotate-0"
                        }`}
                      >
                        {expandedTabs.has(item.id) ? (
                          <FaChevronUp className="text-sm" />
                        ) : (
                          <FaChevronDown className="text-sm" />
                        )}
                      </div>
                    )}
                  </button>

                  {/* Sub-items */}
                  {hasSubItems && (
                    <ul
                      className={`ml-9 mt-1 space-y-1 overflow-hidden transition-all duration-500 ease-in-out`}
                      style={{
                        maxHeight: expandedTabs.has(item.id) ? "1000px" : "0",
                        opacity: expandedTabs.has(item.id) ? "1" : "0",
                        paddingBottom: expandedTabs.has(item.id) ? "8px" : "0",
                        paddingTop: expandedTabs.has(item.id) ? "4px" : "0",
                      }}
                    >
                      {expandedTabs.has(item.id) &&
                        item.subItems.map((subItem) => (
                          <li key={subItem.id}>
                            <button
                              ref={
                                location.pathname === subItem.path
                                  ? activeItemRef
                                  : null
                              }
                              onClick={() => handleNavigation(subItem.path)}
                              className={`w-full text-left px-4 h-[38px] flex items-center rounded-[8px] text-[13.5px] transition-colors
                                                        text-slate-500 hover:bg-slate-50 hover:text-indigo-600 ${
                                                          location.pathname ===
                                                          subItem.path
                                                            ? "bg-slate-50 text-indigo-600 font-semibold"
                                                            : ""
                                                        }`}
                            >
                              <span>
                                {subItem.label}
                              </span>
                            </button>
                          </li>
                        ))}
                    </ul>
                  )}
                </li>
              </div>
            );
          })}
        </ul>
      </nav>
    </div>
  );
};

export default Sidebar;
