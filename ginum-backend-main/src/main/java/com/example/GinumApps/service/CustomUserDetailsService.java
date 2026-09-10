package com.example.GinumApps.service;

import com.example.GinumApps.model.Admin;
import com.example.GinumApps.model.AppUser;
import com.example.GinumApps.model.Company;
import com.example.GinumApps.repository.AdminRepository;
import com.example.GinumApps.repository.CompanyRepository;
import com.example.GinumApps.repository.AppUserRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

import java.util.Optional;

@Service
@RequiredArgsConstructor
public class CustomUserDetailsService implements UserDetailsService {

    private final AdminRepository adminRepository;
    private final CompanyRepository companyRepository;
    private final AppUserRepository userRepository;

    @Override
    public UserDetails loadUserByUsername(String email) throws UsernameNotFoundException {
        if (email == null || email.trim().isEmpty()) {
            throw new UsernameNotFoundException("Email cannot be empty");
        }
        String cleanEmail = email.trim();

        // 1. Check Super Admin
        Optional<Admin> admin = adminRepository.findByEmail(cleanEmail);
        if (admin.isPresent()) return createUserDetails(admin.get());

        // 2. Check AppUser (Role / Employee user)
        Optional<AppUser> user = userRepository.findByEmailIgnoreCase(cleanEmail);
        if (user.isPresent()) return createUserDetails(user.get());

        // 3. Check Company Admin
        Optional<Company> company = companyRepository.findByEmailIgnoreCase(cleanEmail);
        if (company.isPresent()) return createUserDetails(company.get());

        throw new UsernameNotFoundException("User not found with email: " + email);
    }

    private UserDetails createUserDetails(Admin admin) {
        return User.builder()
                .username(admin.getEmail())
                .password(admin.getPassword())
                .roles(admin.getRole().replace("ROLE_", ""))
                .build();
    }

    private UserDetails createUserDetails(Company company) {
        return User.builder()
                .username(company.getEmail())
                .password(company.getPassword())
                .roles(company.getRole().replace("ROLE_", ""))
                .build();
    }

    private UserDetails createUserDetails(AppUser user) {
        return User.builder()
                .username(user.getEmail())
                .password(user.getPassword())
                .roles(user.getRole() != null ? user.getRole().replace("ROLE_", "") : "USER")
                .build();
    }
}