import React from "react";
import { Navigate, Outlet } from "react-router-dom";

// PrivateRoute component to protect authenticated routes
const PrivateRoute = ({ children }) => {
  // Check if the user is authenticated via isLoggedIn cookie
  const getCookie = (name) => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
  };

  const isAuthenticated = getCookie("isLoggedIn") === "true";

  // If the user is not authenticated, redirect to login
  if (!isAuthenticated) {
    return <Navigate to="/login" />;
  }

  return children ? children : <Outlet />;
};

export default PrivateRoute;
