package com.sportshop.repository;

import com.sportshop.entity.Message;
import com.sportshop.entity.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface MessageRepository extends JpaRepository<Message, Integer> {  // Sửa Long sang Integer
    
    @Query("SELECT m FROM Message m WHERE m.user.userId = :uid ORDER BY m.sentAt ASC")
    List<Message> getChatHistoryByUserId(@Param("uid") Integer userId);

    @Query("SELECT DISTINCT m.user FROM Message m")
    List<User> findUsersWhoHaveChatted();
}