import React from "react";
import { Navigate, Outlet } from "react-router-dom";

// PublicRoute component to restrict access to authenticated users
const PublicRoute = ({ children }) => {
  // Check if the user is already authenticated
  const getCookie = (name) => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
  };

  const isAuthenticated = getCookie("isLoggedIn") === "true";

  // Redirect authenticated users to the dashboard
  if (isAuthenticated) {
    return <Navigate to="/dashboard" />;
  }

  return children ? children : <Outlet />;
};

export default PublicRoute;
