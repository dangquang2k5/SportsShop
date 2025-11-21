package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.time.LocalDateTime;

@Entity
@Table(name = "InventoryLog") // Sửa 'Warehouse'
@Data
@NoArgsConstructor
@AllArgsConstructor
public class InventoryLog { // Sửa 'Warehouse'
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "LogID")
    private Integer logId;
    
    // SỬA: LỖI LOGIC
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "ProductDetailID", nullable = false)
    private ProductDetail productDetail; // Sửa từ 'Product'
    
    @Column(name = "QuantityIn", nullable = false)
    private Integer quantityIn = 0;
    
    @Column(name = "QuantityOut", nullable = false)
    private Integer quantityOut = 0;
    
    @Column(name = "Remaining", nullable = false)
    private Integer remaining = 0;
    
    @Column(name = "Reason", length = 255)
    private String reason; // Thêm trường này

    @Column(name = "created_at") // Sửa 'UpdateAt'
    private LocalDateTime createdAt;
}