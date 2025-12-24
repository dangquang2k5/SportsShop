package com.sportshop.repository;

import com.sportshop.entity.ProductDetail;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ProductDetailRepository extends JpaRepository<ProductDetail, Integer> {
    
    /**
     * (HÀM MỚI - Rất quan trọng)
     * Tự động tạo truy vấn: "SELECT * FROM ProductDetail WHERE ProductID = ?"
     * Tên hàm này phải khớp chính xác với tên trường (field) trong ProductDetail.java:
     * "findBy" + "Product" (tên trường) + "ProductId" (trường con - camelCase)
     */
    List<ProductDetail> findByProductProductId(Integer productId);
}