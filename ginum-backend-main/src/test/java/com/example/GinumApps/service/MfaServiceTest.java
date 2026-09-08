package com.example.GinumApps.service;

import dev.samstevens.totp.exceptions.QrGenerationException;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.*;

class MfaServiceTest {

    private MfaService mfaService;

    @BeforeEach
    void setUp() {
        mfaService = new MfaService();
    }

    @Test
    void testGenerateSecretKey() {
        String secret = mfaService.generateSecretKey();
        assertNotNull(secret);
        assertTrue(secret.length() >= 16);
    }

    @Test
    void testGetQrCodeImageUri() throws QrGenerationException {
        String secret = mfaService.generateSecretKey();
        String qrUri = mfaService.getQrCodeImageUri(secret, "test@example.com");
        
        assertNotNull(qrUri);
        assertTrue(qrUri.startsWith("data:image/png;base64,"));
    }

    @Test
    void testVerifyTotpInvalidCode() {
        String secret = mfaService.generateSecretKey();
        // Passing an obviously incorrect code to verify it fails securely
        assertFalse(mfaService.verifyTotp("000000", secret));
    }
}
