package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import java.math.BigDecimal;

@Entity
@Table(name = "OrderDetails") // Sửa 'OrderItems'
@Data
@NoArgsConstructor
@AllArgsConstructor
public class OrderDetail { // Sửa 'OrderItem'
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "OrderDetailID")
    private Integer orderDetailId;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "OrderID", nullable = false)
    private Order order;
    
    // SỬA: LỖI LOGIC "TRÁI TIM"
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "ProductDetailID", nullable = false)
    private ProductDetail productDetail; // Sửa từ 'Product'
    
    @Column(name = "Quantity", nullable = false)
    private Integer quantity;
    
    @Column(name = "Price", nullable = false, precision = 10, scale = 2)
    private BigDecimal price;
}