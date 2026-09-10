package com.example.GinumApps.config;

import com.example.GinumApps.filter.JwtFilter;
import jakarta.servlet.DispatcherType;
import lombok.RequiredArgsConstructor;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.http.HttpMethod;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.annotation.authentication.configuration.AuthenticationConfiguration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.http.SessionCreationPolicy;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.authentication.UsernamePasswordAuthenticationFilter;
import org.springframework.web.cors.CorsConfiguration;
import org.springframework.web.cors.UrlBasedCorsConfigurationSource;

import java.util.Arrays;
import java.util.List;

@Configuration
@EnableWebSecurity
@RequiredArgsConstructor
public class SecurityConfig {

    private final JwtFilter jwtFilter;

    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
        http
                .cors(cors -> cors.configurationSource(corsConfigurationSource()))
                .csrf(csrf -> csrf.disable())
                .headers(headers -> headers
                        .xssProtection(xss -> xss.disable()) // Disable Spring's deprecated XSS protection, rely on CSP
                        .contentSecurityPolicy(csp -> csp.policyDirectives("default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;"))
                        .frameOptions(frame -> frame.deny()) // Clickjacking protection
                        .contentTypeOptions(contentType -> {}) // X-Content-Type-Options: nosniff
                        .httpStrictTransportSecurity(hsts -> hsts.includeSubDomains(true).maxAgeInSeconds(31536000))
                )
                .sessionManagement(session ->
                        session.sessionCreationPolicy(SessionCreationPolicy.STATELESS)
                )
                .authorizeHttpRequests(auth -> auth

                        // Allow CORS preflight requests
                        .requestMatchers(HttpMethod.OPTIONS, "/**").permitAll()

                        // Allow Spring error page also
                        .dispatcherTypeMatchers(DispatcherType.ERROR).permitAll()
                        .requestMatchers("/error").permitAll()

                        // Public endpoints
                        .requestMatchers(
                                "/api/auth/login",
                                "/api/auth/verify-email",
                                "/api/auth/resend-verification",
                                "/api/auth/verify-login-otp",
                                "/api/roles",
                                "/api/roles/**",
                                "/api/companies",
                                "/api/companies/register",
                                "/api/countries",
                                "/api/countries/**",
                                "/api/currencies",
                                "/api/currencies/**"
                        ).permitAll()

                        // Demo Seeder & Reconcile endpoints
                        .requestMatchers(HttpMethod.POST, "/api/demo/seed/company/**").permitAll()
                        .requestMatchers(HttpMethod.POST, "/api/demo/seed-transactions/company/**").permitAll()
                        .requestMatchers(HttpMethod.POST, "/api/demo/reconcile/company/**").permitAll()
                        .requestMatchers(HttpMethod.POST, "/api/demo/reset-excel-demo/company/**").permitAll()
                        .requestMatchers(HttpMethod.POST, "/api/demo/fresh-excel-reset/company/**").permitAll()
                        .requestMatchers(HttpMethod.POST, "/api/demo/excel-phase1/company/**").permitAll()
                        .requestMatchers(HttpMethod.POST, "/api/demo/excel-phase2/company/**").permitAll()
                        .requestMatchers(HttpMethod.POST, "/api/demo/excel-fix-visibility/company/**").permitAll()



                        // All other endpoints need login
                        .anyRequest().authenticated()
                )
                .addFilterBefore(jwtFilter, UsernamePasswordAuthenticationFilter.class);

        return http.build();
    }

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }

    @Bean
    public AuthenticationManager authenticationManager(
            AuthenticationConfiguration config
    ) throws Exception {
        return config.getAuthenticationManager();
    }

    @Bean
    public UrlBasedCorsConfigurationSource corsConfigurationSource() {
        CorsConfiguration config = new CorsConfiguration();

        config.setAllowedOriginPatterns(List.of("*"));

        config.setAllowedMethods(
                Arrays.asList("GET", "POST", "PUT", "DELETE", "OPTIONS", "PATCH")
        );

        config.setAllowedHeaders(
                Arrays.asList("Authorization", "Content-Type", "Accept", "X-DEMO-SEED-TOKEN")
        );

        config.setAllowCredentials(true);

        UrlBasedCorsConfigurationSource source =
                new UrlBasedCorsConfigurationSource();

        source.registerCorsConfiguration("/**", config);
        return source;
    }
}