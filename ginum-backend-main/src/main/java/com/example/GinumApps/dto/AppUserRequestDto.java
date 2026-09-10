package com.example.GinumApps.dto;

import jakarta.validation.constraints.*;
import lombok.Data;

@Data
public class AppUserRequestDto {
    @NotBlank(message = "Email is required")
    @Email(message = "Invalid email format")
    private String email;

    private String password;

    @NotBlank(message = "Role is required")
    private String role;

    private String name;
}