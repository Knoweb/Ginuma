package com.example.GinumApps.dto;

import lombok.Data;

@Data
public class LoginResponse {
    private String token;
    private String role;
    private Integer userId;
    private Integer companyId;
    private String email;
    private String companyName;
    private boolean mfaRequired;
    private java.util.List<String> permissions;
}
