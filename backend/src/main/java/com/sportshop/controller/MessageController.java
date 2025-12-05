package com.sportshop.controller;

import com.sportshop.config.MessageDTO;
import com.sportshop.entity.Message;
import com.sportshop.entity.User;
import com.sportshop.service.MessageService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/messages")
@CrossOrigin(origins = "*") // Cho phép gọi từ PHP (cổng 80) sang Java (cổng 8080)
public class MessageController {

    @Autowired
    private MessageService messageService;

    // Lấy lịch sử chat của 1 user cụ thể
    @GetMapping("/history/{id}")
    public List<Message> getHistory(@PathVariable("id") Integer userId) {
        return messageService.getChatHistory(userId);
    }

    // Gửi tin nhắn từ phía User (Khách hàng)
    @PostMapping("/send")
    public ResponseEntity<?> sendMessage(@RequestBody MessageDTO messageDTO) {
        try {
            // User gửi thì adminFlag = false
            Message message = messageService.sendMessage(messageDTO.getUserId(), messageDTO.getContent(), false);
            return ResponseEntity.ok(message);
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Lỗi gửi tin nhắn: " + e.getMessage());
        }
    }
    
    // API MỚI: Dành cho Admin để lấy danh sách các user đã nhắn tin
    @GetMapping("/users-chatted")
    public List<User> getUsersChatted() {
        return messageService.getUsersWhoChatted(); // Bạn cần đảm bảo Service có hàm này
    }
    // ... (Các code cũ giữ nguyên)

    // API MỚI: Dành cho Admin trả lời tin nhắn
    @PostMapping("/admin/send")
    public ResponseEntity<?> sendAdminMessage(@RequestBody MessageDTO messageDTO) {
        try {
            // Admin gửi thì tham số thứ 3 là TRUE
            Message message = messageService.sendMessage(messageDTO.getUserId(), messageDTO.getContent(), true);
            return ResponseEntity.ok(message);
        } catch (Exception e) {
            return ResponseEntity.badRequest().body("Lỗi gửi tin admin: " + e.getMessage());
        }
    }
}
