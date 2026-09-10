package com.example.GinumApps.service;

import com.example.GinumApps.model.Role;
import com.example.GinumApps.repository.RoleRepository;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

@Service
public class RoleService {

    private final RoleRepository roleRepository;

    public RoleService(RoleRepository roleRepository) {
        this.roleRepository = roleRepository;
    }

    public void createDefaultRolesForCompany(Long companyId) {
        if (companyId == null) return;

        // Default Admin Role
        if (roleRepository.findByCompanyIdAndRoleName(companyId, "COMPANY_ADMIN").isEmpty()) {
            roleRepository.save(new Role(companyId, "COMPANY_ADMIN", "Full access to all company settings and features", false, "ALL"));
        }

        // Accountant Role
        if (roleRepository.findByCompanyIdAndRoleName(companyId, "ACCOUNTANT").isEmpty()) {
            roleRepository.save(new Role(companyId, "ACCOUNTANT", "Access to Invoices, Bills, Transactions and Financial Reports", true, "INVOICES_VIEW,INVOICES_CREATE,BILL_VIEW,REPORTS_VIEW,BANK_VIEW"));
        }

        // Sales Role
        if (roleRepository.findByCompanyIdAndRoleName(companyId, "SALES").isEmpty()) {
            roleRepository.save(new Role(companyId, "SALES", "Access to Sales Orders, Customers and Items", false, "SALES_VIEW,SALES_CREATE,CUSTOMER_VIEW,ITEM_VIEW"));
        }

        // Viewer Role
        if (roleRepository.findByCompanyIdAndRoleName(companyId, "VIEWER").isEmpty()) {
            roleRepository.save(new Role(companyId, "VIEWER", "Read-only access to company dashboards and records", false, "VIEW_ONLY"));
        }
    }

    public List<Role> getRolesByCompany(Long companyId) {
        List<Role> roles = roleRepository.findByCompanyId(companyId);
        if (roles.isEmpty() && companyId != null) {
            createDefaultRolesForCompany(companyId);
            roles = roleRepository.findByCompanyId(companyId);
        }
        return roles;
    }

    public Role saveRole(Role role) {
        return roleRepository.save(role);
    }

    public Optional<Role> getRoleById(Long id) {
        return roleRepository.findById(id);
    }

    public Optional<Role> findByCompanyAndName(Long companyId, String roleName) {
        return roleRepository.findByCompanyIdAndRoleName(companyId, roleName);
    }

    public Role toggleOtpRequired(Long roleId, Boolean otpRequired) {
        Role role = roleRepository.findById(roleId)
                .orElseThrow(() -> new RuntimeException("Role not found with ID: " + roleId));
        role.setOtpRequired(otpRequired != null ? otpRequired : !role.getOtpRequired());
        return roleRepository.save(role);
    }

    public void deleteRole(Long roleId) {
        roleRepository.deleteById(roleId);
    }
}
