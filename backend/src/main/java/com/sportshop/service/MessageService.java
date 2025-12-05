package com.sportshop.service;

import com.sportshop.entity.Message;
import com.sportshop.entity.User;
import com.sportshop.repository.MessageRepository;
import com.sportshop.repository.UserRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.List;

@Service
@RequiredArgsConstructor
@Transactional
public class MessageService {

    private final MessageRepository messageRepository;
    private final UserRepository userRepository;

    public List<Message> getChatHistory(Integer userId) {
        return messageRepository.getChatHistoryByUserId(userId);
    }

    public Message sendMessage(Integer userId, String content, boolean isAdmin) {
        User user = userRepository.findById(userId)
                .orElseThrow(() -> new RuntimeException("User not found with ID: " + userId));

        Message message = new Message();
        message.setUser(user);
        message.setContent(content);
        message.setAdminFlag(isAdmin); 
        message.setSentAt(LocalDateTime.now());

        return messageRepository.save(message);
    }
    
    // Hàm hỗ trợ Admin lấy danh sách người chat
    public List<User> getUsersWhoChatted() {
        return messageRepository.findUsersWhoHaveChatted();
    }
}