package com.example.GinumApps.service;

import com.example.GinumApps.dto.AppUserRequestDto;
import com.example.GinumApps.dto.ChangePasswordRequest;
import com.example.GinumApps.dto.UserProfileDto;
import com.example.GinumApps.model.Admin;
import com.example.GinumApps.model.AppUser;
import com.example.GinumApps.model.Company;
import com.example.GinumApps.model.Employee;
import com.example.GinumApps.repository.AdminRepository;
import com.example.GinumApps.repository.AppUserRepository;
import com.example.GinumApps.repository.CompanyRepository;
import com.example.GinumApps.repository.EmployeeRepository;
import jakarta.transaction.Transactional;
import lombok.RequiredArgsConstructor;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.web.bind.annotation.PathVariable;

import java.util.List;
import java.util.Optional;

@Service
@RequiredArgsConstructor
public class AppUserService {

    private final AppUserRepository appUserRepository;
    private final CompanyRepository companyRepository;
    private final EmployeeRepository employeeRepository;
    private final AdminRepository adminRepository;
    private final PasswordEncoder passwordEncoder;

    @Transactional
    public AppUser createUser(Integer companyId, AppUserRequestDto request) {
        Company company = companyRepository.findById(companyId)
                .orElseThrow(() -> new RuntimeException("Company not found"));

        if (appUserRepository.findByEmail(request.getEmail()).isPresent()) {
            throw new RuntimeException("Email already registered as a user");
        }

        if (request.getPassword() == null || request.getPassword().trim().isEmpty()) {
            throw new RuntimeException("Password is required for new users");
        }
        if (request.getPassword().length() < 6) {
            throw new RuntimeException("Password must be at least 6 characters");
        }

        String cleanRole = request.getRole() != null ? request.getRole().replace("ROLE_", "").toUpperCase() : "USER";

        AppUser newUser = new AppUser();
        newUser.setEmail(request.getEmail());
        newUser.setPassword(passwordEncoder.encode(request.getPassword()));
        newUser.setRole(cleanRole);
        newUser.setCompany(company);

        // Auto-create Employee if record does not exist
        // Note: Disabled because Employee table has non-null constraints (address, designation, etc.)
        // which cause DataIntegrityViolationException and rollback the entire user creation.
        // The system already handles cases where an AppUser does not have a matching Employee.

        return appUserRepository.save(newUser);
    }

    public List<AppUser> getUsersByCompany(Integer companyId) {
        return appUserRepository.findAllByCompanyCompanyId(companyId);
    }

    @Transactional
    public AppUser assignUser(Integer companyId, AppUser user) {
        Optional<AppUser> existingUser = appUserRepository.findByEmail(user.getEmail());
        String cleanRole = user.getRole() != null ? user.getRole().replace("ROLE_", "").toUpperCase() : "USER";

        AppUser savedUser;
        if (existingUser.isPresent()) {
            AppUser userToUpdate = existingUser.get();
            userToUpdate.setRole(cleanRole);
            if (user.getPassword() != null && !user.getPassword().isEmpty()) {
                userToUpdate.setPassword(passwordEncoder.encode(user.getPassword()));
            }
            savedUser = appUserRepository.save(userToUpdate);
        } else {
            Company company = companyRepository.findById(companyId)
                    .orElseThrow(() -> new RuntimeException("Company not found"));
            
            AppUser newUser = new AppUser();
            newUser.setEmail(user.getEmail());
            if (user.getPassword() != null && !user.getPassword().isEmpty()) {
                newUser.setPassword(passwordEncoder.encode(user.getPassword()));
            }
            newUser.setRole(cleanRole);
            newUser.setCompany(company);
            savedUser = appUserRepository.save(newUser);
        }

        // Auto-create Employee if record does not exist
        Optional<Employee> optEmp = employeeRepository.findByEmailAndCompanyCompanyId(user.getEmail(), companyId);
        if (optEmp.isEmpty()) {
            Company company = companyRepository.findById(companyId).orElse(null);
            if (company != null) {
                Employee newEmp = new Employee();
                newEmp.setEmail(user.getEmail());
                newEmp.setCompany(company);
                newEmp.setFirstName(user.getEmail().split("@")[0]);
                newEmp.setLastName("");
                newEmp.setDateAdded(java.time.LocalDate.now());
                employeeRepository.save(newEmp);
            }
        }

        return savedUser;
    }

    @Transactional
    public void deleteUser(Integer userId) {
        appUserRepository.deleteById(userId);
    }

    public UserProfileDto getUserProfile(String email) {
        // Try finding as Company
        Optional<Company> optCompany = companyRepository.findByEmail(email);
        if (optCompany.isPresent()) {
            Company company = optCompany.get();
            UserProfileDto dto = new UserProfileDto();
            dto.setEmail(company.getEmail());
            dto.setName(company.getCompanyName());
            dto.setPhone(company.getPhoneNo() != null ? company.getPhoneNo() : company.getMobileNo());
            dto.setAddress(company.getCompanyRegisteredAddress());
            dto.setRole("ROLE_COMPANY");
            dto.setStatus(company.getStatus() ? "Active" : "Inactive");
            dto.setJoinedDate(company.getDateJoined() != null ? company.getDateJoined().toString() : "");
            return dto;
        }

        // Try finding as AppUser / Employee
        Optional<AppUser> optUser = appUserRepository.findByEmail(email);
        if (optUser.isPresent()) {
            AppUser user = optUser.get();
            UserProfileDto dto = new UserProfileDto();
            dto.setEmail(user.getEmail());
            dto.setRole(user.getRole());
            dto.setStatus("Active");
            
            if (user.getCompany() != null) {
                dto.setCompanyName(user.getCompany().getCompanyName());
                Optional<Employee> optEmployee = employeeRepository.findByEmailAndCompanyCompanyId(
                        user.getEmail(),
                        user.getCompany().getCompanyId()
                );
                if (optEmployee.isPresent()) {
                    Employee employee = optEmployee.get();
                    dto.setName(employee.getFirstName() + " " + employee.getLastName());
                    dto.setPhone(employee.getMobileNo());
                    dto.setAddress(employee.getAddress());
                    dto.setJoinedDate(employee.getDateAdded() != null ? employee.getDateAdded().toString() : "");
                    if (employee.getDesignation() != null) {
                        dto.setDesignation(employee.getDesignation().getName());
                    }
                    if (employee.getDepartment() != null) {
                        dto.setDepartment(employee.getDepartment().getName());
                    }
                } else {
                    dto.setName(user.getEmail().split("@")[0]);
                }
            } else {
                dto.setName(user.getEmail().split("@")[0]);
            }
            return dto;
        }

        // Try finding as Super Admin
        Optional<Admin> optAdmin = adminRepository.findByEmail(email);
        if (optAdmin.isPresent()) {
            Admin admin = optAdmin.get();
            UserProfileDto dto = new UserProfileDto();
            dto.setEmail(admin.getEmail());
            dto.setName("Super Admin");
            dto.setRole("ROLE_SUPER_ADMIN");
            dto.setStatus("Active");
            return dto;
        }

        throw new RuntimeException("Profile not found");
    }

    @Transactional
    public UserProfileDto updateUserProfile(String email, UserProfileDto requestDto) {
        // Try finding as Company
        Optional<Company> optCompany = companyRepository.findByEmail(email);
        if (optCompany.isPresent()) {
            Company company = optCompany.get();
            if (requestDto.getName() != null && !requestDto.getName().trim().isEmpty()) {
                company.setCompanyName(requestDto.getName());
            }
            company.setPhoneNo(requestDto.getPhone());
            company.setCompanyRegisteredAddress(requestDto.getAddress());
            companyRepository.save(company);
            return getUserProfile(email);
        }

        // Try finding as AppUser / Employee
        Optional<AppUser> optUser = appUserRepository.findByEmail(email);
        if (optUser.isPresent()) {
            AppUser user = optUser.get();
            if (user.getCompany() != null) {
                Optional<Employee> optEmployee = employeeRepository.findByEmailAndCompanyCompanyId(
                        user.getEmail(),
                        user.getCompany().getCompanyId()
                );
                if (optEmployee.isPresent()) {
                    Employee employee = optEmployee.get();
                    if (requestDto.getName() != null && !requestDto.getName().trim().isEmpty()) {
                        String[] parts = requestDto.getName().trim().split("\\s+", 2);
                        employee.setFirstName(parts[0]);
                        employee.setLastName(parts.length > 1 ? parts[1] : "");
                    }
                    employee.setMobileNo(requestDto.getPhone());
                    employee.setAddress(requestDto.getAddress());
                    employeeRepository.save(employee);
                }
            }
            return getUserProfile(email);
        }

        throw new RuntimeException("Profile update failed: User not found");
    }

    @Transactional
    public void changePassword(String email, ChangePasswordRequest requestDto) {
        // Try finding as Company
        Optional<Company> optCompany = companyRepository.findByEmail(email);
        if (optCompany.isPresent()) {
            Company company = optCompany.get();
            if (!passwordEncoder.matches(requestDto.getOldPassword(), company.getPassword())) {
                throw new IllegalArgumentException("Incorrect current password");
            }
            company.setPassword(passwordEncoder.encode(requestDto.getNewPassword()));
            companyRepository.save(company);
            return;
        }

        // Try finding as AppUser
        Optional<AppUser> optUser = appUserRepository.findByEmail(email);
        if (optUser.isPresent()) {
            AppUser user = optUser.get();
            if (!passwordEncoder.matches(requestDto.getOldPassword(), user.getPassword())) {
                throw new IllegalArgumentException("Incorrect current password");
            }
            user.setPassword(passwordEncoder.encode(requestDto.getNewPassword()));
            appUserRepository.save(user);
            return;
        }

        // Try finding as Admin
        Optional<Admin> optAdmin = adminRepository.findByEmail(email);
        if (optAdmin.isPresent()) {
            Admin admin = optAdmin.get();
            if (!passwordEncoder.matches(requestDto.getOldPassword(), admin.getPassword())) {
                throw new IllegalArgumentException("Incorrect current password");
            }
            admin.setPassword(passwordEncoder.encode(requestDto.getNewPassword()));
            adminRepository.save(admin);
            return;
        }

        throw new RuntimeException("Change password failed: User not found");
    }
}