import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, TextInput, ActivityIndicator, FlatList } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { useFocusEffect } from '@react-navigation/native';

console.log('MessagesScreen rendered');

export default function MessagesScreen({ route }) {
  const distributor = route?.params?.distributor;
  const [conversations, setConversations] = useState([]);
  const [selectedConsumer, setSelectedConsumer] = useState(null);
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(false);
  const [messageText, setMessageText] = useState('');
  const [sending, setSending] = useState(false);

  const fetchConversations = useCallback(() => {
    console.log('fetchConversations called, distributor:', distributor);
    if (!distributor?.distributor_id) {
      console.log('No distributor_id, returning early');
      return;
    }
    const url = `http://192.168.1.3/pureflowBackend/get_distributor_conversations.php?distributor_id=${distributor.distributor_id}`;
    console.log('Fetching conversations from:', url);
    fetch(url)
      .then(res => res.json())
      .then(data => {
        console.log('Fetched conversations:', data);
        if (data.success && Array.isArray(data.consumers)) setConversations(data.consumers);
        else setConversations([]);
      })
      .catch(err => {
        console.log('Error fetching conversations:', err);
      });
  }, [distributor]);

  useEffect(() => {
    console.log('useEffect called, distributor:', distributor);
    fetchConversations();
  }, [fetchConversations]);

  useFocusEffect(
    useCallback(() => {
      console.log('useFocusEffect called, distributor:', distributor);
      fetchConversations();
    }, [fetchConversations])
  );

  useEffect(() => {
    if (!selectedConsumer) return;
    setLoading(true);
    fetch(`http://192.168.1.3/pureflowBackend/get_messages.php?consumer_id=${selectedConsumer.consumer_id}&distributor_id=${distributor.distributor_id}`)
      .then(res => res.json())
      .then(data => {
        console.log('Fetched messages:', data);
        if (data.success && Array.isArray(data.messages)) setMessages(data.messages);
        else setMessages([]);
        setLoading(false);
      })
      .catch(err => {
        console.log('Error fetching messages:', err);
        setLoading(false);
      });
  }, [selectedConsumer, distributor]);

  const handleSendMessage = async () => {
    if (!selectedConsumer || !messageText.trim()) return;
    setSending(true);
    try {
      const res = await fetch('http://192.168.1.3/pureflowBackend/send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          consumer_id: selectedConsumer.consumer_id,
          distributor_id: distributor.distributor_id,
          message: messageText,
          sender: 'distributor',
        }),
      });
      const data = await res.json();
      if (data.success) {
        fetch(`http://192.168.1.3/pureflowBackend/get_messages.php?consumer_id=${selectedConsumer.consumer_id}&distributor_id=${distributor.distributor_id}`)
          .then(res => res.json())
          .then(data => {
            if (data.success && Array.isArray(data.messages)) setMessages(data.messages);
          });
        setMessageText('');
        fetchConversations();
      }
    } catch (e) {
      console.log('Send message error:', e);
    }
    setSending(false);
  };

  return (
    <View style={styles.container}>
      <LinearGradient colors={["#3FE0E8", "#3578C9"]} style={styles.header}>
        <Text style={styles.headerTitle}>Messages</Text>
      </LinearGradient>
      <View style={styles.contentRow}>
        <View style={styles.convoList}>
          <Text style={styles.sectionTitle}>Conversations</Text>
          <ScrollView>
            {(!conversations || conversations.length === 0) && <Text style={{ color: '#888', marginTop: 10 }}>No conversations yet.</Text>}
            {conversations && conversations.map(consumer => (
              <TouchableOpacity
                key={String(consumer.consumer_id)}
                style={[
                  styles.convoItem,
                  String(selectedConsumer?.consumer_id) === String(consumer.consumer_id) && styles.convoItemSelected
                ]}
                onPress={() => setSelectedConsumer(consumer)}
              >
                <Text style={styles.convoName}>{consumer.name}</Text>
                <Text style={styles.convoEmail}>{consumer.email}</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>
        </View>
        <View style={styles.chatArea}>
          {selectedConsumer ? (
            <>
              <Text style={styles.chatTitle}>Chat with {selectedConsumer.name}</Text>
              {loading ? (
                <ActivityIndicator size="large" color="#3578C9" />
              ) : (
                <FlatList
                  data={messages}
                  keyExtractor={item => String(item.id)}
                  renderItem={({ item }) => (
                    <View style={[styles.bubble, item.sender === 'distributor' ? styles.bubbleRight : styles.bubbleLeft]}>
                      <Text style={styles.bubbleText}>{item.message}</Text>
                      <Text style={styles.bubbleTime}>{item.sent_at ? new Date(item.sent_at).toLocaleString() : ''}</Text>
                    </View>
                  )}
                  contentContainerStyle={{ paddingVertical: 10 }}
                />
              )}
              <View style={styles.inputRow}>
                <TextInput
                  style={styles.input}
                  value={messageText}
                  onChangeText={setMessageText}
                  placeholder="Type your message..."
                  editable={!sending}
                />
                <TouchableOpacity style={styles.sendBtn} onPress={handleSendMessage} disabled={sending || !messageText.trim()}>
                  <Text style={styles.sendBtnText}>{sending ? '...' : 'Send'}</Text>
                </TouchableOpacity>
              </View>
            </>
          ) : (
            <Text style={{ color: '#888', marginTop: 30, textAlign: 'center' }}>Select a conversation to view messages.</Text>
          )}
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f4f7fb' },
  header: { paddingTop: 50, paddingBottom: 20, paddingHorizontal: 20 },
  headerTitle: { fontSize: 24, fontWeight: 'bold', color: '#fff' },
  contentRow: { flex: 1, flexDirection: 'row' },
  convoList: { width: 180, backgroundColor: '#fff', borderRightWidth: 1, borderRightColor: '#e0eaf0', padding: 10 },
  sectionTitle: { fontSize: 16, fontWeight: 'bold', color: '#3578C9', marginBottom: 10 },
  convoItem: { paddingVertical: 12, paddingHorizontal: 8, borderBottomWidth: 1, borderBottomColor: '#f0f0f0' },
  convoItemSelected: { backgroundColor: '#eaf6fa' },
  convoName: { fontSize: 15, fontWeight: 'bold', color: '#3578C9' },
  convoEmail: { fontSize: 13, color: '#888' },
  chatArea: { flex: 1, padding: 15 },
  chatTitle: { fontSize: 17, fontWeight: 'bold', color: '#3578C9', marginBottom: 10 },
  bubble: { maxWidth: '80%', padding: 10, borderRadius: 10, marginBottom: 8 },
  bubbleLeft: { backgroundColor: '#e0e0e0', alignSelf: 'flex-start' },
  bubbleRight: { backgroundColor: '#3FE0E8', alignSelf: 'flex-end' },
  bubbleText: { color: '#222' },
  bubbleTime: { fontSize: 12, color: '#888', marginTop: 5, alignSelf: 'flex-end' },
  inputRow: { flexDirection: 'row', alignItems: 'center', marginTop: 10 },
  input: { flex: 1, backgroundColor: '#f4f7fb', borderRadius: 20, paddingVertical: 10, paddingHorizontal: 15, fontSize: 16, color: '#222', borderWidth: 1, borderColor: '#e0e0e0', marginRight: 10 },
  sendBtn: { backgroundColor: '#3578C9', borderRadius: 20, paddingVertical: 10, paddingHorizontal: 18 },
  sendBtnText: { color: '#fff', fontWeight: 'bold', fontSize: 15 },
}); 