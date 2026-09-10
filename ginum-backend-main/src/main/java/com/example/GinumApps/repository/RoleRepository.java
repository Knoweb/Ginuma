package com.example.GinumApps.repository;

import com.example.GinumApps.model.Role;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface RoleRepository extends JpaRepository<Role, Long> {
    List<Role> findByCompanyId(Long companyId);
    Optional<Role> findByCompanyIdAndRoleName(Long companyId, String roleName);
    Optional<Role> findByRoleName(String roleName);
}
