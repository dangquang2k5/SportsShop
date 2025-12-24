package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@Table(name = "Categories")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Category {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "CategoryID")
    private Integer categoryId;
    
    @Column(name = "CategoryName", nullable = false, length = 100, unique = true)
    private String categoryName;
    
    @Column(name = "CategoryDescription", columnDefinition = "TEXT")
    private String categoryDescription;
}