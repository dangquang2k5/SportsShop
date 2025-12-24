package com.sportshop.controller;

import com.sportshop.entity.CartItem;
import com.sportshop.service.CartService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/cart")
@RequiredArgsConstructor
@CrossOrigin(origins = "*")
public class CartController {
    
    private final CartService cartService;
    
    @GetMapping("/user/{userId}")
    public ResponseEntity<List<CartItem>> getCartItems(@PathVariable Integer userId) {
        return ResponseEntity.ok(cartService.getCartItems(userId));
    }
    
    @PostMapping("/add")
    public ResponseEntity<CartItem> addToCart(
            @RequestParam Integer userId,
            @RequestParam Integer productDetailId,
            @RequestParam(defaultValue = "1") Integer quantity) {
        try {
            CartItem item = cartService.addToCart(userId, productDetailId, quantity);
            return ResponseEntity.ok(item);
        } catch (RuntimeException e) {
            return ResponseEntity.badRequest().build();
        }
    }
    
    @PutMapping("/item/{cartItemId}")
    public ResponseEntity<CartItem> updateCartItem(
            @PathVariable Integer cartItemId,
            @RequestParam Integer quantity) {
        try {
            CartItem updated = cartService.updateCartItem(cartItemId, quantity);
            if (updated == null) {
                return ResponseEntity.noContent().build();
            }
            return ResponseEntity.ok(updated);
        } catch (RuntimeException e) {
            return ResponseEntity.notFound().build();
        }
    }
    
    @DeleteMapping("/item/{cartItemId}")
    public ResponseEntity<Void> removeCartItem(@PathVariable Integer cartItemId) {
        try {
            cartService.removeCartItem(cartItemId);
            return ResponseEntity.ok().build();
        } catch (RuntimeException e) {
            return ResponseEntity.notFound().build();
        }
    }
    
    @DeleteMapping("/user/{userId}/clear")
    public ResponseEntity<Map<String, String>> clearCart(@PathVariable Integer userId) {
        cartService.clearCart(userId);
        Map<String, String> response = new HashMap<>();
        response.put("message", "Cart cleared successfully");
        return ResponseEntity.ok(response);
    }
}
