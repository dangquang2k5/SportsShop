package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.data.annotation.CreatedDate;
import org.springframework.data.annotation.LastModifiedDate;
import org.springframework.data.jpa.domain.support.AuditingEntityListener;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.List; // Thêm import

@Entity
@Table(name = "Product") // Sửa 'Products'
@Data
@NoArgsConstructor
@AllArgsConstructor
@EntityListeners(AuditingEntityListener.class)
public class Product {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "ProductID")
    private Integer productId;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "CategoryID")
    private Category category;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "BrandID")
    private Brand brand;
    
    @Column(name = "ProductName", nullable = false, length = 150)
    private String productName;
    
    @Column(name = "Description", columnDefinition = "TEXT")
    private String description;
    
    @Column(name = "Price", nullable = false, precision = 10, scale = 2)
    private BigDecimal price;
    
    @Column(name = "MainImage", length = 255)
    private String mainImage;
    
    @Column(name = "RatingAvg", precision = 3, scale = 2)
    private BigDecimal ratingAvg = BigDecimal.ZERO;
    
    @Column(name = "Status", length = 50)
    private String status = "active";
    
    @CreatedDate
    @Column(name = "created_at", updatable = false)
    private LocalDateTime createdAt;
    
    @LastModifiedDate
    @Column(name = "updated_at")
    private LocalDateTime updatedAt;

    // Thêm quan hệ với ProductDetail
    @OneToMany(mappedBy = "product")
    private List<ProductDetail> productDetails;
}