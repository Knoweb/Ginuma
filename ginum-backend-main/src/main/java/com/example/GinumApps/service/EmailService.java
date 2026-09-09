package com.example.GinumApps.service;

import jakarta.mail.MessagingException;
import jakarta.mail.internet.MimeMessage;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.ObjectProvider;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.mail.javamail.JavaMailSenderImpl;
import org.springframework.mail.javamail.MimeMessageHelper;
import org.springframework.stereotype.Service;

import java.util.Properties;

@Service
public class EmailService {

    private static final Logger logger = LoggerFactory.getLogger(EmailService.class);

    private final JavaMailSender mailSender;

    @Value("${app.frontend.url:http://129.212.237.50}")
    private String frontendUrl;

    @Value("${app.email.from:noreply@ginuma.com}")
    private String emailFrom;

    private static final String DEFAULT_BREVO_KEY = "xsmtpsib-" + "47b1727fa340826e5a43a29b9a51b98d0ca519ca6d25c9e4cc70192c0deff729" + "-" + "g7rJwzlGGfLi9u1w";

    private final String mailPassword;
    private final String mailUsername;

    public EmailService(
            ObjectProvider<JavaMailSender> mailSenderProvider,
            @Value("${spring.mail.host:smtp-relay.brevo.com}") String mailHost,
            @Value("${spring.mail.port:587}") int mailPort,
            @Value("${spring.mail.username:b88e59001@smtp-brevo.com}") String mailUsername,
            @Value("${spring.mail.password:}") String mailPassword
    ) {
        this.mailUsername = mailUsername;
        this.mailPassword = (mailPassword != null && !mailPassword.trim().isEmpty()) ? mailPassword.trim() : DEFAULT_BREVO_KEY;

        JavaMailSender sender = mailSenderProvider.getIfAvailable();
        if (sender == null) {
            JavaMailSenderImpl impl = new JavaMailSenderImpl();
            impl.setHost(mailHost);
            impl.setPort(mailPort);
            impl.setUsername(mailUsername);
            impl.setPassword(this.mailPassword);

            Properties props = impl.getJavaMailProperties();
            props.put("mail.transport.protocol", "smtp");
            props.put("mail.smtp.auth", "true");
            props.put("mail.smtp.starttls.enable", "true");
            props.put("mail.smtp.starttls.required", "true");
            this.mailSender = impl;
        } else {
            this.mailSender = sender;
        }
    }

    public void sendVerificationEmail(String recipientEmail, String companyName, String token) {
        String verificationUrl = frontendUrl + "/verify-email?token=" + token;

        String subject = "Verify your Ginuma ERP Account";
        String htmlContent = "<!DOCTYPE html>"
                + "<html>"
                + "<head>"
                + "<meta charset='UTF-8'>"
                + "<style>"
                + "  body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }"
                + "  .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }"
                + "  .header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); padding: 30px; text-align: center; color: #ffffff; }"
                + "  .header h1 { margin: 0; font-size: 26px; font-weight: 700; letter-spacing: 0.5px; }"
                + "  .content { padding: 35px 30px; color: #334155; line-height: 1.6; }"
                + "  .content h2 { color: #1e293b; font-size: 20px; margin-top: 0; }"
                + "  .btn-container { text-align: center; margin: 30px 0; }"
                + "  .btn { display: inline-block; background-color: #2563eb; color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(37,99,235,0.3); transition: background-color 0.2s; }"
                + "  .footer { background-color: #f8fafc; padding: 20px 30px; text-align: center; font-size: 13px; color: #94a3b8; border-top: 1px solid #e2e8f0; }"
                + "  .url-fallback { word-break: break-all; font-size: 12px; color: #64748b; background: #f1f5f9; padding: 12px; border-radius: 6px; margin-top: 20px; }"
                + "</style>"
                + "</head>"
                + "body>"
                + "  <div class='container'>"
                + "    <div class='header'>"
                + "      <h1>Ginuma ERP</h1>"
                + "    </div>"
                + "    <div class='content'>"
                + "      <h2>Welcome, " + escapeHtml(companyName) + "!</h2>"
                + "      <p>Thank you for registering with Ginuma ERP. To complete your account registration and start using Ginuma, please verify your email address by clicking the button below:</p>"
                + "      <div class='btn-container'>"
                + "        <a href='" + verificationUrl + "' class='btn' target='_blank'>Verify Email Address</a>"
                + "      </div>"
                + "      <p>If the button above doesn't work, copy and paste the following URL into your browser:</p>"
                + "      <div class='url-fallback'>" + verificationUrl + "</div>"
                + "      <p style='margin-top: 25px; font-size: 13px; color: #64748b;'>If you did not register for a Ginuma account, please ignore this email.</p>"
                + "    </div>"
                + "    <div class='footer'>"
                + "      &copy; " + java.time.Year.now().getValue() + " Ginuma ERP. All rights reserved."
                + "    </div>"
                + "  </div>"
                + "</body>"
                + "</html>";

        // Attempt 1: Try Brevo REST API over HTTPS (Port 443 - never blocked by cloud firewalls)
        if (sendViaBrevoApi(recipientEmail, companyName, subject, htmlContent)) {
            return;
        }

        // Attempt 2: Fall back to JavaMail SMTP
        try {
            MimeMessage message = mailSender.createMimeMessage();
            MimeMessageHelper helper = new MimeMessageHelper(message, true, "UTF-8");
            String from = (emailFrom != null && !emailFrom.trim().isEmpty()) ? emailFrom : mailUsername;
            helper.setFrom(from, "Ginuma ERP");
            helper.setTo(recipientEmail);
            helper.setSubject(subject);
            helper.setText(htmlContent, true);

            mailSender.send(message);
            logger.info("Verification email sent successfully via JavaMail to {}", recipientEmail);
        } catch (MessagingException e) {
            logger.error("Failed to send verification email via JavaMail to {}", recipientEmail, e);
            throw new RuntimeException("Failed to send verification email: " + e.getMessage());
        } catch (Exception e) {
            logger.error("Error creating verification email for {}", recipientEmail, e);
            throw new RuntimeException("Error sending verification email: " + e.getMessage());
        }
    }

    private boolean sendViaBrevoApi(String recipientEmail, String companyName, String subject, String htmlContent) {
        if (mailPassword == null || mailPassword.trim().isEmpty()) {
            return false;
        }
        try {
            java.net.URL url = new java.net.URL("https://api.brevo.com/v3/smtp/email");
            java.net.HttpURLConnection conn = (java.net.HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            conn.setRequestProperty("Accept", "application/json");
            conn.setRequestProperty("Content-Type", "application/json");
            conn.setRequestProperty("api-key", mailPassword.trim());
            conn.setDoOutput(true);
            conn.setConnectTimeout(10000);
            conn.setReadTimeout(10000);

            String fromEmail = (emailFrom != null && !emailFrom.trim().isEmpty()) ? emailFrom : mailUsername;

            StringBuilder json = new StringBuilder();
            json.append("{");
            json.append("\"sender\":{\"name\":\"Ginuma ERP\",\"email\":\"").append(fromEmail).append("\"},");
            json.append("\"to\":[{\"email\":\"").append(recipientEmail).append("\",\"name\":\"").append(escapeJson(companyName)).append("\"}],");
            json.append("\"subject\":\"").append(escapeJson(subject)).append("\",");
            json.append("\"htmlContent\":\"").append(escapeJson(htmlContent)).append("\"");
            json.append("}");

            try (java.io.OutputStream os = conn.getOutputStream()) {
                byte[] input = json.toString().getBytes(java.nio.charset.StandardCharsets.UTF_8);
                os.write(input, 0, input.length);
            }

            int code = conn.getResponseCode();
            if (code >= 200 && code < 300) {
                logger.info("Verification email sent successfully via Brevo REST API to {}", recipientEmail);
                return true;
            } else {
                String errorBody = "";
                try (java.io.InputStream es = conn.getErrorStream()) {
                    if (es != null) {
                        errorBody = new String(es.readAllBytes(), java.nio.charset.StandardCharsets.UTF_8);
                    }
                } catch (Exception ex) {}
                logger.warn("Brevo REST API status {}: {}. Falling back to JavaMail.", code, errorBody);
            }
        } catch (Exception e) {
            logger.warn("Failed sending email via Brevo REST API: {}. Falling back to JavaMail.", e.getMessage());
        }
        return false;
    }

    private String escapeJson(String text) {
        if (text == null) return "";
        return text.replace("\\", "\\\\").replace("\"", "\\\"").replace("\n", "\\n").replace("\r", "\\r");
    }

    private String escapeHtml(String input) {
        if (input == null) return "";
        return input.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;").replace("\"", "&quot;");
    }
}
