package com.sportshop.repository;

import com.sportshop.entity.Review;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface ReviewRepository extends JpaRepository<Review, Integer> {
    
    List<Review> findByProductProductId(Integer productId);
    
    List<Review> findByUserUserId(Integer userId);
    
    List<Review> findByStatus(String status);
    
    @Query("SELECT r FROM Review r WHERE r.product.productId = :productId AND r.status = :status ORDER BY r.createdAt DESC")
    List<Review> findByProductIdAndStatus(@Param("productId") Integer productId, @Param("status") String status);
}
