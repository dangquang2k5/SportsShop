package com.sportshop.config;

import lombok.Data;
@Data
public class MessageDTO {
    // Sửa Long -> Integer để khớp với UserRepository
    private Integer userId; 
    private String content; 
}