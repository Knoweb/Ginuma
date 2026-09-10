package com.example.GinumApps.model;

import jakarta.persistence.*;

@Entity
@Table(name = "custom_roles")
public class Role {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "company_id")
    private Long companyId;

    @Column(name = "role_name", nullable = false)
    private String roleName;

    @Column(name = "description")
    private String description;

    @Column(name = "otp_required", nullable = false)
    private Boolean otpRequired = false;

    @Column(name = "permissions", columnDefinition = "TEXT")
    private String permissions; // CSV of permissions: e.g. "INVOICE_VIEW,INVOICE_CREATE,REPORT_VIEW"

    public Role() {}

    public Role(Long companyId, String roleName, String description, Boolean otpRequired, String permissions) {
        this.companyId = companyId;
        this.roleName = roleName;
        this.description = description;
        this.otpRequired = otpRequired != null ? otpRequired : false;
        this.permissions = permissions;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Long getCompanyId() {
        return companyId;
    }

    public void setCompanyId(Long companyId) {
        this.companyId = companyId;
    }

    public String getRoleName() {
        return roleName;
    }

    public void setRoleName(String roleName) {
        this.roleName = roleName;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public Boolean getOtpRequired() {
        return otpRequired;
    }

    public void setOtpRequired(Boolean otpRequired) {
        this.otpRequired = otpRequired != null ? otpRequired : false;
    }

    public String getPermissions() {
        return permissions;
    }

    public void setPermissions(String permissions) {
        this.permissions = permissions;
    }
}
