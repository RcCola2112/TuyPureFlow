import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

export default function ContactUsScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Contact Us</Text>
      <Text style={styles.text}>This is the Contact Us screen.</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#fff' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#3578C9', marginBottom: 10 },
  text: { fontSize: 16, color: '#444' },
}); 