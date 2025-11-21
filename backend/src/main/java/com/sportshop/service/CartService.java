package com.sportshop.service;

import com.sportshop.entity.Cart;
import com.sportshop.entity.CartItem;
import com.sportshop.entity.ProductDetail;
import com.sportshop.entity.User;
import com.sportshop.repository.CartRepository;
import com.sportshop.repository.CartItemRepository;
import com.sportshop.repository.ProductDetailRepository;
import com.sportshop.repository.UserRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Service
@RequiredArgsConstructor
@Transactional
public class CartService {
    
    private final CartRepository cartRepository;
    private final CartItemRepository cartItemRepository;
    private final UserRepository userRepository;
    private final ProductDetailRepository productDetailRepository;
    
    public Cart getOrCreateCart(Integer userId) {
        Optional<Cart> cartOpt = cartRepository.findByUserUserId(userId);
        if (cartOpt.isPresent()) {
            return cartOpt.get();
        }
        
        User user = userRepository.findById(userId)
                .orElseThrow(() -> new RuntimeException("User not found"));
        
        Cart cart = new Cart();
        cart.setUser(user);
        return cartRepository.save(cart);
    }
    
    public List<CartItem> getCartItems(Integer userId) {
        Cart cart = getOrCreateCart(userId);
        return cartItemRepository.findByCartCartId(cart.getCartId());
    }
    
    public CartItem addToCart(Integer userId, Integer productDetailId, Integer quantity) {
        Cart cart = getOrCreateCart(userId);
        
        ProductDetail productDetail = productDetailRepository.findById(productDetailId)
                .orElseThrow(() -> new RuntimeException("Product detail not found"));
        
        // Check if item already exists in cart
        Optional<CartItem> existingItem = cartItemRepository
                .findByCartCartIdAndProductDetailProductDetailId(cart.getCartId(), productDetailId);
        
        if (existingItem.isPresent()) {
            CartItem item = existingItem.get();
            item.setQuantity(item.getQuantity() + quantity);
            return cartItemRepository.save(item);
        } else {
            CartItem newItem = new CartItem();
            newItem.setCart(cart);
            newItem.setProductDetail(productDetail);
            newItem.setQuantity(quantity);
            newItem.setPrice(productDetail.getProduct().getPrice());
            return cartItemRepository.save(newItem);
        }
    }
    
    public CartItem updateCartItem(Integer cartItemId, Integer quantity) {
        CartItem item = cartItemRepository.findById(cartItemId)
                .orElseThrow(() -> new RuntimeException("Cart item not found"));
        
        if (quantity <= 0) {
            cartItemRepository.delete(item);
            return null;
        }
        
        item.setQuantity(quantity);
        return cartItemRepository.save(item);
    }
    
    public void removeCartItem(Integer cartItemId) {
        CartItem item = cartItemRepository.findById(cartItemId)
                .orElseThrow(() -> new RuntimeException("Cart item not found"));
        cartItemRepository.delete(item);
    }
    
    public void clearCart(Integer userId) {
        Cart cart = getOrCreateCart(userId);
        cartItemRepository.deleteByCartId(cart.getCartId());
    }
}
