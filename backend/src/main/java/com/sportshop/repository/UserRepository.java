package com.sportshop.repository;

import com.sportshop.entity.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface UserRepository extends JpaRepository<User, Integer> {
    
    // SỬA: Đổi từ Username sang Phone
    Optional<User> findByPhone(String phone); 
    
    Optional<User> findByEmail(String email);
    
    // SỬA: Đổi từ Username sang Phone
    boolean existsByPhone(String phone); 
    
    boolean existsByEmail(String email);
    
    // SỬA: Dùng kiểu ENUM mới (nếu bạn đã sửa User.java)
    List<User> findByRole(User.Role role);
    Long countByRole(User.Role role);
}