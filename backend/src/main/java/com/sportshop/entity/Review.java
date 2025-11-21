package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import java.time.LocalDateTime;

@Entity
@Table(name = "Reviews") // Sửa 'Comment'
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Review { // Sửa 'Comment'
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "ReviewID")
    private Integer reviewId;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "UserID", nullable = false)
    private User user;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "ProductID", nullable = false)
    private Product product;
    
    @Column(name = "Content", columnDefinition = "TEXT", nullable = false)
    private String content;
    
    @Column(name = "Rating", nullable = false)
    private Integer rating;
    
    @Column(name = "Status", length = 50, nullable = false)
    private String status = "pending";

    @Column(name = "created_at") // Sửa 'CreateDate'
    private LocalDateTime createdAt;
}