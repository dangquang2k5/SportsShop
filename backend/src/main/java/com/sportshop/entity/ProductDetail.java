package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@Table(name = "ProductDetail")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class ProductDetail {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "ProductDetailID")
    private Integer productDetailId;
    
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "ProductID", nullable = false)
    private Product product;
    
    @Column(name = "Size", length = 20)
    private String size;
    
    @Column(name = "Color", length = 50)
    private String color;
    
    @Column(name = "Quantity", nullable = false)
    private Integer quantity = 0;
    
    @Column(name = "Image", length = 255)
    private String image;
}
