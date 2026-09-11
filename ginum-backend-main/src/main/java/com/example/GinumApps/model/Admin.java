package com.example.GinumApps.model;

import jakarta.persistence.*;
import lombok.Data;

@Data
@Entity
@Table(name = "admin_table")
public class Admin {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Integer id;

    @Column( nullable = false)
    private String admin_name;

    @Column(unique = true, nullable = false)
    private String email;

    @Column(nullable = false)
    private String password;

    @Column(nullable = false)
    private String role = "SUPER_ADMIN";

    @Column(name = "reset_otp", length = 6)
    private String resetOtp;

    @Column(name = "reset_otp_expiry")
    private java.time.LocalDateTime resetOtpExpiry;
}
