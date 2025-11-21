package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import java.math.BigDecimal;

@Entity
@Table(name = "CartItems")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class CartItem {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "CartItemsID")
    private Integer cartItemsId;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "CartID", nullable = false)
    private Cart cart;
    
    // SỬA: LỖI LOGIC "TRÁI TIM"
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "ProductDetailID", nullable = false)
    private ProductDetail productDetail; // Sửa từ 'Product'
    
    @Column(name = "Quantity", nullable = false)
    private Integer quantity = 1;
    
    @Column(name = "Price", nullable = false, precision = 10, scale = 2)
    private BigDecimal price;
}