package com.sportshop.repository;

import com.sportshop.entity.CartItem;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Modifying;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface CartItemRepository extends JpaRepository<CartItem, Integer> {
    
    List<CartItem> findByCartCartId(Integer cartId);
    
    Optional<CartItem> findByCartCartIdAndProductDetailProductDetailId(Integer cartId, Integer productDetailId);
    
    @Modifying
    @Query("DELETE FROM CartItem ci WHERE ci.cart.cartId = :cartId")
    void deleteByCartId(@Param("cartId") Integer cartId);
}
