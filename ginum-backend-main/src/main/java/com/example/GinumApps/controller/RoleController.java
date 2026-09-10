package com.example.GinumApps.controller;

import com.example.GinumApps.model.Role;
import com.example.GinumApps.service.RoleService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/roles")
@CrossOrigin(origins = "*")
public class RoleController {

    private final RoleService roleService;

    public RoleController(RoleService roleService) {
        this.roleService = roleService;
    }

    @GetMapping
    public ResponseEntity<List<Role>> getRoles(@RequestParam(value = "companyId", required = false, defaultValue = "1") Long companyId) {
        List<Role> roles = roleService.getRolesByCompany(companyId);
        return ResponseEntity.ok(roles);
    }

    @PostMapping
    public ResponseEntity<Role> createRole(@RequestBody Role role) {
        if (role.getCompanyId() == null) {
            role.setCompanyId(1L);
        }
        Role created = roleService.saveRole(role);
        return ResponseEntity.ok(created);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Role> updateRole(@PathVariable Long id, @RequestBody Role roleDetails) {
        Role role = roleService.getRoleById(id)
                .orElseThrow(() -> new RuntimeException("Role not found with ID: " + id));

        if (roleDetails.getRoleName() != null) role.setRoleName(roleDetails.getRoleName());
        if (roleDetails.getDescription() != null) role.setDescription(roleDetails.getDescription());
        if (roleDetails.getOtpRequired() != null) role.setOtpRequired(roleDetails.getOtpRequired());
        if (roleDetails.getPermissions() != null) role.setPermissions(roleDetails.getPermissions());

        Role updated = roleService.saveRole(role);
        return ResponseEntity.ok(updated);
    }

    @PutMapping("/{id}/toggle-otp")
    public ResponseEntity<Role> toggleOtp(@PathVariable Long id, @RequestBody(required = false) Map<String, Boolean> body) {
        Boolean otpRequired = (body != null && body.containsKey("otpRequired")) ? body.get("otpRequired") : null;
        Role updated = roleService.toggleOtpRequired(id, otpRequired);
        return ResponseEntity.ok(updated);
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Map<String, String>> deleteRole(@PathVariable Long id) {
        roleService.deleteRole(id);
        return ResponseEntity.ok(Map.of("message", "Role deleted successfully"));
    }
}
