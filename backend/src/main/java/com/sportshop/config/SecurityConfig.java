package com.sportshop.config;

// THÊM CÁC IMPORT NÀY
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity; // Thêm
import org.springframework.security.web.SecurityFilterChain; // Thêm
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;

@Configuration
public class SecurityConfig {

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }

    // --- THÊM KHỐI CODE NÀY VÀO ---
    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
        http
            .csrf(csrf -> csrf.disable()) // Tắt CSRF
            .authorizeHttpRequests(auth -> auth
                .anyRequest().permitAll() // Cho phép TẤT CẢ các request
            );
        return http.build();
    }
}