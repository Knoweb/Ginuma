package com.example.GinumApps.util;

import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.mockito.InjectMocks;
import org.mockito.MockitoAnnotations;
import org.springframework.test.util.ReflectionTestUtils;

import static org.junit.jupiter.api.Assertions.*;

class JwtUtilTest {

    @InjectMocks
    private JwtUtil jwtUtil;

    @BeforeEach
    void setUp() {
        MockitoAnnotations.openMocks(this);
        // Set a dummy base64 secret key for testing (must be at least 256 bits)
        ReflectionTestUtils.setField(jwtUtil, "secretKeyString", "nDifHMj9c8hZfQEXQhuSUwTt6s7effZPjyM88Gt3Ea0=");
        ReflectionTestUtils.setField(jwtUtil, "expirationTime", 3600000L); // 1 hour
    }

    @Test
    void testGenerateAndValidateToken() {
        String token = jwtUtil.generateToken("test@example.com", "ROLE_USER");
        
        assertNotNull(token);
        assertTrue(jwtUtil.validateToken(token));
        assertEquals("test@example.com", jwtUtil.extractUsername(token));
    }

    @Test
    void testInvalidToken() {
        assertFalse(jwtUtil.validateToken("invalid.token.here"));
    }
}
