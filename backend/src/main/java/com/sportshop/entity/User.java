package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.data.annotation.CreatedDate;
import org.springframework.data.annotation.LastModifiedDate;
import org.springframework.data.jpa.domain.support.AuditingEntityListener;

import java.time.LocalDateTime;

@Entity
@Table(name = "Users") // Tên bảng là 'Users'
@Data
@NoArgsConstructor
@AllArgsConstructor
@EntityListeners(AuditingEntityListener.class)
public class User {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "UserID")
    private Integer userId;
    
    @Column(name = "FirstName", nullable = false, length = 50)
    private String firstName;
    
    @Column(name = "LastName", nullable = false, length = 50)
    private String lastName;
    
    @Column(name = "Email", unique = true, nullable = false, length = 100)
    private String email;
    
    @Column(name = "Phone", unique = true, nullable = false, length = 20)
    private String phone;
    
    @Column(name = "Password", nullable = false)
    private String password;
    
    @Column(name = "Address", nullable = false, columnDefinition = "TEXT")
    private String address;
    
    @Enumerated(EnumType.STRING)
    @Column(name = "Role", nullable = false)
    private Role role = Role.customer;
    
    @Column(name = "Status", nullable = false)
    private Boolean status = true; // true = 1 (active), false = 0 (locked)
    
    @CreatedDate
    @Column(name = "created_at", updatable = false)
    private LocalDateTime createdAt;
    
    @LastModifiedDate
    @Column(name = "updated_at")
    private LocalDateTime updatedAt;
    
    public enum Role {
        customer, admin
    }
}